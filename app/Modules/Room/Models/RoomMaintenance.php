<?php

namespace App\Modules\Room\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Lịch bảo trì phòng từ ngày – đến ngày (FR-ROM-002); trong khoảng này phòng không được xếp lịch (BR-ROM-02). */
class RoomMaintenance extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date'];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** Các lịch bảo trì giao với khoảng [$from, $to] (ngày, gồm cả hai đầu). */
    public function scopeOverlapping(Builder $query, string $from, string $to): Builder
    {
        return $query->whereDate('starts_on', '<=', $to)->whereDate('ends_on', '>=', $from);
    }
}
