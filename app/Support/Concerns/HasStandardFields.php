<?php

namespace App\Support\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Trường chuẩn của mọi bản ghi (GC-11): người tạo, người sửa gần nhất.
 * Thời điểm tạo và sửa do Eloquent `timestamps` lo.
 *
 * Bảng cần có cột `created_by`, `updated_by`: dùng `$table->standardColumns()` trong migration.
 */
trait HasStandardFields
{
    public static function bootHasStandardFields(): void
    {
        static::creating(function (Model $model): void {
            $userId = Auth::id();

            if ($userId !== null) {
                $model->created_by ??= $userId;
                $model->updated_by ??= $userId;
            }
        });

        static::updating(function (Model $model): void {
            $userId = Auth::id();

            if ($userId !== null && ! $model->isDirty('updated_by')) {
                $model->updated_by = $userId;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
