<?php

namespace App\Modules\SchoolClass\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\Teacher\Models\Lecturer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * API lớp học phần (FR-CLS-009..012). Mở lớp, sửa, xóa, đổi trạng thái: ACAD (CLS toàn trường). Phân công giảng viên:
 * ACAD (CLS.update) hoặc DEAN trong đơn vị mình (CLS.approve, FR-CLS-011). Xem theo phạm vi (DEAN đơn vị, LEC lớp mình).
 */
class CourseSectionController extends Controller
{
    public function __construct(
        private readonly CourseSectionService $sections,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $list = $this->access->constrain(CourseSection::query(), $request->user(), 'CLS', PermissionAction::View)
            ->with(['subject:id,code,name,credits,department_id', 'term:id,name,academic_year_id', 'term.academicYear:id,name', 'instructors.lecturer:id,code,full_name', 'room:id,code'])
            ->when($request->filled('q'), fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->search($request->string('q')->toString())
                ->orWhereHas('subject', fn (Builder $s) => $s->search($request->string('q')->toString()))))
            ->when($request->filled('term_id'), fn (Builder $q) => $q->where('term_id', $request->integer('term_id')))
            ->when($request->filled('subject_id'), fn (Builder $q) => $q->where('subject_id', $request->integer('subject_id')))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('lecturer_id'), fn (Builder $q) => $q->whereHas('instructors', fn (Builder $i) => $i->where('lecturer_id', $request->integer('lecturer_id'))))
            ->when($request->boolean('without_main_instructor'), fn (Builder $q) => $q->whereDoesntHave('instructors', fn (Builder $i) => $i->where('role', SectionInstructor::MAIN)))
            ->orderBy('code')
            ->paginate(50);

        return response()->json($list->through(fn (CourseSection $s) => $this->present($s)));
    }

    public function show(CourseSection $courseSection): JsonResponse
    {
        Gate::authorize('view', $courseSection);

        return response()->json($this->present($courseSection->load(['subject', 'term.academicYear', 'instructors.lecturer', 'room'])));
    }

    public function store(Request $request): JsonResponse
    {
        $section = $this->sections->open($this->validateSection($request, true));

        return response()->json($this->present($section->load(['subject', 'term.academicYear', 'instructors.lecturer', 'room'])), 201);
    }

    public function update(Request $request, CourseSection $courseSection): JsonResponse
    {
        $section = $this->sections->update($courseSection, $this->validateSection($request, false));

        return response()->json($this->present($section->load(['subject', 'term.academicYear', 'instructors.lecturer', 'room'])));
    }

    public function destroy(CourseSection $courseSection): Response
    {
        $this->sections->delete($courseSection);

        return response()->noContent();
    }

    public function transition(Request $request, CourseSection $courseSection, string $status): JsonResponse
    {
        $target = SectionStatus::tryFrom($status) ?? abort(404);

        if ($target === SectionStatus::Cancelled) {
            $reason = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'lý do hủy lớp'])['reason'];
            $section = $this->sections->cancel($courseSection, $reason);
        } else {
            $section = $this->sections->advance($courseSection, $target);
        }

        return response()->json($this->present($section->load(['subject', 'term.academicYear', 'instructors.lecturer', 'room'])));
    }

    /** Mở khóa lớp đã khóa theo quy trình ngoại lệ (FR-CLS-018): CLS.approve toàn trường, bắt buộc lý do. */
    public function unlock(Request $request, CourseSection $courseSection): JsonResponse
    {
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']], [], ['reason' => 'lý do mở khóa'])['reason'];

        return response()->json($this->present($this->sections->unlock($courseSection, $reason)->load(['subject', 'term.academicYear', 'instructors.lecturer', 'room'])));
    }

    // ---------------------------------------------------------------------
    // Phân công giảng viên (FR-CLS-011)
    // ---------------------------------------------------------------------

    public function assign(Request $request, CourseSection $courseSection): JsonResponse
    {
        $this->authorizeStaffing($courseSection);
        $data = $request->validate([
            'lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
            'role' => ['sometimes', Rule::in([SectionInstructor::MAIN, SectionInstructor::ASSISTANT])],
            'confirm_overload' => ['sometimes', 'boolean'],
        ], [], ['lecturer_id' => 'giảng viên', 'role' => 'vai trò', 'confirm_overload' => 'xác nhận vượt định mức']);

        $result = $this->sections->assign(
            $courseSection,
            Lecturer::query()->findOrFail($data['lecturer_id']),
            $data['role'] ?? SectionInstructor::MAIN,
            (bool) ($data['confirm_overload'] ?? false),
        );

        return response()->json($this->presentInstructor($result['instructor']) + ['warnings' => $result['warnings']], 201);
    }

    public function unassign(SectionInstructor $sectionInstructor): Response
    {
        $this->authorizeStaffing($sectionInstructor->section);
        $this->sections->unassign($sectionInstructor);

        return response()->noContent();
    }

    // ---------------------------------------------------------------------

    /** ACAD sửa lớp, hoặc DEAN duyệt phân công trong đơn vị mình. */
    private function authorizeStaffing(CourseSection $section): void
    {
        abort_unless(
            Gate::any(['update', 'approve'], $section),
            403,
            'Bạn không có quyền phân công giảng viên cho lớp này.'
        );
    }

    /** @return array<string, mixed> */
    private function validateSection(Request $request, bool $creating): array
    {
        return $request->validate([
            'subject_id' => [$creating ? 'required' : 'prohibited', 'integer', 'exists:subjects,id'],
            'term_id' => [$creating ? 'required' : 'prohibited', 'integer', 'exists:terms,id'],
            'section_type' => ['sometimes', Rule::in(['theory', 'practice', 'mixed'])],
            'delivery_mode' => ['sometimes', Rule::in(['in_person', 'online', 'hybrid'])],
            'language' => ['sometimes', Rule::in(['vi', 'en', 'vi_en'])],
            'min_size' => ['sometimes', 'integer', 'min:1'],
            'max_size' => ['sometimes', 'integer', 'min:1'],
            'room_id' => ['sometimes', 'nullable', 'integer', 'exists:rooms,id'],
            'note' => ['sometimes', 'nullable', 'string', 'max:500'],
            'code' => ['prohibited'],
            'status' => ['prohibited'],
        ], [
            'code.prohibited' => 'Mã lớp học phần do hệ thống sinh tự động (FR-CLS-010).',
            'status.prohibited' => 'Đổi trạng thái lớp bằng thao tác chuyển trạng thái (FR-CLS-012).',
            'subject_id.prohibited' => 'Không đổi học phần của lớp đã mở; hủy lớp và mở lớp mới.',
            'term_id.prohibited' => 'Không đổi học kỳ của lớp đã mở; hủy lớp và mở lớp mới.',
        ], [
            'subject_id' => 'học phần',
            'term_id' => 'học kỳ',
            'section_type' => 'loại lớp',
            'delivery_mode' => 'hình thức',
            'language' => 'ngôn ngữ giảng dạy',
            'min_size' => 'sĩ số tối thiểu',
            'max_size' => 'sĩ số tối đa',
            'room_id' => 'phòng',
            'note' => 'ghi chú',
        ]);
    }

    /** @return array<string, mixed> */
    private function present(CourseSection $section): array
    {
        return [
            'id' => $section->id,
            'code' => $section->code,
            'subject' => $section->subject?->only('id', 'code', 'name', 'credits'),
            'term' => $section->term === null ? null : ['id' => $section->term->id, 'name' => $section->term->name, 'academic_year' => $section->term->academicYear?->name],
            'section_type' => $section->section_type,
            'delivery_mode' => $section->delivery_mode,
            'language' => $section->language,
            'min_size' => $section->min_size,
            'max_size' => $section->max_size,
            'room' => $section->room?->only('id', 'code'),
            'status' => $section->status->value,
            'status_label' => $section->status_label,
            'cancel_reason' => $section->cancel_reason,
            'instructors' => $section->instructors->map(fn (SectionInstructor $i) => $this->presentInstructor($i))->values(),
            'has_main_instructor' => $section->instructors->contains('role', SectionInstructor::MAIN),
            'note' => $section->note,
        ];
    }

    /** @return array<string, mixed> */
    private function presentInstructor(SectionInstructor $instructor): array
    {
        return [
            'id' => $instructor->id,
            'lecturer' => $instructor->lecturer?->only('id', 'code', 'full_name'),
            'role' => $instructor->role,
            'role_label' => $instructor->role_label,
        ];
    }
}
