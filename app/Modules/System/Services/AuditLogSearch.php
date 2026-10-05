<?php

namespace App\Modules\System\Services;

use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLog;
use App\Support\Format;
use Carbon\CarbonImmutable;
use Generator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tra cứu nhật ký kiểm toán cho màn hình quản trị (FR-SYS-004): lọc theo người,
 * hành động, đối tượng, khoảng ngày; chuẩn bị dữ liệu để xuất file.
 * Kiểm tra quyền (AuditLogPolicy) ở Controller trước khi gọi.
 */
class AuditLogSearch
{
    /**
     * @param  array{
     *     user_id?: int|string|null,
     *     event?: AuditEvent|string|null,
     *     auditable_type?: string|null,
     *     auditable_id?: int|string|null,
     *     from?: string|null,
     *     to?: string|null,
     * }  $filters  `from`, `to` là ngày theo giờ Việt Nam (Y-m-d), tính trọn ngày
     */
    public function query(array $filters = []): Builder
    {
        $timezone = config('studentmanager.display_timezone');

        return AuditLog::query()
            ->when($filters['user_id'] ?? null, fn (Builder $q, $userId) => $q->where('user_id', $userId))
            ->when($filters['event'] ?? null, fn (Builder $q, $event) => $q->where(
                'event',
                $event instanceof AuditEvent ? $event->value : $event
            ))
            ->when($filters['auditable_type'] ?? null, fn (Builder $q, $type) => $q->where('auditable_type', $type))
            ->when($filters['auditable_id'] ?? null, fn (Builder $q, $id) => $q->where('auditable_id', $id))
            // Ngày người dùng chọn là giờ Việt Nam; dữ liệu lưu UTC nên đổi mốc đầu và cuối ngày sang UTC
            ->when($filters['from'] ?? null, fn (Builder $q, $from) => $q->where(
                'created_at',
                '>=',
                CarbonImmutable::parse($from, $timezone)->startOfDay()->utc()
            ))
            ->when($filters['to'] ?? null, fn (Builder $q, $to) => $q->where(
                'created_at',
                '<=',
                CarbonImmutable::parse($to, $timezone)->endOfDay()->utc()
            ))
            ->orderByDesc('id');
    }

    /**
     * Các dòng để xuất file: dòng đầu là tiêu đề cột. Dùng với dịch vụ xuất Excel dùng chung khi có.
     *
     * @return Generator<int, list<string>>
     */
    public function exportRows(Builder $query): Generator
    {
        yield ['Thời điểm', 'Người thực hiện', 'Hành động', 'Đối tượng', 'Mã đối tượng', 'Giá trị trước', 'Giá trị sau', 'Lý do', 'IP', 'Thiết bị'];

        foreach ($query->cursor() as $log) {
            /** @var AuditLog $log */
            yield [
                Format::dateTime($log->created_at),
                (string) ($log->user_name ?? ''),
                $log->event->label(),
                class_basename((string) $log->auditable_type),
                (string) ($log->auditable_id ?? ''),
                $this->json($log->old_values),
                $this->json($log->new_values),
                (string) ($log->reason ?? ''),
                (string) ($log->ip_address ?? ''),
                (string) ($log->user_agent ?? ''),
            ];
        }
    }

    /** @param  array<string, mixed>|null  $values */
    private function json(?array $values): string
    {
        return $values === null ? '' : (string) json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
