<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Dữ liệu nền của module SYS: danh mục dùng chung (và các phần khác của SYS khi có).
 */
class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LookupSeeder::class,
        ]);
    }
}
