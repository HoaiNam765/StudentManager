<?php

namespace App\Modules\AcademicYear\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Term extends StandardModel
{
    protected $table = 'terms';

    protected $fillable = [
        'academic_year_id',
        'name',
        'type',
        'start_date',
        'end_date',
        'weeks',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'weeks' => 'integer',
        'type' => TermType::class,
        'status' => TermStatus::class,
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
