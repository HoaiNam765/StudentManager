<?php

namespace App\Modules\Teacher\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Teacher\Http\Requests\LecturerProfileRequest;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Models\LecturerChangeRequest;
use App\Modules\Teacher\Services\LecturerProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Cổng giảng viên: hồ sơ của chính mình (BR-TCH-07). Liên hệ và ảnh sửa ngay; họ tên, chức danh, học vị gửi đề nghị
 * chờ cán bộ xác nhận. Tài khoản chưa gắn hồ sơ giảng viên nhận 404.
 */
class LecturerProfileController extends Controller
{
    public function __construct(private readonly LecturerProfileService $profiles) {}

    public function show(Request $request): JsonResponse
    {
        $lecturer = $this->own($request);

        return response()->json($this->present($lecturer));
    }

    public function update(LecturerProfileRequest $request): JsonResponse
    {
        $lecturer = $this->own($request);
        $result = $this->profiles->update($lecturer, $request->payload(), $request->user(), $request->input('reason'));

        return response()->json($this->present($result['lecturer']) + [
            'change_request' => $result['request'] === null ? null : LecturerController::presentRequest($result['request']),
        ]);
    }

    public function photo(Request $request): JsonResponse
    {
        $lecturer = $this->own($request);
        $request->validate(
            ['photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.(int) config('studentmanager.teacher.photo_max_kb')]],
            [],
            ['photo' => 'ảnh'],
        );

        return response()->json($this->present($this->profiles->updatePhoto($lecturer, $request->file('photo'))));
    }

    public function cancelRequest(Request $request, LecturerChangeRequest $lecturerChangeRequest): JsonResponse
    {
        abort_unless($lecturerChangeRequest->lecturer_id === $this->own($request)->id, 404);

        return response()->json(LecturerController::presentRequest($this->profiles->cancel($lecturerChangeRequest, $request->user())));
    }

    private function own(Request $request): Lecturer
    {
        return Lecturer::query()->where('user_id', $request->user()->id)->with('department.faculty')->firstOr(
            fn () => abort(404, 'Tài khoản chưa gắn với hồ sơ giảng viên.')
        );
    }

    /** @return array<string, mixed> */
    private function present(Lecturer $lecturer): array
    {
        return [
            'id' => $lecturer->id,
            'code' => $lecturer->code,
            'full_name' => $lecturer->full_name,
            'email' => $lecturer->email,
            'phone' => $lecturer->phone,
            'photo_url' => $lecturer->photo_path === null ? null : asset('storage/'.$lecturer->photo_path),
            'department' => $lecturer->department?->only('id', 'code', 'name'),
            'faculty' => $lecturer->department?->faculty?->only('id', 'code', 'name'),
            'academic_title' => $lecturer->academic_title->value,
            'academic_title_label' => $lecturer->academic_title_label,
            'academic_degree' => $lecturer->academic_degree?->value,
            'academic_degree_label' => $lecturer->academic_degree_label,
            'expertise' => $lecturer->expertise,
            'status_label' => $lecturer->status_label,
            'editable' => LecturerProfileService::SELF_EDITABLE,
            'needs_approval' => LecturerProfileService::NEEDS_APPROVAL,
            'pending_request' => ($pending = $lecturer->changeRequests()->where('status', LecturerChangeRequest::PENDING)->latest('id')->first()) === null
                ? null
                : LecturerController::presentRequest($pending),
        ];
    }
}
