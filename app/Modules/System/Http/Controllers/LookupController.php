<?php

namespace App\Modules\System\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\System\Http\Requests\StoreLookupCategoryRequest;
use App\Modules\System\Http\Requests\StoreLookupValueRequest;
use App\Modules\System\Http\Requests\UpdateLookupCategoryRequest;
use App\Modules\System\Http\Requests\UpdateLookupValueRequest;
use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use App\Modules\System\Services\LookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * API quản trị danh mục dùng chung (FR-SYS-002). Quyền kiểm tra bằng middleware `permission:SYS.*` ở routes/admin.php.
 * Giao diện quản trị gọi các API này; ô chọn trong biểu mẫu dùng LookupOptionController.
 */
class LookupController extends Controller
{
    public function __construct(private readonly LookupService $lookups) {}

    public function categories(Request $request): JsonResponse
    {
        $categories = LookupCategory::query()
            ->search($request->string('q')->toString())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->withCount('values')
            ->orderBy('code')
            ->paginate(50);

        return response()->json($categories->through(fn (LookupCategory $category) => $this->presentCategory($category)));
    }

    public function storeCategory(StoreLookupCategoryRequest $request): JsonResponse
    {
        return response()->json($this->presentCategory($this->lookups->createCategory($request->validated())), 201);
    }

    public function updateCategory(UpdateLookupCategoryRequest $request, LookupCategory $lookupCategory): JsonResponse
    {
        return response()->json($this->presentCategory($this->lookups->updateCategory($lookupCategory, $request->validated())));
    }

    public function destroyCategory(LookupCategory $lookupCategory): Response
    {
        $this->lookups->deleteCategory($lookupCategory);

        return response()->noContent();
    }

    public function values(Request $request, LookupCategory $lookupCategory): JsonResponse
    {
        $values = $lookupCategory->values()
            ->search($request->string('q')->toString())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(100);

        return response()->json($values->through(fn (LookupValue $value) => $this->presentValue($value)));
    }

    public function storeValue(StoreLookupValueRequest $request, LookupCategory $lookupCategory): JsonResponse
    {
        return response()->json($this->presentValue($this->lookups->createValue($lookupCategory, $request->validated())), 201);
    }

    public function updateValue(UpdateLookupValueRequest $request, LookupValue $lookupValue): JsonResponse
    {
        return response()->json($this->presentValue($this->lookups->updateValue($lookupValue, $request->validated())));
    }

    public function destroyValue(LookupValue $lookupValue): Response
    {
        $this->lookups->deleteValue($lookupValue);

        return response()->noContent();
    }

    /** @return array<string, mixed> */
    private function presentCategory(LookupCategory $category): array
    {
        return [
            'id' => $category->id,
            'code' => $category->code,
            'name' => $category->name,
            'description' => $category->description,
            'is_system' => $category->is_system,
            'status' => $category->status->value,
            'status_label' => $category->status->label(),
            'values_count' => $category->values_count ?? $category->values()->count(),
        ];
    }

    /** @return array<string, mixed> */
    private function presentValue(LookupValue $value): array
    {
        return [
            'id' => $value->id,
            'category_id' => $value->lookup_category_id,
            'code' => $value->code,
            'name' => $value->name,
            'sort_order' => $value->sort_order,
            'status' => $value->status->value,
            'status_label' => $value->status->label(),
        ];
    }
}
