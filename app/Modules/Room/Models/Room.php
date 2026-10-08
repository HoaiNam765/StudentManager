<?php

namespace App\Modules\Room\Models;

use App\Modules\Room\Enums\RoomStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Phòng học / phòng thi (FR-ROM-001, BR-ROM-01). Xếp lịch (TTB, EXM) phải hỏi RoomAvailability trước:
 * chỉ phòng "Sử dụng được" và không trùng lịch bảo trì mới được xếp (BR-ROM-02).
 */
class Room extends StandardModel
{
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'capacity' => 'integer',
            'exam_capacity' => 'integer',
            'equipment' => 'array',
            'status' => RoomStatus::class,
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(RoomMaintenance::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), RoomStatus::Available->value);
    }
}
