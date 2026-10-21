<?php

namespace App\Modules\Grade\Models;

use App\Models\User;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionGradeWorkflow extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => GradePublicationStatus::class,
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
