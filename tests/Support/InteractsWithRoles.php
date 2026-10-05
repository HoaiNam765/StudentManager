<?php

namespace Tests\Support;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\RoleService;
use Database\Seeders\AuthSeeder;

/** Tiện ích cho test: nạp vai trò mặc định và tạo người dùng có vai trò. */
trait InteractsWithRoles
{
    protected function seedRoles(): void
    {
        $this->seed(AuthSeeder::class);
    }

    protected function userWithRoles(string ...$codes): User
    {
        $user = User::factory()->create();

        foreach ($codes as $code) {
            app(RoleService::class)->assign($user, Role::where('code', $code)->firstOrFail());
        }

        return $user;
    }
}
