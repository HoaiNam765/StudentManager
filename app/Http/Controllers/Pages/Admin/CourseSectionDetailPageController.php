<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Chi tiết lớp học phần (FE #18, mock admin/lop-hoc-phan-chi-tiet): thông tin lớp, giảng viên, lịch học, danh sách sinh
 * viên đăng ký, xuất danh sách, hủy đăng ký thay. Dữ liệu do resources/js/pages/admin-course-section-detail.js gọi API
 * CLS / ENR / TTB (cùng middleware quyền và phạm vi như API).
 */
class CourseSectionDetailPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function show(Request $request, CourseSection $courseSection): View
    {
        abort_unless($this->access->allowsOn($request->user(), 'CLS', PermissionAction::View, $courseSection), 403,
            'Lớp học phần này nằm ngoài phạm vi dữ liệu của bạn.');
        $user = $request->user();
        $id = ['courseSection' => $courseSection->id];

        return view('admin.course-section.show', ['section' => $courseSection, 'pageData' => [
            'api' => [
                'show' => route('admin.course-sections.show', $id),
                'enrollments' => route('admin.course-sections.enrollments', $id),
                'export' => route('admin.course-sections.enrollments.export', $id),
                'schedule' => route('admin.course-sections.schedule.index', $id),
                'sessions' => route('admin.course-sections.sessions.index', $id),
                'cancelEnrollment' => route('admin.enrollments.cancel', ['enrollment' => '__ID__']),
            ],
            'links' => [
                'list' => route('admin.pages.course-sections'),
                'student' => route('admin.pages.students.show', ['student' => '__ID__']),
                'grades' => route('admin.pages.grade-section', $id),
            ],
            'can' => [
                'enrollments' => $this->access->allows($user, 'ENR', PermissionAction::View),
                'export' => $this->access->allows($user, 'ENR', PermissionAction::Export),
                'cancel' => $this->access->allowsGlobally($user, 'ENR', PermissionAction::Delete),
                'timetable' => $this->access->allows($user, 'TTB', PermissionAction::View),
                'students' => $this->access->allows($user, 'STU', PermissionAction::View),
                'grades' => $this->access->allows($user, 'GRD', PermissionAction::View),
            ],
        ]]);
    }
}
