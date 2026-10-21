<?php

namespace App\Modules\Enrollment\Events;

use App\Modules\Enrollment\Models\Enrollment;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * EV-ENR-02 (phụ lục A): đăng ký / hủy / rút / đổi lớp thành công (`register`, `cancel`, `withdraw`, `change`), do sinh
 * viên tự làm hoặc cán bộ làm thay. NOT xác nhận cho sinh viên.
 */
class EnrollmentChanged
{
    use Dispatchable;

    public function __construct(
        public readonly Enrollment $enrollment,
        public readonly string $action,
    ) {}
}
