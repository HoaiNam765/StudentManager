<?php

namespace App\Modules\AcademicYear\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /** Lịch học vụ (các mốc) của học kỳ; đọc và sửa qua MilestoneService. */
    public function milestones(): HasMany
    {
        return $this->hasMany(TermMilestone::class);
    }
}
