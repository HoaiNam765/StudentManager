<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Student\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Sinh viên xem điểm của mình (FR-GRD-005; BR-GRD-08): chỉ lớp đã công bố / đã khóa — kể cả điểm thành phần; lớp chưa
 * công bố không trả gì. Lọc theo học kỳ bằng `term_id`.
 */
class StudentGradesController extends Controller
{
    public function mine(Request $request): JsonResponse
    {
        $student = Student::query()->where('user_id', $request->user()->id)->firstOrFail();
        $published = SectionGradeWorkflow::query()
            ->whereIn('status', [GradePublicationStatus::Published->value, GradePublicationStatus::Locked->value])
            ->select('course_section_id');

        $enrollments = Enrollment::query()
            ->with(['subject:id,code,name,credits', 'section:id,code,term_id', 'term:id,name'])
            ->where('student_id', $student->id)
            ->whereIn('course_section_id', $published)
            ->when($request->filled('term_id'), fn ($q) => $q->where('term_id', $request->integer('term_id')))
            ->orderBy('term_id')
            ->get();

        $results = CourseResult::query()->whereIn('enrollment_id', $enrollments->pluck('id'))->get()->keyBy('enrollment_id');
        $entries = GradeEntry::query()->with('component:id,name,weight,sort_order')
            ->whereIn('enrollment_id', $enrollments->pluck('id'))->get()->groupBy('enrollment_id');

        return response()->json([
            'student' => $student->only('id', 'student_code', 'full_name'),
            'grades' => $enrollments->map(fn (Enrollment $e) => [
                'enrollment_id' => $e->id,
                'term' => $e->term?->name,
                'subject' => $e->subject?->only('code', 'name', 'credits'),
                'section' => $e->section?->code,
                'result' => $results->get($e->id)?->only('score10', 'letter', 'point4', 'is_passed', 'status'),
                'components' => ($entries->get($e->id) ?? collect())
                    ->sortBy(fn (GradeEntry $entry) => $entry->component?->sort_order)
                    ->map(fn (GradeEntry $entry) => [
                        'name' => $entry->component?->name,
                        'weight' => $entry->component?->weight,
                        'score' => $entry->score,
                    ])->values(),
            ])->values(),
        ]);
    }
}
