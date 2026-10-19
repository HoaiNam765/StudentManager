<?php

namespace App\Modules\Timetable\Events;

use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * EV-TTB-02 (phụ lục A): đổi lịch / phòng của lớp sau khi đã công bố thời khóa biểu; NOT gửi cho STU, LEC của lớp
 * (thông báo bắt buộc). `$urgent` = thay đổi khẩn cấp dưới hạn báo trước (BR-TTB-05).
 */
class ScheduleChanged
{
    use Dispatchable;

    public function __construct(
        public readonly CourseSection $section,
        public readonly string $reason,
        public readonly bool $urgent = false,
    ) {}
}
