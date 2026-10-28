<?php

namespace App\Modules\SchoolClass\Services;

use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Teacher\Contracts\TeachingAssignments;
use App\Modules\Teacher\Models\Lecturer;

/** Lớp học phần giảng viên đang được phân công mà chưa kết thúc / khóa / hủy (BR-TCH-02, cài cho TCH). */
class SectionTeachingAssignments implements TeachingAssignments
{
    public function activeSections(Lecturer $lecturer): array
    {
        return CourseSection::query()
            ->with(['subject:id,code,name', 'term:id,name,academic_year_id', 'term.academicYear:id,name'])
            ->whereIn('status', SectionStatus::activeValues())
            ->whereHas('instructors', fn ($q) => $q->where('lecturer_id', $lecturer->id))
            ->orderBy('code')
            ->get()
            ->map(fn (CourseSection $s) => "{$s->code} – {$s->subject?->name} ({$s->term?->name} {$s->term?->academicYear?->name})")
            ->all();
    }

    public function termLoads(array $lecturerIds, int $termId): array
    {
        return SectionInstructor::query()
            ->join('course_sections', 'course_sections.id', '=', 'section_instructors.course_section_id')
            ->join('subjects', 'subjects.id', '=', 'course_sections.subject_id')
            ->whereIn('section_instructors.lecturer_id', $lecturerIds)
            ->whereNull('section_instructors.deleted_at')
            ->whereNull('course_sections.deleted_at')
            ->where('course_sections.term_id', $termId)
            ->whereIn('course_sections.status', SectionStatus::activeValues())
            ->groupBy('section_instructors.lecturer_id')
            ->selectRaw('section_instructors.lecturer_id, count(distinct course_sections.id) as sections, sum(subjects.theory_periods + subjects.practice_periods) as periods')
            ->get()
            ->mapWithKeys(fn ($row) => [(int) $row->lecturer_id => ['sections' => (int) $row->sections, 'periods' => (int) $row->periods]])
            ->all();
    }
}
