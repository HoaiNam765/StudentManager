<?php

namespace App\Http\Controllers\Pages\Teacher;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\Grade\Services\GradeWorkflowService;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang sổ điểm lớp học phần của giảng viên (FR-GRD-001, 002, 004, 011; mock teacher/nhap-diem): chọn lớp được phân
 * công, xem cơ cấu điểm, nhập / lưu nháp điểm thành phần, nhập Excel, nộp bảng điểm, xuất sổ điểm PDF / Excel.
 * Bảng điểm dựng sẵn ở máy chủ; các thao tác ghi do resources/js/pages/teacher-gradebook.js gọi API GRD (cùng kiểm
 * tra quyền, phân công, trạng thái bảng điểm trong GradebookService / GradeWorkflowService).
 */
class GradebookPageController extends Controller
{
    public function __construct(
        private readonly GradebookService $gradebook,
        private readonly GradeWorkflowService $workflows,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        // Lớp được phân công (phạm vi GRD của giảng viên), bỏ lớp đã hủy
        $sections = $this->access->constrain(CourseSection::query(), $user, 'GRD', PermissionAction::View)
            ->with('subject:id,code,name,credits', 'term:id,name,academic_year_id,start_date', 'term.academicYear:id,name')
            ->where('status', '!=', SectionStatus::Cancelled->value)
            ->orderBy('code')
            ->get()
            // Học kỳ mới nhất trước (theo ngày bắt đầu, không theo mã học kỳ)
            ->sortByDesc(fn (CourseSection $s) => $s->term?->start_date?->format('Y-m-d'))
            ->values();

        $selected = $sections->firstWhere('id', $request->integer('course_section_id')) ?? $sections->first();
        $workflow = null;
        $book = null;
        $results = collect();
        $canEdit = false;

        if ($selected !== null) {
            $workflow = $this->workflows->current($selected);
            $book = $this->gradebook->gradebook($selected, $user);
            $results = CourseResult::query()->whereIn('enrollment_id', collect($book['students'])->pluck('enrollment_id'))->get()->keyBy('enrollment_id');
            $canEdit = $workflow->status->editable()
                && in_array($selected->status, [SectionStatus::InProgress, SectionStatus::Finished], true)
                && $this->access->allowsOn($user, 'GRD', PermissionAction::Update, $selected);
        }

        return view('teacher.grade.gradebook', [
            'sections' => $sections,
            'selectedSection' => $selected,
            'workflow' => $workflow,
            'gradebookData' => $book,
            'results' => $results,
            'canEdit' => $canEdit,
            'specialStatuses' => collect(config('grade_special_statuses'))->map(fn (array $s) => $s['label'])->all(),
            'pageData' => $selected === null ? [] : [
                'api' => [
                    'saveDraft' => route('teacher.gradebook.draft.save', $selected),
                    'initialize' => route('teacher.gradebook.initialize', $selected),
                    'import' => route('teacher.gradebook.import', $selected),
                    'template' => route('teacher.gradebook.template', $selected),
                    'workflow' => route('teacher.grade-workflow.change', $selected),
                    'report' => route('teacher.grade-report', $selected),
                    'special' => route('teacher.special-status', ['enrollment' => '__ID__']),
                ],
                'sectionCode' => $selected->code,
            ],
        ]);
    }
}
