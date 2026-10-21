<?php

namespace App\Console\Commands;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;

/** Nạp dữ liệu demo trọn vòng đời học kỳ (#122). Chỉ chạy ở môi trường không phải production (BR-SYS-10). */
class SeedDemoData extends Command
{
    protected $signature = 'demo:seed';

    protected $description = 'Nạp dữ liệu demo (300 sinh viên, 2 học kỳ đã khóa, 1 học kỳ đang diễn ra) — không chạy ở production';

    public function handle(): int
    {
        if ($this->laravel->isProduction()) {
            $this->error('Không chạy dữ liệu demo trên môi trường production (BR-SYS-10).');

            return self::FAILURE;
        }

        $this->call('db:seed', ['--class' => DemoDataSeeder::class, '--force' => true]);

        return self::SUCCESS;
    }
}
