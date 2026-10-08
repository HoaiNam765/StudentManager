<?php

namespace App\Modules\Auth\Enums;

/** Trạng thái tài khoản (FR-AUTH-008, BR-AUTH-07). Khóa tạm do nhập sai mật khẩu là chuyện riêng (`locked_until`). */
enum AccountStatus: string
{
    case Active = 'active';

    /** Quản trị viên khóa (vi phạm, nghi bị chiếm…); mở bằng thao tác mở khóa */
    case Locked = 'locked';

    /** Ngừng hoạt động (nghỉ việc, thôi học…) */
    case Inactive = 'inactive';

    /** Chỉ đọc: sinh viên đã tốt nghiệp xem bảng điểm, tải giấy tờ, không thao tác ghi */
    case ReadOnly = 'read_only';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Hoạt động',
            self::Locked => 'Đã khóa',
            self::Inactive => 'Ngừng hoạt động',
            self::ReadOnly => 'Chỉ đọc',
        };
    }

    public function canSignIn(): bool
    {
        return $this === self::Active || $this === self::ReadOnly;
    }
}
