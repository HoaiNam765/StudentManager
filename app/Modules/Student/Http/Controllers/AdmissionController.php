<?php

namespace App\Modules\Student\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\AdmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/** API xác nhận / hủy nhập học (FR-STU-002, UAT-04). */
class AdmissionController extends Controller
{
    public function __construct(private readonly AdmissionService $admissions) {}

    /** Không gửi `admin_class_id` thì tự xếp vào lớp hành chính cùng khóa – ngành – hệ còn chỗ. */
    public function admit(Request $request, Student $student): JsonResponse
    {
        Gate::authorize('approve', $student);
        $data = $request->validate([
            'admin_class_id' => ['sometimes', 'nullable', 'integer', 'exists:admin_classes,id'],
            'admission_date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
        ], [], ['admin_class_id' => 'lớp hành chính', 'admission_date' => 'ngày nhập học']);

        $student = $this->admissions->admit($student, $data['admin_class_id'] ?? null, $data['admission_date'] ?? null, $request->user());
        $member = AdminClassMember::query()->with('adminClass:id,code,name')->where('student_id', $student->id)->current()->first();

        return response()->json([
            'id' => $student->id,
            'student_code' => $student->student_code,
            'school_email' => $student->school_email,
            'status' => $student->status->value,
            'status_label' => $student->status->label(),
            'admission_date' => $student->admission_date?->toDateString(),
            'user_id' => $student->user_id,
            'curriculum' => $student->curriculum?->only('id', 'code', 'name', 'version'),
            'admin_class' => $member?->adminClass?->only('id', 'code', 'name'),
        ]);
    }

    public function cancel(Request $request, Student $student): Response
    {
        Gate::authorize('approve', $student);
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'lý do'])['reason'];

        $this->admissions->cancel($student, $reason);

        return response()->noContent();
    }
}
