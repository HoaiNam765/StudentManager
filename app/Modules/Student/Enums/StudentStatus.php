<?php

namespace App\Modules\Student\Enums;

/**
 * Trạng thái sinh viên (BA 6.2, FR-STU-009). Hồ sơ mới ở "Chờ nhập học"; xác nhận nhập học (#102) chuyển sang
 * "Đang học"; các chuyển trạng thái khác theo quyết định (#114).
 */
enum StudentStatus: string
{
    case Pending = 'pending';
    case Studying = 'studying';
    case Suspended = 'suspended';
    case Withdrawn = 'withdrawn';
    case Expelled = 'expelled';
    case Transferred = 'transferred';
    case Graduated = 'graduated';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ nhập học',
            self::Studying => 'Đang học',
            self::Suspended => 'Bảo lưu',
            self::Withdrawn => 'Thôi học',
            self::Expelled => 'Buộc thôi học',
            self::Transferred => 'Chuyển trường',
            self::Graduated => 'Đã tốt nghiệp',
        };
    }

    /** Trạng thái cuối: chỉ khôi phục bằng quyết định đặc biệt (BR-STU-05). */
    public function isFinal(): bool
    {
        return in_array($this, [self::Withdrawn, self::Expelled, self::Transferred, self::Graduated], true);
    }

    /** Còn thuộc danh sách người học của trường (chờ nhập học, đang học, bảo lưu). */
    public function isEnrolled(): bool
    {
        return in_array($this, [self::Pending, self::Studying, self::Suspended], true);
    }
}
