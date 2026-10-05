<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Định dạng hiển thị theo quy ước Việt Nam (GC-09, NFR-LOC-01):
 * ngày dd/MM/yyyy, giờ theo UTC+7, tiền VND không có phần thập phân.
 */
final class Format
{
    public static function date(CarbonInterface|string|null $value): string
    {
        return self::inDisplayTimezone($value)?->format(config('studentmanager.formats.date')) ?? '';
    }

    public static function dateTime(CarbonInterface|string|null $value): string
    {
        return self::inDisplayTimezone($value)?->format(config('studentmanager.formats.datetime')) ?? '';
    }

    /** 4050000 → "4.050.000 ₫". Tiền lưu dạng số nguyên đồng. */
    public static function money(int|float|string|null $amount): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        return number_format((int) round((float) $amount), 0, ',', '.').' '.config('studentmanager.currency_symbol');
    }

    private static function inDisplayTimezone(CarbonInterface|string|null $value): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Giá trị lưu trong CSDL là UTC; chuỗi không kèm múi giờ cũng được hiểu là UTC.
        return CarbonImmutable::parse($value, 'UTC')->setTimezone(config('studentmanager.display_timezone'));
    }
}
