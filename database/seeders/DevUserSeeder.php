<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Tài khoản dùng thử cho môi trường phát triển (BR-SYS-10: không chạy ở môi trường production),
 * được gán vai trò ADMIN nếu AuthSeeder đã tạo vai trò này.
 */
class DevUserSeeder extends Seeder
{
    public const EMAIL = 'admin@studentmanager.test';

    public const PASSWORD = 'password';

    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $user = User::firstOrCreate(
            ['email' => self::EMAIL],
            ['name' => 'Quản trị thử nghiệm', 'password' => self::PASSWORD],
        );

        $admin = Role::where('code', Role::ADMIN)->first();

        if ($admin !== null && ! $user->roles()->whereKey($admin->id)->exists()) {
            $user->roles()->attach($admin->id, ['valid_from' => now(config('studentmanager.display_timezone'))->toDateString()]);
        }
    }
}
