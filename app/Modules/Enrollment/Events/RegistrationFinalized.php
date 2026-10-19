<?php

namespace App\Modules\Enrollment\Events;

use App\Modules\AcademicYear\Models\Term;
use Illuminate\Foundation\Events\Dispatchable;

/** Đã chốt đăng ký học kỳ (FR-ENR-012): FEE nghe để phát sinh công nợ học phí; NOT thông báo sinh viên khi có. */
class RegistrationFinalized
{
    use Dispatchable;

    public function __construct(
        public readonly Term $term,
        public readonly int $confirmed,
    ) {}
}
