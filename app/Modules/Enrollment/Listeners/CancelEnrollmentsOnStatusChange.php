<?php

namespace App\Modules\Enrollment\Listeners;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Events\StudentStatusChanged;
use Illuminate\Support\Facades\DB;

/**
 * BR-STU-10: sinh viên chuyển Bảo lưu / Thôi học / Buộc thôi học / Chuyển trường → hủy các đăng ký chưa chốt
 * (Đã đăng ký) của học kỳ chưa bắt đầu tính tới ngày hiệu lực. Đăng ký mới bị chặn vì ENR chỉ nhận sinh viên Đang học.
 */
class CancelEnrollmentsOnStatusChange
{
    private const LEAVING = [StudentStatus::Suspended, StudentStatus::Withdrawn, StudentStatus::Expelled, StudentStatus::Transferred];

    public function handle(StudentStatusChanged $event): void
    {
        if (! in_array($event->to, self::LEAVING, true)) {
            return;
        }

        $enrollments = Enrollment::query()
            ->where('student_id', $event->student->id)
            ->where('status', EnrollmentStatus::Registered)
            ->whereIn('term_id', Term::query()->select('id')->where('start_date', '>', $event->history->effective_date->toDateString()))
            ->lockForUpdate()
            ->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        $now = now();
        $reason = "{$event->to->label()} theo quyết định {$event->history->decision_no}";
        $enrollments->each(fn (Enrollment $e) => $e->update(['status' => EnrollmentStatus::Cancelled, 'cancelled_at' => $now]));

        DB::table('enrollment_logs')->insert($enrollments->map(fn (Enrollment $e) => [
            'student_id' => $e->student_id,
            'course_section_id' => $e->course_section_id,
            'action' => 'status_cancelled',
            'success' => true,
            'reasons' => json_encode([$reason], JSON_UNESCAPED_UNICODE),
            'user_id' => auth()->id(),
            'created_at' => $now,
        ])->all());
    }
}
