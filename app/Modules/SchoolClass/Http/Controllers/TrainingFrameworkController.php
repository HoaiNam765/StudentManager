<?php

namespace App\Modules\SchoolClass\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Models\TrainingFrameworkItem;
use App\Modules\SchoolClass\Services\TrainingFrameworkService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * API chương trình khung theo khoa – khóa – học kỳ. Xem: CLS.view (DEAN khoa mình); tạo / sửa / gợi ý / mở lớp: ACAD
 * (CLS toàn trường, middleware ở route); gửi thông báo: NOT.create (phạm vi kiểm tra trong AudienceResolver).
 */
class TrainingFrameworkController extends Controller
{
    public function __construct(
        private readonly TrainingFrameworkService $frameworks,
        private readonly AccessControl $access,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $list = $this->access->constrain(TrainingFramework::query(), $request->user(), 'CLS', PermissionAction::View)
            ->with(['faculty:id,code,name', 'cohort:id,code,start_year', 'term:id,name,academic_year_id,start_date', 'term.academicYear:id,name'])
            ->withCount('items')
            ->when($request->filled('faculty_id'), fn (Builder $q) => $q->where('faculty_id', $request->integer('faculty_id')))
            ->when($request->filled('cohort_id'), fn (Builder $q) => $q->where('cohort_id', $request->integer('cohort_id')))
            ->when($request->filled('term_id'), fn (Builder $q) => $q->where('term_id', $request->integer('term_id')))
            ->orderByDesc('term_id')->orderBy('faculty_id')->orderByDesc('cohort_id')
            ->paginate(50);

        return response()->json($list->through(fn (TrainingFramework $f) => $this->presentSummary($f)));
    }

    public function show(TrainingFramework $trainingFramework): JsonResponse
    {
        Gate::authorize('view', $trainingFramework);

        return response()->json($this->presentDetail($trainingFramework));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'faculty_id' => ['required', 'integer', 'exists:faculties,id'],
            'cohort_id' => ['required', 'integer', 'exists:cohorts,id'],
            'term_id' => ['required', 'integer', 'exists:terms,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [], ['faculty_id' => 'khoa', 'cohort_id' => 'khóa', 'term_id' => 'học kỳ', 'note' => 'ghi chú']);

        return response()->json($this->presentDetail($this->frameworks->create($data)), 201);
    }

    public function update(Request $request, TrainingFramework $trainingFramework): JsonResponse
    {
        $data = $request->validate(['note' => ['nullable', 'string', 'max:1000']], [], ['note' => 'ghi chú']);

        return response()->json($this->presentDetail($this->frameworks->update($trainingFramework, $data)));
    }

    public function destroy(TrainingFramework $trainingFramework): Response
    {
        $this->frameworks->delete($trainingFramework);

        return response()->noContent();
    }

    public function storeItem(Request $request, TrainingFramework $trainingFramework): JsonResponse
    {
        $this->frameworks->addItem($trainingFramework, $this->validateItem($request, true));

        return response()->json($this->presentDetail($trainingFramework->refresh()), 201);
    }

    public function updateItem(Request $request, TrainingFrameworkItem $trainingFrameworkItem): JsonResponse
    {
        $this->frameworks->updateItem($trainingFrameworkItem, $this->validateItem($request, false));

        return response()->json($this->presentDetail($trainingFrameworkItem->framework));
    }

    public function destroyItem(TrainingFrameworkItem $trainingFrameworkItem): JsonResponse
    {
        $framework = $trainingFrameworkItem->framework;
        $this->frameworks->deleteItem($trainingFrameworkItem);

        return response()->json($this->presentDetail($framework));
    }

    public function suggest(TrainingFramework $trainingFramework): JsonResponse
    {
        $result = $this->frameworks->suggestFromCurriculum($trainingFramework);

        return response()->json([
            'planned_term' => $result['planned_term'],
            'students' => $result['students'],
            'added' => count($result['added']),
            'message' => $result['message'],
            'framework' => $this->presentDetail($trainingFramework->refresh()),
        ]);
    }

    public function openSections(Request $request, TrainingFramework $trainingFramework): JsonResponse
    {
        $data = $request->validate([
            'item_ids' => ['sometimes', 'array'],
            'item_ids.*' => ['integer'],
        ], [], ['item_ids' => 'học phần']);

        $result = $this->frameworks->openSections($trainingFramework, $data['item_ids'] ?? null);

        return response()->json([
            'opened' => collect($result['opened'])->map(fn (CourseSection $s) => $s->only('id', 'code'))->values(),
            'errors' => $result['errors'],
            'framework' => $this->presentDetail($trainingFramework->refresh()),
        ]);
    }

    public function announcementDraft(TrainingFramework $trainingFramework): JsonResponse
    {
        Gate::authorize('view', $trainingFramework);

        return response()->json($this->frameworks->draftAnnouncement($trainingFramework));
    }

    public function notify(Request $request, TrainingFramework $trainingFramework): JsonResponse
    {
        Gate::authorize('view', $trainingFramework);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
        ], [], ['title' => 'tiêu đề', 'body' => 'nội dung']);

        $notification = $this->frameworks->notify($request->user(), $trainingFramework, $data['title'], $data['body']);

        return response()->json([
            'notification_id' => $notification->id,
            'recipients' => $notification->recipients()->count(),
            'notified_at' => $trainingFramework->refresh()->notified_at?->setTimezone(config('studentmanager.display_timezone'))->format('d/m/Y H:i'),
        ], 201);
    }

    /** @return array<string, mixed> */
    private function validateItem(Request $request, bool $creating): array
    {
        return $request->validate([
            'subject_id' => [$creating ? 'required' : 'prohibited', 'integer', 'exists:subjects,id'],
            'compulsory' => ['sometimes', 'boolean'],
            'planned_sections' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'max_size' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'note' => ['sometimes', 'nullable', 'string', 'max:500'],
        ], [
            'subject_id.prohibited' => 'Không đổi học phần của dòng đã có; xóa dòng và thêm học phần khác.',
        ], ['subject_id' => 'học phần', 'compulsory' => 'bắt buộc', 'planned_sections' => 'số lớp dự kiến', 'max_size' => 'sĩ số tối đa', 'note' => 'ghi chú']);
    }

    /** @return array<string, mixed> */
    private function presentSummary(TrainingFramework $framework): array
    {
        $tz = config('studentmanager.display_timezone');

        return [
            'id' => $framework->id,
            'faculty' => $framework->faculty?->only('id', 'code', 'name'),
            'cohort' => $framework->cohort?->only('id', 'code', 'start_year'),
            'term' => $framework->term === null ? null : [
                'id' => $framework->term->id,
                'name' => $framework->term->name,
                'academic_year' => $framework->term->academicYear?->name,
                'start_date' => $framework->term->start_date->toDateString(),
            ],
            'items_count' => $framework->items_count ?? $framework->items()->count(),
            'note' => $framework->note,
            'notified_at' => $framework->notified_at?->setTimezone($tz)->format('d/m/Y H:i'),
        ];
    }

    /** @return array<string, mixed> */
    private function presentDetail(TrainingFramework $framework): array
    {
        $framework->load(['faculty', 'cohort', 'term.academicYear', 'items.subject']);
        $sections = CourseSection::query()
            ->whereIn('training_framework_item_id', $framework->items->pluck('id'))
            ->orderBy('code')->get(['id', 'code', 'status', 'training_framework_item_id', 'max_size'])
            ->groupBy('training_framework_item_id');

        return $this->presentSummary($framework) + [
            'planned_term' => $this->frameworks->cohortTermNumber($framework->cohort, $framework->term),
            'students' => $this->frameworks->studentCount($framework),
            'items' => $framework->items->sortBy(fn (TrainingFrameworkItem $i) => [! $i->compulsory, $i->subject->code])->values()
                ->map(function (TrainingFrameworkItem $item) use ($sections): array {
                    $own = $sections->get($item->id) ?? collect();
                    $active = $own->reject(fn (CourseSection $s) => $s->status === SectionStatus::Cancelled);

                    return [
                        'id' => $item->id,
                        'subject' => $item->subject->only('id', 'code', 'name', 'credits'),
                        'compulsory' => $item->compulsory,
                        'planned_sections' => $item->planned_sections,
                        'opened_sections' => $active->count(),
                        'max_size' => $item->max_size,
                        'default_max_size' => (int) config('studentmanager.course_section.default_max_size', 60),
                        'note' => $item->note,
                        'sections' => $own->map(fn (CourseSection $s) => ['id' => $s->id, 'code' => $s->code, 'status' => $s->status->value, 'status_label' => $s->status->label()])->values(),
                    ];
                })->all(),
        ];
    }
}
