<?php

namespace App\Modules\Teacher\Enums;

/** Trạng thái đơn nghỉ: chờ duyệt, đã duyệt, từ chối; người gửi hủy được khi chưa bắt đầu. */
enum LeaveStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ duyệt',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Từ chối',
            self::Cancelled => 'Đã hủy',
        };
    }
}
