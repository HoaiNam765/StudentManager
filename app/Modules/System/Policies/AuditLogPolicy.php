<?php

namespace App\Modules\System\Policies;

use App\Support\Policies\DenyByDefaultPolicy;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Quyền với nhật ký kiểm toán (FR-SYS-004, tác nhân ADMIN).
 *
 * Hiện mặc định từ chối mọi thao tác; Issue RBAC (module AUTH) sẽ mở `viewAny`, `view`, `export`
 * cho ADMIN. `update`, `delete`, `restore`, `forceDelete` giữ nguyên từ chối vĩnh viễn:
 * nhật ký không được sửa hoặc xóa (BR-SYS-01).
 */
class AuditLogPolicy extends DenyByDefaultPolicy
{
    /** Xuất nhật ký ra file (Excel/CSV). */
    public function export(?Authenticatable $user): bool
    {
        return false;
    }
}
