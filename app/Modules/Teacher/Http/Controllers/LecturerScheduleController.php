<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Teacher\Enums\LeaveStatus;
use App\Modules\Teacher\Enums\LeaveType;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Models\LecturerAvailability;
use App\Modules\Teacher\Models\LecturerLeave;
use App\Modules\Teacher\Models\TeachingPreference;
use App\Modules\Teacher\Services\LecturerScheduleService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Lịch bận, nguyện vọng giảng dạy, nghỉ phép (FR-TCH-008, 012).
 *
 * - Cổng giảng viên (`/teacher/...`): giảng viên xem và sửa dữ liệu của chính mình (phạm vi OWN).
 * - Quản trị (`/admin/...`): cán bộ xem theo phạm vi TCH; duyệt / từ chối đơn nghỉ = DEAN trong đơn vị (TCH.approve)
 *   hoặc ACAD (TCH.update toàn trường), không tự duyệt cho mình.
 */
class LecturerScheduleController extends Controller
{
    public function __construct(
        private readonly LecturerScheduleService $schedule,
        private readonly AccessControl $access,
    ) {}

    // ---------------------------------------------------------------------
    // Cổng giảng viên
    // ---------------------------------------------------------------------

    public function myAvailability(Request $request): JsonResponse
    {
        return response()->json($this->availabilityOf($this->own($request)));
    }

    public function storeAvailability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'period_from' => ['required', 'integer', 'min:1'],
            'period_to' => ['required', 'integer', 'min:1'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['required', 'date_format:Y-m-d'],
            'reason' => ['nullable', 'string', 'max:255'],
        ], [], ['day_of_week' => 'thứ', 'period_from' => 'tiết bắt đầu', 'period_to' => 'tiết kết thúc', 'starts_on' => 'từ ngày', 'ends_on' => 'đến ngày', 'reason' => 'lý do']);

        return response()->json($this->presentAvailability($this->schedule->addBusy($this->own($request), $data)), 201);
    }

    public function destroyAvailability(Request $request, LecturerAvailability $lecturerAvailability): Response
    {
        abort_unless($lecturerAvailability->lecturer_id === $this->own($request)->id, 404);
        $this->schedule->removeBusy($lecturerAvailability);

        return response()->noContent();
    }

    public function myPreferences(Request $request): JsonResponse
    {
        $term = Term::query()->findOrFail($request->validate(['term_id' => ['required', 'integer']], [], ['term_id' => 'học kỳ'])['term_id']);

        return response()->json($this->preferencesOf($this->own($request), $term));
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'term_id' => ['required', 'integer', 'exists:terms,id'],
            'subjects' => ['present', 'array', 'max:20'],
            'subjects.*.subject_id' => ['required', 'integer'],
            'subjects.*.priority' => ['sometimes', 'integer', 'between:1,20'],
            'subjects.*.note' => ['nullable', 'string', 'max:255'],
        ], [], ['term_id' => 'học kỳ', 'subjects' => 'học phần', 'subjects.*.subject_id' => 'học phần', 'subjects.*.priority' => 'mức ưu tiên']);

        $lecturer = $this->own($request);
        $term = Term::query()->findOrFail($data['term_id']);
        $this->schedule->setPreferences($lecturer, $term, $data['subjects']);

        return response()->json($this->preferencesOf($lecturer, $term));
    }

    public function myLeaves(Request $request): JsonResponse
    {
        return response()->json($this->own($request)->leaves()->orderByDesc('starts_on')->get()->map(fn (LecturerLeave $l) => $this->presentLeave($l)));
    }

    public function storeLeave(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::enum(LeaveType::class)],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['required', 'date_format:Y-m-d'],
            'reason' => ['required', 'string', 'max:500'],
        ], [], ['type' => 'loại nghỉ', 'starts_on' => 'từ ngày', 'ends_on' => 'đến ngày', 'reason' => 'lý do']);

        return response()->json($this->presentLeave($this->schedule->requestLeave($this->own($request), $data, $request->user())), 201);
    }

    public function cancelLeave(Request $request, LecturerLeave $lecturerLeave): JsonResponse
    {
        abort_unless($lecturerLeave->lecturer_id === $this->own($request)->id, 404);

        return response()->json($this->presentLeave($this->schedule->cancelLeave($lecturerLeave, $request->user())));
    }

    // ---------------------------------------------------------------------
    // Quản trị
    // ---------------------------------------------------------------------

    public function availabilityOfLecturer(Lecturer $lecturer): JsonResponse
    {
        Gate::authorize('view', $lecturer);

        return response()->json($this->availabilityOf($lecturer));
    }

    /** Nguyện vọng của mọi giảng viên trong phạm vi cho một học kỳ, lọc theo học phần (CLS dùng khi phân công). */
    public function preferencesForTerm(Request $request): JsonResponse
    {
        $data = $request->validate(['term_id' => ['required', 'integer'], 'subject_id' => ['nullable', 'integer']]);
        $lecturers = $this->access->constrain(Lecturer::query(), $request->user(), 'TCH', PermissionAction::View)->select('lecturers.id');

        $list = TeachingPreference::query()
            ->where('term_id', $data['term_id'])
            ->whereIn('lecturer_id', $lecturers)
            ->when(isset($data['subject_id']), fn (Builder $q) => $q->where('subject_id', $data['subject_id']))
            ->with(['lecturer:id,code,full_name,status', 'subject:id,code,name'])
            ->orderBy('subject_id')->orderBy('priority')
            ->get();

        return response()->json($list->map(fn (TeachingPreference $p) => [
            'lecturer' => $p->lecturer->only('id', 'code', 'full_name'),
            'subject' => $p->subject->only('id', 'code', 'name'),
            'priority' => $p->priority,
            'note' => $p->note,
        ]));
    }

    public function leaves(Request $request): JsonResponse
    {
        $lecturers = $this->access->constrain(Lecturer::query(), $request->user(), 'TCH', PermissionAction::View)->select('lecturers.id');

        $list = LecturerLeave::query()
            ->whereIn('lecturer_id', $lecturers)
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()), fn (Builder $q) => $q->where('status', LeaveStatus::Pending->value))
            ->when($request->filled('lecturer_id'), fn (Builder $q) => $q->where('lecturer_id', $request->integer('lecturer_id')))
            ->with(['lecturer:id,code,full_name', 'decider:id,name'])
            ->orderBy('starts_on')
            ->paginate(50);

        return response()->json($list->through(fn (LecturerLeave $l) => $this->presentLeave($l)));
    }

    public function approveLeave(Request $request, LecturerLeave $lecturerLeave): JsonResponse
    {
        $this->authorizeDecision($request->user(), $lecturerLeave->lecturer);
        $note = $request->validate(['note' => ['nullable', 'string', 'max:500']])['note'] ?? null;

        return response()->json($this->presentLeave($this->schedule->approveLeave($lecturerLeave, $request->user(), $note)));
    }

    public function rejectLeave(Request $request, LecturerLeave $lecturerLeave): JsonResponse
    {
        $this->authorizeDecision($request->user(), $lecturerLeave->lecturer);
        $note = $request->validate(['note' => ['required', 'string', 'max:500']], [], ['note' => 'lý do từ chối'])['note'];

        return response()->json($this->presentLeave($this->schedule->rejectLeave($lecturerLeave, $request->user(), $note)));
    }

    // ---------------------------------------------------------------------

    private function own(Request $request): Lecturer
    {
        return Lecturer::query()->where('user_id', $request->user()->id)->firstOr(
            fn () => abort(404, 'Tài khoản chưa gắn với hồ sơ giảng viên.')
        );
    }

    /** DEAN duyệt trong đơn vị (TCH.approve, phạm vi FACULTY) hoặc ACAD (TCH.update toàn trường). */
    private function authorizeDecision(User $user, Lecturer $lecturer): void
    {
        $byScope = in_array(DataScope::Faculty, $this->access->scopesFor($user, 'TCH', PermissionAction::Approve), true)
            && Lecturer::query()->whereKey($lecturer->id)->where(fn (Builder $q) => $lecturer->applyDataScope($q, DataScope::Faculty, $user))->exists();

        abort_unless(
            $byScope || $this->access->allowsGlobally($user, 'TCH', PermissionAction::Update),
            403,
            'Bạn không có thẩm quyền duyệt đơn nghỉ của giảng viên này.'
        );
    }

    /** @return list<array<string, mixed>> */
    private function availabilityOf(Lecturer $lecturer): array
    {
        return $lecturer->availabilities()->orderBy('day_of_week')->orderBy('period_from')->get()
            ->map(fn (LecturerAvailability $a) => $this->presentAvailability($a))->all();
    }

    /** @return list<array<string, mixed>> */
    private function preferencesOf(Lecturer $lecturer, Term $term): array
    {
        return $this->schedule->preferences($lecturer, $term)->map(fn (TeachingPreference $p) => [
            'id' => $p->id,
            'subject' => $p->subject->only('id', 'code', 'name', 'credits'),
            'priority' => $p->priority,
            'note' => $p->note,
        ])->all();
    }

    /** @return array<string, mixed> */
    private function presentAvailability(LecturerAvailability $a): array
    {
        return [
            'id' => $a->id,
            'day_of_week' => $a->day_of_week,
            'day_label' => LecturerAvailability::DAYS[$a->day_of_week],
            'period_from' => $a->period_from,
            'period_to' => $a->period_to,
            'starts_on' => $a->starts_on->toDateString(),
            'ends_on' => $a->ends_on->toDateString(),
            'reason' => $a->reason,
        ];
    }

    /** @return array<string, mixed> */
    private function presentLeave(LecturerLeave $l): array
    {
        return [
            'id' => $l->id,
            'lecturer' => $l->lecturer?->only('id', 'code', 'full_name'),
            'type' => $l->type->value,
            'type_label' => $l->type->label(),
            'starts_on' => $l->starts_on->toDateString(),
            'ends_on' => $l->ends_on->toDateString(),
            'reason' => $l->reason,
            'status' => $l->status->value,
            'status_label' => $l->status->label(),
            'decided_by' => $l->decider?->only('id', 'name'),
            'decided_at' => $l->decided_at?->toIso8601String(),
            'decision_note' => $l->decision_note,
        ];
    }
}
