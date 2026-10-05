<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Http\Requests\AssignRoleRequest;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/** Gán và gỡ vai trò của người dùng (FR-AUTH-008, FR-AUTH-010). */
class UserRoleController extends Controller
{
    public function __construct(private readonly RoleService $roles) {}

    public function index(User $user): JsonResponse
    {
        return response()->json([
            'active' => $user->activeRoles()->orderBy('roles.code')->pluck('roles.code')->unique()->values(),
            'history' => $user->roles()->orderByDesc('user_roles.id')->get()->map(fn (Role $role) => [
                'code' => $role->code,
                'name' => $role->name,
                'valid_from' => $role->pivot->valid_from?->toDateString(),
                'valid_to' => $role->pivot->valid_to?->toDateString(),
            ]),
        ]);
    }

    public function store(AssignRoleRequest $request, User $user): JsonResponse
    {
        $role = Role::findOrFail($request->validated('role_id'));
        $this->roles->assign($user, $role, $request->validated('valid_from'), $request->validated('valid_to'));

        return $this->index($user)->setStatusCode(201);
    }

    public function destroy(User $user, Role $role): Response
    {
        $this->roles->revoke($user, $role);

        return response()->noContent();
    }
}
