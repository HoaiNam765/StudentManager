<?php

namespace App\Modules\System\Enums;

enum ImportRowStatus: string
{
    /** Chưa kiểm tra */
    case Pending = 'pending';

    /** Hợp lệ, sẵn sàng lưu */
    case Valid = 'valid';

    /** Có lỗi */
    case Invalid = 'invalid';

    /** Đã lưu vào CSDL */
    case Saved = 'saved';

    /** Bỏ qua (dòng không hợp lệ khi chọn chỉ lưu hợp lệ) */
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ kiểm tra',
            self::Valid => 'Hợp lệ',
            self::Invalid => 'Có lỗi',
            self::Saved => 'Đã lưu',
            self::Skipped => 'Bỏ qua',
        };
    }
}
