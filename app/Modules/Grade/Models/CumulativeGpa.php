<?php

namespace App\Modules\Grade\Models;

use App\Support\Models\StandardModel;

class CumulativeGpa extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['gpa' => 'decimal:2', 'credits_attempted' => 'integer', 'credits_earned' => 'integer'];
    }
}
