<?php

namespace App\Modules\Auth\Http\Middleware;

use App\Modules\Auth\Services\PortalResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chỉ cho vào cổng đúng vai trò (docs/BA.md mục 8.1): sinh viên không mở được Cổng Giảng viên / Quản trị và ngược lại,
 * kể cả trang không gắn quyền riêng (trang chủ cổng). Trang HTML thì quay về trang chủ của mình kèm thông báo; JSON trả
 * 403. Chưa đăng nhập thì để middleware `auth` của route xử lý. Gắn cho 3 cổng trong bootstrap/app.php.
 */
class EnsurePortal
{
    public function __construct(private readonly PortalResolver $portals) {}

    public function handle(Request $request, Closure $next, string $portal): Response
    {
        $user = $request->user();

        if ($user === null || in_array($portal, $this->portals->portalsFor($user), true)) {
            return $next($request);
        }

        $message = 'Tài khoản của bạn không dùng được cổng này.';

        if ($request->expectsJson()) {
            abort(403, $message);
        }

        $home = $this->portals->homeRouteFor($user);

        return redirect()->route($home ?? 'login')->with('error', $message);
    }
}
