<?php

namespace Tests\Feature\Modules\Curriculum;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumBlock;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Curriculum\Services\CurriculumCatalog;
use App\Modules\Curriculum\Services\CurriculumService;
use App\Modules\Curriculum\Services\CurriculumVersionService;
use App\Modules\Curriculum\Services\GraduationRequirementService;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\SubjectService;
use App\Modules\System\Models\PolicySet;
use App\Modules\System\Services\PolicyResolver;
use App\Support\Exceptions\BusinessRuleException;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\SubjectRelationSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class CurriculumVersionTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private CurriculumService $curricula;

    private CurriculumVersionService $versions;

    private GraduationRequirementService $graduation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SystemSeeder::class);
        $this->seedRoles();
        $this->seed([FacultySeeder::class, SubjectSeeder::class, SubjectRelationSeeder::class]);
        $this->curricula = app(CurriculumService::class);
        $this->versions = app(CurriculumVersionService::class);
        $this->graduation = app(GraduationRequirementService::class);
    }

    private function subject(string $code): Subject
    {
        return Subject::query()->where('code', $code)->firstOrFail();
    }

    /** CTĐT 2026 đã ban hành: khối cơ sở ngành có INT101 (3 TC) và GDTC101 (điều kiện). */
    private function published(): Curriculum
    {
        $curriculum = $this->curricula->create([
            'code' => 'CTDT-V', 'name' => 'CTĐT có phiên bản', 'major_id' => Major::query()->where('code', '7480201')->value('id'),
            'training_type_id' => TrainingType::default()->id, 'cohort_from' => 2026, 'total_credits' => 3,
        ]);
        $block = $this->curricula->addBlock($curriculum, ['name' => 'Cơ sở ngành', 'block_type' => 'foundation', 'required_credits' => 3]);
        $this->curricula->addSubject($curriculum, ['subject_id' => $this->subject('INT101')->id, 'curriculum_block_id' => $block->id, 'planned_term' => 1]);
        $this->curricula->addSubject($curriculum, ['subject_id' => $this->subject('GDTC101')->id, 'curriculum_block_id' => $block->id, 'planned_term' => 1]);

        return $this->curricula->publish($curriculum);
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

    public function test_sua_tin_chi_hoc_phan_trong_ctdt_da_ban_hanh_qua_phien_ban_moi_khoa_truoc_khong_doi(): void
    {
        $v1 = $this->published();

        // Không sửa tín chỉ của học phần đang nằm trong CTĐT đã ban hành, cũng không sửa CTĐT trực tiếp
        $this->rejected(fn () => app(SubjectService::class)->update($this->subject('INT101'), ['credits' => 4]));
        $this->rejected(fn () => $this->curricula->update($v1, ['total_credits' => 4]));

        // Phiên bản mới cho K2028: thay INT101 (3 TC) bằng phiên bản học phần 4 TC
        $v2 = $this->versions->newVersion($v1, 2028);
        $this->assertSame(2, $v2->version);
        $this->assertSame('draft', $v2->status->value);
        $this->assertSame(2, $v2->items()->count());

        $int101v2 = app(SubjectService::class)->create([
            'department_id' => Department::query()->where('code', 'BM-KTPM')->value('id'), 'code' => 'INT101B',
            'name' => 'Nhập môn lập trình (4 tín chỉ)', 'credits' => 4, 'practice_credits' => 1, 'knowledge_block' => 'foundation',
        ]);
        $block = $v2->blocks()->firstOrFail();
        $this->curricula->removeSubject($v2->items()->where('subject_id', $this->subject('INT101')->id)->firstOrFail());
        $this->curricula->addSubject($v2, ['subject_id' => $int101v2->id, 'curriculum_block_id' => $block->id, 'planned_term' => 1]);
        $this->curricula->updateBlock($block, ['required_credits' => 4]);
        $this->curricula->update($v2, ['total_credits' => 4]);
        $this->curricula->publish($v2->refresh());

        // Phiên bản cũ dừng ở K2027, khóa 2026 vẫn học INT101 (3 TC); khóa 2028 học INT101B (4 TC)
        $this->assertSame(2027, $v1->refresh()->cohort_to);
        $catalog = app(CurriculumCatalog::class);
        $major = $v1->major_id;
        $type = TrainingType::default()->id;

        $k2026 = $catalog->forStudent($major, 2026, $type);
        $k2028 = $catalog->forStudent($major, 2028, $type);
        $this->assertSame([1, 2], [$k2026->version, $k2028->version]);
        $this->assertSame(['GDTC101', 'INT101'], $this->codes($k2026));
        $this->assertSame(['GDTC101', 'INT101B'], $this->codes($k2028));
        $this->assertSame(3, $k2026->total_credits);
    }

    public function test_phien_ban_moi_chi_tu_ban_dang_ap_dung_cho_khoa_sau_va_mot_ban_nhap(): void
    {
        $v1 = $this->published();

        $e = $this->rejected(fn () => $this->versions->newVersion($v1, 2026));
        $this->assertStringContainsString('khóa sau K2026', $e->getMessage());

        $draft = $this->versions->newVersion($v1, 2027);
        $this->rejected(fn () => $this->versions->newVersion($v1, 2028));
        $this->rejected(fn () => $this->versions->newVersion($draft, 2029));

        // Xóa bản nháp rồi tạo lại được
        $this->curricula->delete($draft);
        $this->assertSame(3, $this->versions->newVersion($v1, 2027)->version);
    }

    public function test_dieu_kien_tot_nghiep_mac_dinh_theo_ctdt_va_bo_quy_che(): void
    {
        $this->seed(CurriculumSeeder::class);
        $cntt = Curriculum::query()->where('code', 'CTDT-7480201-2026')->firstOrFail();

        $effective = $this->graduation->effectiveFor($cntt, 2026);
        $this->assertSame($cntt->total_credits, $effective['min_credits']);
        $this->assertSame(2.0, $effective['min_cgpa']);   // BR-CUR-05, từ bộ quy chế
        $this->assertTrue($effective['requires_internship']);
        $this->assertTrue($effective['requires_thesis']);
        $this->assertTrue($effective['no_active_suspension']);
        $this->assertContains('GDQP101', $effective['condition_subjects']);
        $this->assertNotContains('INT101', $effective['condition_subjects']);
        $this->assertTrue($cntt->graduationRequirement()->exists(), 'Điều kiện được lưu cố định khi ban hành');

        // Bộ quy chế nâng CGPA: CTĐT không ghi CGPA riêng thì theo quy chế
        PolicySet::query()->firstOrFail()->items()->where('key', 'graduation.min_cgpa')->update(['value' => json_encode(2.2)]);
        app(PolicyResolver::class)->flush();
        $this->assertSame(2.2, $this->graduation->effectiveFor($cntt, 2026)['min_cgpa']);

        // CTĐT mẫu nào cũng có thực tập và khóa luận; CTĐT không có khối thực tập thì mặc định không yêu cầu thực tập
        $nntq = Curriculum::query()->where('code', 'CTDT-7220204-2026')->firstOrFail();
        $this->assertTrue($this->graduation->effectiveFor($nntq, 2026)['requires_internship']);

        $draft = $this->curricula->create([
            'code' => 'CTDT-KHONG-TT', 'name' => 'CTĐT không có thực tập', 'major_id' => $nntq->major_id,
            'training_type_id' => TrainingType::default()->id, 'cohort_from' => 2030, 'total_credits' => 3,
        ]);
        $this->curricula->addBlock($draft, ['name' => 'Cơ sở ngành', 'block_type' => 'foundation', 'required_credits' => 3]);
        $this->assertFalse($this->graduation->effectiveFor($draft, 2030)['requires_internship']);
        $this->assertFalse($this->graduation->effectiveFor($draft, 2030)['requires_thesis']);
    }

    public function test_dieu_kien_tot_nghiep_chi_sua_o_ban_nhap_va_phai_khop_noi_dung(): void
    {
        $v1 = $this->published();
        $e = $this->rejected(fn () => $this->graduation->save($v1, ['min_cgpa' => 2.5]));
        $this->assertStringContainsString('phiên bản CTĐT mới', $e->hint());

        $v2 = $this->versions->newVersion($v1, 2027);
        $this->rejected(fn () => $this->graduation->save($v2, ['min_credits' => 10]));
        $this->graduation->save($v2, ['min_cgpa' => 2.5, 'foreign_language_standard' => 'Bậc 3/6', 'requires_internship' => true]);

        // Yêu cầu thực tập nhưng CTĐT chưa có học phần thực tập: chưa ban hành được
        $e = $this->rejected(fn () => $this->curricula->publish($v2->refresh()));
        $this->assertStringContainsString('yêu cầu thực tập', $e->getMessage());

        $internship = $this->curricula->addBlock($v2, ['name' => 'Thực tập', 'block_type' => 'internship', 'required_credits' => 0]);
        $this->curricula->addSubject($v2, ['subject_id' => $this->subject('INT401')->id, 'curriculum_block_id' => $internship->id, 'planned_term' => 7, 'outside_prerequisites_confirmed' => true]);
        $this->curricula->publish($v2->refresh());

        $effective = $this->graduation->effectiveFor($v2->refresh(), 2027);
        $this->assertSame(2.5, $effective['min_cgpa']);
        $this->assertSame('Bậc 3/6', $effective['foreign_language_standard']);
    }

    public function test_phien_ban_moi_chep_ca_dieu_kien_tot_nghiep_va_nhom_tu_chon(): void
    {
        $curriculum = $this->curricula->create([
            'code' => 'CTDT-G', 'name' => 'Có nhóm tự chọn', 'major_id' => Major::query()->where('code', '7480201')->value('id'),
            'training_type_id' => TrainingType::default()->id, 'cohort_from' => 2026, 'total_credits' => 6,
        ]);
        $block = $this->curricula->addBlock($curriculum, ['name' => 'Cơ sở', 'block_type' => 'foundation', 'required_credits' => 6]);
        $group = $this->curricula->addGroup($curriculum, ['curriculum_block_id' => $block->id, 'name' => 'Tự chọn', 'min_credits' => 3]);
        $this->curricula->addSubject($curriculum, ['subject_id' => $this->subject('INT101')->id, 'curriculum_block_id' => $block->id, 'planned_term' => 1]);
        $this->curricula->addSubject($curriculum, ['subject_id' => $this->subject('INT206')->id, 'curriculum_block_id' => $block->id, 'curriculum_elective_group_id' => $group->id, 'planned_term' => 2]);
        $this->graduation->save($curriculum, ['it_standard' => 'Chuẩn kỹ năng CNTT cơ bản']);
        $this->curricula->publish($curriculum);

        $copy = $this->versions->newVersion($curriculum->refresh(), 2027);

        $this->assertSame(1, $copy->electiveGroups()->count());
        $copied = $copy->items()->where('subject_id', $this->subject('INT206')->id)->firstOrFail();
        $this->assertSame($copy->electiveGroups()->value('id'), $copied->curriculum_elective_group_id);
        $this->assertSame(CurriculumBlock::query()->where('curriculum_id', $copy->id)->value('id'), $copied->curriculum_block_id);
        $this->assertSame('Chuẩn kỹ năng CNTT cơ bản', $copy->graduationRequirement->it_standard);
    }

    public function test_api_tao_phien_ban_xem_danh_sach_va_dieu_kien_tot_nghiep(): void
    {
        $v1 = $this->published();
        $this->actingAs($this->userWithRoles('ACAD'));

        $this->putJson("/admin/curricula/{$v1->id}/graduation-requirements", ['min_cgpa' => 2.5])->assertStatus(422);
        $this->getJson("/admin/curricula/{$v1->id}/graduation-requirements?cohort=2026")
            ->assertOk()->assertJsonPath('stored', true)->assertJsonPath('effective.min_cgpa', 2)->assertJsonPath('effective.condition_subjects', ['GDTC101']);

        $id = $this->postJson("/admin/curricula/{$v1->id}/versions", ['cohort_from' => 2027])->assertCreated()->assertJsonPath('version', 2)->json('id');
        $this->putJson("/admin/curricula/{$id}/graduation-requirements", ['min_cgpa' => 2.5])->assertOk()->assertJsonPath('requirement.min_cgpa', '2.50');

        $this->getJson("/admin/curricula/{$v1->id}/versions")->assertOk()->assertJsonCount(2)->assertJsonPath('0.version', 2);

        $this->actingAs($this->userWithRoles('STU'));
        $this->postJson("/admin/curricula/{$v1->id}/versions", ['cohort_from' => 2028])->assertForbidden();
    }

    /** @return list<string> */
    private function codes(Curriculum $curriculum): array
    {
        return CurriculumSubject::query()->where('curriculum_id', $curriculum->id)->with('subject')->get()
            ->map(fn (CurriculumSubject $i) => $i->subject->code)->sort()->values()->all();
    }
}
