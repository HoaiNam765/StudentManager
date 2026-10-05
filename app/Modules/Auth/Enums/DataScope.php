<?php

namespace App\Modules\Auth\Enums;

/**
 * Phạm vi dữ liệu của một quyền (docs/BA.md mục 4.3, FR-AUTH-011).
 */
enum DataScope: string
{
    case All = 'ALL';
    case Faculty = 'FACULTY';
    case Section = 'SECTION';
    case Advisee = 'ADVISEE';
    case Own = 'OWN';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Toàn trường',
            self::Faculty => 'Khoa / bộ môn quản lý',
            self::Section => 'Lớp học phần được phân công',
            self::Advisee => 'Lớp hành chính được cố vấn',
            self::Own => 'Dữ liệu của chính mình',
        };
    }
}
