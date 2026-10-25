<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Services\AttendanceService;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang điểm danh / chuyên cần ba cổng (FE #38, #42, #43; mock teacher/diem-danh, student/chuyen-can,
 * admin/diem-danh-chuyen-can). Chỉ trả view + URL API + cờ quyền; dữ liệu do resources/js/pages/*-attendance.js gọi API ATT.
 */
class AttendancePageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function teacher(Request $request): View
    {
        return view('teacher.attendance', ['pageData' => [
            'api' => [
                'sections' => route('teacher.course-sections.index'),
                'sessions' => route('teacher.attendance.sessions', ['courseSection' => '__ID__']),
                'summary' => route('teacher.attendance.summary', ['courseSection' => '__ID__']),
                'pushGrades' => route('teacher.attendance.push-grades', ['courseSection' => '__ID__']),
                'sheet' => route('teacher.attendance.sheet', ['classSession' => '__ID__']),
                'record' => route('teacher.attendance.record', ['classSession' => '__ID__']),
                'teachingLog' => route('teacher.attendance.teaching-log', ['classSession' => '__ID__']),
                'requestChange' => route('teacher.attendance.change-requests.store', ['attendanceRecord' => '__ID__']),
            ],
            'editDays' => (int) config('studentmanager.attendance.edit_days'),
            'can' => ['grades' => $this->access->allows($request->user(), 'GRD', PermissionAction::Update)],
        ]]);
    }

    public function student(Request $request): View
    {
        return view('student.attendance', ['pageData' => [
            'api' => ['mine' => route('student.attendance')],
        ]]);
    }

    public function admin(Request $request, AttendanceService $attendance): View
    {
        $user = $request->user();

        return view('admin.attendance.index', ['pageData' => [
            'api' => [
                'summaries' => route('admin.attendance.summaries'),
                'export' => route('admin.attendance.summaries.export'),
                'exempt' => route('admin.attendance.exempt', ['attendanceSummary' => '__ID__']),
                'requests' => route('admin.attendance.change-requests'),
                'approve' => route('admin.attendance.change-requests.approve', ['attendanceChangeRequest' => '__ID__']),
                'reject' => route('admin.attendance.change-requests.reject', ['attendanceChangeRequest' => '__ID__']),
                'lock' => route('admin.attendance.lock', ['courseSection' => '__ID__']),
                'unlock' => route('admin.attendance.unlock', ['courseSection' => '__ID__']),
                'sessions' => route('admin.attendance.sessions', ['courseSection' => '__ID__']),
                'sheet' => route('admin.attendance.sheet', ['classSession' => '__ID__']),
                'record' => route('admin.attendance.record', ['classSession' => '__ID__']),
                'terms' => route('admin.academic-years.index'),
                'faculties' => route('admin.units.index', ['unitType' => 'faculties']),
                'sections' => route('admin.course-sections.index'),
            ],
            'can' => [
                'approve' => $attendance->isOffice($user),
                'export' => $this->access->allows($user, 'ATT', PermissionAction::Export),
            ],
        ]]);
    }
}
