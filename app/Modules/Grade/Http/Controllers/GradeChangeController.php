<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Models\GradeVersion;
use App\Modules\Grade\Services\GradeChangeService;
use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeChangeController extends Controller
{
    public function __construct(private readonly GradeChangeService $service) {}

    public function store(Request $request, Enrollment $enrollment): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
            'evidence' => ['nullable', 'file', 'max:5120', 'mimes:pdf,png,jpg,jpeg'],
            'scores' => ['required', 'array', 'min:1'],
            'scores.*.component_id' => ['required', 'integer'],
            'scores.*.score' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);
        $evidence = $request->hasFile('evidence') ? $request->file('evidence')->store('grade-evidence', 'local') : null;

        return response()->json($this->service->request($enrollment, $request->user(), $data['scores'], $data['reason'], $evidence), 201);
    }

    public function approve(Request $request, GradeChangeRequest $gradeChangeRequest): JsonResponse
    {
        return response()->json($this->service->decide($gradeChangeRequest, $request->user(), true));
    }

    public function reject(Request $request, GradeChangeRequest $gradeChangeRequest): JsonResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        return response()->json($this->service->decide($gradeChangeRequest, $request->user(), false, $data['reason']));
    }

    public function history(Request $request, Enrollment $enrollment): JsonResponse
    {
        $section = CourseSection::findOrFail($enrollment->course_section_id);
        abort_unless(app(AccessControl::class)->allowsOn($request->user(), 'GRD', PermissionAction::View, $section), 403);

        return response()->json(GradeVersion::where('enrollment_id', $enrollment->id)->orderByDesc('version')->get());
    }
}
