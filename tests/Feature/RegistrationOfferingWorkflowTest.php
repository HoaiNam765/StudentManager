<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\RegistrationPeriodService;
use App\Modules\Faculty\Models\Major;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Services\TrainingFrameworkService;
use App\Modules\Student\Models\Student;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Models\ScheduleRule;
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
use Database\Seeders\TimetableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class RegistrationOfferingWorkflowTest extends TestCase
{
    use InteractsWithRoles, RefreshDatabase;

    private User $acad;

    private User $studentUser;

    private Student $student;

    private TrainingFramework $framework;

    private RegistrationPeriod $period;

    private array $payload;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 10, 27)->setTime(3, 0));
        $this->seedRoles();
        $this->seed([SystemSeeder::class, NotificationEventSeeder::class, FacultySeeder::class, AcademicYearSeeder::class,
            RoomSeeder::class, SubjectSeeder::class, CurriculumSeeder::class, TeacherSeeder::class,
            GradeStructureSeeder::class, SchoolClassSeeder::class, TimetableSeeder::class]);
        foreach (Subject::query()->pluck('id') as $id) {
            DB::table('syllabi')->insert(['subject_id' => $id, 'version' => 1, 'status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);
        }
        $this->acad = $this->userWithRoles('ACAD');
        $curriculum = Curriculum::query()->inForce()->forCohort(2026)->firstOrFail();
        $term = Term::query()->where('name', 'Học kỳ 2')->orderBy('start_date')->firstOrFail();
        $this->framework = app(TrainingFrameworkService::class)->create([
            'faculty_id' => Major::findOrFail($curriculum->major_id)->faculty_id,
            'cohort_id' => Cohort::where('start_year', 2026)->firstOrFail()->id, 'term_id' => $term->id,
        ]);
        $subject = $curriculum->items()->firstOrFail()->subject;
        app(TrainingFrameworkService::class)->addItem($this->framework, ['subject_id' => $subject->id]);
        $this->period = app(RegistrationPeriodService::class)->create([
            'term_id' => $term->id, 'training_framework_id' => $this->framework->id, 'name' => 'Đợt kiểm thử theo khung',
            'round' => 'round_1', 'opens_at' => '2026-11-01 08:00', 'closes_at' => '2026-12-20 17:00',
        ]);
        $this->studentUser = $this->userWithRoles('STU');
        $this->student = Student::factory()->create(['major_id' => $curriculum->major_id, 'cohort' => 2026, 'status' => 'studying', 'user_id' => $this->studentUser->id]);
        $lecturer = Lecturer::whereHas('department', fn ($q) => $q->where('faculty_id', $this->framework->faculty_id))->firstOrFail();
        $this->payload = [
            'subject_id' => $subject->id, 'lecturer_id' => $lecturer->id, 'min_size' => 1, 'max_size' => 30,
            'schedule' => ['day_of_week' => 6, 'period_from' => 1, 'period_to' => 3, 'week_from' => 2, 'week_to' => 3, 'week_parity' => 'all',
                'room_id' => Room::where('status', 'available')->where('capacity', '>=', 30)->firstOrFail()->id],
        ];
        $this->actingAs($this->acad);
    }

    private function endpoint(string $action, ?int $section = null): string
    {
        return route('admin.registration-periods.'.$action, ['registrationPeriod' => $this->period->id, 'courseSection' => $section]);
    }

    private function openSection(): int
    {
        return $this->postJson($this->endpoint('offerings.store'), $this->payload)->assertCreated()->json('id');
    }

    public function test_preview_then_publication_then_early_open_and_cohort_isolation(): void
    {
        $id = $this->openSection();
        $this->getJson($this->endpoint('offerings.index'))->assertOk()->assertJsonPath('sections.0.id', $id);
        $this->actingAs($this->studentUser)->getJson(route('student.registration.options'))->assertOk()->assertJsonPath('period', null);
        $this->getJson(route('student.registration.options', ['period_id' => $this->period->id]))->assertNotFound();
        $this->actingAs($this->acad)->postJson($this->endpoint('publish'))->assertUnprocessable();
        $this->postJson($this->endpoint('announce'))->assertOk();
        $this->assertDatabaseHas('notification_recipients', ['user_id' => $this->studentUser->id]);
        $this->actingAs($this->studentUser)->getJson(route('student.registration.options'))->assertOk()
            ->assertJsonPath('period.preview_only', true)->assertJsonPath('sections.0.id', $id)
            ->assertJsonPath('sections.0.compulsory', true)->assertJsonPath('sections.0.eligible', false);
        $this->postJson(route('student.registration.enroll'), ['course_section_id' => $id])->assertUnprocessable();
        $this->actingAs($this->acad)->postJson($this->endpoint('publish'))->assertOk();
        $this->actingAs($this->studentUser)->getJson(route('student.registration.options'))->assertOk()->assertJsonPath('period.is_open', false);
        $this->postJson(route('student.registration.enroll'), ['course_section_id' => $id])->assertUnprocessable();
        $this->actingAs($this->acad)->postJson($this->endpoint('open-early'))->assertOk();
        $this->actingAs($this->studentUser)->postJson(route('student.registration.enroll'), ['course_section_id' => $id])->assertCreated();
        foreach (['cohort', 'faculty'] as $outside) {
            $otherUser = $this->userWithRoles('STU');
            Student::factory()->create(['user_id' => $otherUser->id, 'status' => 'studying',
                'cohort' => $outside === 'cohort' ? 2025 : 2026,
                'major_id' => $outside === 'faculty' ? Major::where('faculty_id', '!=', $this->framework->faculty_id)->firstOrFail()->id : $this->student->major_id]);
            $this->actingAs($otherUser)->getJson(route('student.registration.options', ['period_id' => $this->period->id]))->assertNotFound();
            $this->postJson(route('student.registration.enroll'), ['course_section_id' => $id])->assertUnprocessable();
            $this->assertFalse(DB::table('notification_recipients')->where('user_id', $otherUser->id)
                ->whereIn('notification_id', DB::table('notifications')->where('source', 'manual')->select('id'))->exists());
        }
    }

    public function test_teacher_must_be_in_faculty_and_available_with_atomic_rollback(): void
    {
        $other = Lecturer::whereHas('department', fn ($q) => $q->where('faculty_id', '!=', $this->framework->faculty_id))->firstOrFail();
        $this->postJson($this->endpoint('offerings.store'), array_replace($this->payload, ['lecturer_id' => $other->id]))->assertUnprocessable();
        $this->assertSame(0, $this->period->sections()->count());
        $id = $this->openSection();
        $candidate = $this->payload;
        $candidate['schedule']['room_id'] = Room::where('id', '!=', $candidate['schedule']['room_id'])->where('status', 'available')->where('capacity', '>=', 30)->firstOrFail()->id;
        $options = $this->postJson($this->endpoint('lecturers'), ['schedule' => $candidate['schedule']])->assertOk()->json();
        $this->assertFalse(collect($options)->firstWhere('id', $this->payload['lecturer_id'])['available']);
        $this->assertFalse(collect($options)->contains('id', $other->id));
        $this->postJson($this->endpoint('offerings.store'), $candidate)->assertUnprocessable();
        $this->assertSame(1, $this->period->sections()->count());
        $candidate['schedule']['day_of_week'] = 7;
        $this->postJson($this->endpoint('offerings.store'), $candidate)->assertCreated();
        // Sửa lớp đang có thành lịch bị chiếm cũng bị chặn và giữ lịch cũ.
        $candidate['schedule'] = $this->payload['schedule'];
        $candidate['schedule']['day_of_week'] = 7;
        $this->putJson($this->endpoint('offerings.update', $id), $candidate)->assertUnprocessable();
        $this->assertDatabaseHas('schedule_rules', ['course_section_id' => $id, 'day_of_week' => 6, 'deleted_at' => null]);
    }

    public function test_edit_delete_offerings_does_not_modify_framework_and_forbidden_roles_cannot_write(): void
    {
        $this->actingAs($this->studentUser)->postJson($this->endpoint('offerings.store'), $this->payload)->assertForbidden();
        $this->actingAs($this->acad);
        $id = $this->openSection();
        $this->payload['schedule']['day_of_week'] = 7;
        $this->putJson($this->endpoint('offerings.update', $id), $this->payload)->assertOk();
        $extraRule = ScheduleRule::where('course_section_id', $id)->firstOrFail()->replicate();
        $extraRule->day_of_week = 5;
        $extraRule->save();
        $this->putJson($this->endpoint('offerings.update', $id), $this->payload)->assertUnprocessable();
        $this->assertSame(2, ScheduleRule::where('course_section_id', $id)->count());
        $this->deleteJson($this->endpoint('offerings.destroy', $id))->assertOk();
        $this->getJson($this->endpoint('offerings.index'))->assertOk()->assertJsonCount(0, 'sections');
        $this->assertSame(1, $this->framework->items()->count());
        $this->postJson($this->endpoint('announce'))->assertUnprocessable();
        // Lớp đã bỏ vẫn giữ lịch sử nên đợt không xóa được; thông báo chỉ cách dùng lại đợt
        $this->deleteJson(route('admin.registration-periods.destroy', $this->period))
            ->assertUnprocessable()->assertJsonFragment(['message' => 'Đợt đã có lớp học phần (kể cả lớp đã bỏ khỏi đợt) nên không xóa được, để giữ lịch sử lớp. Dùng lại đợt nháp này: bấm Sửa để đổi tên / thời gian rồi thêm lớp mới.']);
        $this->actingAs($this->acad)->get(route('admin.pages.enrollment'))->assertOk();
    }

    public function test_scheduled_open_uses_server_time_and_passed_subject_cannot_bypass_with_improve(): void
    {
        $record = $this->mock(AcademicRecord::class);
        $record->shouldReceive('passedSubjectIds')->andReturn([$this->payload['subject_id']]);
        $record->shouldReceive('attemptedSubjectIds')->andReturn([$this->payload['subject_id']]);
        $record->shouldReceive('onProbation')->andReturn(false);
        $record->shouldReceive('goodStanding')->andReturn(false);
        $id = $this->openSection();
        $this->postJson($this->endpoint('announce'))->assertOk();
        $this->postJson($this->endpoint('publish'))->assertOk();
        $this->travelTo($this->period->opens_at->subSecond());
        $this->actingAs($this->studentUser)->postJson(route('student.registration.enroll'), ['course_section_id' => $id])->assertUnprocessable();
        $this->travelTo($this->period->opens_at);
        $this->getJson(route('student.registration.options'))->assertOk()->assertJsonPath('period.is_open', true);
        $this->postJson(route('student.registration.enroll'), ['course_section_id' => $id])->assertUnprocessable();
        $this->postJson(route('student.registration.enroll'), ['course_section_id' => $id, 'type' => 'improve'])->assertUnprocessable()->assertJsonValidationErrors('type');
        $this->assertDatabaseMissing('enrollments', ['student_id' => $this->student->id, 'course_section_id' => $id]);
        $this->travelTo($this->period->closes_at);
        $this->postJson(route('student.registration.enroll'), ['course_section_id' => $id])->assertUnprocessable();
    }
}
