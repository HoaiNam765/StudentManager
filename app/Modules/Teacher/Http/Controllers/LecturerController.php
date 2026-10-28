<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\Teacher\Contracts\TeachingAssignments;
use App\Modules\Teacher\Enums\LecturerStatus;
use App\Modules\Teacher\Http\Requests\LecturerProfileRequest;
use App\Modules\Teacher\Http\Requests\LecturerRequest;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Models\LecturerChangeRequest;
use App\Modules\Teacher\Models\LecturerUnit;
use App\Modules\Teacher\Services\LecturerProfileService;
use App\Modules\Teacher\Services\LecturerService;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Exports\ExportColumn;
use App\Support\Services\ExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * API quản lý hồ sơ giảng viên (FR-TCH-001, 003; BR-TCH-01, 02, 07). Quyền: middleware `permission:TCH.*` chặn cả route;
 * Policy + AccessControl kiểm tra phạm vi trên từng bản ghi và lọc danh sách (DEAN: giảng viên thuộc đơn vị mình;
 * LEC, ADV: hồ sơ của chính mình). Thông tin nhạy cảm (CCCD, ngày sinh, điện thoại…) chỉ trả cho người sửa được hồ sơ.
 */
class LecturerController extends Controller
{
    public function __construct(
        private readonly LecturerService $lecturers,
        private readonly LecturerProfileService $profiles,
        private readonly AccessControl $access,
        private readonly TeachingAssignments $assignments,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max($request->integer('per_page', 50), 1), 100);
        $list = $this->access->constrain($this->listQuery($request), $request->user(), 'TCH', PermissionAction::View)->paginate($perPage);
        // ?term_id=: số lớp và số tiết được phân công trong học kỳ (cột "Lớp HP", "Giờ chuẩn / thực" của trang giảng viên)
        $loads = $request->filled('term_id') ? $this->assignments->termLoads($list->pluck('id')->all(), $request->integer('term_id')) : null;
        $limit = (int) config('studentmanager.course_section.max_periods_per_term', 400);

        return response()->json($list->through(fn (Lecturer $lecturer) => $this->present($lecturer, $request->user())
            + ($loads === null ? [] : ['load' => ($loads[$lecturer->id] ?? ['sections' => 0, 'periods' => 0]) + ['limit' => $limit]])));
    }

    public function show(Request $request, Lecturer $lecturer): JsonResponse
    {
        Gate::authorize('view', $lecturer);
        $user = $request->user();
        $lecturer->load(['department.faculty', 'gender', 'user']);

        $data = $this->present($lecturer, $user) + [
            'units' => $lecturer->units()->orderByDesc('starts_on')->get()->map(fn (LecturerUnit $unit) => $this->presentUnit($unit)),
        ];

        // Người khác xem CCCD: ghi nhật ký xem dữ liệu nhạy cảm
        if (isset($data['national_id']) && $lecturer->user_id !== $user->id) {
            app(AuditLogger::class)->record(AuditEvent::ViewSensitive, $lecturer, reason: 'Xem hồ sơ giảng viên (CCCD)');
        }

        return response()->json($data);
    }

    public function store(LecturerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->assertDepartmentInScope($request->user(), (int) $data['department_id'], PermissionAction::Create);

        $lecturer = $this->lecturers->create($data, $request->user());

        return response()->json($this->present($lecturer->load('department.faculty', 'user'), $request->user()), 201);
    }

    /**
     * Cán bộ quản lý sửa trực tiếp; giảng viên tự sửa hồ sơ của mình qua đây thì đi đường tự cập nhật (BR-TCH-07).
     */
    public function update(Request $request, Lecturer $lecturer): JsonResponse
    {
        Gate::authorize('update', $lecturer);
        $user = $request->user();

        if (! $this->manages($user, $lecturer)) {
            $result = $this->profiles->update($lecturer, app(LecturerProfileRequest::class)->payload(), $user, $request->input('reason'));

            return response()->json($this->present($result['lecturer'], $user) + [
                'change_request' => $result['request'] === null ? null : $this->presentRequest($result['request']),
            ]);
        }

        $data = app(LecturerRequest::class)->validated();

        if (isset($data['department_id'])) {
            $this->assertDepartmentInScope($user, (int) $data['department_id'], PermissionAction::Update);
        }

        return response()->json($this->present($this->lecturers->update($lecturer, $data)->load('department.faculty', 'user'), $user));
    }

    /** Nghỉ việc, nghỉ hưu, làm việc trở lại (BR-TCH-02). */
    public function changeStatus(Request $request, Lecturer $lecturer): JsonResponse
    {
        Gate::authorize('update', $lecturer);
        abort_unless($this->manages($request->user(), $lecturer), 403, 'Chỉ phòng Đào tạo hoặc lãnh đạo đơn vị ghi nhận tình trạng công tác.');

        $data = $request->validate([
            'status' => ['required', Rule::enum(LecturerStatus::class)],
            'on' => ['nullable', 'date_format:Y-m-d'],
            'reason' => ['nullable', 'string', 'max:500'],
        ], [], ['status' => 'tình trạng', 'on' => 'ngày hiệu lực', 'reason' => 'lý do']);

        $lecturer = $this->lecturers->changeStatus($lecturer, LecturerStatus::from($data['status']), $data['on'] ?? null, $data['reason'] ?? null);

        return response()->json($this->present($lecturer->load('department.faculty', 'user'), $request->user()));
    }

    /** Việc phải chuyển nếu giảng viên nghỉ vào ngày `on` (xem trước khi ghi nhận). */
    public function handover(Request $request, Lecturer $lecturer): JsonResponse
    {
        Gate::authorize('update', $lecturer);
        $on = $request->validate(['on' => ['nullable', 'date_format:Y-m-d']])['on'] ?? now(config('studentmanager.display_timezone'))->toDateString();

        return response()->json(['on' => $on, 'items' => $this->lecturers->workToHandOver($lecturer, $on)]);
    }

    /** Cấp tài khoản cho hồ sơ chưa có (BR-TCH-01). */
    public function createAccount(Request $request, Lecturer $lecturer): JsonResponse
    {
        Gate::authorize('update', $lecturer);
        abort_unless($this->manages($request->user(), $lecturer), 403);

        $email = $request->validate(['email' => ['nullable', 'email', 'max:255']], [], ['email' => 'email'])['email'] ?? null;
        $lecturer = $this->lecturers->createAccount($lecturer, $request->user(), $email);

        return response()->json($this->present($lecturer->load('department.faculty', 'user'), $request->user()), 201);
    }

    /**
     * Xuất danh sách đang lọc (khoa, bộ môn, chức danh, học vị, tình trạng) ra Excel / CSV / PDF (FR-TCH-013).
     * Cần quyền TCH.export (ACAD toàn trường, DEAN đơn vị mình); điện thoại, ngày sinh là cột nhạy cảm.
     */
    public function export(Request $request, ExportService $exports): SymfonyResponse
    {
        $result = $exports->export(
            $this->listQuery($request)->with('department.faculty:id,code,name'),
            [
                new ExportColumn('code', 'Mã giảng viên'),
                new ExportColumn('full_name', 'Họ và tên'),
                new ExportColumn('department.code', 'Mã bộ môn'),
                new ExportColumn('department.name', 'Bộ môn'),
                new ExportColumn('department.faculty.name', 'Khoa'),
                new ExportColumn('academic_title_label', 'Chức danh'),
                new ExportColumn('academic_degree_label', 'Học vị'),
                new ExportColumn('expertise', 'Chuyên môn'),
                new ExportColumn('email', 'Email'),
                new ExportColumn('phone', 'Điện thoại', sensitive: true),
                new ExportColumn('date_of_birth', 'Ngày sinh', sensitive: true),
                new ExportColumn('status_label', 'Tình trạng'),
            ],
            'TCH',
            $request->user(),
            $request->string('format', 'xlsx')->toString(),
            'danh-sach-giang-vien',
        );

        return $result->isQueued()
            ? response()->json(['id' => $result->requestId, 'row_count' => $result->rowCount], 202)
            : $result->download;
    }

    // ---------------------------------------------------------------------
    // Kiêm nhiệm (FR-TCH-003)
    // ---------------------------------------------------------------------

    public function units(Lecturer $lecturer): JsonResponse
    {
        Gate::authorize('view', $lecturer);

        return response()->json($lecturer->units()->orderByDesc('starts_on')->get()->map(fn (LecturerUnit $unit) => $this->presentUnit($unit)));
    }

    public function storeUnit(Request $request, Lecturer $lecturer): JsonResponse
    {
        Gate::authorize('update', $lecturer);
        abort_unless($this->manages($request->user(), $lecturer), 403);

        $data = $request->validate([
            'unit_type' => ['required', 'in:faculty,department'],
            'unit_id' => ['required', 'integer'],
            'starts_on' => ['nullable', 'date_format:Y-m-d'],
            'ends_on' => ['nullable', 'date_format:Y-m-d'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [], ['unit_type' => 'loại đơn vị', 'unit_id' => 'đơn vị', 'starts_on' => 'ngày bắt đầu', 'ends_on' => 'ngày kết thúc', 'note' => 'nội dung kiêm nhiệm']);

        return response()->json($this->presentUnit($this->lecturers->addUnit($lecturer, $data)), 201);
    }

    public function endUnit(Request $request, LecturerUnit $lecturerUnit): JsonResponse
    {
        $this->authorizeUnit($request, $lecturerUnit);
        $endsOn = $request->validate(['ends_on' => ['required', 'date_format:Y-m-d']], [], ['ends_on' => 'ngày kết thúc'])['ends_on'];

        return response()->json($this->presentUnit($this->lecturers->endUnit($lecturerUnit, $endsOn)));
    }

    public function destroyUnit(Request $request, LecturerUnit $lecturerUnit): Response
    {
        $this->authorizeUnit($request, $lecturerUnit);
        $this->lecturers->cancelUnit($lecturerUnit);

        return response()->noContent();
    }

    // ---------------------------------------------------------------------
    // Đề nghị thay đổi thông tin (BR-TCH-07)
    // ---------------------------------------------------------------------

    public function changeRequests(Request $request): JsonResponse
    {
        $lecturers = $this->access->constrain(Lecturer::query(), $request->user(), 'TCH', PermissionAction::View)->select('lecturers.id');

        $list = LecturerChangeRequest::query()
            ->whereIn('lecturer_id', $lecturers)
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()), fn (Builder $q) => $q->where('status', LecturerChangeRequest::PENDING))
            ->with(['lecturer:id,code,full_name', 'requester:id,name', 'decider:id,name'])
            ->latest('id')
            ->paginate(50);

        return response()->json($list->through(fn (LecturerChangeRequest $r) => $this->presentRequest($r)));
    }

    public function approve(Request $request, LecturerChangeRequest $lecturerChangeRequest): JsonResponse
    {
        $this->authorizeDecision($request, $lecturerChangeRequest);
        $note = $request->validate(['note' => ['nullable', 'string', 'max:500']])['note'] ?? null;

        return response()->json($this->presentRequest($this->profiles->approve($lecturerChangeRequest, $request->user(), $note)));
    }

    public function reject(Request $request, LecturerChangeRequest $lecturerChangeRequest): JsonResponse
    {
        $this->authorizeDecision($request, $lecturerChangeRequest);
        $note = $request->validate(['note' => ['required', 'string', 'max:500']], [], ['note' => 'lý do từ chối'])['note'];

        return response()->json($this->presentRequest($this->profiles->reject($lecturerChangeRequest, $request->user(), $note)));
    }

    // ---------------------------------------------------------------------

    private function listQuery(Request $request): Builder
    {
        return Lecturer::query()
            ->with('department:id,code,name,faculty_id', 'department.faculty:id,code,name')
            ->search($request->string('q')->toString())
            ->when($request->filled('department_id'), fn (Builder $q) => $q->where('department_id', $request->integer('department_id')))
            ->when($request->filled('faculty_id'), fn (Builder $q) => $q->whereHas('department', fn (Builder $d) => $d->where('faculty_id', $request->integer('faculty_id'))))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('academic_title'), fn (Builder $q) => $q->where('academic_title', $request->string('academic_title')->toString()))
            ->when($request->filled('academic_degree'), fn (Builder $q) => $q->where('academic_degree', $request->string('academic_degree')->toString()))
            ->orderBy('code');
    }

    /** Có quyền sửa hồ sơ với tư cách cán bộ quản lý (toàn trường hoặc đơn vị), không chỉ là hồ sơ của chính mình. */
    private function manages(User $user, Lecturer $lecturer): bool
    {
        $scopes = $this->access->scopesFor($user, 'TCH', PermissionAction::Update);

        if ($scopes === [DataScope::All]) {
            return true;
        }

        if (! in_array(DataScope::Faculty, $scopes, true)) {
            return false;
        }

        $query = Lecturer::query()->whereKey($lecturer->id);
        $lecturer->applyDataScope($query, DataScope::Faculty, $user);

        return $query->exists();
    }

    /** Người chỉ có quyền trong phạm vi đơn vị (DEAN) chỉ đặt đơn vị chính là bộ môn mình quản lý. */
    private function assertDepartmentInScope(User $user, int $departmentId, PermissionAction $action): void
    {
        if ($this->access->allowsGlobally($user, 'TCH', $action)) {
            return;
        }

        abort_unless(
            in_array($departmentId, array_map(intval(...), app(FacultyAccess::class)->departmentIds($user)), true),
            403,
            'Chỉ được quản lý giảng viên của bộ môn thuộc đơn vị mình phụ trách.'
        );
    }

    private function authorizeUnit(Request $request, LecturerUnit $unit): void
    {
        $lecturer = $unit->lecturer;
        Gate::authorize('update', $lecturer);
        abort_unless($this->manages($request->user(), $lecturer), 403);
    }

    /** Xác nhận: người có quyền duyệt (DEAN trong đơn vị) hoặc sửa toàn trường (ACAD) trên hồ sơ đó. */
    private function authorizeDecision(Request $request, LecturerChangeRequest $changeRequest): void
    {
        $user = $request->user();
        $lecturer = $changeRequest->lecturer;

        abort_unless(
            Gate::forUser($user)->allows('approve', $lecturer) || $this->manages($user, $lecturer),
            403,
            'Bạn không có thẩm quyền xác nhận thay đổi hồ sơ của giảng viên này.'
        );
    }

    /** @return array<string, mixed> */
    private function present(Lecturer $lecturer, User $viewer): array
    {
        $data = [
            'id' => $lecturer->id,
            'code' => $lecturer->code,
            'full_name' => $lecturer->full_name,
            'email' => $lecturer->email,
            'photo_url' => $lecturer->photo_path === null ? null : asset('storage/'.$lecturer->photo_path),
            'department_id' => $lecturer->department_id,
            'department' => $lecturer->department?->only('id', 'code', 'name'),
            'faculty' => $lecturer->department?->faculty?->only('id', 'code', 'name'),
            'academic_title' => $lecturer->academic_title->value,
            'academic_title_label' => $lecturer->academic_title_label,
            'academic_degree' => $lecturer->academic_degree?->value,
            'academic_degree_label' => $lecturer->academic_degree_label,
            'expertise' => $lecturer->expertise,
            'status' => $lecturer->status->value,
            'status_label' => $lecturer->status_label,
        ];

        if (! Gate::forUser($viewer)->allows('update', $lecturer)) {
            return $data;
        }

        return $data + [
            'username' => $lecturer->user?->username,
            'has_account' => $lecturer->user_id !== null,
            'date_of_birth' => $lecturer->date_of_birth?->toDateString(),
            'gender_id' => $lecturer->gender_id,
            'gender' => $lecturer->gender?->name,
            'national_id' => $lecturer->national_id,
            'phone' => $lecturer->phone,
            'hired_on' => $lecturer->hired_on?->toDateString(),
            'left_on' => $lecturer->left_on?->toDateString(),
            'left_reason' => $lecturer->left_reason,
        ];
    }

    /** @return array<string, mixed> */
    private function presentUnit(LecturerUnit $unit): array
    {
        return [
            'id' => $unit->id,
            'unit_type' => $unit->unit_type,
            'unit' => $unit->unit()?->only('id', 'code', 'name'),
            'starts_on' => $unit->starts_on->toDateString(),
            'ends_on' => $unit->ends_on?->toDateString(),
            'note' => $unit->note,
        ];
    }

    /** @return array<string, mixed> */
    public static function presentRequest(LecturerChangeRequest $request): array
    {
        return [
            'id' => $request->id,
            'lecturer' => $request->lecturer?->only('id', 'code', 'full_name'),
            'changes' => $request->changes,
            'reason' => $request->reason,
            'status' => $request->status,
            'requested_by' => $request->requester?->only('id', 'name'),
            'decided_by' => $request->decider?->only('id', 'name'),
            'decided_at' => $request->decided_at?->toIso8601String(),
            'decision_note' => $request->decision_note,
            'created_at' => $request->created_at?->toIso8601String(),
        ];
    }
}
