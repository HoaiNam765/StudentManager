<?php

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Attendance\Services\AttendanceReport;
use App\Modules\Student\Models\Student;
use App\Modules\System\Services\PolicyResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Sinh viên xem chuyên cần của chính mình theo học phần và từng buổi (#144; FR-ATT-007, BR-ATT-08). */
class StudentAttendanceController extends Controller
{
    public function mine(Request $request, AttendanceReport $report, PolicyResolver $policies): JsonResponse
    {
        $student = Student::query()->where('user_id', $request->user()->id)->firstOrFail();
        $cohort = (int) ($student->cohort ?? now()->year);
        $termId = $request->validate(['term_id' => ['nullable', 'integer', 'exists:terms,id']])['term_id'] ?? null;
        // Mặc định học kỳ đang diễn ra; giữa hai học kỳ thì học kỳ gần nhất đã bắt đầu
        $today = now(config('studentmanager.display_timezone'))->toDateString();
        $termId ??= Term::query()->whereDate('start_date', '<=', $today)->orderByDesc('start_date')->value('id');

        return response()->json([
            'student' => $student->only('id', 'student_code', 'full_name', 'cohort'),
            'thresholds' => [
                'warning' => (float) $policies->value('attendance.warning_percent', $cohort),
                'ban' => (float) $policies->value('attendance.ban_percent', $cohort),
            ],
            'term' => $termId === null ? null : Term::query()->find($termId)?->only('id', 'name'),
            'sections' => $report->forStudent($student, $termId === null ? null : (int) $termId),
        ]);
    }
}
