<?php

namespace App\Modules\Subject\Models;

use App\Models\User;
use App\Modules\Subject\Enums\SyllabusStatus;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Một phiên bản đề cương chi tiết của học phần (FR-SUB-006). */
class Syllabus extends StandardModel
{
    protected $table = 'syllabi';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'learning_outcomes' => 'array',
            'content' => 'array',
            'materials' => 'array',
            'status' => SyllabusStatus::class,
            'submitted_at' => 'datetime',
            'decided_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
