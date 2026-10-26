<?php

namespace Tests\Feature;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Enums\RegistrationRound;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\EnrollmentRules;
use App\Modules\Enrollment\Services\RegistrationPeriodService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Modules\Timetable\Models\ClassSession;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\ScheduleRuleService;
use App\Modules\Timetable\Services\SectionStartDates;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\CarbonImmutable;
use Database\Seeders\AcademicYearSeeder;
use Database\Seeders\CurriculumSeeder;
use Database\Seeders\FacultySeeder;
use Database\Seeders\GradeStructureSeeder;
use Database\Seeders\RoomSeeder;
use Database\Seeders\SchoolClassSeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\SystemSeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

/** Ngày bắt đầu học của lớp, ràng buộc "ngày học sau ngày đóng đăng ký", thời khóa biểu sinh viên hiện ngay sau khi đăng ký. */
class RegistrationDatesTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private Term $term;

    /** @var list<CourseSection> */
    private array $sections;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class, SubjectSeeder::class,
            CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class,
        ]);

        $this->term = Term::query()->where('name', 'Học kỳ 2')->orderBy('start_date')->firstOrFail();
        $this->sections = CourseSection::query()->where('term_id', $this->term->id)->orderBy('id')->limit(3)->get()->all();
        ScheduleRule::query()->whereIn('course_section_id', collect($this->sections)->pluck('id'))->delete();
        ClassSession::query()->whereIn('course_section_id', collect($this->sections)->pluck('id'))->delete();
    }

    private function day(int $offset): string
    {
        return CarbonImmutable::parse($this->term->start_date)->addDays($offset)->toDateString();
    }

    public function test_ngay_bat_dau_hoc_lay_buoi_som_nhat_co_hoc_roi_toi_mau_lich(): void
    {
        [$withSessions, $withRule, $empty] = $this->sections;

        // Buổi Hủy / Nghỉ không tính; lấy buổi Dự kiến sớm nhất
        ClassSession::create(['course_section_id' => $withSessions->id, 'date' => $this->day(0), 'period_from' => 1, 'period_to' => 3, 'status' => 'cancelled']);
        ClassSession::create(['course_section_id' => $withSessions->id, 'date' => $this->day(1), 'period_from' => 1, 'period_to' => 3, 'status' => 'off']);
        ClassSession::create(['course_section_id' => $withSessions->id, 'date' => $this->day(8), 'period_from' => 1, 'period_to' => 3, 'status' => 'planned']);
        ClassSession::create(['course_section_id' => $withSessions->id, 'date' => $this->day(15), 'period_from' => 1, 'period_to' => 3, 'status' => 'planned']);

        // Chưa sinh buổi: tính từ mẫu lịch (tuần đầu tiên có học)
        ScheduleRule::create(['course_section_id' => $withRule->id, 'day_of_week' => 3, 'period_from' => 1, 'period_to' => 3, 'week_from' => 2, 'week_to' => 10, 'week_parity' => 'all']);

        $dates = app(SectionStartDates::class)->for([$withSessions->id, $withRule->id, $empty->id]);

        $this->assertSame($this->day(8), $dates[$withSessions->id]?->toDateString());
        $this->assertSame(app(ScheduleRuleService::class)->dateOf($this->term, 2, 3)->toDateString(), $dates[$withRule->id]?->toDateString());
        $this->assertNull($dates[$empty->id]);
    }

    public function test_dot_dang_ky_phai_dong_truoc_ngay_bat_dau_hoc_cua_hoc_ky(): void
    {
        $service = app(RegistrationPeriodService::class);
        $start = CarbonImmutable::parse($this->term->start_date);
        $base = ['term_id' => $this->term->id, 'name' => 'Đợt thử', 'round' => RegistrationRound::Round1->value, 'opens_at' => $start->subDays(20)->format('Y-m-d').' 08:00'];

        try {
            $service->create($base + ['closes_at' => $start->format('Y-m-d').' 07:00']);
            $this->fail('Đợt đóng đúng ngày bắt đầu học phải bị từ chối.');
        } catch (BusinessRuleException $e) {
            $this->assertStringContainsString('không trước ngày bắt đầu học', $e->getMessage());
        }

        $period = $service->create($base + ['closes_at' => $start->subDay()->format('Y-m-d').' 23:00']);
        $this->assertSame(PeriodStatus::Draft, $period->status);
    }

    public function test_lop_bat_dau_hoc_truoc_ngay_dong_dang_ky_thi_khong_dang_ky_duoc(): void
    {
        [$early, $late] = $this->sections;
        $period = RegistrationPeriod::create([
            'term_id' => $this->term->id, 'name' => 'Đợt bổ sung', 'round' => RegistrationRound::Round1->value, 'status' => PeriodStatus::Published,
            'opens_at' => CarbonImmutable::parse($this->term->start_date)->subDays(10), 'closes_at' => CarbonImmutable::parse($this->day(5).' 10:00', config('studentmanager.display_timezone'))->utc(),
        ]);
        ClassSession::create(['course_section_id' => $early->id, 'date' => $this->day(5), 'period_from' => 1, 'period_to' => 3, 'status' => 'planned']);
        ClassSession::create(['course_section_id' => $late->id, 'date' => $this->day(6), 'period_from' => 1, 'period_to' => 3, 'status' => 'planned']);
        $rules = app(EnrollmentRules::class);

        $this->assertTrue(collect($rules->sectionReasons($early, $period))->contains(fn ($r) => str_contains($r, 'bắt đầu học ngày')));
        $this->assertFalse(collect($rules->sectionReasons($late, $period))->contains(fn ($r) => str_contains($r, 'bắt đầu học ngày')));
        // Cán bộ đăng ký thay ngoài đợt thì không áp ràng buộc này
        $this->assertFalse(collect($rules->sectionReasons($early, $period, true, true))->contains(fn ($r) => str_contains($r, 'bắt đầu học ngày')));
    }

    public function test_sinh_vien_thay_lich_lop_vua_dang_ky_khi_tkb_chua_cong_bo_huy_thi_mat(): void
    {
        [$section] = $this->sections;
        ClassSession::create(['course_section_id' => $section->id, 'date' => $this->day(2), 'period_from' => 1, 'period_to' => 3, 'status' => 'planned']);
        $user = $this->userWithRoles('STU');
        $student = Student::factory()->create(['user_id' => $user->id]);
        $enrollment = Enrollment::create([
            'student_id' => $student->id, 'course_section_id' => $section->id, 'term_id' => $this->term->id,
            'subject_id' => $section->subject_id, 'status' => 'registered', 'registered_at' => now(),
        ]);
        $this->actingAs($user);

        $this->getJson('/student/timetable?date='.$this->day(2))->assertOk()
            ->assertJsonPath('timetable_status', 'draft')
            ->assertJsonPath('sessions.0.section', $section->code)
            ->assertJsonPath('message', "Thời khóa biểu {$this->term->name} chưa công bố: đây là lịch dự kiến của các lớp bạn đã đăng ký, có thể còn thay đổi.");

        $enrollment->update(['status' => 'cancelled']);
        $this->getJson('/student/timetable?date='.$this->day(2))->assertOk()->assertJsonCount(0, 'sessions');
    }
}
