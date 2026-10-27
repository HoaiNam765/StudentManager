<?php

namespace Database\Seeders;

use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomType;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Enums\Shift;
use App\Modules\Timetable\Models\Period;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\ScheduleRuleService;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Database\Seeder;

/**
 * TTB: khung 15 tiết dùng chung (sáng 1–5, chiều 6–10, tối 11–15) và mẫu lịch tuần 1–15 cho các lớp học phần
 * đã có giảng viên chính, đi qua ScheduleRuleService để dữ liệu mẫu cũng đúng quy tắc (BR-TTB-03, 07); trùng phòng /
 * giảng viên thì thử phòng khác, buổi khác trong tuần; lớp cùng ngành – học kỳ dự kiến tránh trùng buổi (`place`, dùng lại
 * cho lớp đang học trong AttendanceDemoSeeder).
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
        $sections = CourseSection::query()->with('subject')->whereHas('instructors', fn ($q) => $q->where('role', 'main'))->orderBy('code')->get();
        // Lớp cùng học kỳ dự kiến của một ngành không xếp trùng buổi (sinh viên ngành đó học cùng lúc)
        $majorsBySubject = CurriculumSubject::query()->join('curricula', 'curricula.id', '=', 'curriculum_subjects.curriculum_id')
            ->get(['curriculum_subjects.subject_id', 'curriculum_subjects.planned_term', 'curricula.major_id'])
            ->groupBy('subject_id')->map(fn ($rows) => $rows->map(fn ($r) => "major:{$r->major_id}:{$r->planned_term}")->unique()->values()->all());
        $used = [];

        foreach ($sections->values() as $i => $section) {
            if (! ScheduleRule::query()->where('course_section_id', $section->id)->exists()) {
                self::place($section, $i, $majorsBySubject->get($section->subject_id, []), $used);
            }
        }
    }

    /**
     * Xếp một lớp vào một buổi trong tuần (thứ 2–7, sáng từ tiết 1 hoặc chiều từ tiết 6; hết chỗ mới tới tối từ tiết 11;
     * tuần 1–15) và một phòng lý thuyết /
     * hội trường đủ chỗ, qua ScheduleRuleService (chặn trùng phòng, giảng viên). `$groups` là các nhóm không được học trùng
     * buổi (ngành – học kỳ, hoặc từng sinh viên); `$used` ghi buổi đã dùng của mỗi nhóm. Trả về buổi đã xếp, null nếu
     * không còn chỗ (lớp được bỏ qua).
     *
     * @param  list<string>  $groups
     * @param  array<string, array<string, true>>  $used
     */
    public static function place(CourseSection $section, int $offset, array $groups, array &$used): ?string
    {
        $section->loadMissing('subject');
        $required = (int) $section->subject->theory_periods + (int) $section->subject->practice_periods;
        $length = max(1, min(5, intdiv($required, 15)));
        $rooms = Room::query()->where('status', 'available')->where('capacity', '>=', $section->max_size)
            ->whereHas('type', fn ($q) => $q->whereIn('code', [RoomType::LECTURE, RoomType::HALL]))
            ->orderBy('capacity')->orderBy('code')->get()->values();
        $service = app(ScheduleRuleService::class);

        // Lượt đầu tránh buổi trùng của nhóm; không còn buổi trống thì chấp nhận trùng (chỉ là cảnh báo)
        foreach ([true, false] as $strict) {
            for ($k = 0; $k < 18; $k++) {
                $slot = $k < 12 ? ($offset + $k) % 12 : $k;
                $day = $slot % 6 + 1;
                $start = [1, 6, 11][intdiv($slot, 6)];
                $key = "{$day}-{$start}";

                if ($strict && collect($groups)->contains(fn ($g) => isset($used[$g][$key]))) {
                    continue;
                }

                for ($r = 0; $r < $rooms->count(); $r++) {
                    try {
                        $service->create($section, [
                            'day_of_week' => $day,
                            'period_from' => $start,
                            'period_to' => $start + $length - 1,
                            'room_id' => $rooms[($offset + $r) % $rooms->count()]->id,
                            'week_from' => 1,
                            'week_to' => 15,
                        ]);
                        foreach ($groups as $group) {
                            $used[$group][$key] = true;
                        }

                        return $key;
                    } catch (BusinessRuleException) {
                        // Thử phòng / buổi tiếp theo
                    }
                }
            }
        }

        return null;
    }
}
