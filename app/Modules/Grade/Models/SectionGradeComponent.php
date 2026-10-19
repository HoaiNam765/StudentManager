<?php

namespace App\Modules\Grade\Models;

use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Subject\Enums\ComponentKind;
use App\Modules\Subject\Models\GradeComponentTemplate;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionGradeComponent extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'kind' => ComponentKind::class,
            'weight' => 'decimal:2',
            'min_score' => 'decimal:2',
            'is_required' => 'boolean',
            'is_remote' => 'boolean',
            'sort_order' => 'integer',
            'version' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(GradeComponentTemplate::class, 'grade_component_template_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(GradeEntry::class);
    }
}
