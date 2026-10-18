<?php

namespace App\Modules\Subject\Services;

use App\Models\User;
use App\Modules\Subject\Contracts\SubjectAssignments;

/** Bản mặc định khi không cài TCH / CLS: phạm vi SECTION không có học phần nào. */
class NoSubjectAssignments implements SubjectAssignments
{
    public function subjectIdsFor(User $user): array
    {
        return [];
    }
}
