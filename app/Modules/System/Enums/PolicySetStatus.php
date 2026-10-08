<?php

namespace App\Modules\System\Enums;

enum PolicySetStatus: string
{
    /** Đang soạn: sửa được, chưa áp dụng */
    case Draft = 'draft';

    /** Đã ban hành: áp dụng từ ngày hiệu lực, không sửa được */
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Đang soạn',
            self::Published => 'Đã ban hành',
        };
    }
}
