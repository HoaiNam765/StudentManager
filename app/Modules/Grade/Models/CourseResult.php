<?php

namespace App\Modules\Grade\Models;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\Concerns\HasLegacyAttributeAliases;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseResult extends StandardModel
{
    use HasLegacyAttributeAliases;

    /** Tên cột cũ view FE (bảng điểm, sổ điểm) còn dùng */
    protected const LEGACY_ALIASES = ['score_10' => 'score10', 'score_4' => 'point4', 'passed' => 'is_passed'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'score10' => 'decimal:2',
            'point4' => 'decimal:1',
            'is_passed' => 'boolean',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }
}
