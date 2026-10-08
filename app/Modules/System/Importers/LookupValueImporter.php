<?php

namespace App\Modules\System\Importers;

use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use App\Support\References\ReferenceRegistry;

/**
 * Nhập giá trị danh mục dùng chung từ file (FR-SYS-002 "import danh mục chuẩn").
 * Cột: category_code (mã danh mục, ví dụ ETHNICITY), code, name, sort_order (tùy chọn).
 * Chỉ thêm giá trị mới; mã đã có trong danh mục thì báo lỗi ở dòng đó (mã ổn định, không ghi đè).
 */
class LookupValueImporter extends SpreadsheetImporter
{
    public const KEY = 'lookup_values';

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Danh mục dùng chung (giới tính, dân tộc, tôn giáo…)';
    }

    public function description(): string
    {
        return 'Cột: category_code (mã danh mục, ví dụ ETHNICITY), code (mã giá trị, để định dạng Text), name, sort_order (tùy chọn). Chỉ thêm mã mới, không sửa mã đã có.';
    }

    protected function columns(): array
    {
        return ['required' => ['category_code', 'code', 'name'], 'optional' => ['sort_order']];
    }

    public function validateRow(int $rowNumber, array $rawRow): array
    {
        $errors = [];
        $category = $this->category($rawRow['category_code'] ?? '');
        $code = $rawRow['code'] ?? '';

        if ($category === null) {
            $errors[] = ['column' => 'category_code', 'message' => 'Không có danh mục mã "'.($rawRow['category_code'] ?? '').'" đang hoạt động.'];
        }

        if ($code === '' || preg_match('/^[A-Za-z0-9_.\-]{1,30}$/', $code) !== 1) {
            $errors[] = ['column' => 'code', 'message' => 'Mã bắt buộc, tối đa 30 ký tự, chỉ gồm chữ không dấu, số, dấu chấm, gạch ngang, gạch dưới.'];
        } elseif ($category !== null && $category->values()->withTrashed()->where('code', $code)->exists()) {
            $errors[] = ['column' => 'code', 'message' => "Mã {$code} đã có trong danh mục {$category->code}; mã không được ghi đè hay cấp lại."];
        }

        $name = $rawRow['name'] ?? '';

        if ($name === '' || mb_strlen($name) > 255) {
            $errors[] = ['column' => 'name', 'message' => 'Tên bắt buộc, tối đa 255 ký tự.'];
        }

        $order = $rawRow['sort_order'] ?? '';

        if ($order !== '' && ! ctype_digit($order)) {
            $errors[] = ['column' => 'sort_order', 'message' => 'Thứ tự phải là số nguyên không âm.'];
        }

        return $errors;
    }

    public function saveRow(int $rowNumber, array $rawRow): array
    {
        $category = $this->category($rawRow['category_code']);
        $order = $rawRow['sort_order'] ?? '';

        $value = $category->values()->create([
            'code' => $rawRow['code'],
            'name' => $rawRow['name'],
            'sort_order' => $order === '' ? (int) $category->values()->withTrashed()->max('sort_order') + 1 : (int) $order,
        ]);

        return ['type' => 'lookup_value', 'id' => $value->id];
    }

    public function canRollbackRow(string $modelType, int|string $modelId): bool
    {
        $value = LookupValue::query()->find($modelId);

        return $value === null || ! app(ReferenceRegistry::class)->isReferenced($value);
    }

    public function rollbackRow(string $modelType, int|string $modelId): bool
    {
        // Giá trị do chính lô này tạo và chưa được dùng: xóa hẳn để mã có thể nhập lại
        $value = LookupValue::withTrashed()->find($modelId);

        return $value === null || (bool) $value->forceDelete();
    }

    // Không nhớ đệm: Importer là singleton sống suốt tiến trình worker, danh mục có thể bị ngừng giữa hai lô
    private function category(string $code): ?LookupCategory
    {
        return LookupCategory::query()->where('code', $code)->active()->first();
    }
}
