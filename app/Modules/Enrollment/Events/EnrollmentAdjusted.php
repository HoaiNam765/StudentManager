<?php

namespace App\Modules\Enrollment\Events;

use App\Modules\Enrollment\Models\Enrollment;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Cán bộ đăng ký / hủy thay sau khi đã chốt đăng ký (BR-ENR-08): FEE nghe để điều chỉnh công nợ ngay khi có module FEE.
 * `$action`: register | cancel.
 */
class EnrollmentAdjusted
{
    use Dispatchable;

    public function __construct(
        public readonly Enrollment $enrollment,
        public readonly string $action,
        public readonly string $reason,
    ) {}
}
