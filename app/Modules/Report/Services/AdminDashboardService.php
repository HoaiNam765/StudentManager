<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Services\AcademicYearService;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Grade\Enums\GradePublicationStatus;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Services\GradeChangeService;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard quản trị theo vai trò (FR-RPT-001, 002, 008; BR-RPT-03, 07).
 *
 * - Bộ widget theo vai trò ở `studentmanager.dashboard.widgets`; người nhiều vai trò thấy hợp các bộ.
 * - Mọi chỉ số đọc thẳng từ bảng nghiệp vụ (cùng nguồn với danh sách / báo cáo) và lọc theo phạm vi RPT.view của người
 *   xem: lãnh đạo khoa chỉ thấy sinh viên, lớp, lớp học phần của khoa mình (FR-RPT-008).
 * - Mỗi chỉ số có định nghĩa công bố (`definition`); trang tự làm mới sau `studentmanager.dashboard.refresh_seconds`.
 */
class AdminDashboardService
{
    /** Định nghĩa công bố của từng chỉ số (BR-RPT-07). */
    public const WIDGETS = [
        'students_by_status' => ['title' => 'Sinh viên theo trạng thái', 'definition' => 'Số hồ sơ sinh viên (không tính hồ sơ đã xóa) theo trạng thái hiện tại, trong phạm vi của bạn.'],
        'new_admissions' => ['title' => 'Nhập học mới', 'definition' => 'Số sinh viên đã xác nhận nhập học (không tính hồ sơ chờ nhập học) có ngày nhập học từ đầu năm học hiện hành tới hôm nay, theo ngành.'],
        'section_fill' => ['title' => 'Sĩ số lớp học phần', 'definition' => 'Lớp học phần chưa hủy của học kỳ hiện hành: số đăng ký còn hiệu lực (Đã đăng ký / Đã chốt / Hoàn thành) so với sĩ số tối thiểu – tối đa.'],
        'grading_progress' => ['title' => 'Tiến độ nhập điểm', 'definition' => 'Lớp học phần Đang học / Kết thúc / Đã khóa của học kỳ hiện hành theo trạng thái bảng điểm (chưa có bảng điểm = Nháp).'],
        'admin_class_sizes' => ['title' => 'Sĩ số lớp hành chính', 'definition' => 'Lớp hành chính đang hoạt động: số sinh viên đang là thành viên hôm nay so với sĩ số tối đa.'],
        'pending_grade_changes' => ['title' => 'Yêu cầu điều chỉnh điểm chờ duyệt', 'definition' => 'Yêu cầu điều chỉnh điểm sau công bố đang chờ khoa duyệt hoặc chờ Phòng Đào tạo xác nhận, trong phạm vi của bạn.'],
    ];

    public function __construct(
        private readonly AccessControl $access,
        private readonly AcademicYearService $years,
        private readonly GradeChangeService $gradeChanges,
    ) {}

    /** @return array<string, mixed> */
    public function data(User $user): array
    {
        $codes = $user->activeRoles()->pluck('roles.code')->all();
        $widgets = collect(config('studentmanager.dashboard.widgets', []))->only($codes)->flatten()->unique()->values();
        $term = $this->currentTerm();

        return [
            'generated_at' => now()->toIso8601String(),
            'refresh_seconds' => max(30, (int) config('studentmanager.dashboard.refresh_seconds', 300)),
            'scope' => $this->access->allowsGlobally($user, 'RPT', PermissionAction::View) ? 'Toàn trường' : 'Trong phạm vi được phân quyền',
            'term' => $term ? ['id' => $term->id, 'name' => trim($term->name.' – '.($term->academicYear?->name ?? ''), ' –')] : null,
            'widgets' => $widgets->filter(fn ($code) => isset(self::WIDGETS[$code]))
                ->map(fn (string $code) => ['code' => $code] + self::WIDGETS[$code] + ['data' => $this->{lcfirst(str_replace('_', '', ucwords($code, '_')))}($user, $term)])
                ->values(),
        ];
    }

    private function studentsByStatus(User $user, ?Term $term): array
    {
        $counts = $this->students($user)->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

        return [
            'total' => (int) $counts->sum(),
            'items' => collect(StudentStatus::cases())->map(fn (StudentStatus $s) => ['key' => $s->value, 'label' => $s->label(), 'value' => (int) ($counts[$s->value] ?? 0)])->values(),
        ];
    }

    private function newAdmissions(User $user, ?Term $term): array
    {
        $from = $term?->academicYear?->start_date ?? now()->startOfYear();
        $rows = $this->students($user)->where('students.status', '!=', StudentStatus::Pending->value)->whereNotNull('admission_date')->whereBetween('admission_date', [$from->toDateString(), now()->toDateString()])
            ->join('majors', 'majors.id', '=', 'students.major_id')
            ->select('majors.code', 'majors.name', DB::raw('count(*) as total'))->groupBy('majors.code', 'majors.name')->orderBy('majors.code')->get();

        return [
            'total' => (int) $rows->sum('total'),
            'since' => $from->format('d/m/Y'),
            'items' => $rows->map(fn ($r) => ['key' => $r->code, 'label' => "{$r->code} – {$r->name}", 'value' => (int) $r->total])->values(),
        ];
    }

    private function sectionFill(User $user, ?Term $term): array
    {
        if ($term === null) {
            return ['total' => 0, 'under_min' => 0, 'full' => 0, 'items' => []];
        }

        $sections = $this->sections($user, $term)->where('status', '!=', SectionStatus::Cancelled->value)
            ->select(['course_sections.id', 'course_sections.code', 'course_sections.subject_id', 'course_sections.min_size', 'course_sections.max_size', 'course_sections.status'])
            ->selectSub(DB::table('enrollments')->selectRaw('count(*)')->whereColumn('enrollments.course_section_id', 'course_sections.id')
                ->whereIn('enrollments.status', EnrollmentStatus::activeValues())->whereNull('enrollments.deleted_at'), 'enrolled')
            ->with('subject:id,name')->orderBy('code')->get();

        return [
            'total' => $sections->count(),
            'under_min' => $sections->filter(fn ($s) => $s->enrolled < $s->min_size)->count(),
            'full' => $sections->filter(fn ($s) => $s->enrolled >= $s->max_size)->count(),
            'items' => $sections->sortBy(fn ($s) => $s->max_size ? $s->enrolled / $s->max_size : 0)->take(10)
                ->map(fn ($s) => ['key' => $s->code, 'label' => "{$s->code} – {$s->subject?->name}", 'value' => (int) $s->enrolled, 'min' => $s->min_size, 'max' => $s->max_size])->values(),
        ];
    }

    private function gradingProgress(User $user, ?Term $term): array
    {
        if ($term === null) {
            return ['total' => 0, 'published' => 0, 'items' => []];
        }

        $sections = $this->sections($user, $term)
            ->whereIn('course_sections.status', [SectionStatus::InProgress->value, SectionStatus::Finished->value, SectionStatus::Locked->value])
            ->leftJoin('section_grade_workflows as w', 'w.course_section_id', '=', 'course_sections.id')
            ->select(DB::raw("coalesce(w.status, 'draft') as grade_status"), DB::raw('count(*) as total'))
            ->groupBy('grade_status')->pluck('total', 'grade_status');

        $items = collect(GradePublicationStatus::cases())->map(fn (GradePublicationStatus $s) => ['key' => $s->value, 'label' => $s->label(), 'value' => (int) ($sections[$s->value] ?? 0)]);

        return [
            'total' => (int) $sections->sum(),
            'published' => (int) (($sections['published'] ?? 0) + ($sections['locked'] ?? 0)),
            'items' => $items->values(),
        ];
    }

    private function adminClassSizes(User $user, ?Term $term): array
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();
        $classes = $this->access->constrain(AdminClass::query(), $user, 'RPT', PermissionAction::View)->where('status', 'active')
            ->withCount(['members as size' => fn (Builder $q) => $q->where('effective_from', '<=', $today)
                ->where(fn (Builder $w) => $w->whereNull('effective_to')->orWhere('effective_to', '>=', $today))])
            ->orderBy('code')->get(['id', 'code', 'name', 'max_size']);

        return [
            'total' => $classes->count(),
            'students' => (int) $classes->sum('size'),
            'items' => $classes->take(20)->map(fn ($c) => ['key' => $c->code, 'label' => $c->code, 'value' => (int) $c->size, 'max' => $c->max_size])->values(),
        ];
    }

    private function pendingGradeChanges(User $user, ?Term $term): array
    {
        if (! $this->access->allows($user, 'GRD', PermissionAction::Approve)) {
            return ['total' => 0, 'items' => []];
        }

        $rows = GradeChangeRequest::query()->whereIn('status', GradeChangeService::PENDING)->with('enrollment.section')->get()
            ->filter(fn ($r) => $this->gradeChanges->canReview($user, $r));

        return [
            'total' => $rows->count(),
            'items' => [
                ['key' => 'pending_faculty', 'label' => 'Chờ khoa duyệt', 'value' => $rows->where('status', 'pending_faculty')->count()],
                ['key' => 'pending_confirmation', 'label' => 'Chờ Phòng Đào tạo xác nhận', 'value' => $rows->where('status', 'pending_confirmation')->count()],
            ],
        ];
    }

    private function students(User $user): Builder
    {
        return $this->access->constrain(Student::query(), $user, 'RPT', PermissionAction::View);
    }

    private function sections(User $user, Term $term): Builder
    {
        return $this->access->constrain(CourseSection::query(), $user, 'RPT', PermissionAction::View)->where('course_sections.term_id', $term->id);
    }

    /** Học kỳ hiện hành do Phòng Đào tạo đặt; chưa đặt thì học kỳ đang diễn ra theo ngày, rồi học kỳ gần nhất đã bắt đầu. */
    private function currentTerm(): ?Term
    {
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        return ($this->years->getCurrentTerm()
            ?? Term::query()->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->first()
            ?? Term::query()->whereDate('start_date', '<=', $today)->orderByDesc('start_date')->first()
            ?? Term::query()->orderBy('start_date')->first())?->loadMissing('academicYear');
    }
}
