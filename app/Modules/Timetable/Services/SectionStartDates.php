<?php

namespace App\Modules\Timetable\Services;

use App\Modules\Timetable\Enums\SessionStatus;
use App\Modules\Timetable\Models\ClassSession;
use App\Modules\Timetable\Models\ScheduleRule;
use Carbon\CarbonImmutable;

/**
 * Ngày bắt đầu học của lớp học phần (hiển thị khi đăng ký, ràng buộc "ngày học sau ngày đóng đăng ký").
 *
 * - Đã sinh buổi học: ngày của buổi sớm nhất có học thật (Dự kiến / Đã dạy / Dạy bù; bỏ buổi Nghỉ, Hủy).
 * - Chưa sinh buổi nhưng có mẫu lịch: ngày của tuần học đầu tiên theo mẫu lịch (`ScheduleRuleService::dateOf`).
 * - Chưa xếp lịch: null.
 */
class SectionStartDates
{
    public function __construct(private readonly ScheduleRuleService $rules) {}

    public function of(int $sectionId): ?CarbonImmutable
    {
        return $this->for([$sectionId])[$sectionId] ?? null;
    }

    /**
     * @param  iterable<int>  $sectionIds
     * @return array<int, ?CarbonImmutable> mã lớp => ngày bắt đầu học
     */
    public function for(iterable $sectionIds): array
    {
        $ids = collect($sectionIds)->map(fn ($id) => (int) $id)->unique()->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $dates = ClassSession::query()
            ->whereIn('course_section_id', $ids)
            ->whereIn('status', [SessionStatus::Planned->value, SessionStatus::Taught->value, SessionStatus::Makeup->value])
            ->groupBy('course_section_id')
            ->selectRaw('course_section_id, MIN(date) as first_date')
            ->pluck('first_date', 'course_section_id')
            ->map(fn ($date) => CarbonImmutable::parse($date)->startOfDay())
            ->all();

        $missing = $ids->reject(fn (int $id) => isset($dates[$id]));

        if ($missing->isNotEmpty()) {
            ScheduleRule::query()
                ->with('section.term')
                ->whereIn('course_section_id', $missing)
                ->get()
                ->each(function (ScheduleRule $rule) use (&$dates): void {
                    $weeks = $rule->weeks();

                    if ($weeks === [] || $rule->section?->term === null) {
                        return;
                    }

                    $date = $this->rules->dateOf($rule->section->term, $weeks[0], $rule->day_of_week);
                    $current = $dates[$rule->course_section_id] ?? null;
                    $dates[$rule->course_section_id] = $current === null || $date->lt($current) ? $date : $current;
                });
        }

        return $ids->mapWithKeys(fn (int $id) => [$id => $dates[$id] ?? null])->all();
    }
}
