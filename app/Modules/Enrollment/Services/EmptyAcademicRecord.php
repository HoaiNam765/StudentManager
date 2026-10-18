<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Student\Models\Student;

/** Cài đặt tạm khi chưa có GRD: sinh viên chưa học / chưa đạt học phần nào, không cảnh báo, chưa xếp loại. */
class EmptyAcademicRecord implements AcademicRecord
{
    public function passedSubjectIds(Student $student): array
    {
        return [];
    }

    public function attemptedSubjectIds(Student $student): array
    {
        return [];
    }

    public function onProbation(Student $student): bool
    {
        return false;
    }

    public function goodStanding(Student $student): bool
    {
        return false;
    }
}
