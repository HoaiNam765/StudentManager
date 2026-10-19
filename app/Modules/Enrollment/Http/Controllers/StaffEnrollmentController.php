<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Services\EnrollmentRules;
use App\Modules\Enrollment\Services\EnrollmentService;
use App\Modules\Enrollment\Services\RegistrationFinalizer;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Cán bộ đăng ký / hủy thay sinh viên và chốt đăng ký (FR-ENR-009, 012; BR-ENR-08). Chỉ ENR toàn trường (ACAD).
 * Bỏ qua điều kiện (`skip`: period, capacity, prerequisite, schedule, credits, debt) phải có lý do; người thao tác được
 * ghi là người phê duyệt, nhật ký đăng ký và nhật ký kiểm toán ghi lại.
 */
class StaffEnrollmentController extends Controller
{
    public const SKIPPABLE = ['period', 'capacity', 'prerequisite', 'schedule', 'credits', 'debt'];

    public function __construct(private readonly EnrollmentService $enrollments) {}

    public function store(Request $request, Student $student): JsonResponse
    {
        Gate::authorize('view', $student);
        $data = $request->validate([
            'course_section_id' => ['required', 'integer', 'exists:course_sections,id'],
            'type' => ['sometimes', Rule::in([EnrollmentRules::TYPE_NORMAL, EnrollmentRules::TYPE_RETAKE, EnrollmentRules::TYPE_IMPROVE])],
            'skip' => ['sometimes', 'array'],
            'skip.*' => [Rule::in(self::SKIPPABLE)],
            'reason' => [$request->filled('skip') ? 'required' : 'nullable', 'string', 'max:500'],
        ], [], ['course_section_id' => 'lớp học phần', 'type' => 'loại đăng ký', 'skip' => 'điều kiện bỏ qua', 'reason' => 'lý do ngoại lệ']);

        $enrollment = $this->enrollments->register(
            $student,
            CourseSection::query()->findOrFail($data['course_section_id']),
            type: $data['type'] ?? EnrollmentRules::TYPE_NORMAL,
            actor: $request->user(),
            ip: $request->ip(),
            skip: array_values(array_unique($data['skip'] ?? [])),
            overrideReason: $data['reason'] ?? null,
        );

        return response()->json($this->present($enrollment), 201);
    }

    public function cancel(Request $request, Enrollment $enrollment): JsonResponse
    {
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'lý do'])['reason'];

        return response()->json($this->present($this->enrollments->cancel($enrollment, $request->user(), $request->ip(), staff: true, reason: $reason)));
    }

    public function preview(Term $term, RegistrationFinalizer $finalizer): JsonResponse
    {
        return response()->json($finalizer->preview($term));
    }

    public function finalize(Request $request, Term $term, RegistrationFinalizer $finalizer): JsonResponse
    {
        $ack = $request->validate(['acknowledge_under_min' => ['sometimes', 'boolean']])['acknowledge_under_min'] ?? false;

        return response()->json($finalizer->finalize($term, (bool) $ack, $request->user()));
    }

    /** @return array<string, mixed> */
    private function present(Enrollment $enrollment): array
    {
        $enrollment->loadMissing('section:id,code', 'student:id,student_code,full_name');

        return [
            'id' => $enrollment->id,
            'student' => $enrollment->student->only('id', 'student_code', 'full_name'),
            'section' => $enrollment->section->only('id', 'code'),
            'type' => $enrollment->type,
            'status' => $enrollment->status->value,
            'status_label' => $enrollment->status->label(),
            'source' => $enrollment->source,
            'override_reason' => $enrollment->override_reason,
            'approved_by' => $enrollment->approved_by,
        ];
    }
}
