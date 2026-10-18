<?php

namespace App\Modules\Timetable\Enums;

/** Ca học trong ngày; giữa hai ca là giờ nghỉ (nghỉ trưa, nghỉ chiều) mà buổi học không được vắt qua (BR-TTB-03). */
enum Shift: string
{
    case Morning = 'morning';
    case Afternoon = 'afternoon';
    case Evening = 'evening';

    public function label(): string
    {
        return match ($this) {
            self::Morning => 'Sáng',
            self::Afternoon => 'Chiều',
            self::Evening => 'Tối',
        };
    }
}
