<?php

namespace App\Modules\Attendance;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Timetable\Models\ClassSession;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\ServiceProvider;

/** Điểm danh (ATT, BA 6.8): ghi nhận theo buổi, tỷ lệ vắng, cảnh báo / cấm thi, khóa sổ, sổ đầu bài. */
class AttendanceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $references = app(ReferenceRegistry::class);
        $notDeleted = fn ($q) => $q->whereNull('deleted_at');

        // Buổi học / đăng ký đã có điểm danh thì không xóa cứng
        $references->register(ClassSession::class, 'attendance_records', 'class_session_id', 'điểm danh', constraint: $notDeleted);
        $references->register(Enrollment::class, 'attendance_records', 'enrollment_id', 'điểm danh', constraint: $notDeleted);
    }
}
