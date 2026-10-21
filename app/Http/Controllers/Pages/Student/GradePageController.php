<?php

namespace App\Http\Controllers\Pages\Student;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Grade\Services\GpaService;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang xem kết quả học tập, GPA và bảng điểm cá nhân của sinh viên (#125, #127).
 * Hiển thị dữ liệu chính thức đã công bố. Tuân thủ EV-GRD-01 và BR-GRD-05.
 */
class GradePageController extends Controller
{
    public function __construct(
        private readonly AccessControl $access,
        private readonly GpaService $gpaService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $student = Student::query()->where('user_id', $user->id)->first();

        $terms = Term::query()->orderByDesc('id')->get();
        $selectedTermId = $request->integer('term_id') ?: ($terms->first()?->id ?? null);

        $termGpa = null;
        $cumulativeGpa = null;
        $allTermGpas = collect();
        $courseGrades = collect();

        if ($student !== null) {
            $this->gpaService->recalculate($student);

            $termGpa = TermGpa::query()
                ->where('student_id', $student->id)
                ->where('term_id', $selectedTermId)
                ->first();

            $cumulativeGpa = CumulativeGpa::query()
                ->where('student_id', $student->id)
                ->first();

            $allTermGpas = TermGpa::query()
                ->where('student_id', $student->id)
                ->with('term')
                ->get();

            // Chỉ hiển thị các lớp học phần có bảng điểm ĐÃ CÔNG BỐ (published hoặc locked)
            $enrollments = Enrollment::query()
                ->where('student_id', $student->id)
                ->when($selectedTermId, fn ($q) => $q->whereHas('section', fn ($sq) => $sq->where('term_id', $selectedTermId)))
                ->with(['subject:id,code,name,credits,counts_toward_gpa', 'section:id,code,term_id'])
                ->whereIn('course_section_id', function ($query) {
                    $query->select('course_section_id')->from('section_grade_workflows')
                        ->whereIn('status', ['published', 'locked'])->whereNull('deleted_at');
                })
                ->get();

            $results = CourseResult::query()
                ->whereIn('enrollment_id', $enrollments->pluck('id'))
                ->get()
                ->keyBy('enrollment_id');

            $entries = GradeEntry::query()
                ->whereIn('enrollment_id', $enrollments->pluck('id'))
                ->with('component:id,name,weight')
                ->get()
                ->groupBy('enrollment_id');

            $courseGrades = $enrollments->map(function ($enrollment) use ($results, $entries) {
                return [
                    'enrollment' => $enrollment,
                    'subject' => $enrollment->subject,
                    'section' => $enrollment->section,
                    'result' => $results->get($enrollment->id),
                    'components' => ($entries->get($enrollment->id) ?? collect())->sortBy('component.id')->values(),
                ];
            });
        }

        $pageData = [
            'api' => [
                'grades' => route('student.grades.mine'),
                'gpa' => route('student.gpa.mine'),
                'transcript' => route('student.transcript.mine'),
            ],
            'selectedTermId' => $selectedTermId,
            'hasStudent' => $student !== null,
        ];

        return view('student.grade.grades', compact(
            'student',
            'terms',
            'selectedTermId',
            'termGpa',
            'cumulativeGpa',
            'allTermGpas',
            'courseGrades',
            'pageData'
        ));
    }
}
