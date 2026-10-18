<?php

namespace App\Modules\SchoolClass\Services;

use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
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
}
