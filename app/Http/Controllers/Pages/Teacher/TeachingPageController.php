<?php

namespace App\Http\Controllers\Pages\Teacher;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang cổng giảng viên nối API (FE #24, #25; mock teacher/lich-giang-day, teacher/lop-hoc-phan,
 * teacher/lop-co-van-hoc-tap). Chỉ trả view + URL API + cờ quyền; dữ liệu do resources/js/pages/teacher-*.js gọi API
 * TTB / CLS / RPT của chính giảng viên (phạm vi SECTION, ADVISEE).
 */
class TeachingPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    /** Lịch giảng dạy: buổi dạy hôm nay, thời khóa biểu tuần, lớp phụ trách, việc cần xử lý. */
    public function schedule(Request $request): View
    {
        return view('teacher.teaching.schedule', ['pageData' => [
            'api' => [
                'timetable' => route('teacher.timetable'),
                'sections' => route('teacher.course-sections.index'),
                'dashboard' => route('teacher.dashboard.data'),
            ],
            'links' => $this->links(),
            'user' => $request->user()->name,
        ]]);
    }

    /** Lớp học phần đang phụ trách và danh sách sinh viên từng lớp. */
    public function sections(Request $request): View
    {
        return view('teacher.teaching.sections', ['pageData' => [
            'api' => [
                'sections' => route('teacher.course-sections.index'),
                'students' => route('teacher.course-sections.students.index', ['courseSection' => '__ID__']),
            ],
            'links' => $this->links(),
        ]]);
    }

    /** Lớp cố vấn học tập: sinh viên, GPA, tín chỉ tích lũy, cảnh báo. */
    public function advisees(Request $request): View
    {
        return view('teacher.teaching.advisees', ['pageData' => [
            'api' => [
                'classes' => route('teacher.admin-classes.index'),
                'students' => route('teacher.admin-classes.students.index', ['adminClass' => '__ID__']),
            ],
            'can' => ['grades' => $this->access->allows($request->user(), 'GRD', PermissionAction::View)],
        ]]);
    }

    /** @return array<string, string> */
    private function links(): array
    {
        return [
            'schedule' => route('teacher.pages.schedule'),
            'sections' => route('teacher.pages.sections'),
            'gradebook' => route('teacher.pages.gradebook'),
            'announcements' => route('teacher.pages.announcements'),
        ];
    }
}
