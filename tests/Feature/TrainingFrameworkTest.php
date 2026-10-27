<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Faculty\Models\Major;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Services\TrainingFrameworkService;
use App\Modules\Student\Models\Student;
use App\Modules\Subject\Models\Subject;
use App\Support\Exceptions\BusinessRuleException;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\GradeStructureSeeder;
use Database\Seeders\NotificationEventSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\SchoolClassSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** Chương trình khung theo khoa – khóa – học kỳ: gợi ý từ CTĐT, mở lớp hàng loạt, thông báo cho sinh viên khoa ∩ khóa. */
class TrainingFrameworkTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private User $acad;

    private Curriculum $curriculum;

    private Cohort $cohort;

    private Term $term;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, NotificationEventSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class,
            SubjectSeeder::class, CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class,
        ]);

        // Đề cương đã duyệt cho mọi học phần (BR-SUB-08) để mở lớp được
        foreach (Subject::query()->pluck('id') as $id) {
            DB::table('syllabi')->insert(['subject_id' => $id, 'version' => 1, 'status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);
        }

        $this->acad = $this->userWithRoles('ACAD');
        $this->cohort = Cohort::query()->where('start_year', 2026)->firstOrFail();
        // HK2 của năm học 2026–2027 = học kỳ thứ 2 của khóa 2026
        $this->term = Term::query()->where('name', 'Học kỳ 2')->orderBy('start_date')->firstOrFail();
        $this->curriculum = Curriculum::query()->inForce()->forCohort(2026)
            ->whereHas('items', fn ($q) => $q->where('planned_term', 2))->firstOrFail();
    }

    private function framework(): TrainingFramework
    {
        return app(TrainingFrameworkService::class)->create([
            'faculty_id' => Major::query()->findOrFail($this->curriculum->major_id)->faculty_id,
            'cohort_id' => $this->cohort->id,
            'term_id' => $this->term->id,
        ]);
    }

    public function test_goi_y_tu_ctdt_lay_dung_hoc_ky_thu_n_cua_khoa(): void
    {
        $framework = $this->framework();
        $service = app(TrainingFrameworkService::class);

        $this->assertSame(2, $service->cohortTermNumber($this->cohort, $this->term));

        $result = $service->suggestFromCurriculum($framework);
        $majorIds = Major::query()->where('faculty_id', $framework->faculty_id)->pluck('id');
        $expected = CurriculumSubject::query()
            ->whereIn('curriculum_id', Curriculum::query()->inForce()->forCohort(2026)->whereIn('major_id', $majorIds)->select('id'))
            ->where('planned_term', 2)->distinct()->pluck('subject_id')->sort()->values()->all();

        $this->assertSame(2, $result['planned_term']);
        $this->assertNotEmpty($expected);
        $this->assertSame($expected, $framework->items()->pluck('subject_id')->sort()->values()->all());

        // Gọi lại không thêm trùng
        $this->assertCount(0, $service->suggestFromCurriculum($framework)['added']);
    }

    public function test_mo_lop_hang_loat_khong_mo_trung_va_muc_loi_khong_chan_muc_khac(): void
    {
        $service = app(TrainingFrameworkService::class);
        $framework = $this->framework();
        $inForce = $this->curriculum->items()->where('planned_term', 2)->firstOrFail();
        $good = $service->addItem($framework, ['subject_id' => $inForce->subject_id, 'planned_sections' => 2, 'max_size' => 40]);
        // Học phần chưa có đề cương được duyệt → mở lớp bị từ chối (BR-SUB-08)
        $noSyllabus = $this->curriculum->items()->where('subject_id', '!=', $inForce->subject_id)->value('subject_id');
        DB::table('syllabi')->where('subject_id', $noSyllabus)->delete();
        $bad = $service->addItem($framework, ['subject_id' => $noSyllabus]);

        $result = $service->openSections($framework);

        $this->assertCount(2, $result['opened']);
        $this->assertSame(2, CourseSection::query()->where('training_framework_item_id', $good->id)->where('max_size', 40)->count());
        $this->assertCount(1, $result['errors']);
        $this->assertSame($bad->id, $result['errors'][0]['item_id']);

        // Chạy lại: đã đủ thì không mở thêm
        $this->assertCount(0, $service->openSections($framework)['opened']);

        // Không giảm số lớp dự kiến dưới số đã mở, không xóa dòng đã mở lớp, không xóa khung
        $this->expectException(BusinessRuleException::class);
        $service->updateItem($good, ['planned_sections' => 1]);
    }

    public function test_khong_xoa_muc_hay_khung_da_mo_lop(): void
    {
        $service = app(TrainingFrameworkService::class);
        $framework = $this->framework();
        $item = $service->addItem($framework, ['subject_id' => $this->curriculum->items()->where('planned_term', 2)->value('subject_id')]);
        $service->openSections($framework);

        foreach ([fn () => $service->deleteItem($item), fn () => $service->delete($framework)] as $action) {
            try {
                $action();
                $this->fail('Đã mở lớp thì không được xóa.');
            } catch (BusinessRuleException $e) {
                $this->assertNotNull($e->hint());
            }
        }
    }

    public function test_thong_bao_chi_gui_sinh_vien_thuoc_ca_khoa_va_khoa_hoc(): void
    {
        $framework = $this->framework();
        $facultyMajor = Major::query()->where('faculty_id', $framework->faculty_id)->firstOrFail();
        $otherMajor = Major::query()->where('faculty_id', '!=', $framework->faculty_id)->firstOrFail();
        $make = fn (int $majorId, int $cohort) => Student::factory()->create([
            'major_id' => $majorId, 'cohort' => $cohort, 'status' => 'studying', 'user_id' => User::factory()->create()->id,
        ]);
        $inside = [$make($facultyMajor->id, 2026), $make($facultyMajor->id, 2026)];
        $make($facultyMajor->id, 2025);
        $make($otherMajor->id, 2026);

        $this->actingAs($this->acad);
        $draft = app(TrainingFrameworkService::class)->draftAnnouncement($framework);
        $this->assertSame(2, $draft['students']);

        $this->postJson("/admin/training-frameworks/{$framework->id}/open-sections")->assertNotFound();
        $sent = app(TrainingFrameworkService::class)->notify($this->acad, $framework, $draft['title'], $draft['body']);
        $recipients = DB::table('notification_recipients')->where('notification_id', $sent->id)->pluck('user_id')->sort()->values()->all();
        $this->assertSame(collect($inside)->pluck('user_id')->sort()->values()->all(), $recipients);
        $this->assertNotNull($framework->refresh()->notified_at);
    }

    public function test_api_lap_khung_chi_cho_phong_dao_tao(): void
    {
        $payload = ['faculty_id' => Major::query()->findOrFail($this->curriculum->major_id)->faculty_id, 'cohort_id' => $this->cohort->id, 'term_id' => $this->term->id];

        $this->actingAs($this->userWithRoles('LEC'))->postJson(route('admin.training-frameworks.store'), $payload)->assertForbidden();
        $this->actingAs($this->acad)->postJson(route('admin.training-frameworks.store'), $payload)->assertCreated()->assertJsonPath('planned_term', 2);
        // Trùng khoa – khóa – học kỳ
        $this->postJson(route('admin.training-frameworks.store'), $payload)->assertStatus(422);
        $this->get(route('admin.pages.training-frameworks'))->assertOk()->assertSee('admin-training-frameworks');
    }

    public function test_chon_hoc_ky_va_khoa_thay_cac_khoa_lap_khung_tu_lay_mon_tu_ctdt(): void
    {
        $facultyId = Major::query()->findOrFail($this->curriculum->major_id)->faculty_id;
        $k2025 = Cohort::query()->where('start_year', 2025)->firstOrFail();
        Student::factory()->create(['major_id' => $this->curriculum->major_id, 'cohort' => 2025, 'status' => 'studying']);
        $this->actingAs($this->acad);
        $query = ['term_id' => $this->term->id, 'faculty_id' => $facultyId];

        // Khóa có sinh viên hiện ra, chưa có khung
        $this->getJson(route('admin.training-frameworks.cohort-options', $query))->assertOk()
            ->assertJsonFragment(['cohort' => $k2025->only('id', 'code', 'start_year'), 'planned_term' => 4, 'students' => 1, 'framework' => null]);

        // Lập khung = lấy luôn môn học kỳ thứ n theo CTĐT
        $expected = CurriculumSubject::query()->where('planned_term', 2)
            ->whereIn('curriculum_id', Curriculum::query()->inForce()->forCohort(2026)->whereIn('major_id', Major::query()->where('faculty_id', $facultyId)->select('id'))->select('id'))
            ->distinct()->count('subject_id');
        $created = $this->postJson(route('admin.training-frameworks.store'), $query + ['cohort_id' => $this->cohort->id])->assertCreated()->json();
        $this->assertCount($expected, $created['items']);
        $this->assertGreaterThan(0, $expected);

        $this->getJson(route('admin.training-frameworks.cohort-options', $query))->assertOk()
            ->assertJsonFragment(['framework' => ['id' => $created['id'], 'items_count' => $expected]]);
        // Lập khung trống khi không muốn lấy từ CTĐT
        $this->postJson(route('admin.training-frameworks.store'), $query + ['cohort_id' => $k2025->id, 'suggest' => false])->assertCreated()->assertJsonCount(0, 'items');
    }
}
