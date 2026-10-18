<?php

namespace App\Modules\Curriculum\Enums;

/** Trạng thái CTĐT: nháp (sửa được) → đã ban hành (không sửa trực tiếp, BR-CUR-04) → ngừng áp dụng. */
enum CurriculumStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Retired = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nháp',
            self::Published => 'Đã ban hành',
            self::Retired => 'Ngừng áp dụng',
        };
    }
}
