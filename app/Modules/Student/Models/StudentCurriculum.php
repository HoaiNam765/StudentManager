<?php

namespace App\Modules\Student\Models;

use App\Modules\Curriculum\Models\Curriculum;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Một lần gán CTĐT cho sinh viên, có ngày hiệu lực (FR-CUR-006). */
class StudentCurriculum extends StandardModel
{
    protected $table = 'student_curricula';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function scopeEffectiveOn(Builder $query, string $date): Builder
    {
        return $query->where('effective_from', '<=', $date)
            ->where(fn (Builder $q) => $q->whereNull('effective_to')->orWhere('effective_to', '>=', $date));
    }
}
