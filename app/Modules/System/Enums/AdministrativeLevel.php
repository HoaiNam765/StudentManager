<?php

namespace App\Modules\System\Enums;

enum AdministrativeLevel: string
{
    case Province = 'province';
    case District = 'district';
    case Commune = 'commune';

    public function label(): string
    {
        return match ($this) {
            self::Province => 'Tỉnh / thành phố',
            self::District => 'Quận / huyện / thị xã',
            self::Commune => 'Xã / phường / đặc khu',
        };
    }
}
