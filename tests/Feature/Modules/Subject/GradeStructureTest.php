<?php

namespace Tests\Feature\Modules\Subject;

use App\Modules\Subject\Models\GradeComponentTemplate;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\GradeStructureService;
use App\Modules\System\Models\PolicySet;
use App\Modules\System\Services\PolicyResolver;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\References\ReferenceRegistry;
use Database\Seeders\FacultySeeder;
use Database\Seeders\GradeStructureSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class GradeStructureTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private GradeStructureService $structures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SystemSeeder::class]);
        $this->seedRoles();
        $this->seed([FacultySeeder::class, SubjectSeeder::class]);
        $this->structures = app(GradeStructureService::class);
    }

    private function subject(string $code = 'INT201'): Subject
    {
        return Subject::query()->where('code', $code)->firstOrFail();
    }

    /** @return list<array<string, mixed>> */
    private function standard(): array
    {
        return [
            ['name' => 'Chuyên cần', 'kind' => 'attendance', 'weight' => 10],
            ['name' => 'Giữa kỳ', 'kind' => 'midterm', 'weight' => 30],
            ['name' => 'Thi cuối kỳ', 'kind' => 'final_exam', 'weight' => 60, 'min_score' => 1],
        ];
    }

    private function rejected(callable $callback): BusinessRuleException
    {
        try {
            $callback();
        } catch (BusinessRuleException $e) {
            $this->assertNotEmpty($e->hint(), 'Thông báo từ chối phải kèm cách khắc phục (GC-04).');

            return $e;
        }

        $this->fail('Thao tác lẽ ra bị từ chối.');
    }

    public function test_luu_co_cau_hop_le_va_sao_chep_cho_lop_hoc_phan(): void
    {
        $this->structures->replace($this->subject(), $this->standard());

        $template = $this->structures->templateFor($this->subject());
        $this->assertSame(['Chuyên cần', 'Giữa kỳ', 'Thi cuối kỳ'], array_column($template, 'name'));
        $this->assertSame([10.0, 30.0, 60.0], array_column($template, 'weight'));
        $this->assertSame(1.0, $template[2]['min_score']);
        $this->assertSame([1, 2, 3], array_column($template, 'sort_order'));
    }

    public function test_tong_trong_so_90_phan_tram_bi_tu_choi_neu_ro_thieu_bao_nhieu(): void
    {
        $components = $this->standard();
        $components[1]['weight'] = 20;

        // UAT-03: trọng số điểm phải bằng 100%
        $e = $this->rejected(fn () => $this->structures->replace($this->subject(), $components));
        $this->assertStringContainsString('là 90%, phải đúng 100% (thiếu 10%)', $e->getMessage());

        $components[1]['weight'] = 40.5;
        $e = $this->rejected(fn () => $this->structures->replace($this->subject(), $components));
        $this->assertStringContainsString('thừa 10,5%', $e->getMessage());
        $this->assertSame(0, GradeComponentTemplate::count());
    }

    public function test_toi_thieu_3_thanh_phan_va_ten_khong_trung(): void
    {
        $this->rejected(fn () => $this->structures->replace($this->subject(), [
            ['name' => 'Giữa kỳ', 'kind' => 'midterm', 'weight' => 40],
            ['name' => 'Thi cuối kỳ', 'kind' => 'final_exam', 'weight' => 60],
        ]));

        $components = $this->standard();
        $components[1]['name'] = 'chuyên cần ';
        $this->rejected(fn () => $this->structures->replace($this->subject(), $components));
    }

    public function test_thi_cuoi_ky_toi_thieu_50_va_tu_xa_toi_da_50(): void
    {
        $e = $this->rejected(fn () => $this->structures->replace($this->subject(), [
            ['name' => 'Chuyên cần', 'kind' => 'attendance', 'weight' => 20],
            ['name' => 'Giữa kỳ', 'kind' => 'midterm', 'weight' => 40],
            ['name' => 'Thi cuối kỳ', 'kind' => 'final_exam', 'weight' => 40],
        ]));
        $this->assertStringContainsString('thấp hơn mức tối thiểu 50%', $e->getMessage());

        $e = $this->rejected(fn () => $this->structures->replace($this->subject(), [
            ['name' => 'Bài tập trực tuyến', 'kind' => 'assignment', 'weight' => 20, 'is_remote' => true],
            ['name' => 'Giữa kỳ trực tuyến', 'kind' => 'midterm', 'weight' => 20, 'is_remote' => true],
            ['name' => 'Thi cuối kỳ trực tuyến', 'kind' => 'final_exam', 'weight' => 60, 'is_remote' => true],
        ]));
        $this->assertStringContainsString('vượt mức tối đa 50%', $e->getMessage());
    }

    public function test_nguong_lay_tu_bo_quy_che(): void
    {
        $set = PolicySet::query()->firstOrFail();
        $values = $set->values();
        $this->assertSame(50, (int) $values['grading.final_exam_min_weight']);

        // Bộ quy chế mới (ban hành) hạ ngưỡng thi cuối kỳ xuống 40%: cơ cấu 30/30/40 hợp lệ
        $set->items()->where('key', 'grading.final_exam_min_weight')->update(['value' => json_encode(40)]);
        app(PolicyResolver::class)->flush();

        $this->structures->replace($this->subject(), [
            ['name' => 'Chuyên cần', 'kind' => 'attendance', 'weight' => 30],
            ['name' => 'Giữa kỳ', 'kind' => 'midterm', 'weight' => 30],
            ['name' => 'Thi cuối kỳ', 'kind' => 'final_exam', 'weight' => 40],
        ]);

        $this->assertSame(40.0, $this->structures->rules()['final_exam_min_weight']);
    }

    public function test_hoc_phan_da_co_lop_thi_khong_doi_co_cau(): void
    {
        $subject = $this->subject();
        $this->structures->replace($subject, $this->standard());

        Schema::create('test_course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id');
        });
        app(ReferenceRegistry::class)->register(Subject::class, 'test_course_sections', 'subject_id', 'lớp học phần');
        DB::table('test_course_sections')->insert(['subject_id' => $subject->id]);

        $e = $this->rejected(fn () => $this->structures->replace($subject, $this->standard()));
        $this->assertStringContainsString('phiên bản', $e->hint());
    }

    public function test_chua_co_co_cau_thi_chua_sao_chep_duoc(): void
    {
        $e = $this->rejected(fn () => $this->structures->templateFor($this->subject()));
        $this->assertStringContainsString('chưa có cơ cấu điểm', $e->getMessage());
    }

    public function test_seeder_tao_co_cau_hop_le_cho_ca_60_hoc_phan(): void
    {
        $this->seed(GradeStructureSeeder::class);
        $this->seed(GradeStructureSeeder::class);

        $this->assertSame(60, GradeComponentTemplate::query()->distinct()->count('subject_id'));
        $this->assertSame(180, GradeComponentTemplate::count());

        foreach (Subject::all() as $subject) {
            $this->structures->validate(array_map(fn (array $c) => $c, $this->structures->templateFor($subject)));
        }
    }

    public function test_api_can_bo_dao_tao_luu_sinh_vien_chi_xem(): void
    {
        $subject = $this->subject();

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->putJson("/admin/subjects/{$subject->id}/grade-components", ['components' => $this->standard()])
            ->assertOk()
            ->assertJsonPath('components.2.kind_label', 'Thi cuối kỳ')
            ->assertJsonPath('rules.min_components', 3);

        $wrong = $this->standard();
        $wrong[0]['weight'] = 0;
        $this->putJson("/admin/subjects/{$subject->id}/grade-components", ['components' => $wrong])->assertStatus(422);

        $this->putJson("/admin/subjects/{$subject->id}/grade-components", ['components' => [['name' => 'X', 'kind' => 'abc', 'weight' => 100]]])
            ->assertStatus(422)->assertJsonValidationErrors('components.0.kind');

        $this->actingAs($this->userWithRoles('STU'));
        $this->getJson("/admin/subjects/{$subject->id}/grade-components")->assertOk()->assertJsonCount(3, 'components');
        $this->putJson("/admin/subjects/{$subject->id}/grade-components", ['components' => $this->standard()])->assertForbidden();
    }
}
