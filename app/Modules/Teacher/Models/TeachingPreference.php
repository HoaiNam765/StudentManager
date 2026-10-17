<?php

namespace App\Modules\Teacher\Models;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Subject\Models\Subject;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Nguyện vọng dạy một học phần ở học kỳ sắp tới (FR-TCH-008); CLS dùng làm gợi ý khi phân công. */
class TeachingPreference extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['priority' => 'integer'];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
