<?php

namespace App\Modules\SchoolClass\Models;

use App\Modules\Teacher\Models\Lecturer;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Giảng viên dạy lớp học phần (FR-CLS-011): vai trò `main` (giảng viên chính) hoặc `assistant` (trợ giảng). */
class SectionInstructor extends StandardModel
{
    public const MAIN = 'main';

    public const ASSISTANT = 'assistant';

    protected $guarded = [];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->role === self::MAIN ? 'Giảng viên chính' : 'Trợ giảng';
    }
}
