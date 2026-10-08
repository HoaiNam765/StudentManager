<?php

namespace App\Modules\Faculty\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Faculty\Http\Requests\LeadershipTermRequest;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Modules\Faculty\Services\LeadershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * API lãnh đạo đơn vị theo nhiệm kỳ (FR-FAC-006). Xem: `FAC.view`; giao, kết thúc, hủy: `FAC.update,ALL`
 * (phòng Đào tạo, quản trị viên), ở routes/admin.php.
 */
class LeadershipController extends Controller
{
    public function __construct(private readonly LeadershipService $leadership) {}

    /** Lịch sử nhiệm kỳ, lọc theo đơn vị, người; `current=1` chỉ lấy nhiệm kỳ đang hiệu lực hôm nay. */
    public function index(Request $request): JsonResponse
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        $terms = LeadershipTerm::query()
            ->with('user:id,name,username')
            ->when($request->filled('unit_type'), fn ($q) => $q->where('unit_type', $request->string('unit_type')->toString()))
            ->when($request->filled('unit_id'), fn ($q) => $q->where('unit_id', $request->integer('unit_id')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->boolean('current'), fn ($q) => $q->effectiveOn($today))
            ->orderByDesc('starts_on')
            ->paginate(50);

        return response()->json($terms->through(fn (LeadershipTerm $term) => $this->present($term)));
    }

    public function store(LeadershipTermRequest $request): JsonResponse
    {
        return response()->json($this->present($this->leadership->assign($request->validated(), $request->user())), 201);
    }

    public function end(Request $request, LeadershipTerm $leadershipTerm): JsonResponse
    {
        $data = $request->validate(['ends_on' => ['required', 'date_format:Y-m-d']], [], ['ends_on' => 'ngày kết thúc']);

        return response()->json($this->present($this->leadership->end($leadershipTerm, $data['ends_on'], $request->user())));
    }

    public function destroy(Request $request, LeadershipTerm $leadershipTerm): Response
    {
        $this->leadership->cancel($leadershipTerm, $request->user());

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function present(LeadershipTerm $term): array
    {
        $unit = $term->unit();

        return [
            'id' => $term->id,
            'unit_type' => $term->unit_type,
            'unit' => $unit?->only('id', 'code', 'name'),
            'user' => $term->user?->only('id', 'name', 'username'),
            'position' => $term->position->value,
            'position_label' => $term->position->label(),
            'starts_on' => $term->starts_on->toDateString(),
            'ends_on' => $term->ends_on?->toDateString(),
            'note' => $term->note,
        ];
    }
}
