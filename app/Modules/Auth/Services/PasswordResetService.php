<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Quên mật khẩu và đặt lại bằng liên kết (FR-AUTH-003; BR-AUTH-03, BR-AUTH-09).
 *
 * - Mã đặt lại do password broker của Laravel tạo: lưu dạng băm, dùng một lần (xóa sau khi đặt lại),
 *   hết hạn sau `auth.passwords.users.expire` phút (mặc định 30, cấu hình bằng AUTH_PASSWORD_RESET_MINUTES).
 * - Phản hồi yêu cầu luôn giống nhau dù tài khoản có tồn tại hay không, đang bị giới hạn tần suất hay không.
 * - Đặt lại xong: mật khẩu mới theo chính sách, không trùng mật khẩu gần đây, hủy mọi phiên đang mở và mở khóa tạm.
 */
class PasswordResetService
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly PasswordService $passwords,
    ) {}

    /** Gửi liên kết nếu tài khoản tồn tại; không báo kết quả cho người gọi (BR-AUTH-09). */
    public function requestLink(string $login): void
    {
        $login = trim($login);
        $user = User::query()->where('username', $login)->orWhere('email', $login)->first();

        if ($user === null) {
            $this->audit->record(AuditEvent::PasswordResetRequested, newValues: ['login' => Str::limit($login, 100, '')], reason: 'Tài khoản không tồn tại');

            return;
        }

        if (! $this->canReset($user)) {
            $this->audit->record(AuditEvent::PasswordResetRequested, $user, reason: 'Tài khoản đang bị khóa hoặc ngừng hoạt động: không gửi liên kết');

            return;
        }

        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        $this->audit->record(
            AuditEvent::PasswordResetRequested,
            $user,
            reason: $status === Password::RESET_THROTTLED ? 'Yêu cầu quá dày: chưa gửi liên kết mới' : 'Đã gửi liên kết đặt lại mật khẩu',
        );
    }

    /**
     * Đặt mật khẩu mới bằng mã trong liên kết.
     *
     * @throws ValidationException mã sai, đã dùng, hết hạn; hoặc mật khẩu mới không đạt chính sách
     */
    public function reset(string $email, string $token, string $password): User
    {
        $resetUser = null;

        $status = Password::broker()->reset(
            ['email' => $email, 'token' => $token, 'password' => $password],
            function (User $user, string $password) use (&$resetUser): void {
                if (! $this->canReset($user)) {
                    throw $this->invalidLink();
                }

                $this->passwords->reset($user, $password);
                $resetUser = $user;
            },
        );

        if ($status !== Password::PASSWORD_RESET || $resetUser === null) {
            throw $this->invalidLink();
        }

        return $resetUser;
    }

    /** Tài khoản bị khóa hẳn hoặc ngừng hoạt động (quản lý người dùng) thì không tự đặt lại được. */
    private function canReset(User $user): bool
    {
        return $user->canSignIn();
    }

    private function invalidLink(): ValidationException
    {
        $minutes = (int) config('auth.passwords.users.expire');

        return ValidationException::withMessages([
            'token' => "Liên kết đặt lại mật khẩu không hợp lệ, đã được dùng hoặc đã quá {$minutes} phút. Hãy yêu cầu liên kết mới.",
        ]);
    }
}
