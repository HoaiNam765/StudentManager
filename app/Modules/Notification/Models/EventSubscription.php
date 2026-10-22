<?php

namespace App\Modules\Notification\Models;

use App\Support\Models\StandardModel;

/** Cấu hình thông báo của một sự kiện phụ lục A (FR-NOT-004). */
class EventSubscription extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['channels' => 'array', 'mandatory' => 'boolean', 'enabled' => 'boolean', 'wired' => 'boolean'];
    }

    public function sendsInApp(): bool
    {
        return in_array('in_app', $this->channels ?? [], true);
    }
}
