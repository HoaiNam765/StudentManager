<?php

namespace App\Modules\Auth\Http\Middleware;

use App\Modules\Auth\Services\PasswordService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Buộc đổi mật khẩu trước khi dùng hệ thống (FR-AUTH-004, BR-AUTH-10): lần đăng nhập đầu,
 * sau khi được cấp mật khẩu tạm, hoặc mật khẩu đã quá hạn. Gắn sẵn cho 3 cổng trong bootstrap/app.php.
 */
class EnsurePasswordIsChanged
{
    public function __construct(private readonly PasswordService $passwords) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $this->passwords->mustChange($user)) {
            $message = 'Bạn cần đổi mật khẩu trước khi tiếp tục.';

            // 428: yêu cầu điều kiện trước (không dùng 403 để không bị ghi là "từ chối truy cập")
            return $request->expectsJson()
                ? response()->json(['message' => $message], 428)
                : redirect()->route('password.change')->with('status', $message);
        }

        return $next($request);
    }
}
