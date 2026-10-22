<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Services\GradeChangeService;
use App\Modules\Grade\Services\GradeWorkflowService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Form HTML của giao diện FE admin/grade/index: gọi cùng Service với API JSON, trả về trang kèm thông báo. */
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
        $schedule = isset($data['scheduled_at']) ? CarbonImmutable::parse($data['scheduled_at'], config('studentmanager.display_timezone'))->format('Y-m-d H:i') : null;

        return $this->attempt(fn () => $this->workflows->change($courseSection, $request->user(), GradePublicationStatus::from($data['status']), $data['reason'] ?? null, $schedule),
            'Đã cập nhật quy trình duyệt điểm.');
    }

    public function approve(Request $request, GradeChangeRequest $gradeChangeRequest): RedirectResponse
    {
        return $this->attempt(fn () => $this->changes->decide($gradeChangeRequest, $request->user(), true), 'Đã xử lý yêu cầu điều chỉnh điểm.');
    }

    public function reject(Request $request, GradeChangeRequest $gradeChangeRequest): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        return $this->attempt(fn () => $this->changes->decide($gradeChangeRequest, $request->user(), false, $data['reason']), 'Đã từ chối yêu cầu điều chỉnh điểm.');
    }

    /** Lỗi nghiệp vụ (bước không hợp lệ, thiếu lý do…) hiện trên trang thay vì trang lỗi 422. */
    private function attempt(callable $action, string $message): RedirectResponse
    {
        try {
            $action();
        } catch (BusinessRuleException $e) {
            return back()->withErrors(['grades' => $e->getMessage()]);
        }

        return back()->with('success', $message);
    }
}
