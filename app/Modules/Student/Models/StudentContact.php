<?php

namespace App\Modules\Student\Models;

use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Người thân và liên hệ khẩn cấp của sinh viên (FR-STU-013). */
class StudentContact extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_emergency' => 'boolean',
            'receives_notifications' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
