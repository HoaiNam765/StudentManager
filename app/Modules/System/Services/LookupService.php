<?php

namespace App\Modules\System\Services;

use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

/**
 * Danh mục dùng chung (FR-SYS-002, BR-SYS-09, GC-05).
 *
 * - Mã ổn định: không đổi sau khi tạo, không cấp lại mã đã dùng (kể cả giá trị đã xóa).
 * - Đã được tham chiếu thì chỉ ngừng sử dụng, không xóa. "Được tham chiếu" do ReferenceRegistry trả lời:
 *   module nào lưu khóa ngoại tới danh mục thì đăng ký ở đó.
 * - Danh sách lựa chọn cho các biểu mẫu: `options(LookupCategory::GENDER)` chỉ trả giá trị đang hoạt động.
 */
class LookupService extends BaseService
{
    public function __construct(private readonly ReferenceRegistry $references) {}

    /** @param  array{code: string, name: string, description?: ?string}  $data */
    public function createCategory(array $data): LookupCategory
    {
        if (LookupCategory::withTrashed()->where('code', $data['code'])->exists()) {
            $this->fail(
                "Mã danh mục {$data['code']} đã được dùng.",
                'Mã danh mục không được cấp lại, kể cả khi danh mục cũ đã xóa; hãy chọn mã khác.'
            );
        }

        return LookupCategory::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_system' => false,
        ]);
    }

    /** @param  array{code?: string, name?: string, description?: ?string, status?: string}  $data */
    public function updateCategory(LookupCategory $category, array $data): LookupCategory
    {
        $this->assertCodeUnchanged($category->code, $data['code'] ?? null, 'danh mục');

        $this->transaction(function () use ($category, $data): void {
            $category->update(array_intersect_key($data, array_flip(['name', 'description'])));
            $this->applyStatus($category, $data['status'] ?? null);
        });

        return $category->refresh();
    }

    public function deleteCategory(LookupCategory $category): void
    {
        if ($category->is_system) {
            $this->fail(
                "Không thể xóa danh mục hệ thống {$category->code}.",
                'Danh mục hệ thống được các module dùng; có thể ngừng từng giá trị không dùng nữa.'
            );
        }

        if ($category->values()->withTrashed()->exists()) {
            $this->fail(
                "Danh mục {$category->code} đã có giá trị.",
                'Hãy ngừng sử dụng danh mục thay cho việc xóa.'
            );
        }

        $category->delete();
    }

    /** @param  array{code: string, name: string, sort_order?: ?int}  $data */
    public function createValue(LookupCategory $category, array $data): LookupValue
    {
        if (! $category->isActive()) {
            $this->fail(
                "Danh mục {$category->code} đang ngừng sử dụng.",
                'Kích hoạt lại danh mục trước khi thêm giá trị.'
            );
        }

        if ($category->values()->withTrashed()->where('code', $data['code'])->exists()) {
            $this->fail(
                "Mã {$data['code']} đã có trong danh mục {$category->code}.",
                'Mã không được cấp lại, kể cả khi giá trị cũ đã xóa; hãy chọn mã khác hoặc kích hoạt lại giá trị cũ.'
            );
        }

        return $category->values()->create([
            'code' => $data['code'],
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? (int) $category->values()->withTrashed()->max('sort_order') + 1,
        ]);
    }

    /** @param  array{code?: string, name?: string, sort_order?: int, status?: string}  $data */
    public function updateValue(LookupValue $value, array $data): LookupValue
    {
        $this->assertCodeUnchanged($value->code, $data['code'] ?? null, 'giá trị danh mục');

        $this->transaction(function () use ($value, $data): void {
            $value->update(array_intersect_key($data, array_flip(['name', 'sort_order'])));
            $this->applyStatus($value, $data['status'] ?? null);
        });

        return $value->refresh();
    }

    /** Xóa (mềm) giá trị chưa từng được dùng; đã dùng thì chỉ được ngừng (BR-SYS-09). */
    public function deleteValue(LookupValue $value): void
    {
        $usages = $this->references->usages($value);

        if ($usages !== []) {
            $this->fail(
                "Không thể xóa \"{$value->name}\" vì đang được dùng bởi ".ReferenceRegistry::describe($usages).'.',
                'Hãy ngừng sử dụng giá trị này; dữ liệu cũ vẫn hiển thị đúng.'
            );
        }

        $value->delete();
    }

    /**
     * Các giá trị đang hoạt động của một danh mục, theo thứ tự hiển thị (dùng cho ô chọn trong biểu mẫu).
     *
     * @return Collection<int, LookupValue>
     */
    public function options(string $categoryCode): Collection
    {
        return LookupValue::query()
            ->active()
            ->whereHas('category', fn ($q) => $q->where('code', $categoryCode)->active())
            ->orderBy('sort_order')
            ->orderByVietnamese('name')
            ->get();
    }

    private function assertCodeUnchanged(string $current, ?string $requested, string $label): void
    {
        if ($requested !== null && $requested !== $current) {
            $this->fail(
                "Không đổi được mã {$label} (mã hiện tại: {$current}).",
                'Mã là khóa ổn định để các dữ liệu khác tham chiếu; nếu cần mã mới, hãy tạo mục mới rồi ngừng sử dụng mục cũ.'
            );
        }
    }

    private function applyStatus(LookupCategory|LookupValue $model, ?string $status): void
    {
        match ($status) {
            'active' => $model->activate(),
            'inactive' => $model->deactivate(),
            default => null,
        };
    }
}
