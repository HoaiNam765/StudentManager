<?php

namespace App\Modules\Student\Services;

/**
 * Số giấy tờ tùy thân (CCCD, hộ chiếu): chuẩn hóa, mã băm để kiểm tra trùng không cần giải mã (BR-STU-02),
 * che khi hiển thị (BR-STU-11).
 */
final class IdentityNumber
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = strtoupper(preg_replace('/\s+/', '', $value) ?? '');

        return $value === '' ? null : $value;
    }

    public static function hash(?string $value): ?string
    {
        $value = self::normalize($value);

        return $value === null ? null : hash_hmac('sha256', $value, (string) config('app.key'));
    }

    /** 079203001234 → ********1234 */
    public static function mask(?string $value): ?string
    {
        $value = self::normalize($value);

        if ($value === null) {
            return null;
        }

        $visible = min(4, intdiv(strlen($value), 3));

        return str_repeat('*', strlen($value) - $visible).substr($value, -$visible);
    }

    /** Địa chỉ chỉ hiện ký tự đầu của mỗi từ: "12 Lê Lợi" → "1* L* L**". */
    public static function maskAddress(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        return implode(' ', array_map(
            fn (string $word) => mb_substr($word, 0, 1).str_repeat('*', max(0, mb_strlen($word) - 1)),
            preg_split('/\s+/u', trim($value)) ?: [],
        ));
    }
}
