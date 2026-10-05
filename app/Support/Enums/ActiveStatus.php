<?php

namespace App\Support\Enums;

/**
 * Trạng thái của dữ liệu danh mục (GC-05): thay cho việc xóa.
 */
enum ActiveStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Hoạt động',
            self::Inactive => 'Ngừng hoạt động',
        };
    }
}
