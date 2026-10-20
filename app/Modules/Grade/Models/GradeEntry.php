<?php

namespace App\Modules\Grade\Models;

use App\Modules\Enrollment\Models\Enrollment;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeEntry extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['score' => 'decimal:2'];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(SectionGradeComponent::class, 'section_grade_component_id');
    }
}
