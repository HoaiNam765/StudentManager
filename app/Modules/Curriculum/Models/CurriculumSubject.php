<?php

namespace App\Modules\Curriculum\Models;

use App\Modules\Subject\Models\Subject;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Học phần của CTĐT: khối, bắt buộc hoặc thuộc nhóm tự chọn, học kỳ dự kiến, thứ tự (FR-CUR-003). */
class CurriculumSubject extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['planned_term' => 'integer', 'sort_order' => 'integer', 'outside_prerequisites_confirmed' => 'boolean'];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(CurriculumBlock::class, 'curriculum_block_id');
    }

    public function electiveGroup(): BelongsTo
    {
        return $this->belongsTo(ElectiveGroup::class, 'curriculum_elective_group_id');
    }

    public function isElective(): bool
    {
        return $this->curriculum_elective_group_id !== null;
    }
}
