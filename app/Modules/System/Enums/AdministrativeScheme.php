<?php

namespace App\Modules\System\Enums;

/**
 * Mô hình đơn vị hành chính (BR-STU-09): hai cấp từ 01/07/2025, dữ liệu ba cấp cũ vẫn tra cứu được.
 */
enum AdministrativeScheme: string
{
    case TwoLevel2025 = 'two_level_2025';
    case ThreeLevelLegacy = 'three_level_legacy';

    public function label(): string
    {
        return match ($this) {
            self::TwoLevel2025 => 'Hai cấp (từ 01/07/2025)',
            self::ThreeLevelLegacy => 'Ba cấp (trước 01/07/2025)',
        };
    }

    /** Các cấp có trong mô hình, theo thứ tự từ trên xuống. */
    public function levels(): array
    {
        return match ($this) {
            self::TwoLevel2025 => [AdministrativeLevel::Province, AdministrativeLevel::Commune],
            self::ThreeLevelLegacy => [AdministrativeLevel::Province, AdministrativeLevel::District, AdministrativeLevel::Commune],
        };
    }

    /** Cấp cha bắt buộc của $level trong mô hình này; null là cấp cao nhất. */
    public function parentLevelOf(AdministrativeLevel $level): ?AdministrativeLevel
    {
        $levels = $this->levels();
        $index = array_search($level, $levels, true);

        return $index === false || $index === 0 ? null : $levels[$index - 1];
    }
}
