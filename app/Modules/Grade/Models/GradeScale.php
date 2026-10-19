<?php

namespace App\Modules\Grade\Models;

use App\Support\Models\StandardModel;

class GradeScale extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'cohort' => 'integer',
            'min_score' => 'decimal:2',
            'point4' => 'decimal:1',
        ];
    }
}
