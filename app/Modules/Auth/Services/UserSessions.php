<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Hủy phiên đăng nhập của một người dùng ở phía máy chủ (driver session `database`): khi đổi hoặc đặt lại mật khẩu,
 * khóa hoặc ngừng tài khoản, thu hồi phiên. Đổi luôn `remember_token` để cookie "ghi nhớ đăng nhập" trên các thiết bị
 * khác hết tác dụng (nếu không, thiết bị đó tự đăng nhập lại).
 */
class UserSessions
{
    /** @return int số phiên đã hủy */
    public function terminate(User $user, ?string $keepSessionId = null): int
    {
        $user->forceFill(['remember_token' => Str::random(60)])->saveQuietly();

        if (config('session.driver') !== 'database') {
            return 0;
        }

        return DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->when($keepSessionId !== null, fn ($query) => $query->where('id', '!=', $keepSessionId))
            ->delete();
    }
}
