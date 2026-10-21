<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Services\GradeWorkflowService;
use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GradeWorkflowController extends Controller
{
    public function __construct(private readonly GradeWorkflowService $workflow) {}

    public function show(Request $request, CourseSection $courseSection): JsonResponse
    {
        abort_unless(app(AccessControl::class)->allowsOn($request->user(), 'GRD', PermissionAction::View, $courseSection), 403);

        return response()->json($this->workflow->current($courseSection));
    }

    public function change(Request $request, CourseSection $courseSection): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(GradePublicationStatus::class)],
            'reason' => ['nullable', 'string', 'max:2000'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ]);

        return response()->json([
            'message' => 'Đã xử lý trạng thái duyệt điểm.',
            'workflow' => $this->workflow->change($courseSection, $request->user(), GradePublicationStatus::from($data['status']), $data['reason'] ?? null, $data['scheduled_at'] ?? null),
        ]);
    }
}
