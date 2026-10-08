<?php

namespace App\Providers;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Policies\AcademicYearPolicy;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Auth\Services\PasswordService;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Policies\AuditLogPolicy;
use App\Modules\System\Policies\ImportBatchPolicy;
use App\Modules\System\Services\ImportRegistry;
use App\Support\Audit\AuditLog;
use App\Support\Audit\AuditLogger;
use App\Support\Database\BlueprintMacros;
use App\Support\References\ReferenceRegistry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Một AuditLogger cho mỗi yêu cầu (lý do gắn bằng withReason không lẫn sang yêu cầu khác)
        $this->app->scoped(AuditLogger::class);

        // Một AccessControl cho mỗi yêu cầu: nhớ quyền đã tính, và RoleService::flush() xóa đúng bộ nhớ đó
        $this->app->scoped(AccessControl::class);

        // Registry trung tâm import: một singleton, các module gọi register() khi boot
        $this->app->singleton(ImportRegistry::class);

        // Sổ đăng ký tham chiếu dùng chung: module nào có khóa ngoại tới danh mục thì đăng ký khi boot
        $this->app->singleton(ReferenceRegistry::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BlueprintMacros::register();

        Gate::policy(
            AuditLog::class,
            AuditLogPolicy::class
        );

        Gate::policy(
            AcademicYear::class,
            AcademicYearPolicy::class
        );

        Gate::policy(
            Term::class,
            AcademicYearPolicy::class
        );

        Gate::policy(ImportBatch::class, ImportBatchPolicy::class);

        // Chính sách mật khẩu theo cấu hình (FR-AUTH-005); dùng ở mọi nơi bằng Password::defaults()
        Password::defaults(fn () => PasswordService::rule());

        $this->configureLoginRateLimit();
    }

    /** Giới hạn tần suất gửi biểu mẫu đăng nhập (NFR-SEC-03), bổ sung cho khóa tạm theo tài khoản. */
    private function configureLoginRateLimit(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $config = config('studentmanager.auth');
            $respond = function (Request $request, array $headers) {
                $message = 'Bạn gửi yêu cầu đăng nhập quá nhiều lần. Vui lòng thử lại sau '
                    .($headers['Retry-After'] ?? 60).' giây.';

                return $request->expectsJson()
                    ? response()->json(['message' => $message], 429, $headers)
                    : back()->withErrors(['login' => $message])->withInput($request->only('login'));
            };

            return [
                Limit::perMinute($config['throttle_per_minute'])
                    ->by('login:'.Str::lower((string) $request->input('login')).'|'.$request->ip())
                    ->response($respond),
                Limit::perMinute($config['throttle_per_ip_per_minute'])
                    ->by('login-ip:'.$request->ip())
                    ->response($respond),
            ];
        });
    }
}
