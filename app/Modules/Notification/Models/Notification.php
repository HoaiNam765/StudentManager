<?php

namespace App\Modules\Notification\Models;

use App\Models\User;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Một lần gửi thông báo (tự động theo sự kiện hoặc thủ công); người nhận ở `notification_recipients`. */
class Notification extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'mandatory' => 'boolean',
            'pinned' => 'boolean',
            'sent_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'audience' => 'array',
            'recipient_count' => 'integer',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /** Đã gửi, chưa thu hồi, chưa hết hạn (BR-NOT-06: hết hạn thì ẩn nhưng vẫn lưu). */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('notifications.status', 'sent')
            ->where(fn (Builder $q) => $q->whereNull('notifications.expires_at')->orWhere('notifications.expires_at', '>', now()));
    }
}
