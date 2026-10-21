<?php

namespace App\Modules\Grade\Models;

use App\Modules\Enrollment\Models\Enrollment;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Official per-enrollment result. The section workflow controls disclosure. */
class CourseResult extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['score_10' => 'decimal:1', 'score_4' => 'decimal:2', 'passed' => 'boolean'];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
