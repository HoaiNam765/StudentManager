<?php

namespace App\Modules\Faculty\Models;

use App\Models\User;
use App\Modules\Faculty\Enums\LeadershipPosition;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Một nhiệm kỳ lãnh đạo đơn vị (FR-FAC-006). Đơn vị là khoa hoặc bộ môn (`unit_type` + `unit_id`);
 * không dùng quan hệ đa hình để không đổi cách ghi tên model trong nhật ký kiểm toán của toàn hệ thống.
 */
class LeadershipTerm extends StandardModel
{
    public const UNIT_TYPES = ['faculty' => Faculty::class, 'department' => Department::class];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'position' => LeadershipPosition::class,
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): Faculty|Department|null
    {
        $model = self::UNIT_TYPES[$this->unit_type] ?? null;

        return $model === null ? null : $model::withTrashed()->find($this->unit_id);
    }

    /** Nhiệm kỳ đang có hiệu lực vào ngày $day (Y-m-d). */
    public function scopeEffectiveOn(Builder $query, string $day): Builder
    {
        return $query
            ->whereDate('starts_on', '<=', $day)
            ->where(fn (Builder $q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $day));
    }

    /** Nhiệm kỳ giao với khoảng [$from, $to] ($to null = không giới hạn). */
    public function scopeOverlapping(Builder $query, string $from, ?string $to): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $from))
            ->when($to !== null, fn (Builder $q) => $q->whereDate('starts_on', '<=', $to));
    }

    public function scopeForUnit(Builder $query, string $unitType, int $unitId): Builder
    {
        return $query->where('unit_type', $unitType)->where('unit_id', $unitId);
    }
}
