<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Models\LoginHistory;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Phiên đăng nhập của một người dùng ở phía máy chủ (driver session `database`; FR-AUTH-007).
 *
 * - Hủy phiên khi đổi hoặc đặt lại mật khẩu, khóa hoặc ngừng tài khoản, người dùng tự thu hồi. Đổi luôn `remember_token`
 *   để cookie "ghi nhớ đăng nhập" trên các thiết bị khác hết tác dụng (nếu không, thiết bị đó tự đăng nhập lại).
 * - Liệt kê các phiên còn hiệu lực (hoạt động gần nhất trong `session.lifetime` phút: hết hạn khi không hoạt động).
 *   Mã phiên thật không bao giờ trả ra ngoài; mỗi phiên có một mã đại diện `handle` (HMAC theo APP_KEY).
 */
class UserSessions
{
    public function __construct(private readonly AuditLogger $audit) {}

    /** @return int số phiên đã hủy */
    public function terminate(User $user, ?string $keepSessionId = null): int
    {
        $user->forceFill(['remember_token' => Str::random(60)])->saveQuietly();

        if (! $this->usesDatabase()) {
            return 0;
        }

        return $this->table()
            ->where('user_id', $user->id)
            ->when($keepSessionId !== null, fn ($query) => $query->where('id', '!=', $keepSessionId))
            ->delete();
    }

    /**
     * Các phiên còn hiệu lực, mới hoạt động nhất trước, phiên hiện tại đánh dấu `is_current`.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function active(User $user, ?string $currentSessionId): Collection
    {
        if (! $this->usesDatabase()) {
            return collect();
        }

        $rows = $this->table()
            ->where('user_id', $user->id)
            ->where('last_activity', '>=', now()->subMinutes((int) config('session.lifetime'))->getTimestamp())
            ->orderByDesc('last_activity')
            ->get(['id', 'ip_address', 'user_agent', 'last_activity']);

        $loggedInAt = LoginHistory::query()
            ->whereIn('session_id', $rows->pluck('id'))
            ->pluck('created_at', 'session_id');

        return $rows->map(function (object $row) use ($currentSessionId, $loggedInAt): array {
            $device = DeviceInfo::fromUserAgent($row->user_agent);

            return [
                'handle' => $this->handle($row->id),
                'is_current' => $row->id === $currentSessionId,
                'device' => $device->label(),
                'device_type' => $device->type,
                'ip_address' => $row->ip_address,
                'user_agent' => $row->user_agent,
                'last_activity_at' => Carbon::createFromTimestamp($row->last_activity),
                'logged_in_at' => isset($loggedInAt[$row->id]) ? Carbon::parse($loggedInAt[$row->id]) : null,
            ];
        })->values();
    }

    /**
     * Người dùng thu hồi một phiên khác của mình. Muốn thoát phiên hiện tại thì dùng đăng xuất.
     *
     * @throws BusinessRuleException
     */
    public function revoke(User $user, string $handle, ?string $currentSessionId): void
    {
        $sessionId = $this->table()
            ->where('user_id', $user->id)
            ->pluck('id')
            ->first(fn (string $id) => hash_equals($this->handle($id), $handle));

        if ($sessionId === null) {
            throw new BusinessRuleException('Không tìm thấy phiên đăng nhập này; có thể phiên đã hết hạn hoặc đã bị thu hồi.', 'Tải lại trang để xem danh sách phiên mới nhất.');
        }

        if ($sessionId === $currentSessionId) {
            throw new BusinessRuleException('Không thu hồi được phiên bạn đang dùng.', 'Dùng nút "Đăng xuất" để thoát khỏi thiết bị này.');
        }

        $this->table()->where('id', $sessionId)->delete();
        // Thiết bị bị thu hồi có thể còn cookie "ghi nhớ đăng nhập": đổi remember_token để nó không tự đăng nhập lại
        $user->forceFill(['remember_token' => Str::random(60)])->saveQuietly();

        $this->audit->record(AuditEvent::SessionRevoked, $user, reason: 'Thu hồi một phiên đăng nhập');
    }

    /** Đăng xuất khỏi mọi thiết bị khác, giữ phiên hiện tại. @return int số phiên đã hủy */
    public function revokeOthers(User $user, string $currentSessionId): int
    {
        $count = $this->terminate($user, $currentSessionId);

        $this->audit->record(AuditEvent::SessionRevoked, $user, newValues: ['count' => $count], reason: 'Đăng xuất khỏi các thiết bị khác');

        return $count;
    }

    public function handle(string $sessionId): string
    {
        return substr(hash_hmac('sha256', $sessionId, (string) config('app.key')), 0, 32);
    }

    private function usesDatabase(): bool
    {
        return config('session.driver') === 'database';
    }

    private function table(): Builder
    {
        return DB::table(config('session.table', 'sessions'));
    }
}
