<?php

namespace App\Modules\Faculty\Enums;

/** Trình độ đào tạo của ngành (FR-FAC-003). P1 chỉ dùng đại học. */
enum EducationLevel: string
{
    case Undergraduate = 'undergraduate';
    case Master = 'master';
    case Doctorate = 'doctorate';

    public function label(): string
    {
        return match ($this) {
            self::Undergraduate => 'Đại học',
            self::Master => 'Thạc sĩ',
            self::Doctorate => 'Tiến sĩ',
        };
    }
}
