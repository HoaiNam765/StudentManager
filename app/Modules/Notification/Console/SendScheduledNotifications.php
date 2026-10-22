<?php

namespace App\Modules\Notification\Console;

use App\Modules\Notification\Services\AnnouncementService;
use Illuminate\Console\Command;

/** Gửi thông báo thủ công đã hẹn giờ khi tới giờ (FR-NOT-006; chạy mỗi phút nên bắt đầu gửi trong vòng 5 phút). */
class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';

    protected $description = 'Gửi thông báo hẹn giờ đã tới giờ';

    public function handle(AnnouncementService $service): int
    {
        $this->info($service->sendScheduled().' thông báo hẹn giờ đã gửi.');

        return self::SUCCESS;
    }
}
