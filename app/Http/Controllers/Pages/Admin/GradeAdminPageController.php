<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Services\GradeChangeService;
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
        $terms = Term::query()->orderByDesc('start_date')->get();
        $faculties = Faculty::query()->orderBy('name')->get();

        // Mặc định: học kỳ đang học / đang thi – chấm điểm, rồi học kỳ gần nhất đã bắt đầu (không lấy học kỳ chưa tới)
        $today = now(config('studentmanager.display_timezone'))->toDateString();
        $defaultTerm = $terms->first(fn (Term $t) => in_array($t->status, [TermStatus::IN_PROGRESS, TermStatus::EXAM_GRADING], true))
            ?? $terms->first(fn (Term $t) => $t->start_date->toDateString() <= $today)
            ?? $terms->first();
        $selectedTermId = $request->integer('term_id') ?: $defaultTerm?->id;
        $selectedFacultyId = $request->integer('faculty_id') ?: null;
        $selectedStatus = $request->string('status')->toString();
        $activeTab = $request->string('tab', 'workflow')->toString();

        // 1. Lọc danh sách Lớp học phần
        // Chỉ lớp trong phạm vi GRD.view của người xem (lãnh đạo khoa: lớp của khoa mình)
        $scoped = fn () => $this->access->constrain(CourseSection::query(), $user, 'GRD', PermissionAction::View)->select('id');
        $sectionsQuery = CourseSection::query()->whereIn('id', $scoped())
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
            ->whereIn('enrollment_id', Enrollment::query()->select('id')->whereIn('course_section_id', $scoped()))
            ->with(['enrollment.student', 'enrollment.section.subject', 'creator'])
            ->latest('id')
            ->paginate(10, ['*'], 'changes_page');

        // 3. Thống kê KPI
        $totalSectionsCount = CourseSection::query()->whereIn('id', $scoped())->when($selectedTermId, fn ($q) => $q->where('term_id', $selectedTermId))->count();
        $pendingApprovalCount = SectionGradeWorkflow::query()->whereIn('course_section_id', $scoped())
            ->whereIn('status', ['submitted', 'faculty_approved'])
            ->whereNull('deleted_at')
            ->count();
        $publishedCount = SectionGradeWorkflow::query()->whereIn('course_section_id', $scoped())
            ->whereIn('status', ['published', 'locked'])
            ->whereNull('deleted_at')
            ->count();
        $pendingChangesCount = GradeChangeRequest::query()
            ->whereIn('enrollment_id', Enrollment::query()->select('id')->whereIn('course_section_id', $scoped()))
            ->whereIn('status', GradeChangeService::PENDING)
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
