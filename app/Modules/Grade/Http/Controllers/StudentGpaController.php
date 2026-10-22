<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Student\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** GPA học kỳ / tích lũy, xếp loại của sinh viên (FR-GRD-008, 009): đọc số đã lưu, tính lại khi công bố / điều chỉnh điểm. */
class StudentGpaController extends Controller
{
    public function mine(Request $request): JsonResponse
    {
        $student = Student::query()->where('user_id', $request->user()->id)->firstOrFail();

        return response()->json($this->present($student));
    }

    public function show(Request $request, Student $student, AccessControl $access): JsonResponse
    {
        abort_unless($access->allowsOn($request->user(), 'STU', PermissionAction::View, $student), 403,
            'Sinh viên này ngoài phạm vi dữ liệu của bạn.');

        return response()->json($this->present($student));
    }

    /** @return array<string, mixed> */
    private function present(Student $student): array
    {
        return [
            'student' => $student->only('id', 'student_code', 'full_name', 'cohort'),
            'terms' => TermGpa::query()->with('term:id,name,academic_year_id,start_date', 'term.academicYear:id,name')
                ->where('student_id', $student->id)->get()
                ->sortBy(fn (TermGpa $t) => $t->term?->start_date?->format('Y-m-d'))
                ->map(fn (TermGpa $t) => [
                    'term_id' => $t->term_id,
                    'term' => trim(($t->term?->name ?? '').' – '.($t->term?->academicYear?->name ?? ''), ' –'),
                ] + $t->only('gpa', 'credits_attempted', 'credits_earned', 'classification'))
                ->values(),
            'cumulative' => CumulativeGpa::query()->where('student_id', $student->id)->first()
                ?->only('gpa', 'credits_attempted', 'credits_earned', 'classification', 'updated_at'),
        ];
    }
}
