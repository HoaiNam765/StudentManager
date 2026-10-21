<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Student\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentGradesController extends Controller
{
    /** Never disclose draft grades, including component marks. */
    public function mine(Request $request): JsonResponse
    {
        $student = Student::query()->where('user_id', $request->user()->id)->firstOrFail();
        $enrollments = Enrollment::query()->where('student_id', $student->id)
            ->with(['subject:id,code,name,credits', 'section:id,code,term_id'])
            ->whereIn('course_section_id', function ($query) {
                $query->select('course_section_id')->from('section_grade_workflows')
                    ->whereIn('status', ['published', 'locked'])->whereNull('deleted_at');
            })->get();
        $results = \App\Modules\Grade\Models\CourseResult::query()
            ->whereIn('enrollment_id', $enrollments->pluck('id'))->get()->keyBy('enrollment_id');
        $entries = GradeEntry::query()->whereIn('enrollment_id', $enrollments->pluck('id'))
            ->with('component:id,name,weight')->get()->groupBy('enrollment_id');
        return response()->json(['grades' => $enrollments->map(fn ($e) => [
            'enrollment_id' => $e->id, 'subject' => $e->subject, 'section' => $e->section,
            'result' => $results->get($e->id),
            'components' => ($entries->get($e->id) ?? collect())->map(fn ($entry) => [
                'name' => $entry->component?->name, 'weight' => $entry->component?->weight, 'score' => $entry->score,
            ])->values(),
        ])->values()]);
    }
}
