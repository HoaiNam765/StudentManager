<?php

namespace App\Modules\Teacher\Enums;

/** Học vị của giảng viên (FR-TCH-001). */
enum AcademicDegree: string
{
    case Bachelor = 'bachelor';
    case Engineer = 'engineer';
    case Master = 'master';
    case Doctor = 'doctor';

    public function label(): string
    {
        return match ($this) {
            self::Bachelor => 'Cử nhân',
            self::Engineer => 'Kỹ sư',
            self::Master => 'Thạc sĩ',
            self::Doctor => 'Tiến sĩ',
        };
    }
}
