<?php

namespace App\Modules\Room\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Room\Http\Requests\RoomMaintenanceRequest;
use App\Modules\Room\Http\Requests\RoomRequest;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomMaintenance;
use App\Modules\Room\Services\RoomAvailability;
use App\Modules\Room\Services\RoomMaintenanceService;
use App\Modules\Room\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/** API phòng học, tình trạng và lịch bảo trì (FR-ROM-001, FR-ROM-002). Quyền: middleware `permission:ROM.*`. */
class RoomController extends Controller
{
    public function __construct(
        private readonly RoomService $rooms,
        private readonly RoomMaintenanceService $maintenances,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $rooms = Room::query()
            ->with(['building:id,code,name,campus_id', 'type:id,code,name'])
            ->when($request->filled('campus_id'), fn ($q) => $q->whereHas('building', fn ($b) => $b->where('campus_id', $request->integer('campus_id'))))
            ->when($request->filled('building_id'), fn ($q) => $q->where('building_id', $request->integer('building_id')))
            ->when($request->filled('room_type_id'), fn ($q) => $q->where('room_type_id', $request->integer('room_type_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('min_capacity'), fn ($q) => $q->where('capacity', '>=', $request->integer('min_capacity')))
            ->when($request->filled('min_exam_capacity'), fn ($q) => $q->where('exam_capacity', '>=', $request->integer('min_exam_capacity')))
            ->search($request->string('q')->toString())
            ->orderBy('code')
            ->paginate(50);

        return response()->json($rooms->through(fn (Room $room) => $this->present($room)));
    }

    public function show(Room $room): JsonResponse
    {
        $room->load(['building.campus', 'type']);
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return response()->json($this->present($room) + [
            'campus' => $room->building->campus->only('id', 'code', 'name'),
            'maintenances' => $room->maintenances()
                ->whereDate('ends_on', '>=', $today)
                ->orderBy('starts_on')
                ->get()
                ->map(fn (RoomMaintenance $m) => $this->presentMaintenance($m)),
        ]);
    }

    public function store(RoomRequest $request): JsonResponse
    {
        return response()->json($this->present($this->rooms->createRoom($request->validated())->load(['building', 'type'])), 201);
    }

    public function update(RoomRequest $request, Room $room): JsonResponse
    {
        return response()->json($this->present($this->rooms->updateRoom($room, $request->validated())->load(['building', 'type'])));
    }

    public function destroy(Room $room): Response
    {
        $this->rooms->deleteRoom($room);

        return response()->noContent();
    }

    /** Phòng có xếp lịch được trong khoảng ngày không, kèm lý do (BR-ROM-02). */
    public function availability(Request $request, Room $room, RoomAvailability $availability): JsonResponse
    {
        $data = $request->validate(
            ['from' => ['required', 'date_format:Y-m-d'], 'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from']],
            [],
            ['from' => 'từ ngày', 'to' => 'đến ngày'],
        );

        $problems = $availability->problems($room, $data['from'], $data['to'] ?? null);

        return response()->json(['schedulable' => $problems === [], 'problems' => $problems]);
    }

    public function maintenancesOf(Room $room): JsonResponse
    {
        return response()->json($room->maintenances()->orderByDesc('starts_on')->get()->map(fn (RoomMaintenance $m) => $this->presentMaintenance($m)));
    }

    public function storeMaintenance(RoomMaintenanceRequest $request, Room $room): JsonResponse
    {
        return response()->json($this->presentMaintenance($this->maintenances->schedule($room, $request->validated())), 201);
    }

    public function updateMaintenance(RoomMaintenanceRequest $request, RoomMaintenance $roomMaintenance): JsonResponse
    {
        return response()->json($this->presentMaintenance($this->maintenances->update($roomMaintenance, $request->validated())));
    }

    public function destroyMaintenance(RoomMaintenance $roomMaintenance): Response
    {
        $this->maintenances->cancel($roomMaintenance);

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function present(Room $room): array
    {
        return [
            'id' => $room->id,
            'code' => $room->code,
            'name' => $room->name,
            'building' => $room->building?->only('id', 'code', 'name'),
            'type' => $room->type?->only('id', 'code', 'name'),
            'floor' => $room->floor,
            'capacity' => $room->capacity,
            'exam_capacity' => $room->exam_capacity,
            'equipment' => $room->equipment ?? [],
            'note' => $room->note,
            'status' => $room->status->value,
            'status_label' => $room->status->label(),
        ];
    }

    /** @return array<string, mixed> */
    private function presentMaintenance(RoomMaintenance $maintenance): array
    {
        return [
            'id' => $maintenance->id,
            'room_id' => $maintenance->room_id,
            'starts_on' => $maintenance->starts_on->toDateString(),
            'ends_on' => $maintenance->ends_on->toDateString(),
            'reason' => $maintenance->reason,
        ];
    }
}
