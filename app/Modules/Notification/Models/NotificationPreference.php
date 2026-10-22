<?php

namespace App\Modules\Notification\Models;

use Illuminate\Database\Eloquent\Model;

/** Người dùng bật / tắt nhận một loại thông báo không bắt buộc (FR-NOT-007). */
class NotificationPreference extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['in_app' => 'boolean'];
    }
}
