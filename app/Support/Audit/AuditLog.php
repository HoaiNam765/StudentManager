<?php

namespace App\Support\Audit;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Một dòng nhật ký kiểm toán. Chỉ ghi thêm: sửa hoặc xóa đều bị chặn
 * (ở đây với Eloquent, và bằng trigger ở CSDL với mọi cách khác).
 *
 * Không tự tạo trực tiếp; dùng AuditLogger::record() hoặc trait Auditable.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event' => AuditEvent::class,
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw AuditLogImmutableException::cannotModify());
        static::deleting(fn () => throw AuditLogImmutableException::cannotModify());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
