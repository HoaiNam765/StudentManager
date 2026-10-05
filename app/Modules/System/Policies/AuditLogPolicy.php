<?php

namespace App\Modules\System\Policies;

use App\Modules\Auth\Policies\ModulePolicy;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Quyền với nhật ký kiểm toán (FR-SYS-004): theo ma trận phân quyền, mục "AUDIT"
 * (mặc định chỉ ADMIN được xem và xuất).
 * Sửa, xóa, khôi phục luôn bị từ chối: nhật ký chỉ được ghi thêm (BR-SYS-01).
 */
class AuditLogPolicy extends ModulePolicy
{
    protected string $module = 'AUDIT';

    public function create(?Authenticatable $user): bool
    {
        return false;
    }

    public function update(?Authenticatable $user, Model $model): bool
    {
        return false;
    }

    public function delete(?Authenticatable $user, Model $model): bool
    {
        return false;
    }

    public function restore(?Authenticatable $user, Model $model): bool
    {
        return false;
    }
}
