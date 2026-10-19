<?php

namespace App\Modules\SchoolClass\Events;

use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Lớp học phần bị hủy (BR-CLS-09). Phát trong giao dịch hủy lớp, nghe đồng bộ: ENR hủy các đăng ký còn hiệu lực
 * (EV-ENR-03), TTB hủy các buổi học chưa diễn ra; FEE điều chỉnh công nợ khi có.
 */
class CourseSectionCancelled
{
    use Dispatchable;

    public function __construct(
        public readonly CourseSection $section,
        public readonly string $reason,
    ) {}
}
