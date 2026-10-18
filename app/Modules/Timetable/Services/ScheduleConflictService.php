<?php

namespace App\Modules\Timetable\Services;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Timetable\Contracts\SectionStudents;
use App\Modules\Timetable\Models\ScheduleRule;
use Illuminate\Database\Eloquent\Builder;

/**
 * Phát hiện xung đột thời khóa biểu (FR-TTB-003; BR-TTB-01, 02, 08; BR-ROM-03).
 *
 * Mỗi loại xung đột chặn hoặc chỉ cảnh báo theo `studentmanager.timetable.conflicts`:
 * - `room`: hai lớp cùng phòng cùng lúc; `lecturer`: một giảng viên dạy hai lớp cùng lúc (BR-TTB-01).
 * - `student`: hai học phần bắt buộc cùng học kỳ dự kiến của một CTĐT đang áp dụng xếp trùng giờ — sinh viên cùng khóa
 *   không học được cả hai (FR-TTB-003).
 * - `room_type`: lớp thực hành không xếp vào phòng máy / thí nghiệm (`practice_room_types`, BR-TTB-02, BR-ROM-03).
 * Hai dòng lịch trùng khi cùng học kỳ, cùng thứ, giao tiết và có ít nhất một tuần chung. ENR gọi `sectionsClash()`.
 */
class ScheduleConflictService
{
    public function __construct(private readonly SectionStudents $students) {}

    /** Hai dòng lịch có buổi nào trùng giờ không (cùng thứ, giao tiết, có tuần chung). */
    public function rulesOverlap(ScheduleRule $a, ScheduleRule $b): bool
    {
        return $a->day_of_week === $b->day_of_week
            && $a->period_from <= $b->period_to
            && $b->period_from <= $a->period_to
            && array_intersect($a->weeks(), $b->weeks()) !== [];
    }

    /** Mô tả buổi trùng đầu tiên giữa hai lớp học phần, null nếu không trùng (ENR dùng khi đăng ký, BR-TTB-01). */
    public function sectionsClash(CourseSection $a, CourseSection $b): ?string
    {
        if ($a->id === $b->id || $a->term_id !== $b->term_id) {
            return null;
        }

        $rulesB = ScheduleRule::query()->where('course_section_id', $b->id)->get();

        foreach (ScheduleRule::query()->where('course_section_id', $a->id)->get() as $ruleA) {
            foreach ($rulesB as $ruleB) {
                if ($this->rulesOverlap($ruleA, $ruleB)) {
                    return "{$a->code} ({$ruleA->describe()}) trùng giờ với {$b->code} ({$ruleB->describe()})";
                }
            }
        }

        return null;
    }

    /**
     * Xung đột của một dòng lịch (chưa lưu hoặc đã lưu) với các lớp khác cùng học kỳ.
     *
     * @return array{block: list<string>, warn: list<string>}
     */
    public function check(CourseSection $section, ScheduleRule $candidate): array
    {
        $found = ['room' => [], 'lecturer' => [], 'student' => [], 'room_type' => []];
        $section->loadMissing('subject');

        // Sai loại phòng (BR-ROM-03)
        $room = $candidate->room_id === null ? null : Room::query()->with('type')->find($candidate->room_id);
        $practiceTypes = (array) config('studentmanager.timetable.practice_room_types', []);

        if ($room !== null && $section->section_type === 'practice' && ! in_array($room->type?->code, $practiceTypes, true)) {
            $found['room_type'][] = "Lớp {$section->code} là lớp thực hành nhưng phòng {$room->code} là {$room->type?->name}"
                .' (cần phòng loại '.implode(', ', $practiceTypes).').';
        }

        // Các dòng lịch khác cùng học kỳ, cùng thứ, giao tiết; lọc tuần chung trong PHP
        $others = ScheduleRule::query()
            ->with('section:id,code,term_id,subject_id', 'room:id,code')
            ->whereKeyNot($candidate->id ?? 0)
            ->where('course_section_id', '!=', $section->id)
            ->whereHas('section', fn (Builder $q) => $q->where('term_id', $section->term_id)->active())
            ->where('day_of_week', $candidate->day_of_week)
            ->where('period_from', '<=', $candidate->period_to)
            ->where('period_to', '>=', $candidate->period_from)
            ->get()
            ->filter(fn (ScheduleRule $other) => $this->rulesOverlap($candidate, $other));

        if ($others->isEmpty()) {
            return $this->split($found);
        }

        // Trùng phòng
        if ($candidate->room_id !== null) {
            foreach ($others->where('room_id', $candidate->room_id) as $other) {
                $found['room'][] = "Phòng {$other->room->code} đã có lớp {$other->section->code} ({$other->describe()}).";
            }
        }

        // Trùng giảng viên
        $lecturers = SectionInstructor::query()->with('lecturer:id,code,full_name')->where('course_section_id', $section->id)->get();

        if ($lecturers->isNotEmpty()) {
            $busy = SectionInstructor::query()
                ->whereIn('lecturer_id', $lecturers->pluck('lecturer_id'))
                ->whereIn('course_section_id', $others->pluck('course_section_id'))
                ->get();

            foreach ($busy as $assignment) {
                $lecturer = $lecturers->firstWhere('lecturer_id', $assignment->lecturer_id)->lecturer;
                $other = $others->firstWhere('course_section_id', $assignment->course_section_id);
                $found['lecturer'][] = "Giảng viên {$lecturer->full_name} ({$lecturer->code}) đang dạy lớp {$other->section->code} ({$other->describe()}).";
            }
        }

        // Trùng lịch sinh viên: hai học phần bắt buộc cùng học kỳ dự kiến của một CTĐT đang áp dụng
        $pairs = $this->compulsoryPeers($section->subject_id);

        foreach ($others as $other) {
            if (isset($pairs[$other->section->subject_id])) {
                $found['student'][] = "Lớp {$other->section->code} ({$other->describe()}) là học phần bắt buộc cùng học kỳ của CTĐT "
                    .$pairs[$other->section->subject_id].'; sinh viên cùng khóa không học được cả hai.';
            }
        }

        return $this->split($found);
    }

    /** Tất cả xung đột hiện có của một lớp (để xem lại sau khi xếp, FR-TTB-003). */
    public function sectionConflicts(CourseSection $section): array
    {
        $result = ['block' => [], 'warn' => []];

        foreach (ScheduleRule::query()->where('course_section_id', $section->id)->get() as $rule) {
            $found = $this->check($section, $rule);
            $result['block'] = [...$result['block'], ...$found['block']];
            $result['warn'] = [...$result['warn'], ...$found['warn']];
        }

        return ['block' => array_values(array_unique($result['block'])), 'warn' => array_values(array_unique($result['warn']))];
    }

    /**
     * Sinh viên đã đăng ký lớp bị trùng lịch với lớp khác của chính họ sau khi đổi lịch (BR-TTB-08).
     *
     * @return list<array{student_id: int, clash: string}>
     */
    public function affectedStudents(CourseSection $section): array
    {
        $affected = [];

        foreach ($this->students->studentIdsOf($section) as $studentId) {
            foreach ($this->students->otherSectionsOf($studentId, $section) as $other) {
                $clash = $this->sectionsClash($section, $other);

                if ($clash !== null) {
                    $affected[] = ['student_id' => $studentId, 'clash' => $clash];
                    break;
                }
            }
        }

        return $affected;
    }

    /** @return array<int, string> subject_id học phần bắt buộc cùng học kỳ dự kiến => mã CTĐT */
    private function compulsoryPeers(int $subjectId): array
    {
        $own = CurriculumSubject::query()
            ->whereNull('curriculum_elective_group_id')
            ->where('subject_id', $subjectId)
            ->whereIn('curriculum_id', Curriculum::query()->inForce()->select('id'))
            ->get(['curriculum_id', 'planned_term']);

        $peers = [];

        foreach ($own as $item) {
            $code = Curriculum::query()->whereKey($item->curriculum_id)->value('code');

            CurriculumSubject::query()
                ->whereNull('curriculum_elective_group_id')
                ->where('curriculum_id', $item->curriculum_id)
                ->where('planned_term', $item->planned_term)
                ->where('subject_id', '!=', $subjectId)
                ->pluck('subject_id')
                ->each(function (int $id) use (&$peers, $code): void {
                    $peers[$id] ??= $code;
                });
        }

        return $peers;
    }

    /** @param array<string, list<string>> $found */
    private function split(array $found): array
    {
        $modes = (array) config('studentmanager.timetable.conflicts', []);
        $result = ['block' => [], 'warn' => []];

        foreach ($found as $type => $messages) {
            $key = ($modes[$type] ?? 'block') === 'warn' ? 'warn' : 'block';
            $result[$key] = [...$result[$key], ...$messages];
        }

        return $result;
    }
}
