<?php

namespace App\Modules\Auth\Http\Middleware;

use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Chặn route theo ma trận phân quyền, mặc định từ chối (FR-AUTH-013).
 *
 *     ->middleware('permission:STU.view')        // có quyền xem STU ở bất kỳ phạm vi nào
 *     ->middleware('permission:AUTH.update,ALL') // phải có quyền trên toàn trường
 *
 * Middleware chỉ kiểm tra "có quyền hay không"; quyền trên từng bản ghi vẫn phải kiểm tra
 * bằng Policy (authorize) và danh sách phải lọc bằng AccessControl::constrain().
 */
class EnsurePermission
{
    public function __construct(private readonly AccessControl $access) {}

    public function handle(Request $request, Closure $next, string $permission, ?string $scope = null): Response
    {
        [$module, $action] = explode('.', $permission, 2);
        $scopes = $this->access->scopesFor($request->user(), $module, PermissionAction::from($action));

        $allowed = $scope === null
            ? $scopes !== []
            : in_array(DataScope::from($scope), $scopes, true);

        abort_unless($allowed, 403, 'Bạn không có quyền thực hiện thao tác này.');

        return $next($request);
    }
}
