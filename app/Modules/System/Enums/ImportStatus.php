<?php

namespace App\Modules\System\Enums;

enum ImportStatus: string
{
    /** Vừa tạo lô, chưa bắt đầu kiểm tra */
    case Pending = 'pending';

    /** Đang kiểm tra từng dòng */
    case Validating = 'validating';

    /** Đã kiểm tra xong, chờ người dùng quyết định */
    case Validated = 'validated';

    /** Đang lưu vào CSDL */
    case Saving = 'saving';

    /** Đã lưu xong */
    case Saved = 'saved';

    /** Đã hoàn tác toàn lô */
    case RolledBack = 'rolled_back';

    /** Lỗi hệ thống không thể phục hồi */
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ xử lý',
            self::Validating => 'Đang kiểm tra',
            self::Validated => 'Đã kiểm tra — chờ lưu',
            self::Saving => 'Đang lưu',
            self::Saved => 'Đã lưu',
            self::RolledBack => 'Đã hoàn tác',
            self::Failed => 'Lỗi',
        };
    }

    /** Trạng thái cho phép hủy/hoàn tác lô */
    public function canRollback(): bool
    {
        return $this === self::Saved;
    }

    /** Trạng thái đã hoàn thành (không xử lý thêm) */
    public function isTerminal(): bool
    {
        return in_array($this, [self::Validated, self::Saved, self::RolledBack, self::Failed], true);
    }
}
