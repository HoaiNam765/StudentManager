<?php

namespace App\Modules\Timetable\Enums;

/** Trạng thái buổi học cụ thể (FR-TTB-005). Buổi Nghỉ / Hủy không tính vào tỷ lệ vắng (BR-TTB-06). */
enum SessionStatus: string
{
    case Planned = 'planned';
    case Taught = 'taught';
    case Off = 'off';
    case Makeup = 'makeup';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Dự kiến',
            self::Taught => 'Đã dạy',
            self::Off => 'Nghỉ',
            self::Makeup => 'Dạy bù',
            self::Cancelled => 'Hủy',
        };
    }

    /** Buổi có học thật — tính vào số buổi và tỷ lệ vắng (ATT). */
    public function countsForAttendance(): bool
    {
        return in_array($this, [self::Planned, self::Taught, self::Makeup], true);
    }
}
