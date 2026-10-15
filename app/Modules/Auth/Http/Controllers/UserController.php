<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Enums\AccountStatus;
use App\Modules\Auth\Http\Requests\UserRequest;
use App\Modules\Auth\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API quản lý người dùng (FR-AUTH-008). Quyền: `AUTH.*` phạm vi toàn trường ở routes/admin.php (mặc định ADMIN).
 * Tạo hàng loạt (FR-AUTH-009) đi qua trung tâm import với Importer `users` (xem trước, báo lỗi từng dòng, hoàn tác lô).
 */
class UserController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function index(Request $request): JsonResponse
    {
        $q = $request->string('q')->trim()->toString();

        $users = User::query()
            ->with('activeRoles:id,code,name')
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w->where('username', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%")))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('profile_type'), fn ($query) => $query->where('profile_type', $request->string('profile_type')->toString()))
            ->when($request->filled('role'), fn ($query) => $query->whereHas('activeRoles', fn ($r) => $r->where('roles.code', $request->string('role')->toString())))
            ->orderBy('username')
            ->paginate(50);

        return response()->json($users->through(fn (User $user) => $this->present($user)));
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($this->present($user->load('activeRoles')) + [
            'must_change_password' => (bool) $user->must_change_password,
            'temporarily_locked_until' => $user->locked_until?->isFuture() ? $user->locked_until->toIso8601String() : null,
            'last_login_at' => $user->last_login_at?->toIso8601String(),
            'status_reason' => $user->status_reason,
            'deactivate_at' => $user->deactivate_at?->toIso8601String(),
        ]);
    }

    public function store(UserRequest $request): JsonResponse
    {
        return response()->json($this->present($this->users->create($request->validated(), $request->user())->load('activeRoles')), 201);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        return response()->json($this->present($this->users->update($user, $request->validated())->load('activeRoles')));
    }

    public function lock(Request $request, User $user): JsonResponse
    {
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'lý do khóa'])['reason'];

        return response()->json($this->present($this->users->lock($user, $reason, $request->user())));
    }

    public function unlock(Request $request, User $user): JsonResponse
    {
        return response()->json($this->present($this->users->unlock($user, $request->user())));
    }

    public function deactivate(Request $request, User $user): JsonResponse
    {
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'lý do ngừng'])['reason'];

        return response()->json($this->present($this->users->deactivate($user, $reason, $request->user())));
    }

    public function activate(Request $request, User $user): JsonResponse
    {
        return response()->json($this->present($this->users->activate($user, $request->user())));
    }

    /** Cấp lại mật khẩu tạm, gửi qua email; không trả mật khẩu trong phản hồi (BR-AUTH-02). */
    public function issueTemporaryPassword(User $user): JsonResponse
    {
        $this->users->issueTemporaryPassword($user);

        return response()->json(['message' => "Đã gửi mật khẩu tạm mới tới email của {$user->username}."]);
    }

    /** @return array<string, mixed> */
    private function present(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'status' => ($user->status ?? AccountStatus::Active)->value,
            'status_label' => ($user->status ?? AccountStatus::Active)->label(),
            'profile_type' => $user->profile_type?->value,
            'profile_id' => $user->profile_id,
            'roles' => $user->activeRoles->pluck('code')->unique()->values(),
        ];
    }
}
