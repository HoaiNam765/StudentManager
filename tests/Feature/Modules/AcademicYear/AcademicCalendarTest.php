<?php

namespace Tests\Feature\Modules\AcademicYear;

use App\Models\User;
use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Holiday;
use App\Modules\AcademicYear\Models\MilestoneChangeRequest;
use App\Modules\AcademicYear\Models\MilestoneType;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermMilestone;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Notifications\MilestonesChanged;
use App\Modules\AcademicYear\Services\HolidayService;
use App\Modules\AcademicYear\Services\MilestoneService;
use App\Support\Audit\AuditLog;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\CarbonImmutable;
use Database\Seeders\AcademicYearSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\Support\InteractsWithRoles;
use Tests\TestCase;

class AcademicCalendarTest extends TestCase
{
    use DatabaseMigrations;
    use InteractsWithRoles;

    private MilestoneService $milestones;

    private HolidayService $holidays;

    private AcademicYear $year;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->seedRoles();
        $this->milestones = app(MilestoneService::class);
        $this->holidays = app(HolidayService::class);
        $this->year = AcademicYear::create(['name' => 'Năm thử', 'start_date' => $this->day(-400), 'end_date' => $this->day(400)]);
    }

    private function day(int $offset): string
    {
        return CarbonImmutable::now(config('studentmanager.display_timezone'))->addDays($offset)->toDateString();
    }

    /** Học kỳ bắt đầu sau $startOffset ngày, dài 130 ngày. */
    private function term(int $startOffset, string $name = 'Học kỳ thử'): Term
    {
        return Term::create([
            'academic_year_id' => $this->year->id, 'name' => $name, 'type' => 'main',
            'start_date' => $this->day($startOffset), 'end_date' => $this->day($startOffset + 130), 'weeks' => 18,
            'status' => TermStatus::PLANNED,
        ]);
    }

    /** Lịch hợp lệ tương đối theo ngày bắt đầu học kỳ. */
    private function calendar(Term $term): array
    {
        $at = fn (int $days) => $term->start_date->copy()->addDays($days)->toDateString();

        return [
            'registration_open' => $at(-30), 'registration_close' => $at(-5), 'classes_start' => $at(0),
            'cancel_deadline' => $at(14), 'withdraw_deadline' => $at(60), 'teaching_end' => $at(110),
            'exam_start' => $at(115), 'exam_end' => $at(125), 'grade_entry_deadline' => $at(135), 'grade_publish' => $at(140),
        ];
    }

    private function rejected(callable $callback): BusinessRuleException
    {
        try {
            $callback();
        } catch (BusinessRuleException $exception) {
            $this->assertNotEmpty($exception->hint(), 'Lỗi nghiệp vụ phải kèm cách khắc phục');

            return $exception;
        }

        $this->fail('Thao tác phải bị từ chối.');
    }

    public function test_han_rut_som_hon_han_huy_thi_bi_tu_choi_kem_ly_do(): void
    {
        $term = $this->term(30);
        $dates = $this->calendar($term);
        $dates['withdraw_deadline'] = $term->start_date->copy()->addDays(10)->toDateString(); // trước hạn hủy (ngày 14)

        $e = $this->rejected(fn () => $this->milestones->save($term, $dates, User::factory()->create()));
        $this->assertStringContainsString('Hạn hủy học phần', $e->getMessage());
        $this->assertStringContainsString('phải trước hạn rút học phần', $e->getMessage());
        $this->assertStringContainsString('BR-ACY-02', $e->hint());
        $this->assertSame([], $this->milestones->milestones($term));
    }

    public function test_luu_lich_hop_le_doc_moc_va_bo_moc(): void
    {
        $term = $this->term(30);
        $user = User::factory()->create();

        $this->assertSame($term, $this->milestones->save($term, $this->calendar($term), $user));
        $this->assertCount(10, $this->milestones->milestones($term));
        $this->assertSame($term->start_date->copy()->addDays(60)->toDateString(), $this->milestones->date($term, MilestoneType::WithdrawDeadline)->toDateString());

        // Bỏ mốc rồi đặt lại được
        $this->milestones->save($term, ['grade_publish' => null], $user);
        $this->assertNull($this->milestones->date($term, MilestoneType::GradePublish));
        $this->milestones->save($term, ['grade_publish' => $term->start_date->copy()->addDays(141)->toDateString()], $user);
        $this->assertNotNull($this->milestones->date($term, MilestoneType::GradePublish));

        // Mở đăng ký phải trước đóng đăng ký; bắt đầu học phải trong học kỳ
        $this->rejected(fn () => $this->milestones->save($term, ['registration_open' => $term->start_date->copy()->addDays(-1)->toDateString()], $user));
        $this->rejected(fn () => $this->milestones->save($term, ['classes_start' => $term->start_date->copy()->addDays(-1)->toDateString()], $user));
        $this->rejected(fn () => $this->milestones->save($term, ['khong_co' => $this->day(0)], $user));
    }

    public function test_sua_moc_hoc_ky_da_bat_dau_can_ly_do_va_nguoi_duyet_khac(): void
    {
        $term = $this->term(-10);
        $requester = $this->userWithRoles('ACAD');
        $approver = $this->userWithRoles('ACAD');
        $lecturer = $this->userWithRoles('LEC');
        // Lịch có sẵn từ trước khi học kỳ bắt đầu
        foreach ($this->calendar($term) as $type => $date) {
            $term->milestones()->create(['type' => $type, 'date' => $date]);
        }

        $newExam = $term->start_date->copy()->addDays(117)->toDateString();
        $this->rejected(fn () => $this->milestones->save($term, ['exam_start' => $newExam], $requester));

        $request = $this->milestones->save($term, ['exam_start' => $newExam], $requester, 'Trùng lịch thi tốt nghiệp');
        $this->assertInstanceOf(MilestoneChangeRequest::class, $request);
        $this->assertSame(MilestoneChangeRequest::PENDING, $request->status);
        $this->assertNotSame($newExam, $this->milestones->date($term, MilestoneType::ExamStart)?->toDateString(), 'Chưa duyệt thì chưa áp dụng');

        // Người đề nghị không tự duyệt; người không có quyền duyệt bị từ chối
        $this->rejected(fn () => $this->milestones->approve($request, $requester));
        $this->rejected(fn () => $this->milestones->approve($request, $lecturer));

        $this->milestones->approve($request, $approver, 'Đồng ý');

        $this->assertSame($newExam, $this->milestones->date($term, MilestoneType::ExamStart)->toDateString());
        $this->assertSame(MilestoneChangeRequest::APPROVED, $request->refresh()->status);
        Notification::assertSentTo($requester, MilestonesChanged::class, fn ($n) => $n->approved);

        $log = AuditLog::query()->where('auditable_type', (new TermMilestone)->getMorphClass())->latest('id')->firstOrFail();
        $this->assertStringContainsString('Trùng lịch thi tốt nghiệp', $log->reason);
        $this->assertStringContainsString("Người duyệt: {$approver->name}", $log->reason);

        // Đã xử lý thì không duyệt lại; đề nghị khác bị từ chối kèm lý do
        $this->rejected(fn () => $this->milestones->approve($request, $approver));
        $second = $this->milestones->save($term, ['grade_publish' => $term->start_date->copy()->addDays(142)->toDateString()], $requester, 'Lùi công bố');
        $this->milestones->reject($second, $approver, 'Không cần thiết');
        $this->assertSame(MilestoneChangeRequest::REJECTED, $second->refresh()->status);
        Notification::assertSentTo($requester, MilestonesChanged::class, fn ($n) => ! $n->approved);
    }

    public function test_sao_chep_lich_tu_hoc_ky_truoc_doi_theo_ngay_bat_dau(): void
    {
        $previous = $this->term(-200, 'Học kỳ trước');
        $next = $this->term(40, 'Học kỳ sau');
        $user = User::factory()->create();
        // Học kỳ trước đã qua nên lịch của nó có sẵn từ trước
        foreach ($this->calendar($previous) as $type => $date) {
            $previous->milestones()->create(['type' => $type, 'date' => $date]);
        }

        $this->milestones->copyFrom($previous, $next, $user);

        $this->assertSame($this->calendar($next), $this->milestones->milestones($next));
        $this->rejected(fn () => $this->milestones->copyFrom($next, $previous, $user)); // học kỳ đích đã bắt đầu
    }

    public function test_ngay_nghi_cung_cap_cho_thoi_khoa_bieu_va_khong_sua_ngay_da_qua(): void
    {
        $term = $this->term(10);
        $this->holidays->create(['name' => 'Lễ', 'type' => 'public_holiday', 'starts_on' => $this->day(12), 'ends_on' => $this->day(13)]);
        $this->holidays->create(['name' => 'Nghỉ giữa kỳ', 'type' => 'school_break', 'starts_on' => $this->day(20), 'ends_on' => $this->day(20), 'term_id' => $term->id]);
        $other = $this->term(10, 'Học kỳ khác');

        $this->assertSame([$this->day(12), $this->day(13), $this->day(20)], $this->holidays->daysOff($this->day(10), $this->day(30), $term));
        $this->assertSame([$this->day(12), $this->day(13)], $this->holidays->daysOff($this->day(10), $this->day(30), $other));
        $this->assertTrue($this->holidays->isDayOff($this->day(13)));
        $this->assertFalse($this->holidays->isDayOff($this->day(14)));

        // Nghỉ riêng của học kỳ phải nằm trong học kỳ; ngày kết thúc không trước ngày bắt đầu
        $this->rejected(fn () => $this->holidays->create(['name' => 'Ngoài kỳ', 'type' => 'school_break', 'starts_on' => $this->day(5), 'ends_on' => $this->day(5), 'term_id' => $term->id]));
        $this->rejected(fn () => $this->holidays->create(['name' => 'Ngược', 'type' => 'public_holiday', 'starts_on' => $this->day(9), 'ends_on' => $this->day(8)]));

        $past = $this->holidays->create(['name' => 'Đã qua', 'type' => 'public_holiday', 'starts_on' => $this->day(-3), 'ends_on' => $this->day(-2)]);
        $this->rejected(fn () => $this->holidays->update($past, ['name' => 'Sửa']));
        $this->rejected(fn () => $this->holidays->delete($past));
    }

    public function test_api_lich_hoc_vu_theo_quyen_va_seeder(): void
    {
        $this->seed(AcademicYearSeeder::class);
        $this->seed(AcademicYearSeeder::class);
        $term = Term::query()->where('name', 'Học kỳ 2')->firstOrFail();
        $first = Term::query()->where('name', 'Học kỳ 1')->firstOrFail();
        $this->assertSame(5, Holiday::count());

        $this->actingAs($this->userWithRoles('STU'));
        $this->getJson("/admin/terms/{$first->id}/milestones")->assertOk()->assertJsonPath('milestones.0.date', '2026-08-01');
        $this->putJson("/admin/terms/{$term->id}/milestones", ['milestones' => ['classes_start' => '2027-01-25']])->assertForbidden();
        $this->getJson('/admin/holidays/days-off?from=2026-09-01&to=2026-09-05')->assertOk()->assertJsonPath('days_off', ['2026-09-01', '2026-09-02']);

        $this->actingAs($this->userWithRoles('ADMIN')); // ma trận ACY của ADMIN chỉ có Xem
        $this->putJson("/admin/terms/{$term->id}/milestones", ['milestones' => ['classes_start' => '2027-01-25']])->assertForbidden();

        $this->actingAs($this->userWithRoles('ACAD'));
        $this->postJson("/admin/terms/{$term->id}/milestones/copy", ['from_term_id' => $first->id])->assertOk();
        $this->getJson("/admin/terms/{$term->id}/milestones")->assertOk()->assertJsonPath('milestones.2.date', '2027-01-31'); // 07/09 + 146 ngày
        $this->putJson("/admin/terms/{$term->id}/milestones", ['milestones' => ['withdraw_deadline' => '2027-01-20']])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'Lịch học vụ chưa đúng thứ tự'));
    }
}
