<?php

namespace App\Modules\Notification;

use App\Modules\Notification\Console\PruneNotifications;
use Illuminate\Support\ServiceProvider;

/** Thông báo (NOT): trung tâm thông báo trong ứng dụng, cổng gửi `NotificationDispatcher`. */
class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([PruneNotifications::class]);
        }
    }
}
