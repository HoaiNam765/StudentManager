<?php

namespace App\Modules\Curriculum\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Nhóm tự chọn: sinh viên chọn tối thiểu `min_credits` tín chỉ trong nhóm (FR-CUR-003). */
class ElectiveGroup extends StandardModel
{
    protected $table = 'curriculum_elective_groups';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['min_credits' => 'integer'];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(CurriculumBlock::class, 'curriculum_block_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class, 'curriculum_elective_group_id');
    }
}
