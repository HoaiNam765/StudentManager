<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\Grade\Enums\GradePublicationStatus as Status;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Services\GradeChangeService;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang duyệt và công bố điểm cho cán bộ (FR-GRD-004, 005, 011; mock admin/quan-ly-diem): lớp học phần đang học / kết
 * thúc / đã khóa của học kỳ trong phạm vi GRD.view (lãnh đạo khoa chỉ thấy lớp của khoa mình), lọc theo khoa và trạng thái
 * bảng điểm, thẻ số liệu, nút bước tiếp theo theo vai trò. Các nút gọi API `admin.course-sections.grade-workflow.change`
 * (resources/js/pages/admin-grade-workflows.js); máy chủ kiểm tra lại quyền từng bước trong GradeWorkflowService.
 */
class GradeAdminPageController extends Controller
{
    private const STATUSES = [SectionStatus::InProgress, SectionStatus::Finished, SectionStatus::Locked];

    public function __construct(
        private readonly AccessControl $access,
        private readonly FacultyAccess $faculties,
        private readonly GradeChangeService $changes,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $terms = Term::query()->with('academicYear:id,name')->orderByDesc('start_date')->get();
        $termId = $request->integer('term_id') ?: $this->defaultTerm($terms);
        $global = $this->access->allowsGlobally($user, 'GRD', PermissionAction::View);
        $faculties = Faculty::query()->when(! $global, fn ($q) => $q->whereIn('id', $this->faculties->facultyIds($user)))->orderBy('name')->get(['id', 'name']);
        $facultyId = $request->integer('faculty_id') ?: null;
        $status = $request->string('status')->toString();

        $base = fn (): Builder => $this->access->constrain(CourseSection::query(), $user, 'GRD', PermissionAction::View)
            ->where('course_sections.term_id', $termId)
            ->whereIn('course_sections.status', array_map(fn ($s) => $s->value, self::STATUSES));

        $sections = $base()
            ->with('subject:id,code,name,credits,department_id', 'subject.department:id,faculty_id', 'instructors.lecturer:id,code,full_name')
            ->when($facultyId, fn ($q) => $q->whereHas('subject.department', fn ($d) => $d->where('faculty_id', $facultyId)))
            ->when($status !== '', fn ($q) => $status === Status::Draft->value
                ? $q->whereNotIn('id', SectionGradeWorkflow::query()->select('course_section_id')->where('status', '!=', Status::Draft->value))
                : $q->whereIn('id', SectionGradeWorkflow::query()->select('course_section_id')->where('status', $status)))
            ->withCount(['instructors'])
            ->orderBy('code')
            ->paginate(20)
            ->withQueryString();

        $workflows = SectionGradeWorkflow::query()->whereIn('course_section_id', $base()->select('course_sections.id'))->get()->keyBy('course_section_id');
        $counts = $workflows->countBy(fn (SectionGradeWorkflow $w) => $w->status->value);
        $pendingChanges = GradeChangeRequest::query()->whereIn('status', GradeChangeService::PENDING)->with('enrollment.section')->get()
            ->filter(fn (GradeChangeRequest $r) => $this->changes->canAct($user, $r))->count();

        return view('admin.grade.index', [
            'terms' => $terms,
            'faculties' => $faculties,
            'selectedTermId' => $termId,
            'selectedFacultyId' => $facultyId,
            'selectedStatus' => $status,
            'statuses' => collect(Status::cases())->mapWithKeys(fn (Status $s) => [$s->value => $s->label()]),
            'sections' => $sections,
            'workflows' => $workflows,
            'actions' => $sections->getCollection()->mapWithKeys(fn (CourseSection $s) => [$s->id => $this->actions($user, $s, $workflows->get($s->id)?->status ?? Status::Draft)]),
            'kpi' => [
                'total' => $base()->count(),
                'pending' => ($counts['submitted'] ?? 0) + ($counts['faculty_approved'] ?? 0) + ($counts['confirmed'] ?? 0),
                'published' => ($counts['published'] ?? 0) + ($counts['locked'] ?? 0),
                'changes' => $pendingChanges,
            ],
            'pageData' => [
                'api' => ['change' => route('admin.course-sections.grade-workflow.change', ['courseSection' => '__ID__'])],
            ],
        ]);
    }

    /**
     * Bước người xem được bấm từ trạng thái hiện tại (giống quy tắc trong GradeWorkflowService::authorizeStep):
     * khoa duyệt = DEAN trong phạm vi; trả lại = DEAN hoặc ACAD / EXAM; các bước còn lại = ACAD / EXAM toàn trường.
     *
     * @return list<array{status: string, label: string}>
     */
    private function actions(User $user, CourseSection $section, Status $current): array
    {
        $isDean = $user->hasRole('DEAN') && $this->access->allowsOn($user, 'GRD', PermissionAction::Approve, $section);
        $isOffice = ($user->hasRole('ACAD') || $user->hasRole('EXAM')) && $this->access->allowsGlobally($user, 'GRD', PermissionAction::Approve);
        $facultyRequired = (bool) config('grade_lifecycle.faculty_approval_required', true);
        $labels = [
            Status::FacultyApproved->value => 'Khoa duyệt',
            Status::Confirmed->value => 'Xác nhận',
            Status::Published->value => 'Công bố',
            Status::Locked->value => 'Khóa sổ',
            Status::Returned->value => 'Trả lại',
        ];

        return collect([Status::FacultyApproved, Status::Confirmed, Status::Published, Status::Locked, Status::Returned])
            ->filter(fn (Status $next) => $current->allows($next, $facultyRequired) && match ($next) {
                Status::FacultyApproved => $isDean,
                Status::Returned => $isDean || $isOffice,
                default => $isOffice,
            })
            ->map(fn (Status $next) => ['status' => $next->value, 'label' => $labels[$next->value]])
            ->values()->all();
    }

    /** Học kỳ đang diễn ra theo ngày, không có thì học kỳ gần nhất đã bắt đầu. */
    private function defaultTerm($terms): ?int
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return ($terms->first(fn (Term $t) => $t->start_date->toDateString() <= $today && $t->end_date->toDateString() >= $today)
            ?? $terms->first(fn (Term $t) => $t->start_date->toDateString() <= $today)
            ?? $terms->first())?->id;
    }
}
