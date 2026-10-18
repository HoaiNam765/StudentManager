<?php

namespace Database\Seeders;

use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Enums\Shift;
use App\Modules\Timetable\Models\Period;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\ScheduleRuleService;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Database\Seeder;

/**
 * TTB: khung 15 tiết dùng chung (sáng 1–5, chiều 6–10, tối 11–15) và mẫu lịch tuần 1–15 cho các lớp học phần
 * đã có giảng viên chính, đi qua ScheduleRuleService để dữ liệu mẫu cũng đúng quy tắc (BR-TTB-03, 07).
 */
class TimetableSeeder extends Seeder
{
    private const PERIODS = [
        [1, '07:00', '07:50', Shift::Morning], [2, '07:50', '08:40', Shift::Morning], [3, '08:50', '09:40', Shift::Morning],
        [4, '09:40', '10:30', Shift::Morning], [5, '10:40', '11:30', Shift::Morning],
        [6, '12:30', '13:20', Shift::Afternoon], [7, '13:20', '14:10', Shift::Afternoon], [8, '14:20', '15:10', Shift::Afternoon],
        [9, '15:10', '16:00', Shift::Afternoon], [10, '16:10', '17:00', Shift::Afternoon],
        [11, '17:30', '18:20', Shift::Evening], [12, '18:20', '19:10', Shift::Evening], [13, '19:20', '20:10', Shift::Evening],
        [14, '20:10', '21:00', Shift::Evening], [15, '21:00', '21:50', Shift::Evening],
    ];

    public function run(): void
    {
        foreach (self::PERIODS as [$number, $start, $end, $shift]) {
            Period::firstOrCreate(['campus_id' => null, 'number' => $number], ['starts_at' => $start, 'ends_at' => $end, 'shift' => $shift]);
        }

        $this->seedRules();
    }

    private function seedRules(): void
    {
        $service = app(ScheduleRuleService::class);
        $rooms = Room::query()->where('status', 'available')->where('capacity', '>=', 60)->orderBy('code')->get()->values();
        $sections = CourseSection::query()->with('subject')->whereHas('instructors', fn ($q) => $q->where('role', 'main'))->orderBy('code')->get();

        if ($rooms->isEmpty()) {
            return;
        }

        foreach ($sections->values() as $i => $section) {
            if (ScheduleRule::query()->where('course_section_id', $section->id)->exists()) {
                continue;
            }

            $required = (int) $section->subject->theory_periods + (int) $section->subject->practice_periods;
            $length = max(1, min(5, intdiv($required, 15)));
            $start = $i % 2 === 0 ? 1 : 6;

            try {
                $service->create($section, [
                    'day_of_week' => $i % 5 + 1,
                    'period_from' => $start,
                    'period_to' => $start + $length - 1,
                    'room_id' => $rooms[$i % $rooms->count()]->id,
                    'week_from' => 1,
                    'week_to' => 15,
                ]);
            } catch (BusinessRuleException) {
                // Dữ liệu mẫu không hợp quy tắc thì bỏ qua lớp đó
            }
        }
    }
}
