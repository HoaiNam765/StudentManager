<?php

namespace App\Console\Commands;

use App\Support\Services\ExportService;
use Illuminate\Console\Command;

/**
 * Xóa tệp xuất dữ liệu quá hạn giữ (`studentmanager.export.retention_days`) và chuyển yêu cầu sang `expired`.
 * Chạy hằng ngày theo lịch (routes/console.php); cần `php artisan schedule:run` mỗi phút trên máy chủ.
 */
class PruneExpiredExports extends Command
{
    protected $signature = 'exports:prune';

    protected $description = 'Xóa tệp xuất dữ liệu đã hết hạn giữ và đặt yêu cầu sang trạng thái hết hạn';

    public function handle(ExportService $exports): int
    {
        $pruned = $exports->pruneExpired();

        $this->info("Đã dọn {$pruned} tệp xuất hết hạn.");

        return self::SUCCESS;
    }
}
