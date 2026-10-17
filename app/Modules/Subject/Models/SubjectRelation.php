<?php

namespace App\Modules\Subject\Models;

use App\Modules\Subject\Enums\RelationType;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Quan hệ giữa hai học phần (FR-SUB-004). Dòng (B, A, prerequisite) nghĩa là A là tiên quyết của B. */
class SubjectRelation extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => RelationType::class,
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function relatedSubject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'related_subject_id');
    }

    /** Có hiệu lực vào ngày $day (quan hệ không có ngày thì luôn hiệu lực). */
    public function scopeEffectiveOn(Builder $query, string $day): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('effective_from')->orWhereDate('effective_from', '<=', $day))
            ->where(fn (Builder $q) => $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $day));
    }
}
