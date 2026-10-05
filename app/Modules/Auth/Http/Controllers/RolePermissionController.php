<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Http\Requests\SyncRolePermissionsRequest;
use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use Illuminate\Http\JsonResponse;

/** Danh mục quyền và ma trận phân quyền của vai trò (FR-AUTH-011, FR-AUTH-012). */
class RolePermissionController extends Controller
{
    public function __construct(private readonly RoleService $roles) {}

    public function index(): JsonResponse
    {
        $modules = Permission::query()
            ->orderBy('module')->orderBy('id')
            ->get()
            ->groupBy('module')
            ->map(fn ($permissions, $module) => [
                'module' => $module,
                'permissions' => $permissions->map(fn (Permission $p) => [
                    'key' => $p->key(),
                    'action' => $p->action->value,
                    'action_label' => $p->action->label(),
                    'name' => $p->name,
                ])->values(),
            ])
            ->values();

        $scopes = collect(DataScope::cases())->map(fn (DataScope $s) => ['value' => $s->value, 'label' => $s->label()]);

        return response()->json(['modules' => $modules, 'scopes' => $scopes]);
    }

    public function update(SyncRolePermissionsRequest $request, Role $role): JsonResponse
    {
        $this->roles->syncPermissions($role, $request->validated('permissions'));

        return response()->json(['code' => $role->code, 'permissions' => $role->permissionMatrix()]);
    }
}
