<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Support\Enums\ActiveStatus;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Kiểm soát truy cập phía máy chủ (FR-AUTH-013, BR-AUTH-04): vai trò → hành động → phạm vi dữ liệu,
 * mặc định từ chối. Quyền của người dùng là hợp quyền các vai trò đang hiệu lực hôm nay.
 *
 *     $access->allows($user, 'STU', PermissionAction::View);                 // có quyền ở phạm vi nào đó
 *     $access->allowsOn($user, 'GRD', PermissionAction::Update, $section);   // có quyền trên đúng bản ghi này
 *     $access->constrain(Student::query(), $user, 'STU', PermissionAction::View)->paginate();  // lọc danh sách
 *
 * Kết quả được nhớ trong một yêu cầu; thay đổi phân quyền qua RoleService gọi flush() nên có hiệu lực ngay.
 */
class AccessControl
{
    /** @var array<int|string, array<string, list<DataScope>>> */
    private array $grants = [];

    /**
     * Các phạm vi mà người dùng có cho module.hành động; [] là không có quyền.
     * Nếu có ALL thì chỉ trả về [ALL].
     *
     * @return list<DataScope>
     */
    public function scopesFor(?Authenticatable $user, string $module, PermissionAction $action): array
    {
        if (! $user instanceof User) {
            return [];
        }

        $scopes = $this->grantsFor($user)[$module.'.'.$action->value] ?? [];

        return in_array(DataScope::All, $scopes, true) ? [DataScope::All] : $scopes;
    }

    public function allows(?Authenticatable $user, string $module, PermissionAction $action): bool
    {
        return $this->scopesFor($user, $module, $action) !== [];
    }

    /** Có quyền trên toàn trường (phạm vi ALL). */
    public function allowsGlobally(?Authenticatable $user, string $module, PermissionAction $action): bool
    {
        return $this->scopesFor($user, $module, $action) === [DataScope::All];
    }

    /** Có quyền trên đúng bản ghi $model (kiểm tra phạm vi dữ liệu, chống IDOR – NFR-SEC-04). */
    public function allowsOn(?Authenticatable $user, string $module, PermissionAction $action, Model $model): bool
    {
        $scopes = $this->scopesFor($user, $module, $action);

        if ($scopes === []) {
            return false;
        }

        if ($scopes === [DataScope::All]) {
            return true;
        }

        if (! $model instanceof HasDataScope || ! $model->exists) {
            return false;
        }

        return $this->constrain($model->newQuery()->whereKey($model->getKey()), $user, $module, $action)->exists();
    }

    /** Lọc truy vấn chỉ còn dữ liệu trong phạm vi của người dùng; không có quyền thì không trả về gì. */
    public function constrain(Builder $query, ?Authenticatable $user, string $module, PermissionAction $action): Builder
    {
        $scopes = $this->scopesFor($user, $module, $action);

        if ($scopes === [DataScope::All]) {
            return $query;
        }

        $model = $query->getModel();

        if ($scopes === [] || ! $model instanceof HasDataScope || ! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $group) use ($scopes, $model, $user): void {
            foreach ($scopes as $scope) {
                $group->orWhere(function (Builder $inner) use ($scope, $model, $user): void {
                    $model->applyDataScope($inner, $scope, $user);

                    // Model không thêm điều kiện nào cho phạm vi này: từ chối thay vì mở toàn bộ
                    if ($inner->getQuery()->wheres === []) {
                        $inner->whereRaw('1 = 0');
                    }
                });
            }
        });
    }

    /** Quên kết quả đã nhớ; gọi sau khi thay đổi vai trò hoặc quyền. */
    public function flush(): void
    {
        $this->grants = [];
    }

    /** @return array<string, list<DataScope>> */
    private function grantsFor(User $user): array
    {
        $key = $user->getKey();

        if (isset($this->grants[$key])) {
            return $this->grants[$key];
        }

        $today = now(config('studentmanager.display_timezone'))->toDateString();

        $rows = DB::table('role_permissions as rp')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->join('roles as r', 'r.id', '=', 'rp.role_id')
            ->join('user_roles as ur', 'ur.role_id', '=', 'r.id')
            ->where('ur.user_id', $key)
            ->where('r.status', ActiveStatus::Active->value)
            ->whereNull('r.deleted_at')
            ->where(fn (QueryBuilder $q) => $q->whereNull('ur.valid_from')->orWhere('ur.valid_from', '<=', $today))
            ->where(fn (QueryBuilder $q) => $q->whereNull('ur.valid_to')->orWhere('ur.valid_to', '>=', $today))
            ->get(['p.module', 'p.action', 'rp.scope']);

        $grants = [];

        foreach ($rows as $row) {
            $scope = DataScope::from($row->scope);
            $permission = $row->module.'.'.$row->action;

            if (! in_array($scope, $grants[$permission] ?? [], true)) {
                $grants[$permission][] = $scope;
            }
        }

        return $this->grants[$key] = $grants;
    }
}
