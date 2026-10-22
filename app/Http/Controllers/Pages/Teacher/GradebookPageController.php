<?php

namespace App\Http\Controllers\Pages\Teacher;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\Grade\Services\GradeWorkflowService;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Trang quản trị sổ điểm và nộp điểm lớp học phần của giảng viên (#124, #126, #127).
 * Cho phép nhập điểm, lưu nháp, nộp điểm lên Khoa/P.ĐT, tải mẫu/nhập Excel, và xuất sổ điểm.
 */
class GradebookPageController extends Controller
{
    public function __construct(
        private readonly GradebookService $gradebook,
        private readonly GradeWorkflowService $workflowService,
        private readonly AccessControl $access
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $sectionsQuery = $this->access->constrain(
            CourseSection::query()->with(['subject', 'term']),
            $user,
            'GRD',
            PermissionAction::View
        );

        // Học kỳ mới nhất trước (theo ngày bắt đầu học kỳ), bỏ lớp đã hủy
        $sections = $sectionsQuery->where('status', '!=', SectionStatus::Cancelled->value)->orderBy('code')->get()
            ->sortByDesc(fn (CourseSection $s) => $s->term?->start_date?->format('Y-m-d'))->values();
        $selectedSectionId = $request->integer('course_section_id') ?: ($sections->first()?->id ?? null);

        $selectedSection = null;
        $workflow = null;
        $gradebookData = null;
        $canEdit = false;

        if ($selectedSectionId !== null) {
            $selectedSection = CourseSection::query()->with(['subject', 'term'])->find($selectedSectionId);

            if ($selectedSection !== null) {
                abort_unless($this->access->allowsOn($user, 'GRD', PermissionAction::View, $selectedSection), 403, 'Bạn không có quyền xem sổ điểm lớp này.');

                $workflow = $this->workflowService->current($selectedSection);
                $gradebookData = $this->withResults($this->gradebook->gradebook($selectedSection, $user));

                $statusVal = $workflow->status instanceof GradePublicationStatus ? $workflow->status->value : (string) $workflow->status;
                $canEdit = in_array($statusVal, ['draft', 'returned'], true)
                    && in_array($selectedSection->status, [SectionStatus::InProgress, SectionStatus::Finished], true)
                    && $this->access->allowsOn($user, 'GRD', PermissionAction::Update, $selectedSection);
            }
        }

        $pageData = [
            'api' => [
                'saveDraft' => $selectedSection ? route('teacher.gradebook.draft.save', ['courseSection' => $selectedSection->id]) : null,
                'workflowChange' => $selectedSection ? route('teacher.gradebook.workflow.change', ['courseSection' => $selectedSection->id]) : null,
                'template' => $selectedSection ? route('teacher.gradebook.template', ['courseSection' => $selectedSection->id]) : null,
                'export' => $selectedSection ? route('teacher.gradebook.export', ['courseSection' => $selectedSection->id]) : null,
                'import' => $selectedSection ? route('teacher.gradebook.import', ['courseSection' => $selectedSection->id]) : null,
            ],
            'selectedSectionId' => $selectedSectionId,
            'canEdit' => $canEdit,
        ];

        return view('teacher.grade.gradebook', compact(
            'sections',
            'selectedSection',
            'workflow',
            'gradebookData',
            'canEdit',
            'pageData'
        ));
    }

    /** Nộp bảng điểm từ form của giao diện (FR-GRD-004): tính kết quả, chặn khi thiếu điểm; quay lại trang kèm thông báo. */
    public function workflow(Request $request, CourseSection $courseSection): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in([GradePublicationStatus::Submitted->value])]]);

        try {
            $this->workflowService->change($courseSection, $request->user(), GradePublicationStatus::from($data['status']));
        } catch (BusinessRuleException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Đã nộp bảng điểm lớp {$courseSection->code}.");
    }

    /**
     * Thêm cho từng sinh viên: `student.name` (họ tên) và `final_result` (điểm hệ 10, chữ, hệ 4, đạt) — các khóa view
     * teacher/grade/gradebook đang dùng.
     */
    private function withResults(array $book): array
    {
        $results = CourseResult::query()->whereIn('enrollment_id', collect($book['students'])->pluck('enrollment_id'))->get()->keyBy('enrollment_id');

        $book['students'] = collect($book['students'])->map(function (array $row) use ($results): array {
            $result = $results->get($row['enrollment_id']);
            $row['student']['name'] = $row['student']['full_name'] ?? null;
            $row['final_result'] = $result === null ? null : [
                'score_10' => $result->score10,
                'letter' => $result->letter,
                'score_4' => $result->point4,
                'passed' => $result->is_passed,
            ];

            return $row;
        })->all();

        return $book;
    }
}
