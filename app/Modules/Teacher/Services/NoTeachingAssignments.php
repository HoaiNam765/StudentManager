<?php

namespace App\Modules\Teacher\Services;

use App\Modules\Teacher\Contracts\TeachingAssignments;
use App\Modules\Teacher\Models\Lecturer;

/** Bản mặc định khi chưa có lớp học phần (CLS): không có lớp nào cần chuyển. CLS (#107) thay bằng lớp thật. */
class NoTeachingAssignments implements TeachingAssignments
{
    public function activeSections(Lecturer $lecturer): array
    {
        return [];
    }
}
