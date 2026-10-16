<?php

namespace App\Modules\Subject\Enums;

/** Ngôn ngữ giảng dạy của học phần (FR-SUB-001). */
enum TeachingLanguage: string
{
    case Vietnamese = 'vi';
    case English = 'en';
    case Bilingual = 'vi_en';

    public function label(): string
    {
        return match ($this) {
            self::Vietnamese => 'Tiếng Việt',
            self::English => 'Tiếng Anh',
            self::Bilingual => 'Song ngữ Việt – Anh',
        };
    }
}
