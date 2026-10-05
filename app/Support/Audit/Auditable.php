<?php

namespace App\Support\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tự ghi nhật ký kiểm toán khi model được tạo, sửa, xóa, khôi phục (GC-03).
 * StandardModel đã dùng sẵn trait này.
 *
 * Tùy chỉnh trong model:
 *     protected array $auditExclude = ['last_seen_at'];  // không ghi các cột này
 *     protected array $auditMasked = ['id_number'];       // ghi là "[đã ẩn]" thay cho giá trị thật
 * Các cột trong $hidden (mật khẩu, token…) luôn được ẩn.
 *
 * Lưu ý: ghi hàng loạt bằng query builder (Model::query()->update()) không qua sự kiện model
 * nên không được ghi nhật ký; thao tác quan trọng phải đi qua Eloquent hoặc ghi thủ công.
 */
trait Auditable
{
    public const AUDIT_MASK = '[đã ẩn]';

    public static function bootAuditable(): void
    {
        static::created(function (Model $model): void {
            $model->writeAudit(AuditEvent::Created, [], $model->auditValues($model->getAttributes()));
        });

        static::updated(function (Model $model): void {
            $new = $model->auditValues($model->getChanges());

            // Chỉ đổi các cột bỏ qua (ví dụ khôi phục chỉ đổi deleted_at) thì không ghi "Cập nhật"
            if ($new === []) {
                return;
            }

            $old = $model->auditValues(array_intersect_key($model->getRawOriginal(), $new));
            $model->writeAudit(AuditEvent::Updated, $old, $new);
        });

        static::deleted(function (Model $model): void {
            // Model có xóa mềm: delete() là Xóa, forceDelete() là Xóa vĩnh viễn
            $event = $model->usesSoftDeletesForAudit() && $model->isForceDeleting()
                ? AuditEvent::ForceDeleted
                : AuditEvent::Deleted;

            $model->writeAudit($event, $model->auditValues($model->getAttributes()), []);
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored(function (Model $model): void {
                $model->writeAudit(AuditEvent::Restored, [], []);
            });
        }
    }

    /**
     * Lọc và che các giá trị trước khi đưa vào nhật ký.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function auditValues(array $attributes): array
    {
        $excluded = array_merge(
            ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'search_text'],
            $this->auditExclude ?? [],
        );
        $masked = array_merge($this->getHidden(), $this->auditMasked ?? []);

        $values = array_diff_key($attributes, array_flip($excluded));

        foreach ($values as $key => $value) {
            if ($value !== null && in_array($key, $masked, true)) {
                $values[$key] = self::AUDIT_MASK;
            }
        }

        return $values;
    }

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     */
    protected function writeAudit(AuditEvent $event, array $old, array $new): void
    {
        app(AuditLogger::class)->record($event, $this, $old, $new);
    }

    protected function usesSoftDeletesForAudit(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this), true);
    }
}
