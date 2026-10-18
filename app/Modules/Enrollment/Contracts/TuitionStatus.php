<?php

namespace App\Modules\Enrollment\Contracts;

use App\Modules\Student\Models\Student;

/** Công nợ quá hạn của sinh viên (BR-ENR-09). Mặc định không nợ (NoTuitionDebt); FEE bind cài đặt thật. */
interface TuitionStatus
{
    /** Số tiền (đồng) đã quá hạn chưa nộp. */
    public function overdueAmount(Student $student): int;
}
