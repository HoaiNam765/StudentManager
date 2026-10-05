<?php

namespace App\Modules\Auth\Policies;

use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Support\Policies\DenyByDefaultPolicy;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Policy nền cho model của một module: quyết định theo ma trận phân quyền (AccessControl),
 * kể cả phạm vi dữ liệu trên từng bản ghi. Policy của module chỉ cần khai báo mã module:
 *
 *     class StudentPolicy extends ModulePolicy
 *     {
 *         protected string $module = 'STU';
 *     }
 *
 * Lưu ý với `create` khi người dùng chỉ có phạm vi hẹp (ví dụ sinh viên đăng ký học phần với OWN):
 * Service phải tự bảo đảm bản ghi tạo ra thuộc đúng phạm vi đó.
 * `forceDelete` luôn từ chối: không xóa vật lý dữ liệu nghiệp vụ (GC-02).
 */
abstract class ModulePolicy extends DenyByDefaultPolicy
{
    /** Mã module trong docs/BA.md, ví dụ 'STU', 'GRD'. */
    protected string $module;

    public function viewAny(?Authenticatable $user): bool
    {
        return $this->access()->allows($user, $this->module, PermissionAction::View);
    }

    public function view(?Authenticatable $user, Model $model): bool
    {
        return $this->access()->allowsOn($user, $this->module, PermissionAction::View, $model);
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->access()->allows($user, $this->module, PermissionAction::Create);
    }

    public function update(?Authenticatable $user, Model $model): bool
    {
        return $this->access()->allowsOn($user, $this->module, PermissionAction::Update, $model);
    }

    public function delete(?Authenticatable $user, Model $model): bool
    {
        return $this->access()->allowsOn($user, $this->module, PermissionAction::Delete, $model);
    }

    public function restore(?Authenticatable $user, Model $model): bool
    {
        return $this->access()->allowsOn($user, $this->module, PermissionAction::Delete, $model);
    }

    /** Phê duyệt, xác nhận, khóa (chữ A trong ma trận). */
    public function approve(?Authenticatable $user, Model $model): bool
    {
        return $this->access()->allowsOn($user, $this->module, PermissionAction::Approve, $model);
    }

    /** Xuất dữ liệu (chữ X trong ma trận); danh sách xuất phải lọc bằng AccessControl::constrain(). */
    public function export(?Authenticatable $user): bool
    {
        return $this->access()->allows($user, $this->module, PermissionAction::Export);
    }

    protected function access(): AccessControl
    {
        return app(AccessControl::class);
    }
}
