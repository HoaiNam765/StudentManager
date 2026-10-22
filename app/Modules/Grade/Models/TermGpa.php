<?php

namespace App\Modules\Grade\Models;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Grade\Models\Concerns\HasLegacyAttributeAliases;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermGpa extends StandardModel
{
    use HasLegacyAttributeAliases;

    /** Tên cột cũ view FE (kết quả học tập) còn dùng */
    protected const LEGACY_ALIASES = ['passed_credits' => 'credits_earned', 'total_credits' => 'credits_attempted'];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['gpa' => 'decimal:2', 'credits_attempted' => 'integer', 'credits_earned' => 'integer'];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}
