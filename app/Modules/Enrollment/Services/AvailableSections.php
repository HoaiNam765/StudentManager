<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\StudentCurriculumService;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\SectionStartDates;

/**
 * Danh sách lớp học phần sinh viên được chọn trong đợt (FR-ENR-002): lớp của học kỳ thuộc học phần trong CTĐT hiện hành
 * của sinh viên (đợt theo khung: lớp khung khoa – khóa mình + học bù, xem FrameworkPeriodScope), kèm sĩ số còn lại, lịch,
 * giảng viên, phòng. Lớp không đăng ký được vẫn hiện, đánh dấu kèm lý do.
 *
 * Lý do lấy từ EnrollmentRules (FR-ENR-004): lớp chưa mở / hết chỗ, đã đạt, trùng lịch, tiên quyết, tín chỉ tối đa…
 */
class AvailableSections
{
    public function __construct(
        private readonly StudentCurriculumService $curricula,
        private readonly EnrollmentRules $rules,
        private readonly SectionStartDates $startDates,
    ) {}

    /** @return list<array<string, mixed>> */
    public function listFor(Student $student, RegistrationPeriod $period): array
    {
        $curriculum = $this->curricula->current($student);

        if ($curriculum === null && ! $period->usesFrameworks()) {
            return [];
        }

        $items = CurriculumSubject::query()->where('curriculum_id', $curriculum?->id)->get()->keyBy('subject_id');
        // Đợt theo khung: lớp của khung khoa – khóa mình + lớp học bù (môn kỳ trước chưa đạt); môn đã đạt không hiện
        $scope = $period->usesFrameworks() ? $this->rules->scopeFor($student, $period) : [];
        $frameworkItems = $period->usesFrameworks() ? $period->frameworkFor($student)?->items->keyBy('subject_id') : null;

        $sections = CourseSection::query()
            ->with(['subject:id,code,name,credits', 'room:id,code', 'instructors.lecturer:id,code,full_name'])
            ->where('term_id', $period->term_id)
            ->when($period->usesFrameworks(),
                fn ($q) => $q->whereIn('id', array_keys($scope)),
                fn ($q) => $q->whereNull('registration_period_id')->whereIn('subject_id', $items->keys()))
            ->where('status', '!=', SectionStatus::Cancelled)
            ->orderBy('code')
            ->get();

        $rules = ScheduleRule::query()->with('room:id,code')->whereIn('course_section_id', $sections->pluck('id'))->get()->groupBy('course_section_id');
        $starts = $this->startDates->for($sections->pluck('id'));

        return $sections->map(function (CourseSection $section) use ($items, $frameworkItems, $scope, $rules, $starts, $student, $period): array {
            $item = $items->get($section->subject_id);
            $remaining = $this->rules->remaining($section);
            $reasons = $this->rules->check($student, $section, $period);

            return [
                'id' => $section->id,
                'code' => $section->code,
                'subject' => $section->subject->only('id', 'code', 'name', 'credits'),
                'planned_term' => $item?->planned_term,
                'backlog' => $scope[$section->id]['backlog'] ?? false,
                'compulsory' => $frameworkItems?->get($section->subject_id)?->compulsory
                    ?? ($item !== null && $item->curriculum_elective_group_id === null),
                'status' => $section->status->value,
                'status_label' => $section->status->label(),
                'max_size' => $section->max_size,
                'remaining' => $remaining,
                'instructors' => $section->instructors->map(fn (SectionInstructor $i) => [
                    'code' => $i->lecturer->code,
                    'name' => $i->lecturer->full_name,
                    'role' => $i->role,
                ])->values(),
                'schedule' => ($rules->get($section->id) ?? collect())->map(fn (ScheduleRule $r) => [
                    'description' => $r->describe(),
                    'room' => $r->room?->code,
                ])->values(),
                'starts_on' => ($starts[$section->id] ?? null)?->toDateString(),
                'eligible' => $reasons === [],
                'reasons' => $reasons,
            ];
        })->sortBy([['eligible', 'desc'], ['planned_term', 'asc'], ['code', 'asc']])->values()->all();
    }
}
