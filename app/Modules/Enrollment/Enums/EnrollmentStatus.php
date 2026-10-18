<?php

namespace App\Modules\Enrollment\Enums;

/**
 * Vòng đời đăng ký (BA 6.6): Đã đăng ký → Đã chốt (#113) → Hoàn thành (khi có kết quả); Đã đăng ký → Đã hủy (trong hạn);
 * Đã đăng ký / Đã chốt → Đã rút (trong hạn rút). Danh sách chờ (FR-ENR-014) chưa làm.
 */
enum EnrollmentStatus: string
{
    case Registered = 'registered';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Đã đăng ký',
            self::Confirmed => 'Đã chốt',
            self::Completed => 'Hoàn thành',
            self::Cancelled => 'Đã hủy',
            self::Withdrawn => 'Đã rút',
        };
    }

    /** Còn chiếm chỗ trong lớp. */
    public function isActive(): bool
    {
        return in_array($this, [self::Registered, self::Confirmed, self::Completed], true);
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::Registered => in_array($to, [self::Confirmed, self::Cancelled, self::Withdrawn], true),
            self::Confirmed => in_array($to, [self::Completed, self::Withdrawn], true),
            default => false,
        };
    }

    /** @return list<string> */
    public static function activeValues(): array
    {
        return [self::Registered->value, self::Confirmed->value, self::Completed->value];
    }
}
