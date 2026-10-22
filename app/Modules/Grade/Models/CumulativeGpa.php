<?php

namespace App\Modules\Grade\Models;

use App\Modules\Grade\Models\Concerns\HasLegacyAttributeAliases;
use App\Support\Models\StandardModel;

class CumulativeGpa extends StandardModel
{
    use HasLegacyAttributeAliases;

    /** Tên cột cũ view FE (kết quả học tập) còn dùng */
    protected const LEGACY_ALIASES = ['passed_credits' => 'credits_earned', 'total_credits' => 'credits_attempted'];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['gpa' => 'decimal:2', 'credits_attempted' => 'integer', 'credits_earned' => 'integer'];
    }
}
