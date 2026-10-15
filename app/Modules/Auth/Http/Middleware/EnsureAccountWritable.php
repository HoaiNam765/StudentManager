<?php

namespace App\Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tài khoản chỉ đọc (sinh viên đã tốt nghiệp, BR-AUTH-07): được xem, không được thao tác ghi.
 * Gắn vào nhóm `web` cho mọi yêu cầu; vẫn cho đăng xuất và đổi mật khẩu.
 */
class EnsureAccountWritable
{
    /** Route vẫn cho phép ghi với tài khoản chỉ đọc. */
    private const ALLOWED_ROUTES = ['logout', 'password.update'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null
            || in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)
            || ! $user->isReadOnly()
            || in_array($request->route()?->getName(), self::ALLOWED_ROUTES, true)) {
            return $next($request);
        }

        $message = 'Tài khoản đang ở chế độ chỉ đọc (đã tốt nghiệp): chỉ xem bảng điểm và tải giấy tờ. Liên hệ phòng Đào tạo nếu cần thêm quyền.';

        return $request->expectsJson()
            ? response()->json(['message' => $message], 403)
            : back()->withErrors(['account' => $message]);
    }
}
