<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\StoreRoleRequest;
use App\Modules\Auth\Http\Requests\UpdateRoleRequest;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * API quản lý vai trò (FR-AUTH-010). Quyền được kiểm tra bằng middleware ở routes/admin.php.
 * Giao diện quản trị (issue FE #7) gọi các API này hoặc được nối lại bằng Blade sau.
 */
class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roles) {}

    public function index(Request $request): JsonResponse
    {
        $roles = Role::query()
            ->search($request->string('q')->toString())
            ->withCount('permissions')
            ->orderBy('code')
            ->paginate(20);

        return response()->json($roles->through(fn (Role $role) => $this->present($role)));
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json($this->present($role) + ['permissions' => $role->permissionMatrix()]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roles->create($request->validated());

        return response()->json($this->present($role), 201);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        return response()->json($this->present($this->roles->update($role, $request->validated())));
    }

    public function destroy(Role $role): Response
    {
        $this->roles->delete($role);

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function present(Role $role): array
    {
        return [
            'id' => $role->id,
            'code' => $role->code,
            'name' => $role->name,
            'description' => $role->description,
            'is_system' => $role->is_system,
            'status' => $role->status->value,
            'status_label' => $role->status->label(),
            'permissions_count' => $role->permissions_count ?? $role->permissions()->count(),
        ];
    }
}
