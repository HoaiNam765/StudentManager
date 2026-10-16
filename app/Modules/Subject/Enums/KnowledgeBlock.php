<?php

namespace App\Modules\Subject\Enums;

/** Khối kiến thức của học phần (FR-SUB-003). */
enum KnowledgeBlock: string
{
    case General = 'general';
    case Foundation = 'foundation';
    case Specialized = 'specialized';
    case Internship = 'internship';
    case Thesis = 'thesis';

    public function label(): string
    {
        return match ($this) {
            self::General => 'Giáo dục đại cương',
            self::Foundation => 'Cơ sở ngành',
            self::Specialized => 'Chuyên ngành',
            self::Internship => 'Thực tập',
            self::Thesis => 'Đồ án / khóa luận',
        };
    }
}
