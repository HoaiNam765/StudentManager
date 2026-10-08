<?php

namespace App\Modules\AcademicYear\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Một mốc trong lịch học vụ của học kỳ (FR-ACY-004). Sửa qua MilestoneService để kiểm tra thứ tự và quy trình duyệt. */
class TermMilestone extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['type' => MilestoneType::class, 'date' => 'date'];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
