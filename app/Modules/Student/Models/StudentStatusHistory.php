<?php

namespace App\Modules\Student\Models;

use App\Modules\Student\Enums\StudentStatus;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Một lần chuyển trạng thái sinh viên theo quyết định (FR-STU-010). */
class StudentStatusHistory extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'from_status' => StudentStatus::class,
            'to_status' => StudentStatus::class,
            'effective_date' => 'immutable_date',
            'special' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
