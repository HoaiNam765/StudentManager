<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Events\AccountSecurityAlert;
use App\Modules\Auth\Models\LoginHistory;
use App\Modules\Auth\Notifications\UnusualLoginAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Lịch sử đăng nhập (FR-AUTH-016) và cảnh báo đăng nhập bất thường (EV-AUTH-02).
 *
 * - Ghi mọi lần đăng nhập thành công hoặc thất bại: thời điểm, IP, thiết bị, lý do thất bại.
 * - Bất thường: đăng nhập thành công từ một IP **và** một thiết bị chưa từng đăng nhập thành công trong N ngày gần đây
 *   (`studentmanager.auth.unusual_login_lookback_days`, 0 = tắt), với tài khoản đã có lịch sử. Khi đó gửi email cảnh báo
 *   cho chủ tài khoản (mail driver `log` ở P1); module NOT sẽ báo thêm ADMIN qua thông báo trong hệ thống.
 *
 * Đăng ký `scoped`: nhớ lần đăng nhập thành công vừa ghi để controller gắn mã phiên sau khi `session()->regenerate()`.
 */
class LoginHistoryService
{
    private ?LoginHistory $lastSuccess = null;

    public function __construct(private readonly Request $request) {}

    public function recordFailure(?User $user, string $login, string $reason): LoginHistory
    {
        return $this->create($user, $login, successful: false, reason: $reason);
    }

    public function recordSuccess(User $user, string $login): LoginHistory
    {
        $device = DeviceInfo::fromUserAgent($this->request->userAgent());
        $unusual = $this->isUnusual($user, $this->request->ip(), $device->label());

        $entry = $this->create($user, $login, successful: true, unusual: $unusual);

        if ($unusual) {
            $user->notify(new UnusualLoginAlert($entry));
            AccountSecurityAlert::dispatch($user, 'unusual_login', 'login:'.$entry->id);
        }

        return $this->lastSuccess = $entry;
    }

    /** Gắn mã phiên (sau khi regenerate) vào lần đăng nhập thành công vừa ghi trong cùng yêu cầu. */
    public function bindSession(string $sessionId): void
    {
        $this->lastSuccess?->forceFill(['session_id' => $sessionId])->save();
    }

    private function isUnusual(User $user, ?string $ip, string $device): bool
    {
        $days = (int) config('studentmanager.auth.unusual_login_lookback_days');

        if ($days <= 0) {
            return false;
        }

        $recent = LoginHistory::query()
            ->where('user_id', $user->id)
            ->where('successful', true)
            ->where('created_at', '>=', now()->subDays($days));

        // Lần đầu đăng nhập (hoặc lâu rồi không đăng nhập) thì chưa có gì để so
        if (! (clone $recent)->exists()) {
            return false;
        }

        $knownIp = (clone $recent)->where('ip_address', $ip)->exists();
        $knownDevice = (clone $recent)->where('device', $device)->exists();

        return ! $knownIp && ! $knownDevice;
    }

    private function create(?User $user, string $login, bool $successful, ?string $reason = null, bool $unusual = false): LoginHistory
    {
        $userAgent = $this->request->userAgent();
        $device = DeviceInfo::fromUserAgent($userAgent);

        return LoginHistory::query()->create([
            'user_id' => $user?->id,
            'login' => Str::limit(trim($login), 100, ''),
            'successful' => $successful,
            'failure_reason' => $reason,
            'ip_address' => $this->request->ip(),
            'user_agent' => $userAgent !== null ? Str::limit($userAgent, 500, '') : null,
            'device' => $device->label(),
            'device_type' => $device->type,
            'unusual' => $unusual,
        ]);
    }
}
