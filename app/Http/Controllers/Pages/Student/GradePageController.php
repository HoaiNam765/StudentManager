<?php

namespace App\Http\Controllers\Pages\Student;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Trang xem kết quả học tập, GPA và bảng điểm cá nhân của sinh viên (#125, #127).
 * Hiển thị dữ liệu chính thức đã công bố. Tuân thủ EV-GRD-01 và BR-GRD-05.
 */
class GradePageController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $student = Student::query()->where('user_id', $user->id)->first();

        $terms = Term::query()->orderByDesc('start_date')->get();
        // Mặc định mở học kỳ gần nhất sinh viên đã có GPA (điểm đã công bố); chưa có thì học kỳ gần nhất đã bắt đầu
        $today = now(config('studentmanager.display_timezone'))->toDateString();
        $gradedTermIds = $student === null ? [] : TermGpa::query()->where('student_id', $student->id)->pluck('term_id')->all();
        $defaultTerm = $terms->first(fn (Term $t) => in_array($t->id, $gradedTermIds, true))
            ?? $terms->first(fn (Term $t) => $t->start_date->toDateString() <= $today)
            ?? $terms->first();
        $selectedTermId = $request->integer('term_id') ?: $defaultTerm?->id;

        $termGpa = null;
        $cumulativeGpa = null;
        $allTermGpas = collect();
        $courseGrades = collect();

        if ($student !== null) {
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
                'grades' => route('student.grades'),
                'gpa' => route('student.gpa'),
                'transcript' => route('student.transcript.mine'),
            ],
            'selectedTermId' => $selectedTermId,
            'hasStudent' => $student !== null,
        ];

        // GPA do GpaService tự tính lại khi điểm được công bố (listener), trang chỉ đọc.
        $student = $student === null ? null : $this->profile($student);

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

    /**
     * Thông tin sinh viên theo các khóa view student/grade/grades đang đọc: `cohort?->code` (khóa lưu dạng năm, hiển thị
     * "16DH"), `adminClass?->code` (lớp hành chính hiện hành), `major?->name`.
     */
    private function profile(Student $student): object
    {
        $class = $student->adminClassMembers()->current()->with('adminClass:id,code')->first()?->adminClass;

        return (object) [
            'id' => $student->id,
            'full_name' => $student->full_name,
            'student_code' => $student->student_code,
            'major' => $student->major()->first(['id', 'name']),
            'adminClass' => $class,
            'cohort' => $student->cohort === null ? null : (object) ['code' => Cohort::query()->where('start_year', $student->cohort)->value('code') ?? Cohort::codeFor((int) $student->cohort)],
        ];
    }
}
