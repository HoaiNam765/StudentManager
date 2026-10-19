<?php

namespace App\Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Http\Requests\StudentRequest;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentChangeRequest;
use App\Modules\Student\Models\StudentContact;
use App\Modules\Student\Services\StudentSelfService;
use App\Modules\Student\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Cổng sinh viên — hồ sơ của tôi (FR-STU-006): xem, tự sửa liên hệ / địa chỉ / ảnh / người thân, gửi và hủy yêu cầu sửa
 * trường nhạy cảm. Chỉ trên hồ sơ gắn với tài khoản đang đăng nhập.
 */
class StudentSelfController extends Controller
{
    public function __construct(
        private readonly StudentSelfService $self,
        private readonly StudentService $students,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $student = $this->me($request);

        $response = app(StudentController::class)->show($student);

        return $response->setData([...$response->getData(true), 'change_requests' => $this->requests($student)]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['sometimes', 'nullable', 'string', StudentRequest::PHONE_RULE],
            'personal_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'permanent_address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'permanent_unit_id' => ['sometimes', 'nullable', 'integer', 'exists:administrative_units,id'],
            'temporary_address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'temporary_unit_id' => ['sometimes', 'nullable', 'integer', 'exists:administrative_units,id'],
            'full_name' => ['prohibited'],
            'date_of_birth' => ['prohibited'],
            'national_id' => ['prohibited'],
            'major_id' => ['prohibited'],
        ], [
            'prohibited' => 'Trường :attribute chỉ sửa qua yêu cầu chỉnh sửa kèm minh chứng (BR-STU-06).',
        ], ['phone' => 'số điện thoại', 'personal_email' => 'email cá nhân', 'full_name' => 'họ tên', 'date_of_birth' => 'ngày sinh', 'national_id' => 'số CCCD', 'major_id' => 'ngành']);

        $this->self->updateSelf($this->me($request), $data, $request->user());

        return $this->show($request);
    }

    public function photo(Request $request): JsonResponse
    {
        $request->validate(['photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048']], [], ['photo' => 'ảnh']);
        $student = $this->self->updatePhoto($this->me($request), $request->file('photo'));

        return response()->json(['photo_path' => $student->photo_path]);
    }

    public function storeContact(Request $request): JsonResponse
    {
        $contact = $this->students->addContact($this->me($request), $this->contactData($request, true));

        return response()->json($contact, 201);
    }

    public function updateContact(Request $request, StudentContact $studentContact): JsonResponse
    {
        abort_unless($studentContact->student_id === $this->me($request)->id, 404);

        return response()->json($this->students->updateContact($studentContact, $this->contactData($request, false)));
    }

    public function destroyContact(Request $request, StudentContact $studentContact): Response
    {
        abort_unless($studentContact->student_id === $this->me($request)->id, 404);
        $this->students->removeContact($studentContact);

        return response()->noContent();
    }

    public function storeRequest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'date_of_birth' => ['sometimes', 'nullable', 'date_format:Y-m-d', 'before:today'],
            'national_id' => ['sometimes', 'nullable', 'string', 'regex:/^[0-9 .\-]{12,16}$/'],
            'major_id' => ['sometimes', 'nullable', 'integer', 'exists:majors,id'],
            'reason' => ['required', 'string', 'max:1000'],
            'evidence' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:'.(int) config('studentmanager.student.decision_max_kb', 5120)],
        ], [], ['full_name' => 'họ tên', 'date_of_birth' => 'ngày sinh', 'national_id' => 'số CCCD', 'major_id' => 'ngành', 'reason' => 'lý do', 'evidence' => 'minh chứng']);

        $changeRequest = $this->self->request(
            $this->me($request),
            collect($data)->only(StudentSelfService::REQUEST_FIELDS)->all(),
            $data['reason'],
            $request->file('evidence'),
            $request->user(),
        );

        return response()->json(StudentChangeRequestController::present($changeRequest), 201);
    }

    public function cancelRequest(Request $request, StudentChangeRequest $studentChangeRequest): JsonResponse
    {
        abort_unless($studentChangeRequest->student_id === $this->me($request)->id, 404);

        return response()->json(StudentChangeRequestController::present($this->self->cancel($studentChangeRequest, $request->user())));
    }

    private function me(Request $request): Student
    {
        return Student::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function contactData(Request $request, bool $creating): array
    {
        $required = $creating ? 'required' : 'sometimes';

        return $request->validate([
            'full_name' => [$required, 'string', 'max:255'],
            'relationship' => [$required, 'string', 'max:50'],
            'phone' => ['sometimes', 'nullable', 'string', StudentRequest::PHONE_RULE],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'is_emergency' => ['sometimes', 'boolean'],
            'receives_notifications' => ['sometimes', 'boolean'],
        ], [], ['full_name' => 'họ tên người thân', 'relationship' => 'quan hệ', 'phone' => 'số điện thoại']);
    }

    /** @return list<array<string, mixed>> */
    private function requests(Student $student): array
    {
        return StudentChangeRequest::query()->where('student_id', $student->id)->orderByDesc('id')->limit(10)->get()
            ->map(fn (StudentChangeRequest $r) => StudentChangeRequestController::present($r))->all();
    }
}
