<?php

namespace App\Modules\Room\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Room\Http\Requests\BuildingRequest;
use App\Modules\Room\Http\Requests\CampusRequest;
use App\Modules\Room\Http\Requests\RoomTypeRequest;
use App\Modules\Room\Models\Building;
use App\Modules\Room\Models\Campus;
use App\Modules\Room\Models\RoomType;
use App\Modules\Room\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/** API danh mục cơ sở, tòa nhà, loại phòng (FR-ROM-001). Quyền: middleware `permission:ROM.*` ở routes/admin.php. */
class FacilityController extends Controller
{
    public function __construct(private readonly RoomService $rooms) {}

    public function campuses(Request $request): JsonResponse
    {
        $campuses = Campus::query()
            ->search($request->string('q')->toString())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->withCount('buildings')
            ->orderBy('code')
            ->get();

        return response()->json($campuses->map(fn (Campus $campus) => $this->presentCampus($campus)));
    }

    public function storeCampus(CampusRequest $request): JsonResponse
    {
        return response()->json($this->presentCampus($this->rooms->createCampus($request->validated())), 201);
    }

    public function updateCampus(CampusRequest $request, Campus $campus): JsonResponse
    {
        return response()->json($this->presentCampus($this->rooms->updateCampus($campus, $request->validated())));
    }

    public function destroyCampus(Campus $campus): Response
    {
        $this->rooms->deleteCampus($campus);

        return response()->noContent();
    }

    public function buildings(Request $request): JsonResponse
    {
        $buildings = Building::query()
            ->with('campus:id,code,name')
            ->when($request->filled('campus_id'), fn ($q) => $q->where('campus_id', $request->integer('campus_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->search($request->string('q')->toString())
            ->withCount('rooms')
            ->orderBy('code')
            ->get();

        return response()->json($buildings->map(fn (Building $building) => $this->presentBuilding($building)));
    }

    public function storeBuilding(BuildingRequest $request): JsonResponse
    {
        return response()->json($this->presentBuilding($this->rooms->createBuilding($request->validated())), 201);
    }

    public function updateBuilding(BuildingRequest $request, Building $building): JsonResponse
    {
        return response()->json($this->presentBuilding($this->rooms->updateBuilding($building, $request->validated())));
    }

    public function destroyBuilding(Building $building): Response
    {
        $this->rooms->deleteBuilding($building);

        return response()->noContent();
    }

    public function roomTypes(): JsonResponse
    {
        return response()->json(RoomType::query()->orderBy('code')->get()->map(fn (RoomType $type) => $this->presentType($type)));
    }

    public function storeRoomType(RoomTypeRequest $request): JsonResponse
    {
        return response()->json($this->presentType($this->rooms->createRoomType($request->validated())), 201);
    }

    public function updateRoomType(RoomTypeRequest $request, RoomType $roomType): JsonResponse
    {
        return response()->json($this->presentType($this->rooms->updateRoomType($roomType, $request->validated())));
    }

    /** @return array<string, mixed> */
    private function presentCampus(Campus $campus): array
    {
        return [
            'id' => $campus->id,
            'code' => $campus->code,
            'name' => $campus->name,
            'address' => $campus->address,
            'status' => $campus->status->value,
            'status_label' => $campus->status->label(),
            'buildings_count' => $campus->buildings_count ?? $campus->buildings()->count(),
        ];
    }

    /** @return array<string, mixed> */
    private function presentBuilding(Building $building): array
    {
        return [
            'id' => $building->id,
            'campus_id' => $building->campus_id,
            'campus' => $building->campus?->only('id', 'code', 'name'),
            'code' => $building->code,
            'name' => $building->name,
            'floors' => $building->floors,
            'status' => $building->status->value,
            'status_label' => $building->status->label(),
            'rooms_count' => $building->rooms_count ?? $building->rooms()->count(),
        ];
    }

    /** @return array<string, mixed> */
    private function presentType(RoomType $type): array
    {
        return [
            'id' => $type->id,
            'code' => $type->code,
            'name' => $type->name,
            'description' => $type->description,
            'status' => $type->status->value,
            'status_label' => $type->status->label(),
        ];
    }
}
