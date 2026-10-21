<?php

namespace App\Modules\Grade\Services;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use Illuminate\Support\Facades\DB;

class TranscriptService
{
    /** No unpublished grade is returned. */
    public function personal(Student $student, ?int $termId = null): array
    {
        $attempts = Enrollment::where('student_id', $student->id)
            ->whereNotIn('status', ['cancelled', 'withdrawn'])
            ->whereIn('course_section_id', DB::table('section_grade_workflows')->select('course_section_id')
                ->whereIn('status', ['published', 'locked'])->whereNull('deleted_at'))
            ->when($termId, fn ($q) => $q->where('term_id', $termId))
            ->with('subject:id,code,name,name_en,credits')->orderBy('term_id')->orderBy('id')->get();
        $results = CourseResult::whereIn('enrollment_id', $attempts->pluck('id'))->get()->keyBy('enrollment_id');
        return [
            'student' => $student->only('id', 'student_code', 'full_name'),
            'term_id' => $termId,
            'rows' => $attempts->filter(fn ($e) => $results->has($e->id))->map(fn ($e) => [
                'term_id' => $e->term_id, 'subject_code' => $e->subject?->code,
                'subject_name' => $e->subject?->name, 'subject_name_en' => $e->subject?->name_en,
                'credits' => $e->subject?->credits, 'score_10' => $results[$e->id]->score_10,
                'letter' => $results[$e->id]->letter, 'score_4' => $results[$e->id]->score_4,
                'passed' => $results[$e->id]->passed,
            ])->values()->all(),
            'verification_qr' => null, // DOC module P2 hook; do not forge validation URLs.
        ];
    }

    public function section(CourseSection $section): array
    {
        $enrollments = Enrollment::where('course_section_id', $section->id)
            ->whereNotIn('status', ['cancelled', 'withdrawn'])->with('student:id,student_code,full_name')->get();
        $results = CourseResult::whereIn('enrollment_id', $enrollments->pluck('id'))->get()->keyBy('enrollment_id');
        $rows = $enrollments->map(fn ($e) => [
            'student_code' => $e->student?->student_code, 'student_name' => $e->student?->full_name,
            'score_10' => $results->get($e->id)?->score_10, 'letter' => $results->get($e->id)?->letter,
            'score_4' => $results->get($e->id)?->score_4, 'passed' => $results->get($e->id)?->passed,
        ]);
        $graded = $rows->filter(fn ($row) => $row['score_10'] !== null);
        return [
            'section' => $section->only('id', 'code', 'subject_id', 'term_id'),
            'rows' => $rows->all(),
            'statistics' => [
                'graded' => $graded->count(), 'mean' => $graded->isEmpty() ? null : round($graded->avg('score_10'), 2),
                'pass_rate' => $graded->isEmpty() ? null : round($graded->where('passed', true)->count() * 100 / $graded->count(), 2),
                'distribution' => $graded->groupBy('letter')->map->count()->all(),
            ],
        ];
    }
}
