<?php

namespace App\Modules\Auth\Http\Middleware;

use App\Modules\Auth\Services\PortalResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kiểm soát cổng HTML theo vai trò (docs/BA.md mục 8.1). Các API dùng chung
 * được phép đi qua cổng /admin theo allowlist tên route + HTTP method, sau đó
 * vẫn phải vượt qua `auth`, `permission` và các policy của từng module.
 * Không đăng nhập thì để middleware `auth` của route xử lý.
 */
class EnsurePortal
{
    /**
     * Các API dữ liệu dùng chung (dù URL cũ có tiền tố /admin) được phân quyền bằng
     * middleware `permission` và policy của từng module, không bằng loại tài khoản
     * truy cập cổng HTML. Chỉ ngoại lệ đúng tên route và HTTP method dưới đây.
     *
     * Không mở rộng thành `admin.*`: trang quản trị, cấu hình và các API nhạy cảm
     * phải tiếp tục bị chặn bởi portal:admin.
     *
     * @var array<string, list<string>>
     */
    private const SHARED_ADMIN_API_ROUTES = [
        'admin.terms.milestones.index' => ['GET'],
        'admin.holidays.days-off' => ['GET'],
        'admin.curricula.index' => ['GET'],
        'admin.units.index' => ['GET'],
        'admin.campuses.index' => ['GET'],
        'admin.rooms.index' => ['GET'],
        'admin.rooms.show' => ['GET'],
        'admin.subjects.index' => ['GET'],
        'admin.subjects.show' => ['GET'],
        'admin.subjects.export' => ['GET'],
        'admin.subjects.grade-components.show' => ['GET'],
        'admin.subjects.relations.index' => ['GET'],
        'admin.subjects.syllabi.index' => ['GET'],
        'admin.lecturers.index' => ['GET'],
        'admin.lecturers.show' => ['GET'],
        'admin.lecturers.export' => ['GET'],
        'admin.leadership-terms.index' => ['GET'],
        'admin.syllabi.file.download' => ['GET'],
        'admin.subjects.syllabi.store' => ['POST'],
        'admin.syllabi.update' => ['PUT'],
        'admin.syllabi.file.upload' => ['POST'],
        'admin.syllabi.submit' => ['POST'],
        'admin.syllabi.destroy' => ['DELETE'],
        'admin.lecturers.update' => ['PUT'],
    ];

    /** Các endpoint tải tệp GET không đặt Accept: application/json. */
    private const SHARED_ADMIN_FILE_ROUTES = [
        'admin.subjects.export',
        'admin.lecturers.export',
        'admin.syllabi.file.download',
    ];

    public function __construct(private readonly PortalResolver $portals) {}

    public function handle(Request $request, Closure $next, string $portal): Response
    {
        $user = $request->user();

        $hasPortalAccess = $user === null || in_array($portal, $this->portals->portalsFor($user), true);
        $isSharedAdminApi = $portal === 'admin' && $this->isSharedAdminApi($request);

        if ($hasPortalAccess || $isSharedAdminApi) {
            return $next($request);
        }

        $message = 'Tài khoản của bạn không dùng được cổng này.';

        if ($request->expectsJson()) {
            abort(403, $message);
        }

        $home = $this->portals->homeRouteFor($user);

        return redirect()->route($home ?? 'login')->with('error', $message);
    }

    private function isSharedAdminApi(Request $request): bool
    {
        $name = $request->route()?->getName();

        if ($name === null || ! in_array($request->method(), self::SHARED_ADMIN_API_ROUTES[$name] ?? [], true)) {
            return false;
        }

        // Không mở cổng HTML qua URL API. Ngoại lệ: xuất Excel/tải PDF trả file.
        return $request->expectsJson() || in_array($name, self::SHARED_ADMIN_FILE_ROUTES, true);
    }
}
