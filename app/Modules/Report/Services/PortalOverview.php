<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\StudentCurriculumService;
use App\Modules\Teacher\Models\Lecturer;
use Illuminate\Support\Facades\DB;

/**
 * Dữ liệu trang tổng quan cổng sinh viên và cổng giảng viên (FR-RPT-009, 010; mock student/tong-quan, teacher/tong-quan),
 * dựng thẳng trong view. Dựa trên DashboardService (lịch hôm nay, hạn chót, GPA, thông báo) và bổ sung: hồ sơ, tín chỉ
 * theo khối kiến thức của CTĐT, học phần học kỳ này, kết quả mới công bố (sinh viên); lớp đang dạy, bảng điểm cần xử lý,
 * yêu cầu điều chỉnh điểm (giảng viên). Chỉ dữ liệu của chính người dùng. Trả `null` khi tài khoản chưa gắn hồ sơ.
 */
class PortalOverview
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly StudentCurriculumService $curricula,
        private readonly AcademicRecord $record,
    ) {}

    /** @return array<string, mixed>|null */
    public function student(User $user): ?array
    {
        $student = Student::query()->with('major:id,name')->where('user_id', $user->id)->first();

        if ($student === null) {
            return null;
        }

        $base = $this->dashboard->student($user);
        $curriculum = $this->curricula->current($student);
        $term = $this->currentTerm();
        $current = $term === null ? collect() : Enrollment::query()->with('subject:id,credits')
            ->where('student_id', $student->id)->where('term_id', $term->id)
            ->whereIn('status', EnrollmentStatus::activeValues())->get();
        $earned = (int) ($base['credits_earned'] ?? 0);
        $total = (int) ($curriculum?->total_credits ?? 0);
        $inProgress = (int) $current->sum(fn (Enrollment $e) => $e->subject?->credits);

        return $base + [
            'profile' => [
                'name' => $student->full_name,
                'code' => $student->student_code,
                'major' => $student->major?->name,
                'cohort' => $student->cohort,
                'class' => AdminClassMember::query()->current()->where('student_id', $student->id)->with('adminClass:id,code')->first()?->adminClass?->code,
            ],
            'term' => $term ? trim($term->name.' – '.($term->academicYear?->name ?? ''), ' –') : null,
            'credits' => [
                'earned' => $earned,
                'total' => $total,
                'in_progress' => $inProgress,
                'remaining' => max(0, $total - $earned - $inProgress),
                'pct' => $total > 0 ? round(min(100, $earned * 100 / $total), 1) : 0,
                'in_progress_pct' => $total > 0 ? round(min(100, $inProgress * 100 / $total), 1) : 0,
            ],
            'current' => ['courses' => $current->count(), 'credits' => $inProgress],
            'blocks' => $curriculum === null ? [] : $this->blocks($curriculum->id, $this->record->passedSubjectIds($student)),
            'recent_results' => $this->recentResults($student),
            'gpa10' => $this->gpa10($student),
        ];
    }

    /** @return array<string, mixed>|null */
    public function lecturer(User $user): ?array
    {
        $lecturer = Lecturer::query()->with('department:id,name,faculty_id', 'department.faculty:id,name')->where('user_id', $user->id)->first();

        if ($lecturer === null) {
            return null;
        }

        $base = $this->dashboard->teacher($user);
        $term = $this->currentTerm();
        $sections = $term === null ? collect() : CourseSection::query()
            ->with('subject:id,code,name,credits')
            ->whereIn('id', SectionInstructor::query()->where('lecturer_id', $lecturer->id)->select('course_section_id'))
            ->where('term_id', $term->id)->where('status', '!=', SectionStatus::Cancelled->value)
            ->orderBy('code')->get();
        $workflows = SectionGradeWorkflow::query()->whereIn('course_section_id', $sections->pluck('id'))->get()->keyBy('course_section_id');
        $sizes = DB::table('enrollments')->whereIn('course_section_id', $sections->pluck('id'))
            ->whereIn('status', EnrollmentStatus::activeValues())->whereNull('deleted_at')
            ->groupBy('course_section_id')->selectRaw('course_section_id, count(*) as n')->pluck('n', 'course_section_id');

        return $base + [
            'profile' => [
                'name' => $lecturer->full_name,
                'code' => $lecturer->code,
                'department' => $lecturer->department?->name,
                'faculty' => $lecturer->department?->faculty?->name,
            ],
            'term' => $term ? trim($term->name.' – '.($term->academicYear?->name ?? ''), ' –') : null,
            'sections' => $sections->map(fn (CourseSection $s) => [
                'id' => $s->id,
                'code' => $s->code,
                'subject' => $s->subject?->name,
                'credits' => $s->subject?->credits,
                'students' => (int) ($sizes[$s->id] ?? 0),
                'max' => $s->max_size,
                'status' => $s->status->label(),
                'grade_status' => ($workflows->get($s->id)?->status ?? GradePublicationStatus::Draft)->label(),
                'grade_editable' => ($workflows->get($s->id)?->status ?? GradePublicationStatus::Draft)->editable(),
            ])->values()->all(),
            'change_requests' => GradeChangeRequest::query()->where('requested_by', $user->id)->latest('id')->limit(5)
                ->with('enrollment.student:id,student_code', 'enrollment.section:id,code')->get()
                ->map(fn (GradeChangeRequest $r) => [
                    'section' => $r->enrollment?->section?->code,
                    'student' => $r->enrollment?->student?->student_code,
                    'status' => match ($r->status) {
                        'pending_faculty' => 'Chờ khoa duyệt',
                        'pending_confirmation' => 'Chờ Phòng Đào tạo',
                        'approved' => 'Đã áp dụng',
                        'rejected' => 'Bị từ chối',
                        default => $r->status,
                    },
                ])->all(),
        ];
    }

    /** @return list<array{name: string, required: int, earned: int, pct: float}> */
    private function blocks(int $curriculumId, array $passed): array
    {
        $credits = DB::table('curriculum_subjects')->join('subjects', 'subjects.id', '=', 'curriculum_subjects.subject_id')
            ->where('curriculum_subjects.curriculum_id', $curriculumId)->whereNull('curriculum_subjects.deleted_at')
            ->whereIn('curriculum_subjects.subject_id', $passed ?: [0])
            ->groupBy('curriculum_subjects.curriculum_block_id')
            ->selectRaw('curriculum_subjects.curriculum_block_id as block_id, sum(subjects.credits) as n')
            ->pluck('n', 'block_id');

        return DB::table('curriculum_blocks')->where('curriculum_id', $curriculumId)->whereNull('deleted_at')->orderBy('sort_order')->get()
            ->map(fn ($b) => [
                'name' => $b->name,
                'required' => (int) $b->required_credits,
                'earned' => min((int) $b->required_credits, (int) ($credits[$b->id] ?? 0)),
                'pct' => $b->required_credits > 0 ? round(min(100, (int) ($credits[$b->id] ?? 0) * 100 / $b->required_credits), 1) : 0,
            ])->all();
    }

    /** @return list<array<string, mixed>> 4 kết quả mới nhất của lớp đã công bố / khóa */
    private function recentResults(Student $student): array
    {
        return CourseResult::query()
            ->join('enrollments', 'enrollments.id', '=', 'course_results.enrollment_id')
            ->join('section_grade_workflows as w', 'w.course_section_id', '=', 'enrollments.course_section_id')
            ->join('subjects', 'subjects.id', '=', 'enrollments.subject_id')
            ->join('terms', 'terms.id', '=', 'enrollments.term_id')
            ->where('enrollments.student_id', $student->id)
            ->whereIn('w.status', [GradePublicationStatus::Published->value, GradePublicationStatus::Locked->value])
            ->orderByDesc('w.published_at')->orderByDesc('course_results.id')
            ->limit(4)
            ->get(['subjects.name as subject', 'subjects.credits', 'terms.name as term', 'course_results.score10', 'course_results.letter', 'course_results.is_passed', 'course_results.special_status'])
            ->map(fn ($r) => [
                'subject' => $r->subject,
                'credits' => (int) $r->credits,
                'term' => $r->term,
                'score' => $r->score10 === null ? null : number_format((float) $r->score10, 1, ',', ''),
                'letter' => $r->letter ?? (config("grade_special_statuses.{$r->special_status}.symbol")),
                'passed' => $r->is_passed,
            ])->all();
    }

    /** Điểm trung bình tích lũy hệ 10: trung bình theo tín chỉ của điểm hệ 10 các học phần tính GPA đã công bố / khóa. */
    private function gpa10(Student $student): ?float
    {
        $row = CourseResult::query()
            ->join('enrollments', 'enrollments.id', '=', 'course_results.enrollment_id')
            ->join('section_grade_workflows as w', 'w.course_section_id', '=', 'enrollments.course_section_id')
            ->join('subjects', 'subjects.id', '=', 'enrollments.subject_id')
            ->where('enrollments.student_id', $student->id)->where('subjects.counts_toward_gpa', true)
            ->whereNotNull('course_results.score10')
            ->whereIn('w.status', [GradePublicationStatus::Published->value, GradePublicationStatus::Locked->value])
            ->selectRaw('sum(course_results.score10 * subjects.credits) as total, sum(subjects.credits) as credits')
            ->toBase()->first();

        return $row && $row->credits > 0 ? round($row->total / $row->credits, 2) : null;
    }

    /** Học kỳ đang diễn ra theo ngày; giữa hai học kỳ thì học kỳ gần nhất đã bắt đầu. */
    private function currentTerm(): ?Term
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return (Term::query()->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->orderBy('start_date')->first()
            ?? Term::query()->whereDate('start_date', '<=', $today)->orderByDesc('start_date')->first())?->loadMissing('academicYear:id,name');
    }
}
