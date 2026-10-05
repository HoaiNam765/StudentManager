<?php

namespace App\Support\Text;

use Illuminate\Support\Str;

final class Vietnamese
{
    /**
     * Chuẩn hóa để so khớp: bỏ dấu (kể cả đ → d), hạ chữ thường, gộp khoảng trắng.
     * "Đặng  Hoài Nam" → "dang hoai nam".
     */
    public static function fold(?string $text): string
    {
        $folded = Str::lower(Str::ascii((string) $text));

        return trim((string) preg_replace('/\s+/', ' ', $folded));
    }

    /** Thoát các ký tự đặc biệt của LIKE (%, _) với ký tự thoát `!`. */
    public static function escapeLike(string $value): string
    {
        return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value);
    }
}
