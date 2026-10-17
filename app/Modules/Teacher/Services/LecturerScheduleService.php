<?php

namespace App\Modules\Teacher\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Enums\LeaveStatus;
use App\Modules\Teacher\Enums\LeaveType;
use App\Modules\Teacher\Events\LecturerLeaveApproved;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Models\LecturerAvailability;
use App\Modules\Teacher\Models\LecturerLeave;
use App\Modules\Teacher\Models\TeachingPreference;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Lịch bận, nguyện vọng giảng dạy và nghỉ phép của giảng viên (FR-TCH-008, 012; FR-TTB-010).
 *
 * - Lịch bận: thứ, tiết từ – đến (trong khung tiết `teacher.periods_per_day`), khoảng ngày; không chồng lên khung bận
 *   đã khai báo. TTB hỏi `busySlot()` khi xếp lịch để cảnh báo xếp vào giờ bận.
 * - Nguyện vọng: chỉ cho học kỳ chưa bắt đầu, học phần đang hoạt động; lưu cả danh sách một lần.
 * - Nghỉ phép, công tác: không trùng đơn khác còn hiệu lực; cán bộ khác duyệt (không tự duyệt); duyệt xong phát sự kiện
 *   `LecturerLeaveApproved` để TTB báo nghỉ / tìm người dạy thay. TTB, CLS hỏi `isOnLeave()`.
 */
class LecturerScheduleService extends BaseService
{
    public function __construct(private readonly AuditLogger $audit) {}

    // ---------------------------------------------------------------------
    // Lịch bận (FR-TCH-008, FR-TTB-010)
    // ---------------------------------------------------------------------

    /** @param  array{day_of_week: int, period_from: int, period_to: int, starts_on: string, ends_on: string, reason?: ?string}  $data */
    public function addBusy(Lecturer $lecturer, array $data): LecturerAvailability
    {
        $this->assertWorking($lecturer);
        $max = (int) config('studentmanager.teacher.periods_per_day');
        $from = (int) $data['period_from'];
        $to = (int) $data['period_to'];

        if ($from < 1 || $to > $max || $from > $to) {
            $this->fail("Tiết bận phải nằm trong khoảng 1–{$max} và tiết bắt đầu không sau tiết kết thúc.", 'Chọn lại tiết bắt đầu, tiết kết thúc.');
        }

        [$startsOn, $endsOn] = $this->dates($data['starts_on'], $data['ends_on']);

        $clash = $lecturer->availabilities()->clashing((int) $data['day_of_week'], $from, $to, $startsOn, $endsOn)->first();

        if ($clash !== null) {
            $this->fail("Đã khai báo bận {$clash->describe()}, trùng với khung giờ này.", 'Sửa hoặc xóa khung bận đã có thay vì khai báo chồng lên.');
        }

        return LecturerAvailability::create([
            'lecturer_id' => $lecturer->id,
            'day_of_week' => (int) $data['day_of_week'],
            'period_from' => $from,
            'period_to' => $to,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'reason' => $data['reason'] ?? null,
        ]);
    }

    public function removeBusy(LecturerAvailability $availability): void
    {
        $availability->delete();
    }

    /** Khung bận trùng với một buổi dạy dự kiến (ngày, tiết), hoặc null (FR-TTB-010). */
    public function busySlot(Lecturer $lecturer, string $date, int $periodFrom, int $periodTo): ?LecturerAvailability
    {
        $day = CarbonImmutable::parse($date);

        return $lecturer->availabilities()
            ->clashing($day->dayOfWeekIso, $periodFrom, $periodTo, $day->toDateString(), $day->toDateString())
            ->first();
    }

    // ---------------------------------------------------------------------
    // Nguyện vọng giảng dạy (FR-TCH-008)
    // ---------------------------------------------------------------------

    /**
     * Thay toàn bộ nguyện vọng của giảng viên cho học kỳ $term.
     *
     * @param  list<array{subject_id: int, priority?: int, note?: ?string}>  $items
     * @return Collection<int, TeachingPreference>
     */
    public function setPreferences(Lecturer $lecturer, Term $term, array $items): Collection
    {
        $this->assertWorking($lecturer);

        if ($term->start_date->toDateString() <= $this->today()) {
            $this->fail("Học kỳ {$term->name} đã bắt đầu.", 'Chỉ khai báo nguyện vọng cho học kỳ sắp tới.');
        }

        $ids = array_map(fn (array $i) => (int) $i['subject_id'], $items);

        if (count($ids) !== count(array_unique($ids))) {
            $this->fail('Một học phần được chọn nhiều lần.', 'Mỗi học phần chỉ khai báo một lần; dùng mức ưu tiên để sắp thứ tự.');
        }

        $subjects = Subject::query()->whereKey($ids)->get()->keyBy('id');

        foreach ($ids as $id) {
            if (! $subjects->has($id) || ! $subjects[$id]->isActive()) {
                $this->fail('Có học phần không tồn tại hoặc đã ngừng hoạt động.', 'Chọn học phần đang hoạt động trong danh mục.');
            }
        }

        $this->transaction(function () use ($lecturer, $term, $items): void {
            TeachingPreference::query()->where('lecturer_id', $lecturer->id)->where('term_id', $term->id)->get()->each->delete();

            foreach (array_values($items) as $i => $item) {
                TeachingPreference::create([
                    'lecturer_id' => $lecturer->id,
                    'term_id' => $term->id,
                    'subject_id' => $item['subject_id'],
                    'priority' => $item['priority'] ?? $i + 1,
                    'note' => $item['note'] ?? null,
                ]);
            }
        });

        return $this->preferences($lecturer, $term);
    }

    /** @return Collection<int, TeachingPreference> */
    public function preferences(Lecturer $lecturer, Term $term): Collection
    {
        return TeachingPreference::query()->where('lecturer_id', $lecturer->id)->where('term_id', $term->id)
            ->with('subject')->orderBy('priority')->orderBy('id')->get();
    }

    // ---------------------------------------------------------------------
    // Nghỉ phép, công tác (FR-TCH-012)
    // ---------------------------------------------------------------------

    /** @param  array{type: string, starts_on: string, ends_on: string, reason: string}  $data */
    public function requestLeave(Lecturer $lecturer, array $data, User $actor): LecturerLeave
    {
        $this->assertWorking($lecturer);
        [$startsOn, $endsOn] = $this->dates($data['starts_on'], $data['ends_on']);

        $overlap = $lecturer->leaves()->overlapping($startsOn, $endsOn)->first();

        if ($overlap !== null) {
            $this->fail(
                'Đã có đơn '.mb_strtolower($overlap->type->label()).' từ '.$overlap->starts_on->format('d/m/Y').' đến '.$overlap->ends_on->format('d/m/Y')." ({$overlap->status->label()}).",
                'Hủy hoặc sửa đơn đã có trước khi gửi đơn trùng thời gian.'
            );
        }

        return LecturerLeave::create([
            'lecturer_id' => $lecturer->id,
            'type' => LeaveType::from($data['type']),
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'reason' => $data['reason'],
            'status' => LeaveStatus::Pending,
            'requested_by' => $actor->id,
        ]);
    }

    public function approveLeave(LecturerLeave $leave, User $approver, ?string $note = null): LecturerLeave
    {
        $this->assertDecidable($leave, $approver);

        $leave->update(['status' => LeaveStatus::Approved, 'decided_by' => $approver->id, 'decided_at' => now(), 'decision_note' => $note]);
        $this->audit->record(AuditEvent::Approved, $leave, reason: $note);

        event(new LecturerLeaveApproved($leave->refresh()));

        return $leave;
    }

    public function rejectLeave(LecturerLeave $leave, User $approver, string $note): LecturerLeave
    {
        $this->assertDecidable($leave, $approver);

        $leave->update(['status' => LeaveStatus::Rejected, 'decided_by' => $approver->id, 'decided_at' => now(), 'decision_note' => $note]);
        $this->audit->record(AuditEvent::Rejected, $leave, reason: $note);

        return $leave->refresh();
    }

    /** Người gửi hủy đơn chờ duyệt, hoặc đơn đã duyệt nhưng chưa bắt đầu. */
    public function cancelLeave(LecturerLeave $leave, User $actor): LecturerLeave
    {
        if ($leave->requested_by !== $actor->id && $leave->lecturer->user_id !== $actor->id) {
            $this->fail('Chỉ người gửi đơn mới hủy được.', 'Cán bộ duyệt dùng thao tác từ chối kèm lý do.');
        }

        $cancellable = $leave->status === LeaveStatus::Pending
            || ($leave->status === LeaveStatus::Approved && $leave->starts_on->toDateString() > $this->today());

        if (! $cancellable) {
            $this->fail("Đơn đang ở trạng thái {$leave->status->label()} nên không hủy được.", 'Chỉ hủy được đơn chờ duyệt, hoặc đơn đã duyệt nhưng chưa tới ngày nghỉ.');
        }

        $leave->update(['status' => LeaveStatus::Cancelled]);

        return $leave->refresh();
    }

    /** Giảng viên nghỉ (đơn đã duyệt) vào ngày $date không — TTB, CLS dùng khi xếp lịch, phân công. */
    public function isOnLeave(Lecturer $lecturer, string $date): bool
    {
        $day = CarbonImmutable::parse($date)->toDateString();

        return $lecturer->leaves()
            ->where('status', LeaveStatus::Approved->value)
            ->whereDate('starts_on', '<=', $day)
            ->whereDate('ends_on', '>=', $day)
            ->exists();
    }

    // ---------------------------------------------------------------------

    private function assertDecidable(LecturerLeave $leave, User $approver): void
    {
        if ($leave->status !== LeaveStatus::Pending) {
            $this->fail('Đơn này đã được xử lý.', 'Tải lại danh sách để xem trạng thái mới nhất.');
        }

        if ($leave->requested_by === $approver->id || $leave->lecturer->user_id === $approver->id) {
            $this->fail('Không tự duyệt đơn nghỉ của chính mình.', 'Đơn phải do lãnh đạo đơn vị hoặc phòng Đào tạo duyệt.');
        }
    }

    private function assertWorking(Lecturer $lecturer): void
    {
        if (! $lecturer->isAssignable()) {
            $this->fail("Giảng viên {$lecturer->code} đang ở tình trạng {$lecturer->status_label}.", 'Chỉ giảng viên đang làm việc mới khai báo lịch bận, nguyện vọng, nghỉ phép.');
        }
    }

    /** @return array{0: string, 1: string} */
    private function dates(string $from, string $to): array
    {
        $from = CarbonImmutable::parse($from)->toDateString();
        $to = CarbonImmutable::parse($to)->toDateString();

        if ($to < $from) {
            $this->fail('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.', 'Chọn lại khoảng ngày.');
        }

        return [$from, $to];
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
