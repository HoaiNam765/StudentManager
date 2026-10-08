<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\System\Enums\AdministrativeScheme;
use App\Modules\System\Http\Requests\StoreAdministrativeUnitRequest;
use App\Modules\System\Http\Requests\UpdateAdministrativeUnitRequest;
use App\Modules\System\Models\AdministrativeUnit;
use App\Modules\System\Services\AdministrativeUnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/** API quản trị danh mục đơn vị hành chính (FR-SYS-002, BR-STU-09). Quyền: middleware `permission:SYS.*`. */
class AdministrativeUnitController extends Controller
{
    public function __construct(private readonly AdministrativeUnitService $units) {}

    public function index(Request $request): JsonResponse
    {
        $scheme = AdministrativeScheme::tryFrom($request->string('scheme')->toString()) ?? AdministrativeScheme::TwoLevel2025;

        $units = AdministrativeUnit::query()
            ->scheme($scheme)
            ->when($request->filled('level'), fn ($q) => $q->where('level', $request->string('level')->toString()))
            ->when($request->filled('parent_id'), fn ($q) => $q->where('parent_id', $request->integer('parent_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->search($request->string('q')->toString())
            ->withCount('children')
            ->orderBy('code')
            ->paginate(100);

        return response()->json($units->through(fn (AdministrativeUnit $unit) => self::present($unit)));
    }

    public function show(AdministrativeUnit $administrativeUnit): JsonResponse
    {
        $administrativeUnit->load('parent', 'successor');

        return response()->json(self::present($administrativeUnit) + [
            'full_name' => $administrativeUnit->fullName(),
            'parent' => $administrativeUnit->parent === null ? null : self::present($administrativeUnit->parent),
            'successor' => $administrativeUnit->successor === null ? null : self::present($administrativeUnit->successor),
        ]);
    }

    public function store(StoreAdministrativeUnitRequest $request): JsonResponse
    {
        return response()->json(self::present($this->units->create($request->validated())), 201);
    }

    public function update(UpdateAdministrativeUnitRequest $request, AdministrativeUnit $administrativeUnit): JsonResponse
    {
        return response()->json(self::present($this->units->update($administrativeUnit, $request->validated())));
    }

    public function destroy(AdministrativeUnit $administrativeUnit): Response
    {
        $this->units->delete($administrativeUnit);

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    public static function present(AdministrativeUnit $unit): array
    {
        return [
            'id' => $unit->id,
            'scheme' => $unit->scheme->value,
            'level' => $unit->level->value,
            'level_label' => $unit->level->label(),
            'code' => $unit->code,
            'name' => $unit->name,
            'unit_type' => $unit->unit_type,
            'parent_id' => $unit->parent_id,
            'successor_id' => $unit->successor_id,
            'status' => $unit->status->value,
            'status_label' => $unit->status->label(),
            'children_count' => $unit->children_count,
        ];
    }
}
