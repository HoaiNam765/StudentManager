<?php

namespace App\Modules\Grade\Models;

use App\Modules\AcademicYear\Models\Term;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermGpa extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['gpa' => 'decimal:2', 'credits_attempted' => 'integer', 'credits_earned' => 'integer'];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
