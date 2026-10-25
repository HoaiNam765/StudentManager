<?php

namespace App\Modules\Attendance\Services;

use App\Modules\Attendance\Enums\AttendanceStatus;
use App\Modules\Attendance\Events\AttendanceThresholdReached;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Models\AttendanceSummary;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\System\Services\PolicyResolver;
use App\Modules\Timetable\Enums\SessionStatus;
use App\Modules\Timetable\Models\ClassSession;
use Illuminate\Support\Collection;

/**
 * Tính tỷ lệ vắng, cờ cảnh báo / cấm thi và điểm chuyên cần (#143; FR-ATT-008, 009; BR-ATT-02, 03, 04, 06).
 *
 * - Mẫu số = số buổi dự kiến có học của lớp (dự kiến, đã dạy, dạy bù); buổi nghỉ / hủy do nhà trường không tính.
 * - Vắng quy đổi = vắng không phép + vắng có phép × hệ số + đi muộn quá X phút + ⌊số lần đi muộn còn lại ÷ N⌋.
 * - Cảnh báo khi tỷ lệ ≥ ngưỡng cảnh báo; cấm thi khi tỷ lệ > ngưỡng cấm thi (trừ khi được miễn).
 * Ngưỡng lấy từ bộ quy chế của khóa (PolicyResolver), hệ số / số phút / điểm trừ từ `studentmanager.attendance`.
 */
class AttendanceCalculator
{
    public function __construct(private readonly PolicyResolver $policies) {}

    /**
     * Phép tính thuần (kiểm thử đơn vị được).
     *
     * @param  array{absent?: int, excused?: int, late_short?: int, late_long?: int}  $counts
     * @param  array{excused_weight: float, lates_per_absence: int, score_deduction_per_absence: float}  $config
     * @return array{absences: float, absence_rate: float, warning: bool, banned: bool, attendance_score: float}
     */
    public static function compute(int $totalSessions, array $counts, array $config, float $warningPercent, float $banPercent, bool $exempted = false): array
    {
        $perAbsence = max(1, (int) $config['lates_per_absence']);
        $absences = ($counts['absent'] ?? 0)
            + ($counts['excused'] ?? 0) * (float) $config['excused_weight']
            + ($counts['late_long'] ?? 0)
            + intdiv($counts['late_short'] ?? 0, $perAbsence);
        $rate = $totalSessions > 0 ? round($absences * 100 / $totalSessions, 2) : 0.0;
        $banned = ! $exempted && $rate > $banPercent;

        return [
            'absences' => round($absences, 2),
            'absence_rate' => $rate,
            'warning' => $rate >= $warningPercent && $warningPercent > 0,
            'banned' => $banned,
            'attendance_score' => $banned ? 0.0 : max(0.0, round(10 - $absences * (float) $config['score_deduction_per_absence'], 1)),
        ];
    }

    /** Tính lại cho các đăng ký của một lớp (sau khi điểm danh, đổi lịch, khóa / mở khóa). */
    public function recalculateSection(CourseSection $section): void
    {
        $this->recalculate(Enrollment::query()->active()->where('course_section_id', $section->id)->get());
    }

    /** @param  Collection<int, Enrollment>  $enrollments */
    public function recalculate(Collection $enrollments): void
    {
        $config = config('studentmanager.attendance');
        $totals = [];

        foreach ($enrollments as $enrollment) {
            $sectionId = $enrollment->course_section_id;
            $totals[$sectionId] ??= ClassSession::query()->where('course_section_id', $sectionId)
                ->whereIn('status', self::countedStatuses())->count();
            $this->recalculateOne($enrollment, $totals[$sectionId], $config);
        }
    }

    private function recalculateOne(Enrollment $enrollment, int $total, array $config): AttendanceSummary
    {
        $records = AttendanceRecord::query()->where('enrollment_id', $enrollment->id)
            ->whereHas('session', fn ($q) => $q->whereIn('status', self::countedStatuses()))
            ->get(['status', 'minutes_late']);
        $lateLimit = (int) $config['late_minutes_as_absent'];
        $late = $records->where('status', AttendanceStatus::Late);
        $counts = [
            'present' => $records->where('status', AttendanceStatus::Present)->count(),
            'absent' => $records->where('status', AttendanceStatus::Absent)->count(),
            'excused' => $records->where('status', AttendanceStatus::Excused)->count(),
            'late_long' => $late->filter(fn ($r) => (int) $r->minutes_late > $lateLimit)->count(),
            'late_short' => $late->filter(fn ($r) => (int) $r->minutes_late <= $lateLimit)->count(),
        ];

        $summary = AttendanceSummary::query()->firstOrNew(['enrollment_id' => $enrollment->id], [
            'student_id' => $enrollment->student_id,
            'course_section_id' => $enrollment->course_section_id,
        ]);
        [$warningPercent, $banPercent] = $this->thresholds($enrollment);
        $result = self::compute($total, $counts, $config, $warningPercent, $banPercent, (bool) $summary->exempted);
        $newlyWarned = $result['warning'] && $summary->warned_at === null;
        $newlyBanned = $result['banned'] && $summary->banned_at === null;

        $summary->fill($result + [
            'total_sessions' => $total,
            'recorded_sessions' => $records->count(),
            'present_count' => $counts['present'],
            'absent_count' => $counts['absent'],
            'excused_count' => $counts['excused'],
            'late_count' => $late->count(),
            'warned_at' => $result['warning'] ? ($summary->warned_at ?? now()) : null,
            'banned_at' => $result['banned'] ? ($summary->banned_at ?? now()) : null,
            'calculated_at' => now(),
        ]);

        if ($summary->isDirty() || ! $summary->exists) {
            $summary->save();
        }

        if ($newlyBanned) {
            AttendanceThresholdReached::dispatch($summary, AttendanceThresholdReached::BANNED);
        } elseif ($newlyWarned) {
            AttendanceThresholdReached::dispatch($summary, AttendanceThresholdReached::WARNING);
        }

        return $summary;
    }

    /** @return list<string> trạng thái buổi tính vào số buổi và tỷ lệ vắng (BR-ATT-06) */
    public static function countedStatuses(): array
    {
        return array_values(array_map(fn (SessionStatus $s) => $s->value, array_filter(SessionStatus::cases(), fn (SessionStatus $s) => $s->countsForAttendance())));
    }

    /** @return array{0: float, 1: float} ngưỡng cảnh báo, cấm thi (%) theo bộ quy chế khóa của sinh viên */
    private function thresholds(Enrollment $enrollment): array
    {
        $cohort = (int) ($enrollment->student()->value('cohort') ?? now()->year);

        return [
            (float) $this->policies->value('attendance.warning_percent', $cohort),
            (float) $this->policies->value('attendance.ban_percent', $cohort),
        ];
    }
}
