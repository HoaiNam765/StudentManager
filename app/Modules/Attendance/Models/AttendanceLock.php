<?php

namespace App\Modules\Attendance\Models;

use App\Models\User;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Khóa sổ điểm danh của lớp học phần (FR-ATT-013); đã khóa chỉ Phòng Đào tạo sửa, kèm lý do (BR-ATT-07). */
class AttendanceLock extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['locked' => 'boolean', 'locked_at' => 'datetime'];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
