<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Đăng nhập (FR-AUTH-001, FR-AUTH-006; BR-AUTH-06, BR-AUTH-09, BR-AUTH-10).
 *
 * - Đăng nhập bằng tên đăng nhập (MSSV, mã cán bộ) hoặc email.
 * - Sai, không tồn tại hay đang bị khóa đều nhận CÙNG một thông báo, để không lộ tài khoản có tồn tại.
 * - Sai N lần liên tiếp trong khoảng thời gian cấu hình thì khóa tạm; đang khóa thì đúng mật khẩu cũng bị từ chối.
 * - Mọi lần đăng nhập thành công/thất bại và lần khóa đều ghi nhật ký kiểm toán.
 *
 * Cảnh báo cho chủ tài khoản khi bị khóa (sự kiện EV-AUTH-02) sẽ nối khi có module NOT.
 * Trạng thái tài khoản (khóa hẳn, ngừng hoạt động) thuộc issue quản lý người dùng: kiểm tra thêm trong attempt().
 */
class LoginService
{
    private static ?string $dummyHash = null;

    public function __construct(
        private readonly AuditLogger $audit,
        private readonly PortalResolver $portals,
    ) {}

    /** @throws ValidationException với lỗi ở trường `login` */
    public function attempt(string $login, string $password, bool $remember = false): User
    {
        $login = trim($login);
        $user = User::query()->where('username', $login)->orWhere('email', $login)->first();

        if ($user === null) {
            // Vẫn băm để thời gian phản hồi giống như khi tài khoản tồn tại
            Hash::check($password, self::dummyHash());
            $this->audit->record(AuditEvent::LoginFailed, newValues: ['login' => Str::limit($login, 100, '')], reason: 'Tài khoản không tồn tại');

            throw $this->invalidCredentials();
        }

        if ($user->isLockedOut()) {
            $this->audit->record(AuditEvent::LoginFailed, $user, reason: 'Tài khoản đang tạm khóa');

            throw $this->invalidCredentials();
        }

        if (! Hash::check($password, $user->password)) {
            $this->registerFailure($user);

            throw $this->invalidCredentials();
        }

        // Từ đây người dùng đã chứng minh đúng mật khẩu: được phép biết lý do cụ thể
        if ($user->must_change_password && $user->temporary_password_expires_at?->isPast()) {
            $this->audit->record(AuditEvent::LoginFailed, $user, reason: 'Mật khẩu tạm đã hết hạn');

            throw ValidationException::withMessages(['login' => 'Mật khẩu tạm đã hết hạn. Liên hệ quản trị viên hoặc dùng chức năng quên mật khẩu để nhận mật khẩu mới.']);
        }

        if ($this->portals->homeRouteFor($user) === null) {
            $this->audit->record(AuditEvent::LoginFailed, $user, reason: 'Tài khoản chưa được gán vai trò');

            throw ValidationException::withMessages(['login' => 'Tài khoản chưa được cấp quyền truy cập. Liên hệ phòng Đào tạo hoặc quản trị viên.']);
        }

        $user->forceFill([
            'failed_login_count' => 0,
            'failed_login_started_at' => null,
            'locked_until' => null,
            'last_login_at' => now(),
        ]);

        if (Hash::needsRehash($user->password)) {
            $user->password = $password;
        }

        $user->save();

        Auth::login($user, $remember);
        $this->audit->record(AuditEvent::Login, $user);

        return $user;
    }

    /** Quản trị viên mở khóa trước hạn (FR-AUTH-006); dùng ở màn hình quản lý người dùng. */
    public function unlock(User $user): void
    {
        $user->forceFill(['failed_login_count' => 0, 'failed_login_started_at' => null, 'locked_until' => null])->save();
        $this->audit->record(AuditEvent::Unlocked, $user, reason: 'Mở khóa đăng nhập');
    }

    private function registerFailure(User $user): void
    {
        $config = config('studentmanager.auth');

        DB::transaction(function () use ($user, $config): void {
            $fresh = User::query()->lockForUpdate()->findOrFail($user->id);
            $windowStart = now()->subMinutes($config['lockout_window_minutes']);

            if ($fresh->failed_login_started_at === null || $fresh->failed_login_started_at->lt($windowStart)) {
                $fresh->failed_login_count = 1;
                $fresh->failed_login_started_at = now();
            } else {
                $fresh->failed_login_count++;
            }

            $locked = $fresh->failed_login_count >= $config['lockout_max_attempts'];

            if ($locked) {
                $fresh->locked_until = now()->addMinutes($config['lockout_minutes']);
                $fresh->failed_login_count = 0;
                $fresh->failed_login_started_at = null;
            }

            $fresh->save();

            $this->audit->record(AuditEvent::LoginFailed, $fresh, reason: 'Sai mật khẩu');

            if ($locked) {
                $this->audit->record(
                    AuditEvent::Locked,
                    $fresh,
                    reason: "Nhập sai mật khẩu {$config['lockout_max_attempts']} lần liên tiếp; khóa tạm {$config['lockout_minutes']} phút",
                );
            }
        });
    }

    private function invalidCredentials(): ValidationException
    {
        $minutes = config('studentmanager.auth.lockout_minutes');

        return ValidationException::withMessages([
            'login' => "Tên đăng nhập hoặc mật khẩu không đúng. Nếu nhập sai nhiều lần liên tiếp, tài khoản sẽ tạm khóa {$minutes} phút; có thể dùng chức năng quên mật khẩu.",
        ]);
    }

    private static function dummyHash(): string
    {
        return self::$dummyHash ??= Hash::make(Str::random(32));
    }
}
