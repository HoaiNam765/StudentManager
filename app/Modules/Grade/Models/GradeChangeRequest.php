<?php

namespace App\Modules\Grade\Models;

use App\Support\Models\StandardModel;

class GradeChangeRequest extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['proposed_scores' => 'array'];
    }
}
