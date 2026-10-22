<?php

namespace App\Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Report\Services\AdminDashboardService;
use App\Modules\Report\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardDataController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly AccessControl $access,
    ) {}

    public function student(Request $request): JsonResponse
    {
        abort_unless(
            in_array(
                DataScope::Own,
                $this->access->scopesFor(
                    $request->user(),
                    'STU',
                    PermissionAction::View
                ),
                true
            ),
            403,
            'Bạn không có quyền xem dashboard sinh viên.'
        );

        return response()->json(
            $this->dashboard->student($request->user())
        );
    }

    public function teacher(Request $request): JsonResponse
    {
        abort_unless(
            in_array(
                DataScope::Own,
                $this->access->scopesFor(
                    $request->user(),
                    'TCH',
                    PermissionAction::View
                ),
                true
            ),
            403,
            'Bạn không có quyền xem dashboard giảng viên.'
        );

        return response()->json(
            $this->dashboard->teacher($request->user())
        );
    }

    /** Dashboard quản trị theo vai trò, trong phạm vi RPT.view (#118). */
    public function admin(Request $request, AdminDashboardService $dashboard): JsonResponse
    {
        return response()->json($dashboard->data($request->user()));
    }
}
