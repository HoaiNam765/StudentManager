<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Mật khẩu (FR-AUTH-004, FR-AUTH-005; BR-AUTH-02, BR-AUTH-10).
 * Mật khẩu luôn lưu dạng băm (cast `hashed` của User, mặc định bcrypt); không lưu hay hiển thị bản gốc.
 */
class PasswordService
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly UserSessions $sessions,
    ) {}

    /** Quy tắc mật khẩu mới theo cấu hình; đã đăng ký làm Password::defaults(). */
    public static function rule(): Password
    {
        $config = config('studentmanager.auth.password');
        $rule = Password::min($config['min_length']);

        if ($config['mixed_case']) {
            $rule = $rule->mixedCase();
        }

        if ($config['numbers']) {
            $rule = $rule->numbers();
        }

        if ($config['symbols']) {
            $rule = $rule->symbols();
        }

        return $rule;
    }

    /**
     * Người dùng tự đổi mật khẩu. Các phiên đăng nhập khác bị đăng xuất, trừ phiên $keepSessionId.
     *
     * @throws ValidationException
     */
    public function change(User $user, string $currentPassword, string $newPassword, ?string $keepSessionId = null): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        if ($this->isRecentlyUsed($user, $newPassword)) {
            $count = config('studentmanager.auth.password.history');

            throw ValidationException::withMessages([
                'password' => "Không được dùng lại {$count} mật khẩu gần nhất. Hãy chọn một mật khẩu khác.",
            ]);
        }

        DB::transaction(function () use ($user, $newPassword): void {
            $this->store($user, $newPassword, mustChange: false);
            $this->audit->record(AuditEvent::PasswordChanged, $user);
        });

        $this->logoutOtherSessions($user, $keepSessionId);
    }

    /**
     * Cấp mật khẩu tạm (tài khoản mới hoặc quản trị viên đặt lại): buộc đổi ở lần đăng nhập đầu,
     * hết hạn sau N ngày nếu chưa dùng (BR-AUTH-10). Đăng xuất mọi phiên đang mở.
     */
    public function setTemporaryPassword(User $user, string $temporaryPassword): void
    {
        DB::transaction(function () use ($user, $temporaryPassword): void {
            $this->store($user, $temporaryPassword, mustChange: true);
            $this->audit->record(AuditEvent::PasswordChanged, $user, reason: 'Cấp mật khẩu tạm');
        });

        $this->logoutOtherSessions($user, null);
    }

    /** Buộc đổi mật khẩu: lần đầu đăng nhập, sau khi được cấp mật khẩu tạm, hoặc mật khẩu đã quá hạn. */
    public function mustChange(User $user): bool
    {
        if ($user->must_change_password) {
            return true;
        }

        $days = config('studentmanager.auth.password.expires_days');
        $changedAt = $user->password_changed_at ?? $user->created_at;

        return $days !== null && $changedAt !== null && $changedAt->lt(now()->subDays($days));
    }

    private function store(User $user, string $plainPassword, bool $mustChange): void
    {
        $user->forceFill([
            'password' => $plainPassword,
            'must_change_password' => $mustChange,
            'temporary_password_expires_at' => $mustChange
                ? now()->addDays(config('studentmanager.auth.temporary_password_days'))
                : null,
            'password_changed_at' => now(),
            'remember_token' => Str::random(60), // vô hiệu "ghi nhớ đăng nhập" trên các thiết bị khác
        ])->save();

        $user->passwordHistories()->create(['password' => $user->password]);
    }

    private function isRecentlyUsed(User $user, string $plainPassword): bool
    {
        $count = (int) config('studentmanager.auth.password.history');

        if ($count <= 0) {
            return false;
        }

        if (Hash::check($plainPassword, $user->password)) {
            return true;
        }

        return $user->passwordHistories()
            ->latest('id')
            ->limit($count)
            ->pluck('password')
            ->contains(fn (string $hash) => Hash::check($plainPassword, $hash));
    }

    private function logoutOtherSessions(User $user, ?string $keepSessionId): void
    {
        $this->sessions->terminate($user, $keepSessionId);
    }
}
