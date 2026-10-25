<?php

namespace App\Modules\Attendance\Models;

use App\Modules\Attendance\Models\Concerns\ScopedByStudentAndSection;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Tổng hợp chuyên cần của một đăng ký học phần: số buổi vắng quy đổi, tỷ lệ vắng, cờ cảnh báo / cấm thi (FR-ATT-008, 009). */
class AttendanceSummary extends StandardModel implements HasDataScope
{
    use ScopedByStudentAndSection;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'absences' => 'float',
            'absence_rate' => 'float',
            'attendance_score' => 'float',
            'warning' => 'boolean',
            'banned' => 'boolean',
            'exempted' => 'boolean',
            'warned_at' => 'datetime',
            'banned_at' => 'datetime',
            'calculated_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }
}
