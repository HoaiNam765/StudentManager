<?php

namespace App\Modules\Attendance\Enums;

/** Trạng thái điểm danh của một sinh viên trong một buổi học (FR-ATT-002). */
enum AttendanceStatus: string
{
    case Present = 'present';
    case Absent = 'absent';
    case Excused = 'excused';
    case Late = 'late';

    public function label(): string
    {
        return match ($this) {
            self::Present => 'Có mặt',
            self::Absent => 'Vắng không phép',
            self::Excused => 'Vắng có phép',
            self::Late => 'Đi muộn',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
