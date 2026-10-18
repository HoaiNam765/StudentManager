<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Enrollment\Contracts\TuitionStatus;
use App\Modules\Student\Models\Student;

/** Cài đặt tạm khi chưa có FEE: không ai nợ. */
class NoTuitionDebt implements TuitionStatus
{
    public function overdueAmount(Student $student): int
    {
        return 0;
    }
}
