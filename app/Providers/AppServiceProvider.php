<?php

namespace App\Providers;

use App\Modules\System\Policies\AuditLogPolicy;
use App\Support\Audit\AuditLog;
use App\Support\Audit\AuditLogger;
use App\Support\Database\BlueprintMacros;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Một AuditLogger cho mỗi yêu cầu (lý do gắn bằng withReason không lẫn sang yêu cầu khác)
        $this->app->scoped(AuditLogger::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BlueprintMacros::register();

        Gate::policy(AuditLog::class, AuditLogPolicy::class);
    }
}
