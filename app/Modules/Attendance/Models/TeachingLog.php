<?php

namespace App\Modules\Attendance\Models;

use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Models\ClassSession;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Sổ đầu bài: giảng viên xác nhận buổi đã dạy, số tiết thực dạy, nội dung (FR-ATT-012). */
class TeachingLog extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['periods' => 'integer', 'confirmed_at' => 'datetime'];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
