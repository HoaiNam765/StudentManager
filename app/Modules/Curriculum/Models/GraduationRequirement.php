<?php

namespace App\Modules\Curriculum\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Điều kiện tốt nghiệp của một phiên bản CTĐT (FR-CUR-004; BR-CUR-05). */
class GraduationRequirement extends StandardModel
{
    protected $table = 'curriculum_graduation_requirements';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'min_credits' => 'integer',
            'min_cgpa' => 'decimal:2',
            'requires_internship' => 'boolean',
            'requires_thesis' => 'boolean',
            'allows_thesis_alternative' => 'boolean',
            'no_active_suspension' => 'boolean',
        ];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }
}
