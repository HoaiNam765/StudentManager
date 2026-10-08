<?php

namespace App\Support\Concerns;

use App\Support\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trạng thái Hoạt động / Ngừng hoạt động cho dữ liệu danh mục (GC-05).
 *
 * Chỉ dùng cho danh mục (khoa, phòng, học phần…). Những thứ có vòng đời riêng
 * như sinh viên hay lớp học phần có máy trạng thái riêng, không dùng trait này.
 *
 * Bảng cần có cột `status`: dùng `$table->activeStatus()` trong migration.
 */
trait HasActiveStatus
{
    public function initializeHasActiveStatus(): void
    {
        $this->mergeCasts(['status' => ActiveStatus::class]);

        if (! array_key_exists('status', $this->attributes)) {
            $this->attributes['status'] = ActiveStatus::Active->value;
        }
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), ActiveStatus::Active->value);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), ActiveStatus::Inactive->value);
    }

    public function isActive(): bool
    {
        return $this->status === ActiveStatus::Active;
    }

    /** Nhãn tiếng Việt của trạng thái (`$model->status_label`), dùng khi xuất Excel hoặc hiển thị. */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function activate(): bool
    {
        return $this->forceFill(['status' => ActiveStatus::Active])->save();
    }

    public function deactivate(): bool
    {
        return $this->forceFill(['status' => ActiveStatus::Inactive])->save();
    }
}
