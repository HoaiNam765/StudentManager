<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Services\EnrollmentRules;
use App\Modules\Enrollment\Services\EnrollmentService;
use App\Modules\Enrollment\Services\RegistrationAccess;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Models\ScheduleRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * API đăng ký học phần của sinh viên (FR-ENR-003, 005..007) và danh sách sinh viên của lớp (cán bộ, theo phạm vi).
 * Sinh viên chỉ thao tác trên đăng ký của chính mình; thời điểm lấy theo máy chủ (BR-ENR-11).
 */
class EnrollmentController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
        private readonly RegistrationAccess $access,
        private readonly AccessControl $acl,
    ) {}

    /** Đăng ký của tôi (mặc định các học kỳ có đăng ký còn hiệu lực; `term_id` để lọc). */
    public function mine(Request $request): JsonResponse
    {
        $student = $this->access->studentOf($request->user());
        $list = Enrollment::query()
            ->with(['section:id,code,status,subject_id,term_id', 'subject:id,code,name,credits', 'term:id,name'])
            ->where('student_id', $student->id)
            ->when($request->filled('term_id'), fn ($q) => $q->where('term_id', $request->integer('term_id')))
            ->orderByDesc('registered_at')
            ->get();

        return response()->json([
            'student' => $student->only('id', 'student_code', 'full_name'),
            'active_credits' => $list->filter(fn (Enrollment $e) => $e->status->isActive())->sum(fn (Enrollment $e) => (int) $e->subject->credits),
            'data' => $list->map(fn (Enrollment $e) => $this->present($e))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'course_section_id' => ['required', 'integer', 'exists:course_sections,id'],
            'type' => ['sometimes', Rule::in([EnrollmentRules::TYPE_NORMAL, EnrollmentRules::TYPE_RETAKE])],
        ], [], ['course_section_id' => 'lớp học phần', 'type' => 'loại đăng ký']);

        $enrollment = $this->enrollments->register(
            $this->access->studentOf($request->user()),
            CourseSection::query()->findOrFail($data['course_section_id']),
            type: $data['type'] ?? EnrollmentRules::TYPE_NORMAL,
            actor: $request->user(),
            ip: $request->ip(),
        );

        return response()->json($this->present($enrollment->load('section', 'subject', 'term')), 201);
    }

    public function cancel(Request $request, Enrollment $enrollment): JsonResponse
    {
        $this->assertOwn($request, $enrollment);

        return response()->json($this->present($this->enrollments->cancel($enrollment, $request->user(), $request->ip())->load('section', 'subject', 'term')));
    }

    public function withdraw(Request $request, Enrollment $enrollment): JsonResponse
    {
        $this->assertOwn($request, $enrollment);

        return response()->json($this->present($this->enrollments->withdraw($enrollment, $request->user(), $request->ip())->load('section', 'subject', 'term')));
    }

    public function change(Request $request, Enrollment $enrollment): JsonResponse
    {
        $this->assertOwn($request, $enrollment);
        $data = $request->validate(['course_section_id' => ['required', 'integer', 'exists:course_sections,id']], [], ['course_section_id' => 'lớp học phần']);
        $new = $this->enrollments->change($enrollment, CourseSection::query()->findOrFail($data['course_section_id']), $request->user(), $request->ip());

        return response()->json($this->present($new->load('section', 'subject', 'term')), 201);
    }

    /** Danh sách sinh viên đăng ký của một lớp học phần (ACAD; DEAN khoa mình; LEC lớp mình dạy). */
    public function roster(Request $request, CourseSection $courseSection): JsonResponse
    {
        abort_unless($this->acl->allowsOn($request->user(), 'ENR', PermissionAction::View, $courseSection), 403, 'Bạn không có quyền thực hiện thao tác này.');

        $list = Enrollment::query()
            ->with('student:id,student_code,full_name,school_email,major_id,cohort,status', 'student.major:id,name')
            ->where('course_section_id', $courseSection->id)
            ->when(! $request->boolean('all'), fn ($q) => $q->active())
            ->get()
            ->sortBy(fn (Enrollment $e) => $e->student->student_code)
            ->values();
        $classes = AdminClassMember::query()->current()->whereIn('student_id', $list->pluck('student_id'))
            ->with('adminClass:id,code')->get()->keyBy('student_id');

        return response()->json([
            'section' => $courseSection->only('id', 'code', 'max_size'),
            'count' => $list->filter(fn (Enrollment $e) => $e->status->isActive())->count(),
            'data' => $list->map(fn (Enrollment $e) => [
                'id' => $e->id,
                'student' => $e->student->only('id', 'student_code', 'full_name', 'school_email', 'cohort') + [
                    'major' => $e->student->major?->name,
                    'admin_class' => $classes->get($e->student_id)?->adminClass?->code,
                ],
                'type' => $e->type,
                'status' => $e->status->value,
                'status_label' => $e->status->label(),
                'source' => $e->source,
                'registered_at' => $e->registered_at->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    private function assertOwn(Request $request, Enrollment $enrollment): void
    {
        abort_unless($enrollment->student_id === $this->access->studentOf($request->user())->id, 404);
    }

    /** @return array<string, mixed> */
    private function present(Enrollment $enrollment): array
    {
        $tz = config('studentmanager.display_timezone');

        return [
            'id' => $enrollment->id,
            'section' => $enrollment->section?->only('id', 'code'),
            'subject' => $enrollment->subject?->only('id', 'code', 'name', 'credits'),
            'term' => $enrollment->term?->name,
            'type' => $enrollment->type,
            'type_label' => Enrollment::TYPES[$enrollment->type] ?? $enrollment->type,
            'status' => $enrollment->status->value,
            'status_label' => $enrollment->status->label(),
            'source' => $enrollment->source,
            'registered_at' => $enrollment->registered_at->setTimezone($tz)->format('Y-m-d H:i:s'),
            'cancelled_at' => $enrollment->cancelled_at?->setTimezone($tz)->format('Y-m-d H:i:s'),
            'withdrawn_at' => $enrollment->withdrawn_at?->setTimezone($tz)->format('Y-m-d H:i:s'),
            'schedule' => ScheduleRule::query()->where('course_section_id', $enrollment->course_section_id)->get()->map(fn (ScheduleRule $r) => $r->describe())->values(),
        ];
    }
}
