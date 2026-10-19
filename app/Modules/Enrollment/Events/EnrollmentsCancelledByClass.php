<?php

namespace App\Modules\Enrollment\Events;

use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * EV-ENR-03 (phụ lục A): lớp học phần bị hủy, các đăng ký đã bị hủy theo. NOT gửi thông báo cho sinh viên; FEE điều
 * chỉnh công nợ (BR-CLS-09) khi có.
 */
class EnrollmentsCancelledByClass
{
    use Dispatchable;

    /** @param list<int> $studentIds */
    public function __construct(
        public readonly CourseSection $section,
        public readonly array $studentIds,
        public readonly string $reason,
    ) {}
}
