<?php

namespace App\Support\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Policy nền: mặc định từ chối mọi thao tác (GC-08, BR-AUTH-04).
 * Policy của module chỉ ghi đè những thao tác được phép.
 * Phần kiểm tra theo vai trò và phạm vi dữ liệu sẽ nối vào đây ở Issue RBAC (module AUTH).
 *
 *     class FacultyPolicy extends DenyByDefaultPolicy
 *     {
 *         public function viewAny(?Authenticatable $user): bool
 *         {
 *             return $user !== null;
 *         }
 *     }
 *
 * Đăng ký trong AppServiceProvider::boot():  Gate::policy(Faculty::class, FacultyPolicy::class);
 */
abstract class DenyByDefaultPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return false;
    }

    public function view(?Authenticatable $user, Model $model): bool
    {
        return false;
    }

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

    public function forceDelete(?Authenticatable $user, Model $model): bool
    {
        return false;
    }
}
