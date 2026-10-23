<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Giao diện vận hành năm học, học kỳ, lịch học vụ và ngày nghỉ (#9).
 * Controller chỉ truyền URL API và cờ quyền; dữ liệu nghiệp vụ vẫn được kiểm tra lại ở API.
 */
class AcademicYearPageController extends Controller
{
    public function __construct(private readonly AccessControl $access) {}

    public function index(Request $request): View
    {
        return view('admin.academic-year.index', ['pageData' => [
            'api' => [
                'academicYears' => route('admin.academic-years.index'),
                'academicYear' => route('admin.academic-years.show', ['academicYear' => '__ID__']),
                'currentTerm' => route('admin.academic-years.current-term'),
                'termsStore' => route('admin.academic-years.terms.store'),
                'termCurrent' => route('admin.academic-years.terms.current', ['term' => '__ID__']),
                'termStatus' => route('admin.academic-years.terms.status', ['term' => '__ID__', 'status' => '__STATUS__']),
                'milestones' => route('admin.terms.milestones.index', ['term' => '__ID__']),
                'milestonesCopy' => route('admin.terms.milestones.copy', ['term' => '__ID__']),
                'changeRequests' => route('admin.milestone-change-requests.index'),
                'changeApprove' => route('admin.milestone-change-requests.approve', ['milestoneChangeRequest' => '__ID__']),
                'changeReject' => route('admin.milestone-change-requests.reject', ['milestoneChangeRequest' => '__ID__']),
                'holidays' => route('admin.holidays.index'),
                'holiday' => route('admin.holidays.update', ['holiday' => '__ID__']),
                'holidayDelete' => route('admin.holidays.destroy', ['holiday' => '__ID__']),
            ],
            'can' => [
                'create' => $this->access->allowsGlobally($request->user(), 'ACY', PermissionAction::Create),
                'update' => $this->access->allows($request->user(), 'ACY', PermissionAction::Update),
                'approve' => $this->access->allowsGlobally($request->user(), 'ACY', PermissionAction::Approve),
                'delete' => $this->access->allows($request->user(), 'ACY', PermissionAction::Delete),
            ],
        ]]);
    }
}
