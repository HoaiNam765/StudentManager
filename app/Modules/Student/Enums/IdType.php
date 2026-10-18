<?php

namespace App\Modules\Student\Enums;

/** Loại giấy tờ tùy thân (BR-STU-02): CCCD cho công dân Việt Nam, hộ chiếu cho sinh viên nước ngoài. */
enum IdType: string
{
    case NationalId = 'national_id';
    case Passport = 'passport';

    public function label(): string
    {
        return match ($this) {
            self::NationalId => 'CCCD',
            self::Passport => 'Hộ chiếu',
        };
    }
}
