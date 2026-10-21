<?php

namespace App\Modules\Grade\Models;

use App\Modules\Enrollment\Models\Enrollment;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeChangeRequest extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['proposed_scores' => 'array'];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
