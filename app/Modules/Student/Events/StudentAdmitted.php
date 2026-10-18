<?php

namespace App\Modules\Student\Events;

use App\Modules\Student\Models\Student;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * EV-STU-01 (phụ lục A): sinh viên được xác nhận nhập học, đã có MSSV, email trường, tài khoản, CTĐT và lớp hành chính.
 * Phát sau khi giao dịch hoàn tất; NOT gửi thông báo chào mừng khi có module thông báo.
 */
class StudentAdmitted
{
    use Dispatchable;

    public function __construct(public readonly Student $student) {}
}
