<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Attendance\Models\AttendanceChangeRequest;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Models\AttendanceSummary;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Student\Models\Student;
use App\Modules\Timetable\Models\ClassSession;
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

/** Điểm danh (#142, #143, #144): ghi theo buổi, khung sửa, yêu cầu sửa quá hạn, khóa sổ, cấm thi, chuyên cần cá nhân. */
class AttendanceTest extends TestCase
{
    use InteractsWithRoles;
    use RefreshDatabase;

    private CourseSection $section;

    private User $lecturer;

    /** @var list<Enrollment> */
    private array $enrollments = [];

    /** @var list<ClassSession> */
    private array $sessions = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->seed([
            SystemSeeder::class, NotificationEventSeeder::class, FacultySeeder::class, AcademicYearSeeder::class, RoomSeeder::class,
            SubjectSeeder::class, CurriculumSeeder::class, TeacherSeeder::class, GradeStructureSeeder::class, SchoolClassSeeder::class,
        ]);

        $this->section = CourseSection::query()->whereHas('instructors')->firstOrFail();
        $this->lecturer = SectionInstructor::query()->where('course_section_id', $this->section->id)->firstOrFail()->lecturer->user;
        $this->lecturer->forceFill(['must_change_password' => false, 'temporary_password_expires_at' => null, 'password_changed_at' => now()])->save();

        foreach (range(1, 3) as $i) {
            $student = Student::factory()->create(['cohort' => 2026]);
            $this->enrollments[] = Enrollment::create([
                'student_id' => $student->id, 'course_section_id' => $this->section->id, 'term_id' => $this->section->term_id,
                'subject_id' => $this->section->subject_id, 'status' => 'registered', 'registered_at' => now(),
            ]);
        }

        // 10 buổi đã diễn ra (cách nhau 1 ngày, gần nhất là hôm qua) + 1 buổi tương lai + 1 buổi nghỉ lễ
        foreach (range(10, 1) as $daysAgo) {
            $this->sessions[] = ClassSession::create(['course_section_id' => $this->section->id, 'date' => now()->subDays($daysAgo)->toDateString(), 'period_from' => 1, 'period_to' => 3, 'status' => 'planned']);
        }
        ClassSession::create(['course_section_id' => $this->section->id, 'date' => now()->addDays(3)->toDateString(), 'period_from' => 1, 'period_to' => 3, 'status' => 'planned']);
        ClassSession::create(['course_section_id' => $this->section->id, 'date' => now()->subDays(2)->toDateString(), 'period_from' => 4, 'period_to' => 6, 'status' => 'off', 'off_reason' => 'Nghỉ lễ']);
    }

    private function items(string $status = 'present', array $override = []): array
    {
        return array_map(fn (Enrollment $e) => ['enrollment_id' => $e->id, 'status' => $override[$e->id] ?? $status], $this->enrollments);
    }

    public function test_giang_vien_diem_danh_tat_ca_co_mat_roi_chinh_ngoai_le(): void
    {
        $session = end($this->sessions);
        $this->actingAs($this->lecturer);

        $this->getJson("/teacher/class-sessions/{$session->id}/attendance")->assertOk()->assertJsonCount(3, 'rows')->assertJsonPath('can.record', true);
        $this->putJson("/teacher/class-sessions/{$session->id}/attendance", ['items' => $this->items('present', [$this->enrollments[0]->id => 'absent'])])
            ->assertOk()->assertJsonPath('changed', 3);

        $this->assertSame(2, AttendanceRecord::query()->where('status', 'present')->count());
        $summary = AttendanceSummary::query()->where('enrollment_id', $this->enrollments[0]->id)->firstOrFail();
        // Mẫu số = 11 buổi có học (10 đã qua + 1 sắp tới), buổi nghỉ lễ không tính (BR-ATT-06)
        $this->assertSame(11, $summary->total_sessions);
        $this->assertSame(1, $summary->absent_count);
    }

    public function test_khong_diem_danh_buoi_tuong_lai_buoi_nghi_va_giang_vien_khac(): void
    {
        $future = ClassSession::query()->where('date', '>', now()->toDateString())->firstOrFail();
        $off = ClassSession::query()->where('status', 'off')->firstOrFail();
        $this->actingAs($this->lecturer);

        $this->putJson("/teacher/class-sessions/{$future->id}/attendance", ['items' => $this->items()])->assertStatus(422);
        $this->putJson("/teacher/class-sessions/{$off->id}/attendance", ['items' => $this->items()])->assertStatus(422);

        $other = $this->userWithRoles('LEC');
        $this->actingAs($other)->putJson("/teacher/class-sessions/{$this->sessions[9]->id}/attendance", ['items' => $this->items()])->assertForbidden();
    }

    public function test_vang_3_tren_10_buoi_bi_cam_thi_va_thong_bao(): void
    {
        $this->actingAs($this->lecturer);
        $target = $this->enrollments[1];
        // Chỉ các buổi trong khung sửa 7 ngày giảng viên ghi được; buổi cũ hơn do Phòng Đào tạo ghi
        $acad = $this->userWithRoles('ACAD');

        foreach ($this->sessions as $i => $session) {
            $actor = $session->date->diffInDays(now()->startOfDay()) <= 7 ? $this->lecturer : $acad;
            $this->actingAs($actor)->putJson(
                ($actor === $acad ? '/admin' : '/teacher')."/class-sessions/{$session->id}/attendance",
                ['items' => $this->items('present', $i < 3 ? [$target->id => 'absent'] : []), 'reason' => 'Nhập bù dữ liệu'],
            )->assertOk();
        }

        $summary = AttendanceSummary::query()->where('enrollment_id', $target->id)->firstOrFail();
        $this->assertSame(3, $summary->absent_count);
        $this->assertTrue($summary->banned);
        $this->assertTrue(DB::table('notifications')->where('title', 'Vắng vượt ngưỡng cấm thi')->exists());

        // Phòng Đào tạo miễn cấm thi có lý do (BR-ATT-03)
        $this->actingAs($acad)->postJson("/admin/attendance-summaries/{$summary->id}/exempt", ['exempted' => true, 'reason' => 'Ốm có giấy viện'])
            ->assertOk()->assertJsonPath('banned', false);
    }

    public function test_qua_7_ngay_giang_vien_gui_yeu_cau_phong_dao_tao_duyet(): void
    {
        $old = $this->sessions[0];
        $acad = $this->userWithRoles('ACAD');
        $this->actingAs($acad)->putJson("/admin/class-sessions/{$old->id}/attendance", ['items' => $this->items(), 'reason' => 'Nhập bù'])->assertOk();
        $record = AttendanceRecord::query()->where('class_session_id', $old->id)->where('enrollment_id', $this->enrollments[2]->id)->firstOrFail();

        $this->actingAs($this->lecturer)->putJson("/teacher/class-sessions/{$old->id}/attendance", ['items' => $this->items('absent')])->assertStatus(422);
        $this->actingAs($this->lecturer)->postJson("/teacher/attendance-records/{$record->id}/change-requests", ['status' => 'excused', 'reason' => 'Có giấy phép'])->assertCreated();
        $request = AttendanceChangeRequest::query()->firstOrFail();

        $this->actingAs($acad)->postJson("/admin/attendance-change-requests/{$request->id}/approve")->assertOk();
        $this->assertSame('excused', $record->fresh()->status->value);
    }

    public function test_khoa_so_thi_giang_vien_khong_sua_duoc(): void
    {
        $acad = $this->userWithRoles('ACAD');
        $this->actingAs($acad)->postJson("/admin/course-sections/{$this->section->id}/attendance/lock")->assertOk();

        $this->actingAs($this->lecturer)->putJson("/teacher/class-sessions/{$this->sessions[9]->id}/attendance", ['items' => $this->items()])->assertStatus(422);

        $this->actingAs($acad)->postJson("/admin/course-sections/{$this->section->id}/attendance/unlock", ['reason' => 'Sửa sai sót'])->assertOk();
        $this->actingAs($this->lecturer)->putJson("/teacher/class-sessions/{$this->sessions[9]->id}/attendance", ['items' => $this->items()])->assertOk();
    }

    public function test_danh_sach_buoi_tong_hop_va_cac_trang(): void
    {
        $this->actingAs($this->lecturer)->putJson("/teacher/class-sessions/{$this->sessions[9]->id}/attendance", ['items' => $this->items('absent')])->assertOk();

        $sessions = collect($this->actingAs($this->lecturer)->getJson("/teacher/course-sections/{$this->section->id}/attendance/sessions")->assertOk()
            ->assertJsonCount(12, 'sessions')->json('sessions'));
        $this->assertSame(3, $sessions->firstWhere('id', $this->sessions[9]->id)['counts']['absent']);
        $this->assertFalse($sessions->firstWhere('status', 'off')['open']);
        $this->actingAs($this->lecturer)->getJson("/teacher/course-sections/{$this->section->id}/attendance/summary")->assertOk()->assertJsonCount(3, 'data');
        $this->actingAs($this->lecturer)->get('/teacher/diem-danh')->assertOk()->assertSee('data-page="teacher-attendance"', false);

        $acad = $this->userWithRoles('ACAD');
        $this->actingAs($acad)->getJson('/admin/attendance/summaries?course_section_id='.$this->section->id)->assertOk()
            ->assertJsonPath('counts.total', 3)->assertJsonPath('data.0.absent_count', 1);
        $this->actingAs($acad)->get('/admin/diem-danh')->assertOk()->assertSee('data-page="admin-attendance"', false);
    }

    public function test_sinh_vien_chi_xem_chuyen_can_cua_minh_va_so_dau_bai(): void
    {
        $this->actingAs($this->lecturer)->putJson("/teacher/class-sessions/{$this->sessions[9]->id}/attendance", ['items' => $this->items('late')])->assertOk();
        $this->actingAs($this->lecturer)->putJson("/teacher/class-sessions/{$this->sessions[9]->id}/teaching-log", ['periods' => 3, 'content' => 'Chương 1'])->assertOk();
        $this->assertSame('taught', $this->sessions[9]->fresh()->status->value);

        $student = Student::query()->findOrFail($this->enrollments[0]->student_id);
        $user = $this->userWithRoles('STU');
        $student->forceFill(['user_id' => $user->id])->save();

        $this->actingAs($user)->getJson('/student/attendance?term_id='.$this->section->term_id)->assertOk()
            ->assertJsonCount(1, 'sections')->assertJsonPath('sections.0.records.0.status', 'late');
        $this->actingAs($user)->getJson('/admin/attendance/summaries')->assertForbidden();
    }
}
