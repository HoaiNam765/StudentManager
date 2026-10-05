<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Tài khoản dùng thử cho môi trường phát triển (BR-SYS-10: không chạy ở môi trường production).
 * Vai trò và quyền của tài khoản này sẽ được gán khi có module AUTH (issue RBAC).
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

        User::firstOrCreate(
            ['email' => self::EMAIL],
            ['name' => 'Quản trị thử nghiệm', 'password' => self::PASSWORD],
        );
    }
}
