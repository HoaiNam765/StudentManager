<?php

namespace App\Modules\AcademicYear\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends StandardModel
{
    protected $table = 'academic_years';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }
}
