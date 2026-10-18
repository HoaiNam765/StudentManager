<?php

namespace App\Modules\Enrollment\Enums;

/** Vòng đăng ký của một đợt (FR-ENR-001). */
enum RegistrationRound: string
{
    case Round1 = 'round_1';
    case Round2 = 'round_2';
    case Supplementary = 'supplementary';

    public function label(): string
    {
        return match ($this) {
            self::Round1 => 'Vòng 1',
            self::Round2 => 'Vòng 2',
            self::Supplementary => 'Bổ sung',
        };
    }
}
