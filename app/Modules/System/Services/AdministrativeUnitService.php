<?php

namespace App\Modules\System\Services;

use App\Modules\System\Enums\AdministrativeLevel;
use App\Modules\System\Enums\AdministrativeScheme;
use App\Modules\System\Models\AdministrativeUnit;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;

/**
 * Đơn vị hành chính (FR-SYS-002, BR-STU-09, BR-SYS-09).
 *
 * - Hai cấp (từ 01/07/2025): tỉnh không có cha; xã/phường/đặc khu thuộc đúng một tỉnh; không có cấp huyện.
 * - Ba cấp cũ: tỉnh → huyện → xã. Đơn vị cũ có thể trỏ tới đơn vị mới thay thế (`successor_id`).
 * - Mã, mô hình, cấp và đơn vị cha không đổi sau khi tạo; đã có đơn vị con hoặc đã được tham chiếu thì
 *   chỉ ngừng sử dụng, không xóa.
 */
class AdministrativeUnitService extends BaseService
{
    public function __construct(private readonly ReferenceRegistry $references) {}

    /**
     * @param  array{scheme: string, level: string, code: string, name: string, unit_type: string, parent_id?: ?int, successor_id?: ?int}  $data
     */
    public function create(array $data): AdministrativeUnit
    {
        $scheme = AdministrativeScheme::from($data['scheme']);
        $level = AdministrativeLevel::from($data['level']);

        if (! in_array($level, $scheme->levels(), true)) {
            $this->fail(
                "Mô hình {$scheme->label()} không có cấp {$level->label()}.",
                'Từ 01/07/2025 chỉ còn cấp tỉnh và cấp xã; cấp huyện chỉ dùng cho dữ liệu ba cấp cũ.'
            );
        }

        $parentId = $this->resolveParent($scheme, $level, $data['parent_id'] ?? null);

        if (AdministrativeUnit::withTrashed()->scheme($scheme)->where('code', $data['code'])->exists()) {
            $this->fail(
                "Mã {$data['code']} đã có trong {$scheme->label()}.",
                'Mỗi mã đơn vị hành chính chỉ dùng một lần trong cùng mô hình; kiểm tra lại mã theo danh mục của Cục Thống kê.'
            );
        }

        return AdministrativeUnit::create([
            'scheme' => $scheme,
            'level' => $level,
            'code' => $data['code'],
            'name' => $data['name'],
            'unit_type' => $data['unit_type'],
            'parent_id' => $parentId,
            'successor_id' => $this->resolveSuccessor($scheme, $data['successor_id'] ?? null),
        ]);
    }

    /** @param  array{name?: string, unit_type?: string, successor_id?: ?int, status?: string, code?: string, parent_id?: ?int}  $data */
    public function update(AdministrativeUnit $unit, array $data): AdministrativeUnit
    {
        if (array_key_exists('code', $data) && $data['code'] !== $unit->code) {
            $this->fail(
                "Không đổi được mã đơn vị hành chính {$unit->code}.",
                'Khi đơn vị bị sáp nhập hoặc đổi mã, hãy tạo đơn vị mới, ngừng đơn vị cũ và trỏ đơn vị cũ tới đơn vị mới.'
            );
        }

        if (array_key_exists('parent_id', $data) && (int) $data['parent_id'] !== (int) $unit->parent_id) {
            $this->fail(
                'Không đổi được đơn vị cấp trên của một đơn vị hành chính.',
                'Khi đơn vị chuyển về tỉnh khác, hãy tạo đơn vị mới và ngừng đơn vị cũ.'
            );
        }

        $this->transaction(function () use ($unit, $data): void {
            $changes = array_intersect_key($data, array_flip(['name', 'unit_type']));

            if (array_key_exists('successor_id', $data)) {
                $changes['successor_id'] = $this->resolveSuccessor($unit->scheme, $data['successor_id']);
            }

            $unit->update($changes);

            match ($data['status'] ?? null) {
                'active' => $this->activate($unit),
                'inactive' => $this->deactivate($unit),
                default => null,
            };
        });

        return $unit->refresh();
    }

    public function delete(AdministrativeUnit $unit): void
    {
        if ($unit->children()->withTrashed()->exists()) {
            $this->fail(
                "Không thể xóa {$unit->name} vì còn đơn vị trực thuộc.",
                'Hãy ngừng sử dụng đơn vị thay cho việc xóa.'
            );
        }

        $usages = $this->references->usages($unit);

        if ($usages !== []) {
            $this->fail(
                "Không thể xóa {$unit->name} vì đang được dùng bởi ".ReferenceRegistry::describe($usages).'.',
                'Hãy ngừng sử dụng đơn vị; địa chỉ đã lưu vẫn hiển thị đúng.'
            );
        }

        $unit->delete();
    }

    /**
     * Danh sách để chọn địa chỉ: mặc định mô hình hai cấp, chỉ đơn vị đang hoạt động.
     *
     * @return Builder<AdministrativeUnit>
     */
    public function options(AdministrativeScheme $scheme, ?int $parentId = null, ?AdministrativeLevel $level = null, ?string $term = null): Builder
    {
        return AdministrativeUnit::query()
            ->scheme($scheme)
            ->active()
            ->when($parentId !== null, fn (Builder $q) => $q->where('parent_id', $parentId))
            ->when($parentId === null && $level === null, fn (Builder $q) => $q->whereNull('parent_id'))
            ->when($level !== null, fn (Builder $q) => $q->where('level', $level->value))
            ->search($term)
            ->orderBy('code');
    }

    private function deactivate(AdministrativeUnit $unit): void
    {
        $activeChildren = $unit->children()->active()->count();

        if ($activeChildren > 0) {
            $this->fail(
                "{$unit->name} còn {$activeChildren} đơn vị trực thuộc đang hoạt động.",
                'Ngừng hoặc chuyển các đơn vị trực thuộc trước, rồi mới ngừng đơn vị cấp trên.'
            );
        }

        $unit->deactivate();
    }

    private function activate(AdministrativeUnit $unit): void
    {
        if ($unit->parent !== null && ! $unit->parent->isActive()) {
            $this->fail(
                "Đơn vị cấp trên {$unit->parent->name} đang ngừng sử dụng.",
                'Kích hoạt đơn vị cấp trên trước.'
            );
        }

        $unit->activate();
    }

    private function resolveParent(AdministrativeScheme $scheme, AdministrativeLevel $level, ?int $parentId): ?int
    {
        $parentLevel = $scheme->parentLevelOf($level);

        if ($parentLevel === null) {
            if ($parentId !== null) {
                $this->fail(
                    "{$level->label()} là cấp cao nhất nên không có đơn vị cấp trên.",
                    'Bỏ trống đơn vị cấp trên.'
                );
            }

            return null;
        }

        $parent = $parentId === null ? null : AdministrativeUnit::query()->find($parentId);

        if ($parent === null || $parent->scheme !== $scheme || $parent->level !== $parentLevel) {
            $this->fail(
                "{$level->label()} phải thuộc một {$parentLevel->label()} của cùng mô hình {$scheme->label()}.",
                'Chọn đúng đơn vị cấp trên (ví dụ xã/phường thuộc tỉnh/thành phố).'
            );
        }

        if (! $parent->isActive()) {
            $this->fail(
                "Đơn vị cấp trên {$parent->name} đang ngừng sử dụng.",
                'Chọn đơn vị cấp trên đang hoạt động hoặc kích hoạt lại đơn vị đó.'
            );
        }

        return $parent->id;
    }

    private function resolveSuccessor(AdministrativeScheme $scheme, ?int $successorId): ?int
    {
        if ($successorId === null) {
            return null;
        }

        if ($scheme !== AdministrativeScheme::ThreeLevelLegacy) {
            $this->fail(
                'Chỉ đơn vị thuộc dữ liệu ba cấp cũ mới trỏ tới đơn vị thay thế.',
                'Bỏ trống đơn vị thay thế.'
            );
        }

        $successor = AdministrativeUnit::query()->find($successorId);

        if ($successor === null || $successor->scheme !== AdministrativeScheme::TwoLevel2025) {
            $this->fail(
                'Đơn vị thay thế phải thuộc mô hình hai cấp từ 01/07/2025.',
                'Chọn tỉnh hoặc xã/phường mới đã tiếp nhận đơn vị cũ.'
            );
        }

        return $successor->id;
    }
}
