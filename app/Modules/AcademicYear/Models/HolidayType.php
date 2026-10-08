<?php

namespace App\Modules\AcademicYear\Models;

/** Loại ngày nghỉ (FR-ACY-005). */
enum HolidayType: string
{
    case PublicHoliday = 'public_holiday';
    case SummerBreak = 'summer_break';
    case SchoolBreak = 'school_break';

    public function label(): string
    {
        return match ($this) {
            self::PublicHoliday => 'Nghỉ lễ',
            self::SummerBreak => 'Nghỉ hè',
            self::SchoolBreak => 'Nghỉ theo lịch của trường',
        };
    }
}
