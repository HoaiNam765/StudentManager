<?php

namespace App\Modules\Subject\Enums;

/**
 * Loại quan hệ giữa hai học phần (FR-SUB-004). Với dòng (B, A, loại):
 * - tiên quyết: phải học và Đạt A trước khi đăng ký B;
 * - học trước: phải đã học A (không cần Đạt) trước khi đăng ký B;
 * - song hành: A học trước hoặc cùng học kỳ với B;
 * - tương đương: A thay được cho B (và ngược lại) trong khoảng hiệu lực (BR-SUB-07).
 */
enum RelationType: string
{
    case Prerequisite = 'prerequisite';
    case Prior = 'prior';
    case Corequisite = 'corequisite';
    case Equivalent = 'equivalent';

    public function label(): string
    {
        return match ($this) {
            self::Prerequisite => 'Tiên quyết',
            self::Prior => 'Học trước',
            self::Corequisite => 'Song hành',
            self::Equivalent => 'Tương đương',
        };
    }

    /** Quan hệ quy định thứ tự học: không được tạo thành vòng (BR-SUB-03). */
    public function isOrdering(): bool
    {
        return in_array($this, [self::Prerequisite, self::Prior], true);
    }
}
