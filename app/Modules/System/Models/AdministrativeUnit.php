<?php

namespace App\Modules\System\Models;

use App\Modules\System\Enums\AdministrativeLevel;
use App\Modules\System\Enums\AdministrativeScheme;
use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Đơn vị hành chính (FR-SYS-002, BR-STU-09).
 *
 * - Mô hình hai cấp (từ 01/07/2025): tỉnh / thành phố → xã / phường / đặc khu.
 * - Dữ liệu ba cấp cũ: tỉnh → quận / huyện / thị xã → xã / phường / thị trấn; vẫn tra cứu được và có thể
 *   trỏ tới đơn vị mới thay thế (`successor_id`).
 */
class AdministrativeUnit extends StandardModel
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    protected function casts(): array
    {
        return [
            'scheme' => AdministrativeScheme::class,
            'level' => AdministrativeLevel::class,
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function successor(): BelongsTo
    {
        return $this->belongsTo(self::class, 'successor_id');
    }

    public function scopeScheme(Builder $query, AdministrativeScheme $scheme): Builder
    {
        return $query->where($this->qualifyColumn('scheme'), $scheme->value);
    }

    /** Tên đầy đủ để ghi địa chỉ, ví dụ "Phường Ba Đình, Thành phố Hà Nội". */
    public function fullName(): string
    {
        $parts = [];
        $unit = $this;

        while ($unit !== null) {
            $parts[] = $unit->name;
            $unit = $unit->parent;
        }

        return implode(', ', $parts);
    }
}
