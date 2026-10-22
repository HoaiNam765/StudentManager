<?php

namespace App\Modules\Grade\Models;

use App\Modules\Enrollment\Models\Enrollment;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseResult extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'score10' => 'decimal:2',
            'point4' => 'decimal:1',
            'is_passed' => 'boolean',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
