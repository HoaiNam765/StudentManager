<?php

namespace Tests\Feature\Modules\Teacher;

use App\Models\User;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\LeadershipService;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Enums\LecturerStatus;
use App\Modules\Teacher\Events\LecturerLeaveApproved;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Services\LecturerScheduleService;
use App\Modules\Teacher\Services\LecturerService;
use Carbon\CarbonImmutable;
use Database\Seeders\FacultySeeder;
use Database\Seeders\SubjectSeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class LecturerScheduleTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private LecturerScheduleService $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRoles();
        $this->seed([FacultySeeder::class, SubjectSeeder::class, TeacherSeeder::class]);
        User::query()->where('profile_type', 'teacher')->update(['must_change_password' => false, 'temporary_password_expires_at' => null]);
        $this->schedule = app(LecturerScheduleService::class);
    }

    private function lecturer(string $code = 'GV0002'): Lecturer
    {
        return Lecturer::query()->where('code', $code)->firstOrFail();
    }

    private function day(int $offset = 0): string
    {
        return CarbonImmutable::now(config('studentmanager.display_timezone'))->addDays($offset)->toDateString();
    }

    /** Học kỳ bắt đầu sau $offset ngày. */
    private function term(int $offset = 30): Term
    {
        $start = CarbonImmutable::parse($this->day($offset));
        $year = AcademicYear::query()->create(['name' => 'Năm học thử '.$offset, 'start_date' => $start->toDateString(), 'end_date' => $start->addYear()->toDateString()]);

        return Term::query()->create([
            'academic_year_id' => $year->id, 'name' => 'Học kỳ thử '.$offset, 'type' => 'main',
            'start_date' => $start->toDateString(), 'end_date' => $start->addWeeks(15)->toDateString(), 'weeks' => 15,
        ]);
    }

    private function headOf(string $departmentCode, string $lecturerCode): User
    {
        $user = $this->lecturer($lecturerCode)->user;
        app(LeadershipService::class)->assign([
            'unit_type' => 'department', 'unit_id' => Department::query()->where('code', $departmentCode)->value('id'),
            'user_id' => $user->id, 'position' => 'head', 'starts_on' => $this->day(),
        ]);

        return $user->refresh();
    }

    public function test_giang_vien_khai_bao_lich_ban_khong_chong_nhau_va_ttb_hoi_duoc(): void
    {
        $this->actingAs($this->lecturer()->user);
        $range = ['starts_on' => $this->day(), 'ends_on' => $this->day(120)];

        $id = $this->postJson('/teacher/availability', ['day_of_week' => 2, 'period_from' => 1, 'period_to' => 3, 'reason' => 'Họp bộ môn'] + $range)
            ->assertCreated()->assertJsonPath('day_label', 'Thứ Ba')->json('id');

        $this->postJson('/teacher/availability', ['day_of_week' => 2, 'period_from' => 3, 'period_to' => 5] + $range)
            ->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'Thứ Ba tiết 1–3'));
        $this->postJson('/teacher/availability', ['day_of_week' => 2, 'period_from' => 4, 'period_to' => 16] + $range)->assertStatus(422);
        $this->postJson('/teacher/availability', ['day_of_week' => 3, 'period_from' => 4, 'period_to' => 6] + $range)->assertCreated();

        $this->getJson('/teacher/availability')->assertOk()->assertJsonCount(2);

        // TTB: buổi dạy thứ Ba tới, tiết 2–4 trùng giờ bận
        $tuesday = CarbonImmutable::parse($this->day())->next(CarbonImmutable::TUESDAY)->toDateString();
        $this->assertNotNull($this->schedule->busySlot($this->lecturer(), $tuesday, 2, 4));
        $this->assertNull($this->schedule->busySlot($this->lecturer(), $tuesday, 6, 8));

        // Không xóa được khung bận của người khác
        $this->actingAs($this->lecturer('GV0003')->user);
        $this->deleteJson("/teacher/availability/{$id}")->assertNotFound();
    }

    public function test_nguyen_vong_chi_cho_hoc_ky_sap_toi_va_hoc_phan_dang_hoat_dong(): void
    {
        $this->actingAs($this->lecturer()->user);
        $next = $this->term(30);
        $current = $this->term(-10);
        $int302 = Subject::query()->where('code', 'INT302')->value('id');
        $int303 = Subject::query()->where('code', 'INT303')->value('id');

        $this->putJson('/teacher/preferences', ['term_id' => $next->id, 'subjects' => [['subject_id' => $int302], ['subject_id' => $int303, 'note' => 'Lớp buổi tối']]])
            ->assertOk()->assertJsonPath('0.subject.code', 'INT302')->assertJsonPath('1.priority', 2);

        $this->putJson('/teacher/preferences', ['term_id' => $current->id, 'subjects' => [['subject_id' => $int302]]])->assertStatus(422);
        $this->putJson('/teacher/preferences', ['term_id' => $next->id, 'subjects' => [['subject_id' => $int302], ['subject_id' => $int302]]])->assertStatus(422);

        Subject::query()->whereKey($int303)->first()->deactivate();
        $this->putJson('/teacher/preferences', ['term_id' => $next->id, 'subjects' => [['subject_id' => $int303]]])->assertStatus(422);

        // Lưu lại thay cả danh sách
        $this->putJson('/teacher/preferences', ['term_id' => $next->id, 'subjects' => [['subject_id' => $int302, 'priority' => 1]]])->assertOk()->assertJsonCount(1);
        $this->getJson("/teacher/preferences?term_id={$next->id}")->assertOk()->assertJsonCount(1);

        // Cán bộ đào tạo xem nguyện vọng theo học phần để phân công
        $this->actingAs($this->userWithRoles('ACAD'));
        $this->getJson("/admin/teaching-preferences?term_id={$next->id}&subject_id={$int302}")->assertOk()->assertJsonPath('0.lecturer.code', 'GV0002');
    }

    public function test_don_nghi_khong_trung_va_truong_bo_mon_duyet_phat_su_kien(): void
    {
        Event::fake([LecturerLeaveApproved::class]);
        $me = $this->lecturer('GV0002');   // BM-KTPM
        $this->actingAs($me->user);

        $id = $this->postJson('/teacher/leaves', ['type' => 'business_trip', 'starts_on' => $this->day(5), 'ends_on' => $this->day(7), 'reason' => 'Hội thảo khoa học'])
            ->assertCreated()->assertJsonPath('status', 'pending')->json('id');

        $this->postJson('/teacher/leaves', ['type' => 'annual', 'starts_on' => $this->day(7), 'ends_on' => $this->day(9), 'reason' => 'Việc riêng'])
            ->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'Chờ duyệt'));

        // Giảng viên không duyệt được; trưởng bộ môn khác không có thẩm quyền
        $this->postJson("/admin/lecturer-leaves/{$id}/approve")->assertForbidden();
        $this->actingAs($this->headOf('BM-HTTT', 'GV0004'));
        $this->postJson("/admin/lecturer-leaves/{$id}/approve")->assertForbidden();

        $this->actingAs($this->headOf('BM-KTPM', 'GV0001'));
        $this->getJson('/admin/lecturer-leaves')->assertOk()->assertJsonPath('total', 1);
        $this->postJson("/admin/lecturer-leaves/{$id}/approve", ['note' => 'Đồng ý'])->assertOk()->assertJsonPath('status_label', 'Đã duyệt');

        Event::assertDispatched(LecturerLeaveApproved::class, fn ($e) => $e->leave->id === $id);
        $this->assertTrue($this->schedule->isOnLeave($me, $this->day(6)));
        $this->assertFalse($this->schedule->isOnLeave($me, $this->day(8)));
        $this->assertDatabaseHas('audit_logs', ['event' => 'approved', 'auditable_id' => $id]);
    }

    public function test_khong_tu_duyet_tu_choi_can_ly_do_va_huy_don(): void
    {
        $head = $this->headOf('BM-KTPM', 'GV0001');
        $this->actingAs($head);

        $own = $this->postJson('/teacher/leaves', ['type' => 'sick', 'starts_on' => $this->day(1), 'ends_on' => $this->day(2), 'reason' => 'Ốm'])->json('id');
        $this->postJson("/admin/lecturer-leaves/{$own}/approve")->assertStatus(422)->assertJsonPath('message', fn ($m) => str_contains($m, 'chính mình'));

        $this->actingAs($this->lecturer('GV0002')->user);
        $id = $this->postJson('/teacher/leaves', ['type' => 'annual', 'starts_on' => $this->day(10), 'ends_on' => $this->day(11), 'reason' => 'Việc gia đình'])->json('id');

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->postJson("/admin/lecturer-leaves/{$id}/reject")->assertStatus(422)->assertJsonValidationErrors('note');
        $this->postJson("/admin/lecturer-leaves/{$id}/reject", ['note' => 'Trùng lịch thi'])->assertOk()->assertJsonPath('status', 'rejected');

        $this->actingAs($this->lecturer('GV0002')->user);
        $again = $this->postJson('/teacher/leaves', ['type' => 'annual', 'starts_on' => $this->day(10), 'ends_on' => $this->day(11), 'reason' => 'Gửi lại'])->assertCreated()->json('id');
        $this->postJson("/teacher/leaves/{$again}/cancel")->assertOk()->assertJsonPath('status', 'cancelled');
        $this->postJson("/teacher/leaves/{$again}/cancel")->assertStatus(422);
        $this->getJson('/teacher/leaves')->assertOk()->assertJsonCount(2);
    }

    public function test_giang_vien_da_nghi_viec_khong_khai_bao_duoc(): void
    {
        $me = app(LecturerService::class)->changeStatus($this->lecturer(), LecturerStatus::Resigned, reason: 'Thôi việc');

        $this->actingAs($me->user);
        $this->postJson('/teacher/leaves', ['type' => 'annual', 'starts_on' => $this->day(1), 'ends_on' => $this->day(2), 'reason' => 'X'])->assertStatus(422);
        $this->postJson('/teacher/availability', ['day_of_week' => 1, 'period_from' => 1, 'period_to' => 2, 'starts_on' => $this->day(), 'ends_on' => $this->day(10)])->assertStatus(422);
    }

    public function test_xem_lich_ban_cua_giang_vien_theo_pham_vi(): void
    {
        $this->schedule->addBusy($this->lecturer(), ['day_of_week' => 5, 'period_from' => 7, 'period_to' => 9, 'starts_on' => $this->day(), 'ends_on' => $this->day(30)]);
        $id = $this->lecturer()->id;

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->getJson("/admin/lecturers/{$id}/availability")->assertOk()->assertJsonPath('0.day_label', 'Thứ Sáu');

        $this->actingAs($this->lecturer('GV0003')->user);
        $this->getJson("/admin/lecturers/{$id}/availability")->assertForbidden();
    }
}
