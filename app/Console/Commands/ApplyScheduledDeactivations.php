<?php

namespace App\Console\Commands;

use App\Modules\Auth\Services\UserService;
use Illuminate\Console\Command;

/**
 * Ngừng các tài khoản đã đến ngày hẹn (sinh viên thôi học, buộc thôi học, chuyển trường; BR-AUTH-07).
 * Chạy hằng ngày theo lịch (routes/console.php).
 */
class ApplyScheduledDeactivations extends Command
{
    protected $signature = 'users:apply-deactivations';

    protected $description = 'Ngừng các tài khoản đã đến ngày hẹn ngừng';

    public function handle(UserService $users): int
    {
        $count = $users->applyScheduledDeactivations();

        $this->info("Đã ngừng {$count} tài khoản đến hạn.");

        return self::SUCCESS;
    }
}
