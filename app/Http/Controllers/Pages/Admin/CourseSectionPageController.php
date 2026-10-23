<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang quản trị lớp học phần (#18): mở lớp, phân công giảng viên, vòng đời và xử lý lớp thiếu sĩ số.
 * Chỉ trả view + URL API; toàn bộ dữ liệu thật do resources/js/pages/admin-course-sections.js gọi.
 */
class CourseSectionPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.course-section.index', ['pageData' => [
            'api' => [
                'list' => route('admin.course-sections.index'),
                'show' => route('admin.course-sections.show', ['courseSection' => '__ID__']),
                'store' => route('admin.course-sections.store'),
                'update' => route('admin.course-sections.update', ['courseSection' => '__ID__']),
                'destroy' => route('admin.course-sections.destroy', ['courseSection' => '__ID__']),
                'status' => route('admin.course-sections.status', ['courseSection' => '__ID__', 'status' => '__STATUS__']),
                'assign' => route('admin.course-sections.instructors.store', ['courseSection' => '__ID__']),
                'unassign' => route('admin.section-instructors.destroy', ['sectionInstructor' => '__ID__']),
                'underMin' => route('admin.terms.under-min-sections', ['term' => '__TERM__']),
                'merge' => route('admin.course-sections.merge', ['courseSection' => '__ID__']),
                'unlock' => route('admin.course-sections.unlock', ['courseSection' => '__ID__']),
                'terms' => route('admin.academic-years.index'),
                'subjects' => route('admin.subjects.index'),
                'lecturers' => route('admin.lecturers.index'),
                'rooms' => route('admin.rooms.index'),
            ],
            'can' => [
                'create' => $this->access->allowsGlobally($user, 'CLS', PermissionAction::Create),
                'update' => $this->access->allowsGlobally($user, 'CLS', PermissionAction::Update),
                'delete' => $this->access->allowsGlobally($user, 'CLS', PermissionAction::Delete),
                'approve' => $this->access->allowsGlobally($user, 'CLS', PermissionAction::Approve),
            ],
        ]]);
    }
}
