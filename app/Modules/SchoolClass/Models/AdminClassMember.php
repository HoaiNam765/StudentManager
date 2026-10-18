<?php

namespace App\Modules\SchoolClass\Models;

use App\Modules\Student\Models\Student;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Sinh viên thuộc lớp hành chính trong một khoảng thời gian (BR-CLS-08). Dòng đang hiệu lực có `effective_to` trống. */
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
        return $query->whereNull($this->qualifyColumn('effective_to'));
    }
}
