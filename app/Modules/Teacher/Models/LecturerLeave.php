<?php

namespace App\Modules\Teacher\Models;

use App\Models\User;
use App\Modules\Teacher\Enums\LeaveStatus;
use App\Modules\Teacher\Enums\LeaveType;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Nghỉ phép, nghỉ ốm, công tác của giảng viên (FR-TCH-012). */
class LecturerLeave extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => LeaveType::class,
            'status' => LeaveStatus::class,
            'starts_on' => 'date',
            'ends_on' => 'date',
            'decided_at' => 'datetime',
        ];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /** Đơn còn hiệu lực (chờ duyệt hoặc đã duyệt) giao với khoảng [$from, $to]. */
    public function scopeOverlapping(Builder $query, string $from, string $to): Builder
    {
        return $query->whereIn('status', [LeaveStatus::Pending->value, LeaveStatus::Approved->value])
            ->whereDate('starts_on', '<=', $to)
            ->whereDate('ends_on', '>=', $from);
    }
}
