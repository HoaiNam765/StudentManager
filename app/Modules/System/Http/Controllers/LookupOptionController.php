<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\System\Enums\AdministrativeLevel;
use App\Modules\System\Enums\AdministrativeScheme;
use App\Modules\System\Models\AdministrativeUnit;
use App\Modules\System\Models\LookupValue;
use App\Modules\System\Services\AdministrativeUnitService;
use App\Modules\System\Services\LookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Danh sách lựa chọn cho biểu mẫu (giới tính, dân tộc, địa chỉ…): mọi người dùng đã đăng nhập đều đọc được,
 * chỉ trả giá trị đang hoạt động. Dữ liệu ba cấp cũ lấy bằng `?scheme=three_level_legacy` để tra địa chỉ cũ.
 */
class LookupOptionController extends Controller
{
    public function options(string $category, LookupService $lookups): JsonResponse
    {
        return response()->json($lookups->options($category)->map(fn (LookupValue $value) => [
            'id' => $value->id,
            'code' => $value->code,
            'name' => $value->name,
        ])->values());
    }

    public function administrativeUnits(Request $request, AdministrativeUnitService $units): JsonResponse
    {
        $scheme = AdministrativeScheme::tryFrom($request->string('scheme')->toString()) ?? AdministrativeScheme::TwoLevel2025;
        $level = AdministrativeLevel::tryFrom($request->string('level')->toString());
        $parentId = $request->filled('parent_id') ? $request->integer('parent_id') : null;

        $list = $units->options($scheme, $parentId, $level, $request->string('q')->toString())->limit(500)->get();

        return response()->json($list->map(fn (AdministrativeUnit $unit) => [
            'id' => $unit->id,
            'code' => $unit->code,
            'name' => $unit->name,
            'unit_type' => $unit->unit_type,
            'level' => $unit->level->value,
            'parent_id' => $unit->parent_id,
        ])->values());
    }
}
