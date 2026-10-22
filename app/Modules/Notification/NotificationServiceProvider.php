<?php

namespace App\Modules\Notification;

use App\Modules\Notification\Console\PruneNotifications;
use App\Modules\Notification\Console\SendScheduledNotifications;
use App\Modules\Notification\Listeners\EventNotificationSubscriber;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Thông báo (NOT): trung tâm thông báo trong ứng dụng, cổng gửi `NotificationDispatcher`, thông báo tự động theo sự kiện
 * của các module (`EventNotificationSubscriber`, phụ lục A).
 */
class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::subscribe(EventNotificationSubscriber::class);

        if ($this->app->runningInConsole()) {
            $this->commands([PruneNotifications::class, SendScheduledNotifications::class]);
        }
    }
}
