<?php

namespace App\Modules\SchoolClass\Models;

use App\Modules\Teacher\Models\Lecturer;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Lịch sử cố vấn học tập của lớp hành chính (FR-CLS-004, BR-CLS-07). */
class AdvisorAssignment extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function adminClass(): BelongsTo
    {
        return $this->belongsTo(AdminClass::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $this->scopeEffectiveOn($query, now(config('studentmanager.display_timezone'))->toDateString());
    }

    public function scopeEffectiveOn(Builder $query, string $date): Builder
    {
        return $query->whereDate($this->qualifyColumn('effective_from'), '<=', $date)
            ->where(fn (Builder $period) => $period
                ->whereNull($this->qualifyColumn('effective_to'))
                ->orWhereDate($this->qualifyColumn('effective_to'), '>=', $date));
    }
}
