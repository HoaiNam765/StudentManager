<?php

namespace App\Modules\Timetable\Enums;

/** Trạng thái thời khóa biểu của học kỳ (FR-TTB-006). */
enum TimetableStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Locked = 'locked';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nháp',
            self::Published => 'Đã công bố',
            self::Locked => 'Đã khóa',
        };
    }
}
