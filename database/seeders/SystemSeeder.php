<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Dữ liệu nền của module SYS: danh mục dùng chung, bộ quy chế mặc định. Tham số hệ thống không cần seed:
 * chưa lưu thì dùng giá trị mặc định (SettingDefinitions).
 */
class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LookupSeeder::class,
            PolicySeeder::class,
        ]);
    }
}
