<?php

namespace App\Console\Commands;

use App\Modules\Faculty\Services\LeadershipService;
use Illuminate\Console\Command;

/**
 * Đồng bộ vai trò DEAN theo nhiệm kỳ lãnh đạo đơn vị (BR-FAC-06). Chạy hằng ngày theo lịch (routes/console.php).
 * Quyền tự có hiệu lực theo ngày nhờ ngày hiệu lực của vai trò; lệnh này sửa các dòng vai trò bị lệch và ghi nhật ký
 * các nhiệm kỳ bắt đầu hôm nay, kết thúc hôm qua.
 */
class SyncLeadershipRoles extends Command
{
    protected $signature = 'faculty:sync-leadership';

    protected $description = 'Đồng bộ vai trò DEAN và phạm vi khoa theo nhiệm kỳ lãnh đạo đơn vị';

    public function handle(LeadershipService $leadership): int
    {
        $result = $leadership->sync();

        $this->info("Đã đồng bộ nhiệm kỳ lãnh đạo: sửa {$result['fixed']} dòng vai trò, {$result['started']} nhiệm kỳ bắt đầu hôm nay, {$result['ended']} nhiệm kỳ kết thúc hôm qua.");

        return self::SUCCESS;
    }
}
