<?php

namespace App\Modules\Timetable\Models;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Timetable\Enums\TimetableStatus;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Trạng thái thời khóa biểu của một học kỳ (FR-TTB-006). */
class TermTimetable extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => TimetableStatus::class,
            'published_at' => 'immutable_datetime',
            'locked_at' => 'immutable_datetime',
        ];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
