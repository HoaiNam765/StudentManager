<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Services\GradeChangeService;
use App\Modules\Grade\Services\GradeWorkflowService;
use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Web form adapter: giữ API JSON cũ, trả về redirect/flash cho Blade. */
class GradeAdminActionController extends Controller
{
    public function __construct(
        private readonly GradeWorkflowService $workflows,
        private readonly GradeChangeService $changes,
    ) {}

    public function workflow(Request $request, CourseSection $courseSection): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(GradePublicationStatus::class)],
            'reason' => ['nullable', 'string', 'max:2000'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ]);
        $this->workflows->change($courseSection, $request->user(), GradePublicationStatus::from($data['status']), $data['reason'] ?? null, $data['scheduled_at'] ?? null);

        return back()->with('success', 'Đã cập nhật quy trình duyệt điểm.');
    }

    public function approve(Request $request, GradeChangeRequest $gradeChangeRequest): RedirectResponse
    {
        $this->changes->decide($gradeChangeRequest, $request->user(), true);

        return back()->with('success', 'Đã xử lý yêu cầu điều chỉnh điểm.');
    }

    public function reject(Request $request, GradeChangeRequest $gradeChangeRequest): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);
        $this->changes->decide($gradeChangeRequest, $request->user(), false, $data['reason']);

        return back()->with('success', 'Đã từ chối yêu cầu điều chỉnh điểm.');
    }
}
