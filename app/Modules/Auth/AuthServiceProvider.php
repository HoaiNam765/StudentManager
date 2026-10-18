<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Importers\UserImporter;
use App\Modules\Auth\Services\LoginHistoryService;
use App\Modules\System\Services\ImportRegistry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module Auth: Importer tạo tài khoản hàng loạt (FR-AUTH-009), giới hạn tần suất quên mật khẩu,
 * lịch sử đăng nhập (scoped: nhớ lần đăng nhập vừa ghi trong một yêu cầu).
 * Các dịch vụ scoped của phân quyền (AccessControl, AuditLogger) vẫn ở AppServiceProvider như trước.
 */
class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(LoginHistoryService::class);
    }

    public function boot(): void
    {
        $this->app->make(ImportRegistry::class)
            ->registerLazy(UserImporter::KEY, fn () => $this->app->make(UserImporter::class));

        // Biểu mẫu quên mật khẩu và đặt lại: tính riêng từng biểu mẫu theo địa chỉ IP (NFR-SEC-03)
        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute((int) config('studentmanager.auth.password_reset_per_minute'))
            ->by('password-reset:'.$request->route()?->getName().'|'.$request->ip())
            ->response(function (Request $request, array $headers) {
                $message = 'Bạn gửi yêu cầu quá nhiều lần. Vui lòng thử lại sau '.($headers['Retry-After'] ?? 60).' giây.';

                return $request->expectsJson()
                    ? response()->json(['message' => $message], 429, $headers)
                    : back()->withErrors([$request->routeIs('password.store') ? 'token' : 'login' => $message]);
            }));
    }
}
