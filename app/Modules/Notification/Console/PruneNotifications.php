<?php

namespace App\Modules\Notification\Console;

use App\Modules\Notification\Models\Notification;
use Illuminate\Console\Command;

/** Xóa hẳn thông báo đã hết hạn quá `notifications.retention_days` ngày (BR-NOT-06: hết hạn thì ẩn, giữ theo chính sách). */
class PruneNotifications extends Command
{
    protected $signature = 'notifications:prune';

    protected $description = 'Xóa thông báo đã hết hạn quá thời gian lưu';

    public function handle(): int
    {
        $before = now()->subDays(max(1, (int) config('notifications.retention_days', 365)));
        $count = 0;

        Notification::query()->withTrashed()->whereNotNull('expires_at')->where('expires_at', '<', $before)
            ->chunkById(200, function ($rows) use (&$count): void {
                foreach ($rows as $row) {
                    $row->forceDelete();
                    $count++;
                }
            });

        $this->info("Đã xóa {$count} thông báo hết hạn quá thời gian lưu.");

        return self::SUCCESS;
    }
}
