<?php

namespace App\Modules\Enrollment\Listeners;

use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Events\EnrollmentsCancelledByClass;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Events\CourseSectionCancelled;
use Illuminate\Support\Facades\DB;

/** Lớp bị hủy → hủy mọi đăng ký còn hiệu lực của lớp, ghi nhật ký đăng ký, phát EV-ENR-03 (BR-CLS-09). */
class CancelEnrollmentsOfSection
{
    public function handle(CourseSectionCancelled $event): void
    {
        $enrollments = Enrollment::query()
            ->where('course_section_id', $event->section->id)
            ->whereIn('status', [EnrollmentStatus::Registered->value, EnrollmentStatus::Confirmed->value])
            ->lockForUpdate()
            ->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        $now = now();

        foreach ($enrollments as $enrollment) {
            $enrollment->update(['status' => EnrollmentStatus::Cancelled, 'cancelled_at' => $now]);
        }

        DB::table('enrollment_logs')->insert($enrollments->map(fn (Enrollment $e) => [
            'student_id' => $e->student_id,
            'course_section_id' => $e->course_section_id,
            'action' => 'class_cancelled',
            'success' => true,
            'reasons' => json_encode(["Lớp bị hủy: {$event->reason}"], JSON_UNESCAPED_UNICODE),
            'user_id' => auth()->id(),
            'created_at' => $now,
        ])->all());

        EnrollmentsCancelledByClass::dispatch($event->section, $enrollments->pluck('student_id')->all(), $event->reason);
    }
}
