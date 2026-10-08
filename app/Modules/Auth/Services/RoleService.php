<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Models\Role;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Quản lý vai trò, ma trận phân quyền và vai trò của người dùng (FR-AUTH-010, 011, 012).
 * Mọi thay đổi có hiệu lực ngay và được ghi nhật ký kiểm toán (BR-AUTH-08).
 *
 * Thông báo cho người bị ảnh hưởng (sự kiện EV-AUTH-03) sẽ nối khi có module NOT.
 */
class RoleService extends BaseService
{
    /** ADMIN phải luôn giữ các quyền này trên toàn trường, nếu không sẽ không ai quản lý được phân quyền. */
    public const ADMIN_REQUIRED_PERMISSIONS = ['AUTH.view', 'AUTH.update'];

    public function __construct(
        private readonly AccessControl $access,
        private readonly AuditLogger $audit,
    ) {}

    /** @param  array{code: string, name: string, description?: string|null}  $data */
    public function create(array $data): Role
    {
        return Role::create([
            'code' => Str::upper($data['code']),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_system' => false,
        ]);
    }

    /** @param  array{name?: string, description?: string|null, status?: string}  $data */
    public function update(Role $role, array $data): Role
    {
        $this->transaction(function () use ($role, $data): void {
            $role->update(array_intersect_key($data, array_flip(['name', 'description'])));

            match ($data['status'] ?? null) {
                'active' => $role->activate(),
                'inactive' => $this->deactivate($role),
                default => null,
            };
        });

        $this->access->flush();

        return $role->refresh();
    }

    public function deactivate(Role $role): void
    {
        if ($role->code === Role::ADMIN) {
            $this->fail(
                'Không thể ngừng vai trò ADMIN.',
                'Vai trò này cần để quản lý tài khoản và phân quyền; hãy gỡ ADMIN khỏi từng người dùng nếu cần.'
            );
        }

        $role->deactivate();
        $this->access->flush();
    }

    public function delete(Role $role): void
    {
        if ($role->is_system) {
            $this->fail(
                "Không thể xóa vai trò hệ thống mặc định {$role->code}.",
                'Có thể ngừng sử dụng vai trò thay cho việc xóa.'
            );
        }

        if ($this->activeAssignments($role)->exists()) {
            $this->fail(
                "Vai trò {$role->code} đang được gán cho người dùng.",
                'Gỡ vai trò khỏi các người dùng trước, hoặc ngừng sử dụng vai trò.'
            );
        }

        $role->delete();
        $this->access->flush();
    }

    /**
     * Thay toàn bộ quyền của vai trò bằng ma trận mới.
     *
     * @param  array<string, string>  $matrix  ['STU.view' => 'ALL', 'GRD.update' => 'SECTION', ...]
     */
    public function syncPermissions(Role $role, array $matrix): void
    {
        $permissions = Permission::all()->keyBy(fn (Permission $p) => $p->key());
        $sync = [];

        foreach ($matrix as $key => $scope) {
            $permission = $permissions->get($key) ?? $this->fail(
                "Quyền {$key} không có trong danh mục.",
                'Xem danh mục quyền ở /admin/permissions.'
            );
            $dataScope = DataScope::tryFrom((string) $scope) ?? $this->fail(
                "Phạm vi dữ liệu {$scope} không hợp lệ.",
                'Dùng một trong: ALL, FACULTY, SECTION, ADVISEE, OWN.'
            );
            $sync[$permission->id] = ['scope' => $dataScope->value];
        }

        if ($role->code === Role::ADMIN) {
            foreach (self::ADMIN_REQUIRED_PERMISSIONS as $required) {
                if (($matrix[$required] ?? null) !== DataScope::All->value) {
                    $this->fail(
                        'Vai trò ADMIN phải giữ quyền quản lý tài khoản và phân quyền trên toàn trường.',
                        "Giữ quyền {$required} với phạm vi ALL."
                    );
                }
            }
        }

        $before = $role->permissionMatrix();

        $this->transaction(function () use ($role, $sync, $before): void {
            $role->permissions()->sync($sync);
            $after = $role->permissionMatrix();

            if ($before !== $after) {
                $this->audit->record(
                    AuditEvent::Updated,
                    $role,
                    ['permissions' => array_diff_assoc($before, $after)],
                    ['permissions' => array_diff_assoc($after, $before)],
                );
            }
        });

        $this->access->flush();
    }

    /** Gán vai trò cho người dùng; ngày bắt đầu mặc định là hôm nay, ngày kết thúc để trống là không giới hạn. */
    public function assign(User $user, Role $role, ?string $validFrom = null, ?string $validTo = null): void
    {
        $validFrom ??= $this->today()->toDateString();

        if (! $role->isActive()) {
            $this->fail("Vai trò {$role->code} đang ngừng hoạt động.", 'Kích hoạt vai trò trước khi gán.');
        }

        if ($validTo !== null && $validTo < $validFrom) {
            $this->fail('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.', 'Chọn lại ngày kết thúc, hoặc để trống nếu không giới hạn.');
        }

        // Trùng thời gian với một lần gán khác của cùng vai trò (bỏ qua các lần gán đã hủy: valid_to < valid_from)
        $overlap = DB::table('user_roles')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->when($validTo !== null, fn ($q) => $q->where(fn ($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', $validTo)))
            ->where(fn ($q) => $q->whereNull('valid_to')->orWhere('valid_to', '>=', $validFrom))
            ->whereRaw('(valid_to is null or valid_from is null or valid_to >= valid_from)')
            ->exists();

        if ($overlap) {
            $this->fail(
                "Người dùng đã có vai trò {$role->code} trong khoảng thời gian này.",
                'Gỡ hoặc chờ lần gán hiện có hết hạn trước khi gán lại.'
            );
        }

        $before = $this->activeRoleCodes($user);

        $this->transaction(function () use ($user, $role, $validFrom, $validTo, $before): void {
            $user->roles()->attach($role->id, [
                'valid_from' => $validFrom,
                'valid_to' => $validTo,
                'assigned_by' => Auth::id(),
            ]);

            $this->audit->record(
                AuditEvent::Updated,
                $user,
                ['roles' => $before],
                ['roles' => $this->activeRoleCodes($user), 'assigned' => $role->code, 'valid_from' => $validFrom, 'valid_to' => $validTo],
            );
        });

        $this->access->flush();
    }

    /** Gỡ vai trò từ hôm nay. Không xóa dòng gán: đặt ngày kết thúc để giữ lịch sử (GC-06). */
    public function revoke(User $user, Role $role): void
    {
        $today = $this->today();
        $current = DB::table('user_roles')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->where(fn ($q) => $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today->toDateString()))
            ->get(['id', 'valid_from']);

        if ($current->isEmpty()) {
            $this->fail("Người dùng không có vai trò {$role->code}.", 'Xem danh sách vai trò hiện có của người dùng trước khi gỡ.');
        }

        if ($role->code === Role::ADMIN) {
            $this->ensureNotLastAdmin($user);
        }

        $before = $this->activeRoleCodes($user);
        $yesterday = $today->subDay()->toDateString();

        $this->transaction(function () use ($current, $yesterday, $user, $role, $before): void {
            foreach ($current as $row) {
                // Lần gán bắt đầu từ hôm nay hoặc tương lai được đóng ngay trước ngày bắt đầu, coi như đã hủy
                $end = $row->valid_from !== null && $row->valid_from > $yesterday
                    ? CarbonImmutable::parse($row->valid_from)->subDay()->toDateString()
                    : $yesterday;

                DB::table('user_roles')->where('id', $row->id)->update([
                    'valid_to' => $end,
                    'updated_at' => now(),
                ]);
            }

            $this->audit->record(
                AuditEvent::Updated,
                $user,
                ['roles' => $before],
                ['roles' => $this->activeRoleCodes($user), 'revoked' => $role->code],
            );
        });

        $this->access->flush();
    }

    /**
     * BR-AUTH-05: không được làm mất ADMIN cuối cùng đang hoạt động.
     * UserService gọi hàm này trước khi khóa hoặc ngừng tài khoản; ADMIN đã khóa, ngừng hoặc chỉ đọc không được tính.
     */
    public function ensureNotLastAdmin(User $user): void
    {
        if (! $user->hasRole(Role::ADMIN)) {
            return;
        }

        // Chỉ tính ADMIN còn thao tác được: tài khoản đang hoạt động, chưa tới ngày hẹn ngừng
        $activeAdmins = User::query()
            ->operational()
            ->whereHas('activeRoles', fn ($q) => $q->where('roles.code', Role::ADMIN))
            ->count();

        if ($activeAdmins <= 1) {
            $this->fail(
                'Không thể gỡ quyền của ADMIN cuối cùng đang hoạt động.',
                'Gán vai trò ADMIN cho một tài khoản khác trước.'
            );
        }
    }

    /** @return list<string> */
    private function activeRoleCodes(User $user): array
    {
        return $user->activeRoles()->orderBy('roles.code')->pluck('roles.code')->unique()->values()->all();
    }

    private function activeAssignments(Role $role): QueryBuilder
    {
        $today = $this->today()->toDateString();

        return DB::table('user_roles')
            ->where('role_id', $role->id)
            ->where(fn ($q) => $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today));
    }

    private function today(): CarbonImmutable
    {
        return CarbonImmutable::now(config('studentmanager.display_timezone'))->startOfDay();
    }
}
