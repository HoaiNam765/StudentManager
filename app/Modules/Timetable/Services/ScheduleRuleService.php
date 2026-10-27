<?php

namespace App\Modules\Timetable\Services;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Services\OfferingLecturers;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Services\RoomAvailability;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Modules\Timetable\Enums\WeekParity;
use App\Modules\Timetable\Events\ScheduleChanged;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Closure;

/**
 * Xếp lịch lớp học phần (FR-TTB-002).
 *
 * - Chỉ xếp khi lớp còn Dự kiến / Mở đăng ký / Đóng đăng ký; lớp đã học thì điều chỉnh từng buổi (FR-TTB-007, P2).
 * - Tiết phải có trong khung tiết của cơ sở, cùng một ca (không vắt qua giờ nghỉ trưa) và kết thúc không muộn hơn
 *   `studentmanager.timetable.latest_end` (BR-TTB-03).
 * - Các buổi nằm trong học kỳ (tuần ≤ số tuần, ngày trong khoảng bắt đầu – kết thúc; BR-TTB-07).
 * - Lớp học trực tiếp phải có phòng; phòng xếp được trong cả khoảng (BR-ROM-02) và đủ sức chứa sĩ số tối đa (BR-TTB-02).
 * - Hai dòng lịch của cùng lớp không chồng nhau; trùng phòng / giảng viên / lịch sinh viên, sai loại phòng giữa các lớp
 *   kiểm tra ở ScheduleConflictService (FR-TTB-003), chặn hay cảnh báo theo cấu hình.
 * - Tổng số tiết lệch số tiết quy định của học phần thì **cảnh báo**, không chặn (BR-TTB-04).
 */
class ScheduleRuleService extends BaseService
{
    public function __construct(
        private readonly PeriodService $periods,
        private readonly RoomAvailability $rooms,
        private readonly ScheduleConflictService $conflicts,
    ) {}

    /** @return array{rule: ScheduleRule, warnings: list<string>} */
    public function create(CourseSection $section, array $data): array
    {
        $attributes = $this->check($section, $data);
        $candidate = new ScheduleRule(['course_section_id' => $section->id, ...$attributes]);
        $conflicts = $this->assertNoConflict($section, $candidate);
        $rule = $this->guardPublished($section, $data, null, $candidate, function () use ($section, $attributes): ScheduleRule {
            $rule = ScheduleRule::create(['course_section_id' => $section->id, ...$attributes]);
            $this->regenerateSessions($section);

            return $rule;
        });

        return ['rule' => $rule->load('room:id,code,capacity'), 'warnings' => [...$conflicts, ...$this->summary($section)['warnings']]];
    }

    /** @return array{rule: ScheduleRule, warnings: list<string>} */
    public function update(ScheduleRule $rule, array $data): array
    {
        $section = $rule->section;
        $attributes = $this->check($section, [
            'day_of_week' => $data['day_of_week'] ?? $rule->day_of_week,
            'period_from' => $data['period_from'] ?? $rule->period_from,
            'period_to' => $data['period_to'] ?? $rule->period_to,
            'room_id' => array_key_exists('room_id', $data) ? $data['room_id'] : $rule->room_id,
            'week_from' => $data['week_from'] ?? $rule->week_from,
            'week_to' => $data['week_to'] ?? $rule->week_to,
            'week_parity' => $data['week_parity'] ?? $rule->week_parity->value,
            'note' => array_key_exists('note', $data) ? $data['note'] : $rule->note,
        ], $rule->id);

        $candidate = (clone $rule)->fill($attributes);
        $conflicts = $this->assertNoConflict($section, $candidate);
        $this->guardPublished($section, $data, $rule, $candidate, function () use ($rule, $attributes, $section): void {
            $rule->update($attributes);
            $this->regenerateSessions($section);
        });

        // BR-TTB-08: sinh viên đã đăng ký bị trùng lịch với lớp khác của họ sau khi đổi
        $found = $this->conflicts->affectedStudents($section);
        $codes = Student::query()->whereIn('id', array_column($found, 'student_id'))->pluck('student_code', 'id');
        $affected = array_map(
            fn (array $a) => 'Sinh viên '.($codes[$a['student_id']] ?? "#{$a['student_id']}")." bị trùng lịch: {$a['clash']}.",
            $found,
        );

        return ['rule' => $rule->refresh()->load('room:id,code,capacity'), 'warnings' => [...$conflicts, ...$affected, ...$this->summary($section)['warnings']]];
    }

    /** @param array{reason?: ?string, urgent?: bool} $data */
    public function delete(ScheduleRule $rule, array $data = []): void
    {
        $this->assertEditable($rule->section);
        $this->guardPublished($rule->section, $data, $rule, null, function () use ($rule): void {
            $rule->delete();
            $this->regenerateSessions($rule->section);
        });
    }

    /**
     * Sau khi công bố thời khóa biểu: bắt buộc lý do, báo trước đủ giờ hoặc khẩn cấp (BR-TTB-05), ghi lý do vào nhật ký
     * và phát EV-TTB-02 (`ScheduleChanged`). Còn nháp thì ghi thẳng.
     */
    private function guardPublished(CourseSection $section, array $data, ?ScheduleRule $before, ?ScheduleRule $after, Closure $write): mixed
    {
        $urgent = (bool) ($data['urgent'] ?? false);
        $dates = array_filter([$this->nextDate($section, $before), $this->nextDate($section, $after)]);
        $reason = app(TimetablePublication::class)->assertChangeAllowed($section, $data['reason'] ?? null, $urgent, $dates);

        if ($reason === null) {
            return $write();
        }

        $result = app(AuditLogger::class)->withReason(($urgent ? '[Khẩn cấp] ' : '').$reason, $write);
        ScheduleChanged::dispatch($section, $reason, $urgent);

        return $result;
    }

    /** Ngày học sắp tới (từ hôm nay) của một dòng lịch. */
    private function nextDate(CourseSection $section, ?ScheduleRule $rule): ?CarbonImmutable
    {
        if ($rule === null) {
            return null;
        }

        $section->loadMissing('term');
        $today = CarbonImmutable::now(config('studentmanager.display_timezone'))->startOfDay();

        foreach ($rule->weeks() as $week) {
            $date = $this->dateOf($section->term, $week, $rule->day_of_week);

            if ($date->gte($today)) {
                return $date;
            }
        }

        return null;
    }

    /**
     * Tổng số tiết đã xếp so với số tiết quy định của học phần (BR-TTB-04).
     *
     * @return array{required: int, scheduled: int, warnings: list<string>}
     */
    public function summary(CourseSection $section): array
    {
        $section->loadMissing('subject');
        $required = (int) $section->subject->theory_periods + (int) $section->subject->practice_periods;
        $scheduled = ScheduleRule::query()
            ->where('course_section_id', $section->id)
            ->get()
            ->sum(fn (ScheduleRule $r) => $r->periodCount() * count($r->weeks()));

        $warnings = [];

        if ($required > 0 && $scheduled !== $required) {
            $diff = abs($scheduled - $required);
            $warnings[] = "Lớp {$section->code} đã xếp {$scheduled} tiết, học phần quy định {$required} tiết ("
                .($scheduled < $required ? "thiếu {$diff}" : "thừa {$diff}").' tiết, BR-TTB-04).';
        }

        return ['required' => $required, 'scheduled' => $scheduled, 'warnings' => $warnings];
    }

    /** Ngày học của một dòng lịch: tuần 1 là tuần (Thứ Hai – Chủ nhật) chứa ngày bắt đầu học kỳ. */
    public function dateOf(Term $term, int $week, int $dayOfWeek): CarbonImmutable
    {
        return CarbonImmutable::parse($term->start_date)->startOfWeek()->addWeeks($week - 1)->addDays($dayOfWeek - 1);
    }

    /** @return array<string, mixed> thuộc tính hợp lệ để lưu */
    private function check(CourseSection $section, array $data, ?int $ignoreId = null): array
    {
        $this->assertEditable($section);
        $section->loadMissing('term');
        $term = $section->term;

        $day = (int) $data['day_of_week'];
        $from = (int) $data['period_from'];
        $to = (int) $data['period_to'];
        $weekFrom = (int) $data['week_from'];
        $weekTo = (int) $data['week_to'];
        $parity = WeekParity::from($data['week_parity'] ?? WeekParity::All->value);
        $roomId = array_key_exists('room_id', $data) ? $data['room_id'] : $section->room_id;
        $room = $roomId === null ? null : Room::query()->with('building')->findOrFail($roomId);

        if ($from > $to) {
            $this->fail("Tiết bắt đầu ({$from}) sau tiết kết thúc ({$to}).", 'Nhập tiết bắt đầu nhỏ hơn hoặc bằng tiết kết thúc.');
        }

        // BR-TTB-03: tiết có trong khung, cùng ca, không kết thúc muộn hơn giờ quy định
        $periods = $this->periods->periodsFor($room?->building?->campus_id);

        for ($n = $from; $n <= $to; $n++) {
            if (! $periods->has($n)) {
                $this->fail("Khung tiết học không có tiết {$n}.", 'Khai báo khung tiết (FR-TTB-001) hoặc chọn tiết khác.');
            }
        }

        $first = $periods->get($from);
        $last = $periods->get($to);

        if ($first->shift !== $last->shift) {
            $this->fail(
                "Buổi học tiết {$from}–{$to} vắt qua giờ nghỉ giữa ca {$first->shift->label()} và ca {$last->shift->label()} (BR-TTB-03).",
                'Tách thành hai buổi, mỗi buổi nằm gọn trong một ca.'
            );
        }

        $latest = substr((string) config('studentmanager.timetable.latest_end', '21:00'), 0, 5);

        if ($last->endTime() > $latest) {
            $this->fail(
                "Tiết {$to} kết thúc lúc {$last->endTime()}, muộn hơn giờ quy định {$latest} (BR-TTB-03).",
                'Chọn tiết sớm hơn hoặc đổi tham số TIMETABLE_LATEST_END.'
            );
        }

        // Trong học kỳ (BR-TTB-07)
        if ($weekFrom < 1 || $weekFrom > $weekTo || $weekTo > $term->weeks) {
            $this->fail(
                "Tuần học {$weekFrom}–{$weekTo} không hợp lệ: học kỳ {$term->name} có {$term->weeks} tuần.",
                "Chọn tuần trong khoảng 1–{$term->weeks}, tuần bắt đầu không sau tuần kết thúc."
            );
        }

        $weeks = array_values(array_filter(range($weekFrom, $weekTo), fn (int $w) => $parity->includes($w)));

        if ($weeks === []) {
            $this->fail("Tuần {$weekFrom}–{$weekTo} không có {$parity->label()} nào.", 'Mở rộng khoảng tuần hoặc chọn mẫu tuần khác.');
        }

        $firstDate = $this->dateOf($term, $weeks[0], $day);
        $lastDate = $this->dateOf($term, end($weeks), $day);
        $termStart = CarbonImmutable::parse($term->start_date)->startOfDay();
        $termEnd = CarbonImmutable::parse($term->end_date)->startOfDay();

        if ($firstDate->lt($termStart) || $lastDate->gt($termEnd)) {
            $outside = $firstDate->lt($termStart) ? $firstDate : $lastDate;
            $this->fail(
                'Buổi ngày '.$outside->format('d/m/Y').' nằm ngoài học kỳ ('.$termStart->format('d/m/Y').' – '.$termEnd->format('d/m/Y').', BR-TTB-07).',
                'Đổi thứ trong tuần hoặc khoảng tuần để mọi buổi nằm trong học kỳ.'
            );
        }

        // Phòng (BR-TTB-02, BR-ROM-02)
        if ($room === null && $section->delivery_mode !== 'online') {
            $this->fail("Lớp {$section->code} học trực tiếp nên phải chọn phòng.", 'Chọn phòng hoặc đổi hình thức lớp sang trực tuyến.');
        }

        if ($room !== null) {
            if ($room->capacity < $section->max_size) {
                $this->fail(
                    "Phòng {$room->code} chứa {$room->capacity} chỗ, ít hơn sĩ số tối đa {$section->max_size} của lớp (BR-TTB-02).",
                    'Chọn phòng lớn hơn hoặc giảm sĩ số tối đa của lớp.'
                );
            }

            $this->rooms->assertSchedulable($room, $firstDate, $lastDate);
        }

        // Không chồng với dòng lịch khác của cùng lớp
        $siblings = ScheduleRule::query()
            ->where('course_section_id', $section->id)
            ->where('day_of_week', $day)
            ->where('period_from', '<=', $to)
            ->where('period_to', '>=', $from)
            ->when($ignoreId !== null, fn ($q) => $q->whereKeyNot($ignoreId))
            ->get();

        foreach ($siblings as $sibling) {
            if (array_intersect($weeks, $sibling->weeks()) !== []) {
                $this->fail("Trùng với lịch đã có của lớp: {$sibling->describe()}.", 'Sửa dòng lịch đã có thay vì thêm dòng mới, hoặc chọn thứ / tiết / tuần khác.');
            }
        }

        return [
            'day_of_week' => $day,
            'period_from' => $from,
            'period_to' => $to,
            'room_id' => $room?->id,
            'week_from' => $weekFrom,
            'week_to' => $weekTo,
            'week_parity' => $parity,
            'note' => $data['note'] ?? null,
        ];
    }

    /**
     * Chặn xung đột loại "block", trả các xung đột loại "warn" để hiển thị (FR-TTB-003).
     *
     * @return list<string>
     */
    private function assertNoConflict(CourseSection $section, ScheduleRule $candidate): array
    {
        if ($section->registration_period_id !== null) {
            foreach ($section->instructors()->with('lecturer')->get() as $instructor) {
                $reason = app(OfferingLecturers::class)
                    ->conflict($instructor->lecturer, $section->term, $candidate->getAttributes(), $section->id);
                if ($reason !== null) {
                    $this->fail($reason, 'Chọn giảng viên hoặc lịch học khác.');
                }
            }
        }
        $found = $this->conflicts->check($section, $candidate);

        if ($found['block'] !== []) {
            $this->fail('Lịch bị xung đột: '.implode(' ', $found['block']), 'Đổi phòng, thứ, tiết hoặc tuần học; xem lớp gây xung đột nêu ở trên (BR-TTB-01, 02).');
        }

        return $found['warn'];
    }

    /** Lịch đổi thì sinh lại buổi học cụ thể (FR-TTB-005); lấy qua container để tránh phụ thuộc vòng với SessionService. */
    private function regenerateSessions(CourseSection $section): void
    {
        app(SessionService::class)->generate($section);
    }

    private function assertEditable(CourseSection $section): void
    {
        $allowed = [SectionStatus::Planned, SectionStatus::RegistrationOpen, SectionStatus::RegistrationClosed];

        if (! in_array($section->status, $allowed, true)) {
            $this->fail(
                "Lớp {$section->code} đang ở trạng thái {$section->status->label()} nên không sửa mẫu lịch được.",
                'Lớp đã bắt đầu học thì điều chỉnh từng buổi học (FR-TTB-007); lớp đã hủy không xếp lịch.'
            );
        }
    }
}
