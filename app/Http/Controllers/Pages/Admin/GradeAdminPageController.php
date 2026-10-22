<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Services\GradeWorkflowService;
use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang quản lý quy trình duyệt, công bố điểm và điều chỉnh điểm của Quản trị / Phòng ĐT (#124, #126, #127).
 * Hỗ trợ duyệt khoa (DEAN), xác nhận & công bố (ACAD/EXAM), trả về kèm lý do, và duyệt yêu cầu sửa điểm.
 */
class GradeAdminPageController extends Controller
{
    public function __construct(
        private readonly GradeWorkflowService $workflowService,
        private readonly AccessControl $access
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $terms = Term::query()->orderByDesc('id')->get();
        $faculties = Faculty::query()->orderBy('name')->get();

        $selectedTermId = $request->integer('term_id') ?: ($terms->first()?->id ?? null);
        $selectedFacultyId = $request->integer('faculty_id') ?: null;
        $selectedStatus = $request->string('status')->toString();
        $activeTab = $request->string('tab', 'workflow')->toString();

        // 1. Lọc danh sách Lớp học phần
        $sectionsQuery = CourseSection::query()
            ->with(['subject.department.faculty', 'term', 'instructors.lecturer'])
            ->when($selectedTermId, fn ($q) => $q->where('term_id', $selectedTermId))
            ->when($selectedFacultyId, fn ($q) => $q->whereHas('subject.department', fn ($dq) => $dq->where('faculty_id', $selectedFacultyId)));

        if ($selectedStatus && $selectedStatus !== 'all') {
            $sectionsQuery->whereIn('id', function ($sub) use ($selectedStatus) {
                $sub->select('course_section_id')->from('section_grade_workflows')
                    ->where('status', $selectedStatus)
                    ->whereNull('deleted_at');
            });
        }

        $sections = $sectionsQuery->orderByDesc('id')->paginate(15)->withQueryString();

        // Lấy workflows tương ứng
        $workflows = SectionGradeWorkflow::query()
            ->whereIn('course_section_id', $sections->pluck('id'))
            ->get()
            ->keyBy('course_section_id');

        // 2. Danh sách yêu cầu điều chỉnh điểm sau công bố (#126)
        $changeRequests = GradeChangeRequest::query()
            ->with(['enrollment.student', 'enrollment.section.subject', 'creator'])
            ->latest('id')
            ->paginate(10, ['*'], 'changes_page');

        // 3. Thống kê KPI
        $totalSectionsCount = CourseSection::query()->when($selectedTermId, fn ($q) => $q->where('term_id', $selectedTermId))->count();
        $pendingApprovalCount = SectionGradeWorkflow::query()
            ->whereIn('status', ['submitted', 'faculty_approved'])
            ->whereNull('deleted_at')
            ->count();
        $publishedCount = SectionGradeWorkflow::query()
            ->whereIn('status', ['published', 'locked'])
            ->whereNull('deleted_at')
            ->count();
        $pendingChangesCount = GradeChangeRequest::query()
            ->whereIn('status', ['pending_faculty', 'pending_acad'])
            ->count();

        $canApprove = $this->access->allows($user, 'GRD', PermissionAction::Approve);

        $pageData = [
            'api' => [
                'workflowChange' => route('admin.course-sections.grade-workflow.change', ['courseSection' => '__ID__']),
                'export' => route('admin.course-sections.gradebook.export', ['courseSection' => '__ID__']),
                'approveChange' => route('admin.grade-change-requests.approve', ['gradeChangeRequest' => '__ID__']),
                'rejectChange' => route('admin.grade-change-requests.reject', ['gradeChangeRequest' => '__ID__']),
            ],
            'canApprove' => $canApprove,
        ];

        return view('admin.grade.index', compact(
            'terms',
            'faculties',
            'selectedTermId',
            'selectedFacultyId',
            'selectedStatus',
            'activeTab',
            'sections',
            'workflows',
            'changeRequests',
            'totalSectionsCount',
            'pendingApprovalCount',
            'publishedCount',
            'pendingChangesCount',
            'canApprove',
            'pageData'
        ));
    }
}
