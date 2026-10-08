<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\System\Http\Requests\StorePolicySetRequest;
use App\Modules\System\Http\Requests\UpdatePolicyItemsRequest;
use App\Modules\System\Http\Requests\UpdatePolicySetRequest;
use App\Modules\System\Models\PolicySet;
use App\Modules\System\Services\PolicyResolver;
use App\Modules\System\Services\PolicySetService;
use App\Modules\System\Settings\PolicyDefinitions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/** API bộ quy chế đào tạo theo khóa (FR-SYS-003). Quyền: `permission:SYS.view` / `SYS.update` ở routes/admin.php. */
class PolicySetController extends Controller
{
    public function __construct(private readonly PolicySetService $policies) {}

    /** Danh mục tham số: nhãn, nhóm, giá trị mặc định (phụ lục C) để giao diện dựng biểu mẫu. */
    public function definitions(): JsonResponse
    {
        $rows = [];

        foreach (PolicyDefinitions::all() as $key => $definition) {
            $rows[] = ['key' => $key, 'label' => $definition['label'], 'group' => $definition['group'], 'default' => $definition['default']];
        }

        return response()->json($rows);
    }

    public function index(Request $request): JsonResponse
    {
        $sets = PolicySet::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('code'), fn ($q) => $q->where('code', $request->string('code')->toString()))
            ->orderByDesc('effective_from')
            ->orderByDesc('version')
            ->paginate(20);

        return response()->json($sets->through(fn (PolicySet $set) => $this->present($set)));
    }

    public function show(PolicySet $policySet): JsonResponse
    {
        return response()->json($this->present($policySet) + ['items' => $policySet->load('items')->values()]);
    }

    public function store(StorePolicySetRequest $request): JsonResponse
    {
        $basedOn = $request->filled('based_on_id') ? PolicySet::query()->find($request->integer('based_on_id')) : null;
        $set = $this->policies->createDraft($request->safe()->except('based_on_id'), $basedOn);

        return response()->json($this->present($set) + ['items' => $set->values()], 201);
    }

    public function update(UpdatePolicySetRequest $request, PolicySet $policySet): JsonResponse
    {
        return response()->json($this->present($this->policies->update($policySet, $request->validated())));
    }

    public function updateItems(UpdatePolicyItemsRequest $request, PolicySet $policySet): JsonResponse
    {
        $set = $this->policies->setItems($policySet, $request->input('items'));

        return response()->json($this->present($set) + ['items' => $set->values()]);
    }

    public function publish(Request $request, PolicySet $policySet): JsonResponse
    {
        return response()->json($this->present($this->policies->publish($policySet, $request->user())));
    }

    public function destroy(PolicySet $policySet): Response
    {
        $this->policies->delete($policySet);

        return response()->noContent();
    }

    /** Xem trước bộ quy chế và giá trị áp dụng cho một khóa vào một ngày (mặc định hôm nay). */
    public function resolve(Request $request, PolicyResolver $resolver): JsonResponse
    {
        $request->validate(
            ['cohort' => ['required', 'integer', 'between:1990,2100'], 'date' => ['nullable', 'date']],
            [],
            ['cohort' => 'khóa', 'date' => 'ngày'],
        );

        $set = $resolver->setFor($request->integer('cohort'), $request->input('date'));

        return response()->json([
            'policy_set' => $this->present($set),
            'values' => $resolver->values($request->integer('cohort'), $request->input('date')),
        ]);
    }

    /** @return array<string, mixed> */
    private function present(PolicySet $set): array
    {
        return [
            'id' => $set->id,
            'code' => $set->code,
            'version' => $set->version,
            'name' => $set->name,
            'description' => $set->description,
            'cohort_from' => $set->cohort_from,
            'cohort_to' => $set->cohort_to,
            'cohort_label' => $set->cohortLabel(),
            'effective_from' => $set->effective_from->toDateString(),
            'status' => $set->status->value,
            'status_label' => $set->status->label(),
            'based_on_id' => $set->based_on_id,
            'published_at' => $set->published_at?->toIso8601String(),
            'published_by' => $set->published_by,
        ];
    }
}
