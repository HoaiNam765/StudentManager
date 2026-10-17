<?php

namespace App\Modules\Teacher\Enums;

/** Loại vắng mặt của giảng viên (FR-TCH-012). */
enum LeaveType: string
{
    case Annual = 'annual';
    case Sick = 'sick';
    case BusinessTrip = 'business_trip';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Annual => 'Nghỉ phép',
            self::Sick => 'Nghỉ ốm',
            self::BusinessTrip => 'Công tác',
            self::Other => 'Khác',
        };
    }
}
