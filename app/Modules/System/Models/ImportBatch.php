<?php

namespace App\Modules\System\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\System\Enums\ImportStatus;
use App\Support\Concerns\HasStandardFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Lô import (FR-SYS-007, BR-SYS-07).
 * Mỗi lần import là một lô có mã duy nhất.
 * Không dùng StandardModel để tránh ghi audit log tự động
 * (audit log được ghi thủ công khi cần, tránh log spam từ job update progress).
 *
 * Phạm vi dữ liệu: người có quyền toàn trường (ALL) thấy mọi lô; phạm vi OWN chỉ thấy lô do mình tạo.
 */
class ImportBatch extends Model implements HasDataScope
{
    use HasStandardFields;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'importer',
        'original_filename',
        'disk',
        'path',
        'status',
        'total_rows',
        'valid_rows',
        'invalid_rows',
        'saved_rows',
        'save_mode',
        'error_summary',
        'progress',
        'job_id',
        'started_at',
        'finished_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => ImportStatus::class,
        'total_rows' => 'integer',
        'valid_rows' => 'integer',
        'invalid_rows' => 'integer',
        'saved_rows' => 'integer',
        'progress' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Own => $query->where('created_by', $user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public function rows(): HasMany
    {
        return $this->hasMany(ImportRow::class, 'import_batch_id');
    }

    public function validRows(): HasMany
    {
        return $this->hasMany(ImportRow::class, 'import_batch_id')
            ->where('status', 'valid');
    }

    public function invalidRows(): HasMany
    {
        return $this->hasMany(ImportRow::class, 'import_batch_id')
            ->where('status', 'invalid');
    }

    public function savedRows(): HasMany
    {
        return $this->hasMany(ImportRow::class, 'import_batch_id')
            ->where('status', 'saved');
    }

    /** Đã xong xuôi (không xử lý thêm) */
    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    /** Có thể hoàn tác không */
    public function canRollback(): bool
    {
        return $this->status->canRollback();
    }

    /** Đặt tiến trình và lưu */
    public function updateProgress(int $progress, ImportStatus $status): void
    {
        $this->update([
            'progress' => $progress,
            'status' => $status,
        ]);
    }
}
