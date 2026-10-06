<?php

namespace App\Modules\System\Models;

use App\Modules\System\Enums\ImportRowStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Từng dòng dữ liệu trong lô import (BR-SYS-07).
 * Lưu dữ liệu thô, trạng thái kiểm tra và lỗi theo từng cột.
 */
class ImportRow extends Model
{
    protected $fillable = [
        'import_batch_id',
        'row_number',
        'raw_data',
        'status',
        'errors',
        'saved_model_id',
        'saved_model_type',
    ];

    protected $casts = [
        'row_number' => 'integer',
        'raw_data' => 'array',
        'status' => ImportRowStatus::class,
        'errors' => 'array',
        'saved_model_id' => 'integer',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class, 'import_batch_id');
    }

    public function isValid(): bool
    {
        return $this->status === ImportRowStatus::Valid;
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }

    /**
     * Số lỗi của dòng.
     */
    public function errorCount(): int
    {
        return count($this->errors ?? []);
    }

    /**
     * Đánh dấu dòng hợp lệ.
     */
    public function markValid(): void
    {
        $this->update(['status' => ImportRowStatus::Valid, 'errors' => null]);
    }

    /**
     * Đánh dấu dòng có lỗi, kèm chi tiết lỗi theo cột.
     *
     * @param  array<int, array{column: string, message: string}>  $errors
     */
    public function markInvalid(array $errors): void
    {
        $this->update(['status' => ImportRowStatus::Invalid, 'errors' => $errors]);
    }

    /**
     * Đánh dấu đã lưu.
     */
    public function markSaved(string $modelType, int|string $modelId): void
    {
        $this->update([
            'status' => ImportRowStatus::Saved,
            'saved_model_type' => $modelType,
            'saved_model_id' => $modelId,
        ]);
    }

    /**
     * Đánh dấu bỏ qua (dòng lỗi khi chế độ chỉ lưu hợp lệ).
     */
    public function markSkipped(): void
    {
        $this->update(['status' => ImportRowStatus::Skipped]);
    }
}
