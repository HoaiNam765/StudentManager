<?php

namespace App\Modules\SchoolClass\Services;

use App\Models\User;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Subject\Contracts\SubjectAssignments;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Services\DepartmentSubjectAssignments;

/**
 * Học phần giảng viên phụ trách (phạm vi SECTION): học phần của các lớp được phân công (chưa hủy), cộng thêm học phần
 * của bộ môn chính / kiêm nhiệm để giảng viên vẫn soạn được đề cương trước khi có lớp.
 */
class SectionSubjectAssignments implements SubjectAssignments
{
    public function __construct(private readonly DepartmentSubjectAssignments $departments) {}

    public function subjectIdsFor(User $user): array
    {
        $fromSections = CourseSection::query()
            ->where('status', '!=', 'cancelled')
            ->whereHas('instructors', fn ($q) => $q->whereIn('lecturer_id', Lecturer::query()->select('id')->where('user_id', $user->id)))
            ->distinct()
            ->pluck('subject_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return array_values(array_unique([...$fromSections, ...$this->departments->subjectIdsFor($user)]));
    }
}
