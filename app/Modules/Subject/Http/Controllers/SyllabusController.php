<?php

namespace App\Modules\Subject\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Subject\Enums\SyllabusStatus;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\Syllabus;
use App\Modules\Subject\Services\SyllabusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * API đề cương học phần (FR-SUB-006; BR-SUB-08). Quyền theo học phần của đề cương:
 * xem = SUB.view (người không sửa được học phần chỉ thấy bản đã duyệt); soạn, sửa, đính tệp, gửi duyệt = SUB.update
 * (LEC: học phần mình phụ trách; DEAN: bộ môn mình; ACAD); duyệt / trả lại = SUB.approve (DEAN, ACAD), không tự duyệt.
 */
class SyllabusController extends Controller
{
    public function __construct(private readonly SyllabusService $syllabi) {}

    public function index(Subject $subject): JsonResponse
    {
        Gate::authorize('view', $subject);
        $editor = Gate::allows('update', $subject);

        $list = Syllabus::query()->where('subject_id', $subject->id)
            ->when(! $editor, fn ($q) => $q->whereIn('status', [SyllabusStatus::Approved->value, SyllabusStatus::Superseded->value]))
            ->with(['author:id,name', 'decider:id,name'])
            ->orderByDesc('version')
            ->get();

        return response()->json([
            'subject' => $subject->only('id', 'code', 'name'),
            'can_edit' => $editor,
            'applicable_version' => $this->syllabi->applicableFor($subject)?->version,
            'versions' => $list->map(fn (Syllabus $s) => $this->present($s, full: false)),
        ]);
    }

    public function show(Syllabus $syllabus): JsonResponse
    {
        $this->authorizeView($syllabus);

        return response()->json($this->present($syllabus->load(['subject', 'author', 'decider']), full: true));
    }

    public function store(Subject $subject): JsonResponse
    {
        Gate::authorize('update', $subject);

        return response()->json($this->present($this->syllabi->createVersion($subject), full: true), 201);
    }

    public function update(Request $request, Syllabus $syllabus): JsonResponse
    {
        Gate::authorize('update', $syllabus->subject);

        $data = $request->validate([
            'objectives' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'learning_outcomes' => ['sometimes', 'nullable', 'array', 'max:50'],
            'learning_outcomes.*.code' => ['required', 'string', 'max:20'],
            'learning_outcomes.*.description' => ['required', 'string', 'max:1000'],
            'content' => ['sometimes', 'nullable', 'array', 'max:60'],
            'content.*.unit' => ['required', 'string', 'max:50'],
            'content.*.topic' => ['required', 'string', 'max:1000'],
            'content.*.hours' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'teaching_methods' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'assessment' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'materials' => ['sometimes', 'nullable', 'array', 'max:50'],
            'materials.*.title' => ['required', 'string', 'max:500'],
            'materials.*.kind' => ['required', 'in:main,reference'],
        ], [], [
            'objectives' => 'mục tiêu', 'learning_outcomes' => 'chuẩn đầu ra', 'content' => 'nội dung',
            'teaching_methods' => 'phương pháp giảng dạy', 'assessment' => 'đánh giá', 'materials' => 'tài liệu',
        ]);

        return response()->json($this->present($this->syllabi->update($syllabus, $data), full: true));
    }

    public function uploadFile(Request $request, Syllabus $syllabus): JsonResponse
    {
        Gate::authorize('update', $syllabus->subject);

        $request->validate(
            ['file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:'.(int) config('studentmanager.subject.syllabus_max_kb')]],
            ['file.mimes' => 'Chỉ nhận tệp PDF.', 'file.mimetypes' => 'Chỉ nhận tệp PDF.'],
            ['file' => 'tệp đề cương'],
        );

        return response()->json($this->present($this->syllabi->attachFile($syllabus, $request->file('file')), full: true));
    }

    public function downloadFile(Syllabus $syllabus): StreamedResponse
    {
        $this->authorizeView($syllabus);
        abort_if($syllabus->file_path === null, 404, 'Đề cương chưa có tệp đính kèm.');

        return Storage::disk(config('studentmanager.subject.syllabus_disk'))
            ->download($syllabus->file_path, $syllabus->file_name ?? "de-cuong-v{$syllabus->version}.pdf");
    }

    public function submit(Syllabus $syllabus): JsonResponse
    {
        Gate::authorize('update', $syllabus->subject);

        return response()->json($this->present($this->syllabi->submit($syllabus), full: false));
    }

    public function approve(Request $request, Syllabus $syllabus): JsonResponse
    {
        Gate::authorize('approve', $syllabus->subject);
        $note = $request->validate(['note' => ['nullable', 'string', 'max:500']])['note'] ?? null;

        return response()->json($this->present($this->syllabi->approve($syllabus, $request->user(), $note), full: false));
    }

    public function reject(Request $request, Syllabus $syllabus): JsonResponse
    {
        Gate::authorize('approve', $syllabus->subject);
        $note = $request->validate(['note' => ['required', 'string', 'max:500']], [], ['note' => 'lý do trả lại'])['note'];

        return response()->json($this->present($this->syllabi->reject($syllabus, $request->user(), $note), full: false));
    }

    public function destroy(Syllabus $syllabus): Response
    {
        Gate::authorize('update', $syllabus->subject);
        $this->syllabi->delete($syllabus);

        return response()->noContent();
    }

    // ---------------------------------------------------------------------

    /** Người không sửa được học phần chỉ xem bản đã duyệt / đã thay thế. */
    private function authorizeView(Syllabus $syllabus): void
    {
        Gate::authorize('view', $syllabus->subject);

        $published = in_array($syllabus->status, [SyllabusStatus::Approved, SyllabusStatus::Superseded], true);
        abort_unless($published || Gate::allows('update', $syllabus->subject), 404);
    }

    /** @return array<string, mixed> */
    private function present(Syllabus $syllabus, bool $full): array
    {
        $data = [
            'id' => $syllabus->id,
            'subject_id' => $syllabus->subject_id,
            'version' => $syllabus->version,
            'status' => $syllabus->status->value,
            'status_label' => $syllabus->status->label(),
            'has_file' => $syllabus->file_path !== null,
            'file_name' => $syllabus->file_name,
            'author' => $syllabus->author?->only('id', 'name'),
            'submitted_at' => $syllabus->submitted_at?->toIso8601String(),
            'decided_by' => $syllabus->decider?->only('id', 'name'),
            'decided_at' => $syllabus->decided_at?->toIso8601String(),
            'decision_note' => $syllabus->decision_note,
        ];

        return ! $full ? $data : $data + $syllabus->only(['objectives', 'learning_outcomes', 'content', 'teaching_methods', 'assessment', 'materials']);
    }
}
