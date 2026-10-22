<?php

namespace App\Http\Controllers\Pages\Teacher;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\Grade\Services\GradeWorkflowService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Teacher\Models\Lecturer;
use Illuminate\Http\Request;
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
        $lecturer = Lecturer::query()->where('user_id', $user->id)->first();

        $sectionsQuery = $this->access->constrain(
            CourseSection::query()->with(['subject', 'term']),
            $user,
            'GRD',
            PermissionAction::View
        );

        $sections = $sectionsQuery->orderByDesc('id')->get();
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
                $gradebookData = $this->gradebook->gradebook($selectedSection, $user);

                $statusVal = $workflow->status instanceof GradePublicationStatus ? $workflow->status->value : (string) $workflow->status;
                $canEdit = in_array($statusVal, ['draft', 'returned'], true)
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
}
