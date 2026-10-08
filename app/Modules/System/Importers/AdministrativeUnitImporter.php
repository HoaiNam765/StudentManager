<?php

namespace App\Modules\System\Importers;

use App\Modules\System\Enums\AdministrativeLevel;
use App\Modules\System\Enums\AdministrativeScheme;
use App\Modules\System\Models\AdministrativeUnit;
use App\Support\References\ReferenceRegistry;

/**
 * Nhập danh mục đơn vị hành chính từ file (FR-SYS-002, BR-STU-09), ví dụ danh sách xã/phường của Cục Thống kê.
 * Cột: scheme (two_level_2025 | three_level_legacy; trống = two_level_2025), level (province | district | commune),
 * code, name, unit_type, parent_code (mã đơn vị cấp trên), successor_code (dữ liệu cũ: mã đơn vị mới thay thế).
 *
 * Đơn vị cấp trên phải có sẵn trong hệ thống trước khi nhập: nhập tỉnh trước, rồi mới nhập xã/phường ở lô sau.
 */
class AdministrativeUnitImporter extends SpreadsheetImporter
{
    public const KEY = 'administrative_units';

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Đơn vị hành chính (tỉnh, xã/phường; dữ liệu ba cấp cũ)';
    }

    public function description(): string
    {
        return 'Cột: scheme (trống = two_level_2025), level, code, name, unit_type, parent_code, successor_code. Nhập đơn vị cấp trên trước (lô riêng), cột mã để định dạng Text.';
    }

    protected function columns(): array
    {
        return [
            'required' => ['level', 'code', 'name', 'unit_type'],
            'optional' => ['scheme', 'parent_code', 'successor_code'],
        ];
    }

    public function validateRow(int $rowNumber, array $rawRow): array
    {
        $errors = [];
        $scheme = AdministrativeScheme::tryFrom(($rawRow['scheme'] ?? '') ?: AdministrativeScheme::TwoLevel2025->value);
        $level = AdministrativeLevel::tryFrom($rawRow['level'] ?? '');
        $code = $rawRow['code'] ?? '';

        if ($scheme === null) {
            $errors[] = ['column' => 'scheme', 'message' => 'Mô hình phải là two_level_2025 hoặc three_level_legacy.'];
        }

        if ($level === null) {
            $errors[] = ['column' => 'level', 'message' => 'Cấp phải là province, district hoặc commune.'];
        } elseif ($scheme !== null && ! in_array($level, $scheme->levels(), true)) {
            $errors[] = ['column' => 'level', 'message' => 'Mô hình hai cấp (từ 01/07/2025) không có cấp huyện.'];
        }

        if (preg_match('/^\d{2,10}$/', $code) !== 1) {
            $errors[] = ['column' => 'code', 'message' => 'Mã gồm 2 đến 10 chữ số (để cột mã ở định dạng Text để giữ số 0 ở đầu).'];
        } elseif ($scheme !== null && AdministrativeUnit::withTrashed()->scheme($scheme)->where('code', $code)->exists()) {
            $errors[] = ['column' => 'code', 'message' => "Mã {$code} đã có trong {$scheme->label()}."];
        }

        if (($rawRow['name'] ?? '') === '') {
            $errors[] = ['column' => 'name', 'message' => 'Tên bắt buộc.'];
        }

        if (($rawRow['unit_type'] ?? '') === '') {
            $errors[] = ['column' => 'unit_type', 'message' => 'Loại đơn vị bắt buộc (Tỉnh, Thành phố, Xã, Phường, Đặc khu…).'];
        }

        if ($scheme !== null && $level !== null) {
            $parentLevel = $scheme->parentLevelOf($level);
            $parentCode = $rawRow['parent_code'] ?? '';

            if ($parentLevel === null && $parentCode !== '') {
                $errors[] = ['column' => 'parent_code', 'message' => 'Cấp tỉnh không có đơn vị cấp trên; bỏ trống cột này.'];
            } elseif ($parentLevel !== null && $this->parent($scheme, $parentLevel, $parentCode) === null) {
                $errors[] = ['column' => 'parent_code', 'message' => "Không có {$parentLevel->label()} mã \"{$parentCode}\" đang hoạt động; nhập đơn vị cấp trên trước."];
            }

            $successorCode = $rawRow['successor_code'] ?? '';

            if ($successorCode !== '' && ($scheme !== AdministrativeScheme::ThreeLevelLegacy || $this->successor($successorCode) === null)) {
                $errors[] = ['column' => 'successor_code', 'message' => 'Chỉ dữ liệu ba cấp cũ mới có đơn vị thay thế, và mã đó phải có trong mô hình hai cấp.'];
            }
        }

        return $errors;
    }

    public function saveRow(int $rowNumber, array $rawRow): array
    {
        $scheme = AdministrativeScheme::from(($rawRow['scheme'] ?? '') ?: AdministrativeScheme::TwoLevel2025->value);
        $level = AdministrativeLevel::from($rawRow['level']);
        $parentLevel = $scheme->parentLevelOf($level);
        $successorCode = $rawRow['successor_code'] ?? '';

        $unit = AdministrativeUnit::create([
            'scheme' => $scheme,
            'level' => $level,
            'code' => $rawRow['code'],
            'name' => $rawRow['name'],
            'unit_type' => $rawRow['unit_type'],
            'parent_id' => $parentLevel === null ? null : $this->parent($scheme, $parentLevel, $rawRow['parent_code'])?->id,
            'successor_id' => $successorCode === '' ? null : $this->successor($successorCode)?->id,
        ]);

        return ['type' => 'administrative_unit', 'id' => $unit->id];
    }

    public function canRollbackRow(string $modelType, int|string $modelId): bool
    {
        $unit = AdministrativeUnit::query()->find($modelId);

        return $unit === null
            || (! $unit->children()->withTrashed()->exists() && ! app(ReferenceRegistry::class)->isReferenced($unit));
    }

    public function rollbackRow(string $modelType, int|string $modelId): bool
    {
        $unit = AdministrativeUnit::withTrashed()->find($modelId);

        return $unit === null || (bool) $unit->forceDelete();
    }

    private function parent(AdministrativeScheme $scheme, AdministrativeLevel $level, string $code): ?AdministrativeUnit
    {
        return $code === '' ? null : AdministrativeUnit::query()->scheme($scheme)->where('level', $level->value)->where('code', $code)->active()->first();
    }

    private function successor(string $code): ?AdministrativeUnit
    {
        return AdministrativeUnit::query()->scheme(AdministrativeScheme::TwoLevel2025)->where('code', $code)->first();
    }
}
