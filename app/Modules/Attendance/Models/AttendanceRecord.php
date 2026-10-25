<?php

namespace App\Modules\Attendance\Models;

use App\Models\User;
use App\Modules\Attendance\Enums\AttendanceStatus;
use App\Modules\Attendance\Models\Concerns\ScopedByStudentAndSection;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Modules\Timetable\Models\ClassSession;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Điểm danh một sinh viên trong một buổi học (FR-ATT-002). Mọi lần sửa ghi nhật ký kiểm toán (giá trị cũ – mới). */
class AttendanceRecord extends StandardModel implements HasDataScope
{
    use ScopedByStudentAndSection;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'minutes_late' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
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

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
