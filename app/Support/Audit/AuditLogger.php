<?php

namespace App\Support\Audit;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Ghi nhật ký kiểm toán (FR-SYS-004).
 *
 * Thay đổi của model kế thừa StandardModel được ghi tự động (trait Auditable).
 * Hành động không phải tạo/sửa/xóa thì ghi thủ công:
 *
 *     app(AuditLogger::class)->record(AuditEvent::ViewSensitive, $student, reason: 'Xem đầy đủ số CCCD');
 *
 * Gắn lý do cho các thay đổi bên trong (BA yêu cầu thấy được lý do khi tra nhật ký):
 *
 *     app(AuditLogger::class)->withReason('Sửa sai sót khi nhập', fn () => $grade->update([...]));
 */
class AuditLogger
{
    private ?string $reason = null;

    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    public function record(
        AuditEvent $event,
        ?Model $subject = null,
        array $oldValues = [],
        array $newValues = [],
        ?string $reason = null,
    ): AuditLog {
        $user = Auth::user();
        [$ip, $userAgent, $url] = $this->origin();

        return AuditLog::create([
            'event' => $event,
            'auditable_type' => $subject?->getMorphClass(),
            'auditable_id' => $subject?->getKey(),
            'user_id' => $user?->getAuthIdentifier(),
            'user_name' => $user === null ? null : data_get($user, 'name'),
            'old_values' => $oldValues === [] ? null : $oldValues,
            'new_values' => $newValues === [] ? null : $newValues,
            'reason' => $reason ?? $this->reason,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'url' => $url,
        ]);
    }

    /** Mọi nhật ký ghi trong $callback mang lý do $reason. */
    public function withReason(string $reason, Closure $callback): mixed
    {
        $previous = $this->reason;
        $this->reason = $reason;

        try {
            return $callback();
        } finally {
            $this->reason = $previous;
        }
    }

    /** @return array{0: ?string, 1: ?string, 2: ?string} IP, thiết bị (user agent), đường dẫn */
    private function origin(): array
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return [null, 'console', null];
        }

        $request = request();

        return [
            $request->ip(),
            Str::limit((string) $request->userAgent(), 500, ''),
            Str::limit($request->fullUrl(), 500, ''),
        ];
    }
}
