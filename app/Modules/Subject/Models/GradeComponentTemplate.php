<?php

namespace App\Modules\Subject\Models;

use App\Modules\Subject\Enums\ComponentKind;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Một thành phần trong cơ cấu điểm mặc định của học phần (FR-SUB-005). */
class GradeComponentTemplate extends StandardModel
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
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /** Dữ liệu để GRD sao chép sang lớp học phần. */
    public function toComponent(): array
    {
        return [
            'name' => $this->name,
            'kind' => $this->kind->value,
            'weight' => (float) $this->weight,
            'min_score' => $this->min_score === null ? null : (float) $this->min_score,
            'is_required' => $this->is_required,
            'is_remote' => $this->is_remote,
            'sort_order' => $this->sort_order,
        ];
    }
}
