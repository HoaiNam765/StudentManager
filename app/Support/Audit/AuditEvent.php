<?php

namespace App\Support\Audit;

/**
 * Các loại hành động được ghi nhật ký. Cần loại mới (ví dụ "chốt đăng ký") thì thêm case ở đây.
 */
enum AuditEvent: string
{
    // Tự ghi khi model kế thừa StandardModel thay đổi
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case ForceDeleted = 'force_deleted';

    // Ghi thủ công bằng AuditLogger::record()
    case Login = 'login';
    case LoginFailed = 'login_failed';
    case Logout = 'logout';
    case AccessDenied = 'access_denied';
    case ViewSensitive = 'view_sensitive';
    case Exported = 'exported';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Locked = 'locked';
    case Unlocked = 'unlocked';
    case PasswordChanged = 'password_changed';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Tạo mới',
            self::Updated => 'Cập nhật',
            self::Deleted => 'Xóa',
            self::Restored => 'Khôi phục',
            self::ForceDeleted => 'Xóa vĩnh viễn',
            self::Login => 'Đăng nhập',
            self::LoginFailed => 'Đăng nhập thất bại',
            self::Logout => 'Đăng xuất',
            self::AccessDenied => 'Từ chối truy cập',
            self::ViewSensitive => 'Xem dữ liệu nhạy cảm',
            self::Exported => 'Xuất dữ liệu',
            self::Approved => 'Duyệt',
            self::Rejected => 'Từ chối',
            self::Locked => 'Khóa',
            self::Unlocked => 'Mở khóa',
            self::PasswordChanged => 'Đổi mật khẩu',
        };
    }
}
