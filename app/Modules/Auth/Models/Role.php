<?php

namespace App\Modules\Auth\Models;

use App\Models\User;
use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Vai trò (docs/BA.md mục 4.1). Vai trò hệ thống mặc định không xóa được (FR-AUTH-010).
 */
class Role extends StandardModel
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    public const ADMIN = 'ADMIN';

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::deleting(function (Role $role): void {
            if ($role->is_system) {
                throw new BusinessRuleException(
                    "Không thể xóa vai trò hệ thống mặc định {$role->code}.",
                    'Có thể ngừng sử dụng vai trò thay cho việc xóa.'
                );
            }
        });
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withPivot('scope')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->using(UserRole::class)
            ->withPivot('id', 'valid_from', 'valid_to', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Ma trận quyền của vai trò dạng ['STU.view' => 'ALL', ...].
     *
     * @return array<string, string>
     */
    public function permissionMatrix(): array
    {
        return $this->permissions()
            ->orderBy('module')->orderBy('action')
            ->get()
            ->mapWithKeys(fn (Permission $permission) => [$permission->key() => $permission->pivot->scope])
            ->all();
    }
}
