<?php

namespace App\Modules\Timetable\Enums;

/** Mẫu tuần học của một dòng lịch (FR-TTB-002). */
enum WeekParity: string
{
    case All = 'all';
    case Odd = 'odd';
    case Even = 'even';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Mọi tuần',
            self::Odd => 'Tuần lẻ',
            self::Even => 'Tuần chẵn',
        };
    }

    public function includes(int $week): bool
    {
        return match ($this) {
            self::All => true,
            self::Odd => $week % 2 === 1,
            self::Even => $week % 2 === 0,
        };
    }
}
