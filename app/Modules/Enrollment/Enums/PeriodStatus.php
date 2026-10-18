<?php

namespace App\Modules\Enrollment\Enums;

/** Trạng thái đợt đăng ký: nháp (chỉ cán bộ thấy) → công bố (sinh viên đăng ký trong cửa sổ thời gian) → đã chốt (#113). */
enum PeriodStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Finalized = 'finalized';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nháp',
            self::Published => 'Đã công bố',
            self::Finalized => 'Đã chốt',
        };
    }
}
