<?php

namespace App\Modules\Subject\Enums;

/**
 * Bắt buộc hay tự chọn (FR-SUB-003). Đây là phân loại mặc định của học phần; CTĐT (CUR) có thể xếp lại
 * khi gán học phần vào từng khối của chương trình.
 */
enum SubjectRequirement: string
{
    case Compulsory = 'compulsory';
    case Elective = 'elective';

    public function label(): string
    {
        return match ($this) {
            self::Compulsory => 'Bắt buộc',
            self::Elective => 'Tự chọn',
        };
    }
}
