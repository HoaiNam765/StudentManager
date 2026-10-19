<?php

namespace App\Modules\Timetable\Services;

use App\Modules\AcademicYear\Models\Holiday;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Services\RoomAvailability;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Enums\SessionStatus;
use App\Modules\Timetable\Enums\TimetableStatus;
use App\Modules\Timetable\Models\ClassSession;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Buổi học cụ thể (FR-TTB-005, 009; BR-TTB-06).
 *
 * - `generate($lop)`: từ mẫu lịch sinh một buổi cho mỗi tuần học; ngày trùng ngày nghỉ của ACY (`holidays` áp dụng cho
 *   học kỳ hoặc toàn trường) thành **Nghỉ** kèm tên ngày nghỉ, cần dạy bù. Sinh lại khi lịch đổi: các buổi Dự kiến / Nghỉ
 *   từ hôm nay trở đi bị xóa mềm (còn trong lịch sử, nhật ký kiểm toán) rồi sinh mới; buổi đã qua, Đã dạy, Dạy bù, Hủy giữ
 *   nguyên. ScheduleRuleService gọi sau mỗi lần thêm / sửa / xóa dòng lịch.
 * - `applyHolidays($hk)`: ngày nghỉ khai báo sau khi đã sinh buổi → chuyển các buổi Dự kiến trùng ngày sang Nghỉ
 *   (lệnh `timetable:apply-holidays`).
 * - `makeup($buoiNghi, ...)`: xếp buổi dạy bù cho buổi Nghỉ / Hủy, trong học kỳ, phòng xếp được và không trùng buổi khác
 *   của phòng / của lớp cùng ngày.
 * Buổi Nghỉ / Hủy không tính vào số buổi và tỷ lệ vắng (`SessionStatus::countsForAttendance`).
 */
class SessionService extends BaseService
{
    public function __construct(
        private readonly ScheduleRuleService $rules,
        private readonly RoomAvailability $rooms,
    ) {}

    /** @return array{created: int, removed: int, off: int} */
    public function generate(CourseSection $section): array
    {
        $section->loadMissing('term');
        $term = $section->term;
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return $this->transaction(function () use ($section, $term, $today): array {
            $removed = 0;

            ClassSession::query()
                ->where('course_section_id', $section->id)
                ->whereIn('status', [SessionStatus::Planned, SessionStatus::Off])
                ->whereNull('makeup_for_id')
                ->where('date', '>=', $today)
                ->whereDoesntHave('makeups')
                ->get()
                ->each(function (ClassSession $s) use (&$removed): void {
                    $s->delete();
                    $removed++;
                });

            $kept = ClassSession::query()->where('course_section_id', $section->id)->get(['schedule_rule_id', 'date', 'period_from']);
            $holidays = $this->holidayNames($term);
            $created = 0;
            $off = 0;

            foreach (ScheduleRule::query()->where('course_section_id', $section->id)->get() as $rule) {
                foreach ($rule->weeks() as $week) {
                    $date = $this->rules->dateOf($term, $week, $rule->day_of_week)->toDateString();

                    // Ngày đã qua hoặc đã có buổi (giữ lại) thì không sinh lại
                    if ($date < $today || $kept->contains(fn ($k) => $k->date->toDateString() === $date && $k->period_from === $rule->period_from)) {
                        continue;
                    }

                    $holiday = $holidays[$date] ?? null;
                    ClassSession::create([
                        'course_section_id' => $section->id,
                        'schedule_rule_id' => $rule->id,
                        'date' => $date,
                        'week' => $week,
                        'period_from' => $rule->period_from,
                        'period_to' => $rule->period_to,
                        'room_id' => $rule->room_id,
                        'status' => $holiday ? SessionStatus::Off : SessionStatus::Planned,
                        'off_reason' => $holiday ? "Nghỉ: {$holiday} — cần xếp dạy bù" : null,
                    ]);
                    $created++;
                    $off += $holiday ? 1 : 0;
                }
            }

            return ['created' => $created, 'removed' => $removed, 'off' => $off];
        });
    }

    /** Chuyển buổi Dự kiến trùng ngày nghỉ (khai báo sau) sang Nghỉ. @return int số buổi đã chuyển */
    public function applyHolidays(Term $term): int
    {
        $holidays = $this->holidayNames($term);

        if ($holidays === []) {
            return 0;
        }

        $sessions = ClassSession::query()
            ->whereIn('course_section_id', CourseSection::query()->select('id')->where('term_id', $term->id))
            ->where('status', SessionStatus::Planned)
            ->whereIn('date', array_keys($holidays))
            ->get();

        foreach ($sessions as $session) {
            $session->update([
                'status' => SessionStatus::Off,
                'off_reason' => 'Nghỉ: '.$holidays[$session->date->toDateString()].' — cần xếp dạy bù',
            ]);
        }

        return $sessions->count();
    }

    /** Xếp buổi dạy bù cho buổi Nghỉ / Hủy (FR-TTB-009). */
    public function makeup(ClassSession $original, string $date, int $periodFrom, int $periodTo, ?int $roomId, ?string $note = null): ClassSession
    {
        if (! in_array($original->status, [SessionStatus::Off, SessionStatus::Cancelled], true)) {
            $this->fail("Buổi ngày {$original->date->format('d/m/Y')} đang {$original->status->label()}.", 'Chỉ xếp dạy bù cho buổi Nghỉ hoặc Hủy.');
        }

        if (ClassSession::query()->where('makeup_for_id', $original->id)->exists()) {
            $this->fail('Buổi này đã có buổi dạy bù.', 'Sửa hoặc hủy buổi dạy bù đã xếp thay vì xếp thêm.');
        }

        $section = $original->section()->with('term')->first();
        $this->assertNotLocked($section);
        $day = CarbonImmutable::parse($date);
        $term = $section->term;

        if ($day->lt($term->start_date) || $day->gt($term->end_date)) {
            $this->fail('Ngày dạy bù '.$day->format('d/m/Y').' nằm ngoài học kỳ.', 'Chọn ngày từ '.$term->start_date->format('d/m/Y').' đến '.$term->end_date->format('d/m/Y').'.');
        }

        if ($periodFrom > $periodTo) {
            $this->fail('Tiết bắt đầu sau tiết kết thúc.', 'Nhập lại tiết dạy bù.');
        }

        $roomId ??= $original->room_id;

        if ($roomId !== null) {
            $this->rooms->assertSchedulable(Room::query()->findOrFail($roomId), $day);
        }

        $busy = ClassSession::query()
            ->with('section:id,code')
            ->whereDate('date', $day->toDateString())
            ->whereIn('status', [SessionStatus::Planned, SessionStatus::Makeup, SessionStatus::Taught])
            ->where('period_from', '<=', $periodTo)
            ->where('period_to', '>=', $periodFrom)
            ->where(fn ($q) => $q->where('course_section_id', $section->id)->when($roomId !== null, fn ($w) => $w->orWhere('room_id', $roomId)))
            ->first();

        if ($busy !== null) {
            $this->fail(
                'Giờ dạy bù trùng buổi của lớp '.$busy->section->code." (tiết {$busy->period_from}–{$busy->period_to}).",
                'Chọn ngày, tiết hoặc phòng khác (BR-TTB-01).'
            );
        }

        return ClassSession::create([
            'course_section_id' => $section->id,
            'date' => $day->toDateString(),
            'period_from' => $periodFrom,
            'period_to' => $periodTo,
            'room_id' => $roomId,
            'status' => SessionStatus::Makeup,
            'makeup_for_id' => $original->id,
            'note' => $note,
        ]);
    }

    /** Hủy một buổi (có lý do), ví dụ giảng viên báo nghỉ đột xuất. */
    public function cancel(ClassSession $session, string $reason): ClassSession
    {
        if (! in_array($session->status, [SessionStatus::Planned, SessionStatus::Makeup], true)) {
            $this->fail("Buổi đang {$session->status->label()} nên không hủy được.", 'Chỉ hủy buổi Dự kiến hoặc Dạy bù.');
        }

        $this->assertNotLocked($session->section()->with('term')->first());
        $session->update(['status' => SessionStatus::Cancelled, 'off_reason' => $reason]);

        return $session;
    }

    private function assertNotLocked(CourseSection $section): void
    {
        if (app(TimetablePublication::class)->status($section->term) === TimetableStatus::Locked) {
            $this->fail("Thời khóa biểu {$section->term->name} đã khóa.", 'Không điều chỉnh buổi học sau khi khóa.');
        }
    }

    /** @return array<string, string> ngày nghỉ áp dụng cho học kỳ => tên */
    private function holidayNames(Term $term): array
    {
        $days = [];
        $start = CarbonImmutable::parse($term->start_date);
        $end = CarbonImmutable::parse($term->end_date);

        foreach (Holiday::query()->applicableTo($term->id)->overlapping($start->toDateString(), $end->toDateString())->get() as $holiday) {
            for ($d = CarbonImmutable::parse(max($holiday->starts_on, $start)); $d->lte(min(CarbonImmutable::parse($holiday->ends_on), $end)); $d = $d->addDay()) {
                $days[$d->toDateString()] ??= $holiday->name;
            }
        }

        return $days;
    }
}
