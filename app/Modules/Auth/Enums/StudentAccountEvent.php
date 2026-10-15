<?php

namespace App\Modules\Auth\Enums;

/**
 * Thay đổi tình trạng học tập ảnh hưởng tới tài khoản sinh viên (BR-AUTH-07). Module STU gọi
 * `UserService::applyStudentStatus($user, StudentAccountEvent::Dropped)` khi đổi trạng thái hồ sơ.
 */
enum StudentAccountEvent: string
{
    /** Thôi học */
    case Dropped = 'dropped';

    /** Buộc thôi học */
    case Expelled = 'expelled';

    /** Chuyển trường */
    case Transferred = 'transferred';

    /** Đã tốt nghiệp: chuyển sang chỉ đọc */
    case Graduated = 'graduated';

    /** Học lại / phục hồi: tài khoản hoạt động bình thường */
    case Reinstated = 'reinstated';

    public function label(): string
    {
        return match ($this) {
            self::Dropped => 'Thôi học',
            self::Expelled => 'Buộc thôi học',
            self::Transferred => 'Chuyển trường',
            self::Graduated => 'Đã tốt nghiệp',
            self::Reinstated => 'Phục hồi học tập',
        };
    }
}
