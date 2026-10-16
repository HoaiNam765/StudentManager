<?php

namespace App\Modules\Teacher\Models;

use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Đơn vị kiêm nhiệm của giảng viên (FR-TCH-003, BR-FAC-03): bộ môn hoặc khoa (`unit_type` + `unit_id`), có ngày hiệu lực.
 * Chức vụ quản lý (trưởng, phó) là nhiệm kỳ lãnh đạo của FAC (`LeadershipTerm`), không lưu ở đây.
 */
class LecturerUnit extends StandardModel
{
    public const UNIT_TYPES = ['faculty' => Faculty::class, 'department' => Department::class];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date'];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function unit(): Faculty|Department|null
    {
        $model = self::UNIT_TYPES[$this->unit_type] ?? null;

        return $model === null ? null : $model::withTrashed()->find($this->unit_id);
    }

    /** Có hiệu lực vào ngày $day (Y-m-d). */
    public function scopeEffectiveOn(Builder $query, string $day): Builder
    {
        return $query
            ->whereDate('starts_on', '<=', $day)
            ->where(fn (Builder $q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $day));
    }

    /** Giao với khoảng [$from, $to] ($to null = không giới hạn). */
    public function scopeOverlapping(Builder $query, string $from, ?string $to): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $from))
            ->when($to !== null, fn (Builder $q) => $q->whereDate('starts_on', '<=', $to));
    }
}
