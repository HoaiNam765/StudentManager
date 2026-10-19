<?php

namespace App\Modules\SchoolClass\Models;

use App\Modules\Student\Models\Student;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Sinh viên thuộc lớp hành chính trong một khoảng thời gian (BR-CLS-08). */
class AdminClassMember extends StandardModel
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

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeCurrent(Builder $query): Builder
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return $this->scopeEffectiveOn($query, $today);
    }

    public function scopeEffectiveOn(Builder $query, string $date): Builder
    {
        return $query->whereDate($this->qualifyColumn('effective_from'), '<=', $date)
            ->where(fn (Builder $period) => $period
                ->whereNull($this->qualifyColumn('effective_to'))
                ->orWhereDate($this->qualifyColumn('effective_to'), '>=', $date));
    }
}
