<?php

namespace App\Modules\Teacher\Services;

use App\Models\User;
use App\Modules\Subject\Contracts\SubjectAssignments;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;

/**
 * Tạm thời (chờ CLS #107): giảng viên phụ trách các học phần do bộ môn chính hoặc bộ môn kiêm nhiệm đang hiệu lực
 * của mình quản lý. Giảng viên đã nghỉ việc / nghỉ hưu không phụ trách học phần nào.
 */
class DepartmentSubjectAssignments implements SubjectAssignments
{
    public function subjectIdsFor(User $user): array
    {
        $lecturer = Lecturer::query()->where('user_id', $user->id)->first();

        if ($lecturer === null || ! $lecturer->isAssignable()) {
            return [];
        }

        $today = now(config('studentmanager.display_timezone'))->toDateString();
        $departments = $lecturer->units()->effectiveOn($today)->where('unit_type', 'department')->pluck('unit_id')
            ->push($lecturer->department_id)
            ->unique()
            ->all();

        return Subject::query()->whereIn('department_id', $departments)->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}
