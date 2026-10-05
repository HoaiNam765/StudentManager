<?php

namespace App\Modules\Auth\Concerns;

use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Vai trò của người dùng (dùng trong App\Models\User). Một người có thể giữ nhiều vai trò;
 * quyền là hợp các vai trò đang hiệu lực (BR-AUTH-04).
 */
trait HasRoles
{
    /** Mọi lần gán vai trò, kể cả đã hết hiệu lực (lịch sử). */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->using(UserRole::class)
            ->withPivot('id', 'valid_from', 'valid_to', 'assigned_by')
            ->withTimestamps();
    }

    /** Vai trò đang có hiệu lực hôm nay (theo giờ Việt Nam) và đang hoạt động. */
    public function activeRoles(): BelongsToMany
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return $this->roles()
            ->active()
            ->where(fn (Builder $q) => $q->whereNull('user_roles.valid_from')->orWhere('user_roles.valid_from', '<=', $today))
            ->where(fn (Builder $q) => $q->whereNull('user_roles.valid_to')->orWhere('user_roles.valid_to', '>=', $today));
    }

    public function hasRole(string $code): bool
    {
        return $this->activeRoles()->where('roles.code', $code)->exists();
    }
}
