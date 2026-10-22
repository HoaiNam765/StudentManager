<?php

namespace App\Http\Controllers\Pages\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Models\GradeVersion;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\Grade\Services\TranscriptService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GradeAdminDetailController extends Controller
{
    public function __construct(
        private readonly AccessControl $access,
        private readonly GradebookService $gradebook,
        private readonly TranscriptService $transcripts,
    ) {}

    public function section(Request $request, CourseSection $courseSection): View
    {
        abort_unless($this->access->allowsOn($request->user(), 'GRD', PermissionAction::View, $courseSection), 403);
        $courseSection->load(['subject', 'term']);
        $book = $this->gradebook->gradebook($courseSection, $request->user());
        $resultIds = collect($book['students'])->pluck('enrollment_id');
        $results = CourseResult::query()->whereIn('enrollment_id', $resultIds)->get()->keyBy('enrollment_id');
        $workflow = SectionGradeWorkflow::query()->where('course_section_id', $courseSection->id)->first();
        $report = $this->transcripts->section($courseSection);

        return view('admin.grade.section', compact('courseSection', 'book', 'results', 'workflow', 'report'));
    }

    public function history(Request $request, Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'subject', 'section']);
        abort_unless($enrollment->section && $this->access->allowsOn($request->user(), 'GRD', PermissionAction::View, $enrollment->section), 403);
        $versions = GradeVersion::query()->where('enrollment_id', $enrollment->id)->orderByDesc('version')->get();
        $requests = GradeChangeRequest::query()->where('enrollment_id', $enrollment->id)->latest('id')->get();

        return view('admin.grade.history', compact('enrollment', 'versions', 'requests'));
    }

    public function gpa(Request $request): View
    {
        $request->validate(['student_code' => ['nullable', 'string', 'max:50']]);
        // Scope by accessible course sections, never query arbitrary student GPA globally.
        $sections = $this->access->constrain(CourseSection::query(), $request->user(), 'GRD', PermissionAction::View)->select('id');
        $studentCode = trim((string) $request->query('student_code', ''));
        $student = null;
        $termRows = collect();
        $cumulative = null;
        if ($studentCode !== '') {
            $student = Student::query()->where('student_code', $studentCode)
                ->whereIn('id', Enrollment::query()->select('student_id')->whereIn('course_section_id', $sections))
                ->first();
            if ($student) {
                // Only after at least one published/locked attempt in scope, otherwise deny disclosure.
                $hasPublished = Enrollment::query()->where('student_id', $student->id)
                    ->whereIn('course_section_id', $this->access->constrain(CourseSection::query(), $request->user(), 'GRD', PermissionAction::View)->select('id'))
                    ->whereIn('course_section_id', DB::table('section_grade_workflows')->select('course_section_id')->whereIn('status', ['published', 'locked'])->whereNull('deleted_at'))->exists();
                if (! $hasPublished) {
                    $student = null;
                } else {
                    $termRows = TermGpa::query()->where('student_id', $student->id)->orderByDesc('term_id')->get();
                    $cumulative = CumulativeGpa::query()->where('student_id', $student->id)->first();
                }
            }
        }

        return view('admin.grade.gpa', compact('studentCode', 'student', 'termRows', 'cumulative'));
    }

    public function evidence(Request $request, GradeChangeRequest $gradeChangeRequest): BinaryFileResponse
    {
        $enrollment = Enrollment::query()->findOrFail($gradeChangeRequest->enrollment_id);
        $section = CourseSection::query()->findOrFail($enrollment->course_section_id);
        abort_unless($this->access->allowsOn($request->user(), 'GRD', PermissionAction::View, $section), 403);
        abort_unless($gradeChangeRequest->evidence_path && Storage::disk('local')->exists($gradeChangeRequest->evidence_path), 404);

        return response()->download(Storage::disk('local')->path($gradeChangeRequest->evidence_path));
    }
}
