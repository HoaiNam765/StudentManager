<?php

namespace App\Modules\Curriculum\Models;

use App\Modules\Subject\Enums\KnowledgeBlock;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Khối kiến thức của CTĐT và số tín chỉ yêu cầu (FR-CUR-002). */
class CurriculumBlock extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['block_type' => KnowledgeBlock::class, 'required_credits' => 'integer', 'sort_order' => 'integer'];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    public function electiveGroups(): HasMany
    {
        return $this->hasMany(ElectiveGroup::class);
    }
}
