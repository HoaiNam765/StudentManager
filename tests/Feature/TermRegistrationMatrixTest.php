<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\AvailableSections;
use App\Modules\Enrollment\Services\EnrollmentRules;
use App\Modules\Enrollment\Services\RegistrationOfferingService;
use App\Modules\Faculty\Models\Major;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Services\TrainingFrameworkService;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentCurriculum;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;
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

/**
 * Đợt đăng ký theo chương trình khung cho cả học kỳ: bảng Khoa × Khóa (ô theo khoa – khóa có sinh viên, chọn kỳ CTĐT),
 * chặn công bố khi thiếu ô / khung chưa có lớp, không đổi kỳ khi khung đã có lớp; sinh viên chỉ thấy lớp của khung mình
 * và lớp học bù (môn kỳ trước chưa đạt), không thấy môn đã đạt hay lớp khoa khác.
 */
class TermRegistrationMatrixTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private User $acad;

    private Term $term;

    private Curriculum $cnttCurriculum;

    private Student $cntt26;

    private Student $kt26;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 27)->setTime(3, 0));
        $this->seedRoles();
        $this->seed([SystemSeeder::class, NotificationEventSeeder::class, FacultySeeder::class, AcademicYearSeeder::class,
            RoomSeeder::class, SubjectSeeder::class, CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class]);
        foreach (Subject::query()->pluck('id') as $id) {
            DB::table('syllabi')->insert(['subject_id' => $id, 'version' => 1, 'status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);
        }
        $this->acad = $this->userWithRoles('ACAD');
        $this->term = Term::query()->where('name', 'Học kỳ 2')->orderBy('start_date')->firstOrFail();

        // Ba ô: CNTT – 16DH, Kinh tế – 16DH, CNTT – 15DH (Ngoại ngữ không có sinh viên nên không có ô)
        $cnttMajor = Major::query()->whereHas('faculty', fn ($q) => $q->where('code', 'CNTT'))->where('code', '7480201')->firstOrFail();
        $ktMajor = Major::query()->whereHas('faculty', fn ($q) => $q->where('code', 'KT'))->firstOrFail();
        $this->cnttCurriculum = Curriculum::query()->inForce()->forCohort(2026)->where('major_id', $cnttMajor->id)->firstOrFail();
        $this->cntt26 = Student::factory()->create(['major_id' => $cnttMajor->id, 'cohort' => 2026, 'status' => 'studying']);
        StudentCurriculum::create(['student_id' => $this->cntt26->id, 'curriculum_id' => $this->cnttCurriculum->id, 'effective_from' => now()->toDateString()]);
        $this->kt26 = Student::factory()->create(['major_id' => $ktMajor->id, 'cohort' => 2026, 'status' => 'studying']);
        Student::factory()->create(['major_id' => $cnttMajor->id, 'cohort' => 2025, 'status' => 'studying']);
        $this->actingAs($this->acad);
    }

    private function createPeriod(): RegistrationPeriod
    {
        $id = $this->postJson(route('admin.registration-periods.store'), [
            'term_id' => $this->term->id, 'uses_frameworks' => true, 'name' => 'Đăng ký HK2 theo khung', 'round' => 'round_1',
            'opens_at' => '2026-11-01 08:00', 'closes_at' => '2026-12-20 17:00',
        ])->assertCreated()->assertJsonPath('uses_frameworks', true)->json('id');

        return RegistrationPeriod::findOrFail($id);
    }

    private function setTerm(RegistrationPeriod $period, string $faculty, int $year, int $term): TrainingFramework
    {
        $facultyId = DB::table('faculties')->where('code', $faculty)->value('id');
        $cohortId = Cohort::query()->where('start_year', $year)->value('id');
        $this->putJson(route('admin.registration-periods.frameworks.set-term', $period), ['faculty_id' => $facultyId, 'cohort_id' => $cohortId, 'planned_term' => $term])->assertOk();

        return TrainingFramework::query()->where(['faculty_id' => $facultyId, 'cohort_id' => $cohortId, 'term_id' => $this->term->id])->firstOrFail();
    }

    private function openClass(RegistrationPeriod $period, TrainingFramework $framework, int $subjectId): int
    {
        $item = $framework->items()->where('subject_id', $subjectId)->firstOrFail();
        $lecturer = Lecturer::query()->whereHas('department', fn ($q) => $q->where('faculty_id', $framework->faculty_id))->orderBy('code')->firstOrFail();

        return app(RegistrationOfferingService::class)->addClasses($period, $framework, [['item_id' => $item->id, 'lecturer_id' => $lecturer->id]], true)[0]->id;
    }

    public function test_bang_khoa_khoa_hoc_chon_ky_va_chan_cong_bo_khi_chua_du(): void
    {
        $period = $this->createPeriod();
        $cells = collect($this->getJson(route('admin.registration-periods.offerings.index', $period))->assertOk()->json('cells'));
        $this->assertCount(3, $cells);
        $this->assertSame([2, 2, 4], $cells->sortBy('suggested_term')->pluck('suggested_term')->values()->all());
        $this->assertTrue($cells->every(fn ($c) => $c['framework_id'] === null));

        $this->postJson(route('admin.registration-periods.publish', $period))->assertUnprocessable()
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'Chưa chọn kỳ cho'));

        // Chọn kỳ: khung có đúng các môn kỳ đó theo CTĐT các ngành của khoa
        $cntt26 = $this->setTerm($period, 'CNTT', 2026, 2);
        $expected = CurriculumSubject::query()->where('planned_term', 2)
            ->whereIn('curriculum_id', Curriculum::query()->inForce()->forCohort(2026)->whereIn('major_id', Major::query()->whereHas('faculty', fn ($q) => $q->where('code', 'CNTT'))->select('id'))->select('id'))
            ->distinct()->count('subject_id');
        $this->assertSame($expected, $cntt26->items()->count());
        $this->assertSame(2, $cntt26->planned_term);
        $this->setTerm($period, 'KT', 2026, 2);
        $this->setTerm($period, 'CNTT', 2025, 4);

        $this->postJson(route('admin.registration-periods.publish', $period))->assertUnprocessable()
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'Khung chưa có lớp nào'));

        // Khung đã có lớp thì không đổi kỳ được
        $this->openClass($period, $cntt26, $cntt26->items()->value('subject_id'));
        $facultyId = $cntt26->faculty_id;
        $this->putJson(route('admin.registration-periods.frameworks.set-term', $period), ['faculty_id' => $facultyId, 'cohort_id' => $cntt26->cohort_id, 'planned_term' => 3])
            ->assertUnprocessable()->assertJsonPath('message', fn ($m) => str_contains($m, 'không đổi kỳ được'));
        $this->deleteJson(route('admin.registration-periods.frameworks.detach', [$period, $cntt26]))->assertUnprocessable();

        $this->actingAs($this->userWithRoles('LEC'))->putJson(route('admin.registration-periods.frameworks.set-term', $period), ['faculty_id' => $facultyId, 'cohort_id' => $cntt26->cohort_id, 'planned_term' => 2])->assertForbidden();
    }

    public function test_khung_gom_nhieu_nganh_sinh_vien_chi_thay_mon_cua_nganh_minh(): void
    {
        $period = $this->createPeriod();
        $framework = $this->setTerm($period, 'CNTT', 2026, 2);
        // Khoa CNTT có hai ngành: CNTT (sinh viên cntt26) và Kỹ thuật phần mềm
        $seMajor = Major::query()->whereHas('faculty', fn ($q) => $q->where('code', 'CNTT'))->where('code', '7480103')->firstOrFail();
        $seCurriculum = Curriculum::query()->inForce()->forCohort(2026)->where('major_id', $seMajor->id)->firstOrFail();
        $se26 = Student::factory()->create(['major_id' => $seMajor->id, 'cohort' => 2026, 'status' => 'studying']);
        StudentCurriculum::create(['student_id' => $se26->id, 'curriculum_id' => $seCurriculum->id, 'effective_from' => now()->toDateString()]);
        $cnttSubjects = CurriculumSubject::query()->where('curriculum_id', $this->cnttCurriculum->id)->pluck('subject_id');
        $seSubjects = CurriculumSubject::query()->where('curriculum_id', $seCurriculum->id)->pluck('subject_id');
        $seOnly = $seSubjects->diff($cnttSubjects)->first();
        $shared = $seSubjects->intersect($cnttSubjects)->first();
        // Môn thêm tay ngoài CTĐT của mọi ngành trong khoa (ví dụ môn của khoa Kinh tế)
        $extra = Subject::query()->whereNotIn('id', $cnttSubjects->merge($seSubjects))->where('status', 'active')->orderBy('code')->value('id');
        $this->assertNotNull($seOnly);
        $this->assertNotNull($shared);
        $this->assertNotNull($extra);
        foreach ([$seOnly, $shared, $extra] as $subjectId) {
            if (! $framework->items()->where('subject_id', $subjectId)->exists()) {
                app(TrainingFrameworkService::class)->addItem($framework, ['subject_id' => $subjectId, 'compulsory' => false]);
            }
        }
        $seClass = $this->openClass($period, $framework, $seOnly);
        $sharedClass = $this->openClass($period, $framework, $shared);
        $extraClass = $this->openClass($period, $framework, $extra);
        $period->refresh();

        $cntt = collect(app(AvailableSections::class)->listFor($this->cntt26, $period))->pluck('id');
        $this->assertFalse($cntt->contains($seClass), 'Môn chỉ có ở CTĐT ngành khác cùng khoa không hiện');
        $this->assertTrue($cntt->contains($sharedClass));
        $this->assertTrue($cntt->contains($extraClass), 'Môn thêm tay ngoài CTĐT các ngành vẫn hiện cho cả khoa – khóa');
        $reasons = app(EnrollmentRules::class)->check($this->cntt26, CourseSection::findOrFail($seClass), $period);
        $this->assertTrue(collect($reasons)->contains(fn ($r) => str_contains($r, 'không dành cho khoa – khóa')));

        $se = collect(app(AvailableSections::class)->listFor($se26, $period))->pluck('id');
        $this->assertTrue($se->contains($seClass));
        $this->assertTrue($se->contains($sharedClass));
    }

    public function test_sinh_vien_chi_thay_lop_khung_minh_va_hoc_bu_khong_thay_mon_da_dat(): void
    {
        $period = $this->createPeriod();
        $cntt26 = $this->setTerm($period, 'CNTT', 2026, 2);
        $kt26 = $this->setTerm($period, 'KT', 2026, 2);
        // Khung 15DH chọn kỳ 1 để có lớp các môn kỳ 1 (học bù cho 16DH)
        $cntt25 = $this->setTerm($period, 'CNTT', 2025, 1);

        $term1 = CurriculumSubject::query()->where('curriculum_id', $this->cnttCurriculum->id)->where('planned_term', 1)->pluck('subject_id');
        $ownSubject = $cntt26->items()->whereIn('subject_id', Subject::query()->where('code', 'like', 'INT%')->select('id'))->value('subject_id')
            ?? $cntt26->items()->value('subject_id');
        // Hai môn kỳ 1 của CTĐT 16DH mở ở khung 15DH (CTĐT mẫu chỉ áp dụng từ khóa 2026 nên khung 15DH thêm tay)
        $backlogSubject = Subject::query()->whereIn('id', $term1)->where('code', 'like', 'INT%')->value('id');
        $passedSubject = Subject::query()->whereIn('id', $term1)->where('id', '!=', $backlogSubject)->value('id');
        foreach ([$backlogSubject, $passedSubject] as $subjectId) {
            if (! $cntt25->items()->where('subject_id', $subjectId)->exists()) {
                app(TrainingFrameworkService::class)->addItem($cntt25, ['subject_id' => $subjectId]);
            }
        }
        $ktOnly = $kt26->items()->whereNotIn('subject_id', $cntt26->items()->select('subject_id'))->value('subject_id');
        $this->assertNotNull($backlogSubject);
        $this->assertNotNull($passedSubject);
        $this->assertNotNull($ktOnly);

        $own = $this->openClass($period, $cntt26, $ownSubject);
        $backlog = $this->openClass($period, $cntt25, $backlogSubject);
        $passed = $this->openClass($period, $cntt25, $passedSubject);
        $other = $this->openClass($period, $kt26, $ktOnly);

        $record = $this->mock(AcademicRecord::class);
        $record->shouldReceive('passedSubjectIds')->andReturn([$passedSubject]);
        $record->shouldReceive('attemptedSubjectIds')->andReturn([]);
        $record->shouldReceive('onProbation')->andReturn(false);
        $record->shouldReceive('goodStanding')->andReturn(false);

        $period->refresh();
        $rows = collect(app(AvailableSections::class)->listFor($this->cntt26, $period))->keyBy('id');
        $this->assertTrue($rows->has($own));
        $this->assertFalse($rows[$own]['backlog']);
        $this->assertTrue($rows->has($backlog));
        $this->assertTrue($rows[$backlog]['backlog']);
        $this->assertFalse($rows->has($passed), 'Môn đã đạt không hiện');
        $this->assertFalse($rows->has($other), 'Lớp khoa khác không hiện');

        // Gọi thẳng đăng ký lớp ngoài phạm vi bị từ chối kèm lý do
        $reasons = app(EnrollmentRules::class)->check($this->cntt26, CourseSection::findOrFail($other), $period);
        $this->assertTrue(collect($reasons)->contains(fn ($r) => str_contains($r, 'không dành cho khoa – khóa')));

        $ktRows = collect(app(AvailableSections::class)->listFor($this->kt26, $period))->pluck('id');
        $this->assertTrue($ktRows->contains($other));
        $this->assertFalse($ktRows->contains($own));
    }
}
