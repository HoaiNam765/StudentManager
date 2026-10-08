<?php

namespace App\Modules\System\Services;

use App\Models\User;
use App\Modules\Auth\Models\Role;
use App\Modules\System\Notifications\ImportantConfigurationChanged;
use Illuminate\Support\Facades\Notification;

/** Gửi thông báo thay đổi cấu hình quan trọng cho các ADMIN đang hoạt động, trừ người thực hiện (BR-SYS-08). */
class ConfigurationChangeNotifier
{
    /** @param  list<string>  $lines */
    public function notifyOtherAdmins(?User $actor, string $summary, array $lines): void
    {
        $admins = User::query()
            ->whereHas('activeRoles', fn ($q) => $q->where('roles.code', Role::ADMIN))
            ->when($actor !== null, fn ($q) => $q->whereKeyNot($actor->getKey()))
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new ImportantConfigurationChanged($summary, $lines, $actor?->name ?? 'Hệ thống'));
    }
}
