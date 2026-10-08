<?php

use App\Modules\Auth\Http\Middleware\EnsureAccountWritable;
use App\Modules\Auth\Http\Middleware\EnsurePasswordIsChanged;
use App\Modules\Auth\Http\Middleware\EnsurePermission;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            // Ba cổng giao diện theo docs/BA.md mục 8.1; trang công khai nằm ở routes/web.php.
            // password.changed: chưa đổi mật khẩu tạm / mật khẩu quá hạn thì chưa dùng được các cổng.
            Route::middleware(['web', 'password.changed'])->prefix('student')->name('student.')
                ->group(base_path('routes/student.php'));
            Route::middleware(['web', 'password.changed'])->prefix('teacher')->name('teacher.')
                ->group(base_path('routes/teacher.php'));
            Route::middleware(['web', 'password.changed'])->prefix('admin')->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => EnsurePermission::class,
            'password.changed' => EnsurePasswordIsChanged::class,
        ]);

        // Tài khoản chỉ đọc (sinh viên đã tốt nghiệp, BR-AUTH-07) không thao tác ghi ở bất kỳ trang nào
        $middleware->web(append: [EnsureAccountWritable::class]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        // Đã đăng nhập mà mở trang đăng nhập thì về trang chủ theo vai trò
        $middleware->redirectUsersTo(fn (Request $request) => route('root'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Vi phạm quy tắc nghiệp vụ không phải lỗi hệ thống: báo lý do và cách khắc phục (GC-04, UX-09)
        $exceptions->render(function (BusinessRuleException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->userMessage()], 422);
            }

            return back()->withInput()->withErrors(['business_rule' => $e->userMessage()]);
        });

        // Truy cập trái quyền bị từ chối thì ghi nhật ký (UAT-02, NFR-SEC-08); vẫn trả trang 403 như thường
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($e->getStatusCode() === 403) {
                try {
                    app(AuditLogger::class)->record(
                        AuditEvent::AccessDenied,
                        reason: Str::limit($e->getMessage() !== '' ? $e->getMessage() : 'Từ chối truy cập', 500, ''),
                    );
                } catch (Throwable $logError) {
                    report($logError);
                }
            }

            return null;
        });
    })->create();
