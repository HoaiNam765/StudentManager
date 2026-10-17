<?php

namespace App\Modules\Teacher\Events;

use App\Modules\Teacher\Models\LecturerLeave;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Đơn nghỉ của giảng viên đã được duyệt (FR-TCH-012). TTB lắng nghe để chuyển các buổi dạy trong khoảng nghỉ sang
 * trạng thái Nghỉ và đề xuất dạy bù / người dạy thay; NOT gửi thông báo cho sinh viên các lớp liên quan.
 */
class LecturerLeaveApproved
{
    use Dispatchable;

    public function __construct(public readonly LecturerLeave $leave) {}
}
