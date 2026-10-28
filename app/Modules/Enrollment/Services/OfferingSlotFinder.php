<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomType;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\PeriodService;
use Illuminate\Support\Collection;

/**
 * Tìm lịch còn trống cho một lớp của đợt đăng ký theo khung (nút "Tự xếp"): số tiết mỗi buổi và số tuần theo tín chỉ,
 * thứ 2–7 ban ngày trước (sáng tiết 1, chiều tiết 6), hết chỗ mới tới tối; không trùng buổi với lớp khác của cùng khoa –
 * khóa (sinh viên học chung), phòng đúng loại – đủ chỗ – chưa ai dùng, giảng viên của khoa trống lịch và ít tiết nhất.
 * Chỉ gợi ý: ghi lớp vẫn qua CourseSectionService / ScheduleRuleService để kiểm tra lại đầy đủ.
 */
class OfferingSlotFinder
{
    public function __construct(
        private readonly OfferingLecturers $lecturers,
        private readonly CourseSectionService $sections,
        private readonly PeriodService $periods,
    ) {}

    /**
     * Số tiết mỗi buổi và tuần kết thúc theo tín chỉ: lý thuyết 3 tiết / buổi (thiếu tuần thì 4, 5), thực hành tối thiểu
     * `practice_min_periods`; số tiết lấy theo loại lớp như hộp thoại Sửa lớp.
     *
     * @return array{length: int, week_from: int, week_to: int}
     */
    public function plan(Subject $subject, string $type, int $termWeeks): array
    {
        $theory = (int) $subject->theory_periods;
        $practice = (int) $subject->practice_periods;
        $total = match ($type) {
            'theory' => $theory,
            'practice' => $practice,
            default => $theory + $practice,
        } ?: $theory + $practice;
        $min = $type === 'practice' ? (int) config('studentmanager.timetable.practice_min_periods', 5) : 3;
        $length = $min;

        while ($total > 0 && $length < 5 && (int) ceil($total / $length) > $termWeeks) {
            $length++;
        }

        $sessions = $total > 0 ? (int) ceil($total / $length) : min(15, $termWeeks);

        return ['length' => $length, 'week_from' => 1, 'week_to' => min($sessions, $termWeeks)];
    }

    /**
     * Buổi trống đầu tiên. `$need`: length, week_from, week_to, section_type, max_size, lecturer_id (giữ giảng viên đã chọn).
     *
     * @return array{day_of_week: int, period_from: int, period_to: int, week_from: int, week_to: int, week_parity: string, room_id: int, lecturer_id: int}|null
     */
    public function find(Term $term, TrainingFramework $framework, array $need, ?int $ignoreSection = null): ?array
    {
        $length = (int) $need['length'];
        $rooms = $this->rooms($need['section_type'] ?? 'theory', (int) $need['max_size']);
        $lecturers = $this->candidates($framework, $term, $need['lecturer_id'] ?? null);

        if ($rooms->isEmpty() || $lecturers->isEmpty()) {
            return null;
        }

        $group = $this->groupBusy($framework, $ignoreSection);

        foreach ($this->slots($length, $group) as [$day, $from]) {
            $to = $from + $length - 1;
            $busyRooms = $this->busyRooms($term, $day, $from, $to, $ignoreSection);
            $room = $rooms->first(fn (Room $r) => ! in_array($r->id, $busyRooms, true));

            if ($room === null) {
                continue;
            }

            $schedule = [
                'day_of_week' => $day, 'period_from' => $from, 'period_to' => $to, 'room_id' => $room->id,
                'week_from' => (int) $need['week_from'], 'week_to' => (int) $need['week_to'], 'week_parity' => 'all',
            ];
            $lecturer = $lecturers->first(fn (Lecturer $l) => $this->lecturers->conflict($l, $term, $schedule, $ignoreSection) === null);

            if ($lecturer !== null) {
                return $schedule + ['lecturer_id' => $lecturer->id];
            }
        }

        return null;
    }

    /** Phòng đang dùng được, đúng loại (thực hành: phòng máy / thí nghiệm), đủ sĩ số tối đa; phòng nhỏ trước. */
    private function rooms(string $type, int $maxSize): Collection
    {
        $types = $type === 'practice'
            ? config('studentmanager.timetable.practice_room_types', [RoomType::COMPUTER_LAB, RoomType::LABORATORY])
            : [RoomType::LECTURE, RoomType::HALL];

        return Room::query()->where('status', 'available')->where('capacity', '>=', $maxSize)
            ->whereHas('type', fn ($q) => $q->whereIn('code', $types))
            ->orderBy('capacity')->orderBy('code')->get();
    }

    /** Giảng viên đã chọn, hoặc giảng viên đang công tác của khoa xếp theo số tiết đã nhận trong học kỳ (ít trước). */
    private function candidates(TrainingFramework $framework, Term $term, ?int $lecturerId): Collection
    {
        if ($lecturerId) {
            return Lecturer::query()->whereKey($lecturerId)->get();
        }

        return $this->lecturers->inFaculty($framework->faculty_id)->orderBy('code')->get()
            ->sortBy(fn (Lecturer $l) => $this->sections->loadInTerm($l, $term->id))->values();
    }

    /** Buổi đã dùng của các lớp cùng khung (cùng khoa – khóa): [thứ, tiết từ, tiết đến]. */
    private function groupBusy(TrainingFramework $framework, ?int $ignoreSection): array
    {
        return ScheduleRule::query()
            ->whereHas('section', fn ($q) => $q->active()->whereHas('trainingFrameworkItem', fn ($q) => $q->where('training_framework_id', $framework->id)))
            ->when($ignoreSection, fn ($q) => $q->where('course_section_id', '!=', $ignoreSection))
            ->get(['day_of_week', 'period_from', 'period_to'])
            ->map(fn (ScheduleRule $r) => [(int) $r->day_of_week, (int) $r->period_from, (int) $r->period_to])->all();
    }

    /**
     * Thứ – tiết bắt đầu hợp lệ theo khung tiết (cùng ca, không quá giờ kết thúc muộn nhất), không trùng buổi của nhóm.
     * Ban ngày trước tối; trong cùng nhóm ưu tiên ngày ít buổi để rải đều trong tuần.
     *
     * @return list<array{int, int}>
     */
    private function slots(int $length, array $group): array
    {
        $frame = $this->periods->periodsFor(null);
        $latest = substr((string) config('studentmanager.timetable.latest_end', '21:00'), 0, 5);
        $starts = $frame->groupBy(fn ($p) => $p->shift->value)->map(fn ($shift) => $shift->min('number'))->sort()->values();
        $perDay = array_count_values(array_map(fn ($b) => $b[0], $group));
        $slots = [];

        foreach ($starts as $rank => $from) {
            $first = $frame->get($from);
            $last = $frame->get($from + $length - 1);

            if ($last === null || $last->shift !== $first->shift || $last->endTime() > $latest) {
                continue;
            }

            foreach (range(1, 6) as $day) {
                $clash = collect($group)->contains(fn ($b) => $b[0] === $day && $b[1] <= $from + $length - 1 && $from <= $b[2]);

                if (! $clash) {
                    $slots[] = [$day, $from, min($rank, 2), $perDay[$day] ?? 0];
                }
            }
        }

        // Ca sáng / chiều trước ca tối, rồi ngày ít buổi của nhóm, rồi thứ, rồi ca
        usort($slots, fn ($a, $b) => [$a[2] >= 2, $a[3], $a[0], $a[1]] <=> [$b[2] >= 2, $b[3], $b[0], $b[1]]);

        return array_map(fn ($s) => [$s[0], $s[1]], $slots);
    }

    /** Phòng đã có lớp (đang hoạt động, cùng học kỳ) học trùng thứ và chồng tiết. */
    private function busyRooms(Term $term, int $day, int $from, int $to, ?int $ignoreSection): array
    {
        return ScheduleRule::query()
            ->whereNotNull('room_id')->where('day_of_week', $day)->where('period_from', '<=', $to)->where('period_to', '>=', $from)
            ->whereHas('section', fn ($q) => $q->active()->where('term_id', $term->id))
            ->when($ignoreSection, fn ($q) => $q->where('course_section_id', '!=', $ignoreSection))
            ->pluck('room_id')->map(fn ($id) => (int) $id)->all();
    }
}
