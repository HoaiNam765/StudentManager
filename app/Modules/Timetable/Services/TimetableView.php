<?php

namespace App\Modules\Timetable\Services;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Faculty\Models\Department;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Subject\Models\Subject;
use App\Modules\Timetable\Contracts\SectionStudents;
use App\Modules\Timetable\Models\ClassSession;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Xem thời khóa biểu theo đối tượng (FR-TTB-004): sinh viên, giảng viên, lớp học phần, lớp hành chính, phòng, khoa;
 * dạng tuần (Thứ Hai – Chủ nhật chứa `date`), tháng hoặc cả học kỳ. Dữ liệu là các buổi học cụ thể (`class_sessions`,
 * gồm cả buổi Nghỉ / Dạy bù để người xem biết lịch thay đổi). Phạm vi quyền kiểm tra ở controller.
 */
class TimetableView extends BaseService
{
    public const TARGETS = ['student', 'lecturer', 'section', 'admin_class', 'room', 'faculty'];

    public function __construct(
        private readonly SectionStudents $students,
        private readonly PeriodService $periods,
    ) {}

    /** @return array{from: CarbonImmutable, to: CarbonImmutable} */
    public function range(string $view, ?string $date, Term $term): array
    {
        $day = $date ? CarbonImmutable::parse($date) : CarbonImmutable::now(config('studentmanager.display_timezone'))->startOfDay();

        return match ($view) {
            'week' => ['from' => $day->startOfWeek(), 'to' => $day->endOfWeek()->startOfDay()],
            'month' => ['from' => $day->startOfMonth(), 'to' => $day->endOfMonth()->startOfDay()],
            default => ['from' => CarbonImmutable::parse($term->start_date), 'to' => CarbonImmutable::parse($term->end_date)],
        };
    }

    /** @return list<array<string, mixed>> */
    public function sessions(string $target, int $id, Term $term, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $query = ClassSession::query()
            ->with(['section:id,code,subject_id', 'section.subject:id,code,name', 'room:id,code,building_id', 'room.building:id,campus_id'])
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->whereHas('section', fn ($q) => $q->where('term_id', $term->id));

        match ($target) {
            'student' => $query->whereIn('course_section_id', $this->students->sectionIdsOfStudents([$id], $term->id)),
            'lecturer' => $query->whereIn('course_section_id', SectionInstructor::query()->where('lecturer_id', $id)->select('course_section_id')),
            'section' => $query->where('course_section_id', $id),
            'admin_class' => $query->whereIn('course_section_id', $this->students->sectionIdsOfStudents(
                AdminClassMember::query()->where('admin_class_id', $id)->current()->pluck('student_id')->all(),
                $term->id,
            )),
            'room' => $query->where('room_id', $id),
            'faculty' => $query->whereIn('course_section_id', CourseSection::query()->select('id')->whereIn(
                'subject_id',
                Subject::query()->select('id')->whereIn('department_id', Department::query()->select('id')->where('faculty_id', $id)),
            )),
            default => $this->fail("Đối tượng \"{$target}\" không hợp lệ.", 'Chọn một trong: '.implode(', ', self::TARGETS).'.'),
        };

        $instructors = SectionInstructor::query()->with('lecturer:id,full_name')->where('role', 'main')->get()->keyBy('course_section_id');

        return $query->orderBy('date')->orderBy('period_from')->get()->map(function (ClassSession $s) use ($instructors): array {
            $frame = $this->periods->periodsFor($s->room?->building?->campus_id);

            return [
                'id' => $s->id,
                'date' => $s->date->toDateString(),
                'day_of_week' => $s->date->dayOfWeekIso,
                'period_from' => $s->period_from,
                'period_to' => $s->period_to,
                'time' => ($frame->get($s->period_from)?->startTime() ?? '?').'–'.($frame->get($s->period_to)?->endTime() ?? '?'),
                'section_id' => $s->course_section_id,
                'section' => $s->section->code,
                'subject' => $s->section->subject->name,
                'lecturer' => $instructors->get($s->course_section_id)?->lecturer?->full_name,
                'room' => $s->room?->code,
                'status' => $s->status->value,
                'status_label' => $s->status->label(),
                'note' => $s->off_reason ?? $s->note,
            ];
        })->all();
    }
}
