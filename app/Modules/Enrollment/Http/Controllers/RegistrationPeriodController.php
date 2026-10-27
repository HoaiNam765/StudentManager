<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Enums\RegistrationRound;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Models\RegistrationPeriodTarget;
use App\Modules\Enrollment\Services\RegistrationPeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/** API đợt đăng ký học phần (FR-ENR-001). Thời gian nhập / trả theo giờ Việt Nam, lưu UTC. */
class RegistrationPeriodController extends Controller
{
    public function __construct(
        private readonly RegistrationPeriodService $periods,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $list = RegistrationPeriod::query()
            ->with('term:id,name,academic_year_id', 'term.academicYear:id,name', 'targets.cohort:id,code,start_year', 'targets.major:id,code,name', 'targets.adminClass:id,code')
            ->when(! $this->seesDrafts($request), fn ($q) => $q->where('status', '!=', PeriodStatus::Draft))
            ->when($request->filled('term_id'), fn ($q) => $q->where('term_id', $request->integer('term_id')))
            ->orderByDesc('opens_at')
            ->get();

        return response()->json($list->map(fn (RegistrationPeriod $p) => $this->present($p)));
    }

    public function show(Request $request, RegistrationPeriod $registrationPeriod): JsonResponse
    {
        abort_if($registrationPeriod->status === PeriodStatus::Draft && ! $this->seesDrafts($request), 404);

        return response()->json($this->present($registrationPeriod->load('term.academicYear', 'targets.cohort', 'targets.major', 'targets.adminClass')));
    }

    public function store(Request $request): JsonResponse
    {
        $period = $this->periods->create($this->validated($request, true));

        return response()->json($this->present($period->load('term.academicYear', 'targets.cohort', 'targets.major', 'targets.adminClass')), 201);
    }

    public function update(Request $request, RegistrationPeriod $registrationPeriod): JsonResponse
    {
        $period = $this->periods->update($registrationPeriod, $this->validated($request, false));

        return response()->json($this->present($period->load('term.academicYear', 'targets.cohort', 'targets.major', 'targets.adminClass')));
    }

    public function publish(Request $request, RegistrationPeriod $registrationPeriod): JsonResponse
    {
        return response()->json($this->present($this->periods->publish($registrationPeriod, $request->user())->load('term.academicYear', 'targets')));
    }

    public function openEarly(Request $request, RegistrationPeriod $registrationPeriod): JsonResponse
    {
        return response()->json($this->present($this->periods->openEarly($registrationPeriod, $request->user())->load('term.academicYear', 'targets')));
    }

    public function destroy(RegistrationPeriod $registrationPeriod): Response
    {
        $this->periods->delete($registrationPeriod);

        return response()->noContent();
    }

    /** Đợt nháp chỉ cán bộ có ENR toàn trường (ACAD…) thấy. */
    private function seesDrafts(Request $request): bool
    {
        return $this->access->allowsGlobally($request->user(), 'ENR', PermissionAction::View);
    }

    private function validated(Request $request, bool $creating): array
    {
        $required = $creating ? 'required' : 'sometimes';

        return $request->validate([
            'term_id' => [$creating ? 'required' : 'prohibited', 'integer', 'exists:terms,id'],
            'training_framework_id' => [$creating ? 'sometimes' : 'prohibited', 'nullable', 'integer', 'exists:training_frameworks,id'],
            'name' => [$required, 'string', 'max:255'],
            'round' => [$required, Rule::enum(RegistrationRound::class)],
            'opens_at' => ['sometimes', 'nullable', 'date_format:Y-m-d H:i'],
            'closes_at' => ['sometimes', 'nullable', 'date_format:Y-m-d H:i'],
            'min_credits' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:60'],
            'max_credits' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:60'],
            'targets' => ['sometimes', 'array'],
            'targets.*.cohort_id' => ['nullable', 'integer', 'exists:cohorts,id'],
            'targets.*.major_id' => ['nullable', 'integer', 'exists:majors,id'],
            'targets.*.admin_class_id' => ['nullable', 'integer', 'exists:admin_classes,id'],
        ], [], [
            'term_id' => 'học kỳ', 'name' => 'tên đợt', 'round' => 'vòng', 'opens_at' => 'thời gian mở', 'closes_at' => 'thời gian đóng',
            'min_credits' => 'tín chỉ tối thiểu', 'max_credits' => 'tín chỉ tối đa', 'targets' => 'đối tượng',
        ]);
    }

    /** @return array<string, mixed> */
    private function present(RegistrationPeriod $period): array
    {
        $tz = config('studentmanager.display_timezone');

        return [
            'id' => $period->id,
            'name' => $period->name,
            'training_framework_id' => $period->training_framework_id,
            'framework_label' => $period->framework ? $period->framework->faculty->name.' – '.$period->framework->cohort->code : null,
            'preview_at' => $period->preview_at?->setTimezone($tz)->format('Y-m-d H:i'),
            'term' => $period->term ? ['id' => $period->term->id, 'name' => $period->term->name, 'academic_year' => $period->term->academicYear?->name] : null,
            'round' => $period->round->value,
            'round_label' => $period->round->label(),
            'opens_at' => $period->opens_at->setTimezone($tz)->format('Y-m-d H:i'),
            'closes_at' => $period->closes_at->setTimezone($tz)->format('Y-m-d H:i'),
            'min_credits' => $period->min_credits,
            'max_credits' => $period->max_credits,
            'status' => $period->status->value,
            'status_label' => $period->status === PeriodStatus::Draft && $period->preview_at !== null ? 'Đã thông báo – chỉ xem trước' : $period->status->label(),
            'is_open' => $period->isOpenAt(now()),
            'targets' => $period->targets->map(fn (RegistrationPeriodTarget $t) => [
                'cohort_id' => $t->cohort_id,
                'major_id' => $t->major_id,
                'admin_class_id' => $t->admin_class_id,
                'description' => $t->describe(),
            ])->values(),
        ];
    }
}
