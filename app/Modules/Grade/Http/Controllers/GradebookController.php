<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\Grade\Services\GradebookSpreadsheet;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Subject\Enums\ComponentKind;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GradebookController extends Controller
{
    public function __construct(
        private readonly GradebookService $gradebook,
        private readonly GradebookSpreadsheet $spreadsheet,
    ) {}

    public function show(Request $request, CourseSection $courseSection): JsonResponse
    {
        return response()->json($this->gradebook->gradebook($courseSection, $request->user()));
    }

    public function initialize(Request $request, CourseSection $courseSection): JsonResponse
    {
        $components = $this->gradebook->initialize($courseSection, $request->user());

        return response()->json([
            'message' => 'Đã khởi tạo hoặc lấy lại cơ cấu điểm lớp học phần.',
            'components' => $components,
        ]);
    }

    public function replaceStructure(Request $request, CourseSection $courseSection): JsonResponse
    {
        $data = $request->validate([
            'components' => ['required', 'array', 'max:20'],
            'components.*.name' => ['required', 'string', 'max:100'],
            'components.*.kind' => ['required', Rule::enum(ComponentKind::class)],
            'components.*.weight' => ['required', 'numeric', 'gt:0', 'max:100'],
            'components.*.min_score' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'components.*.is_required' => ['sometimes', 'boolean'],
            'components.*.is_remote' => ['sometimes', 'boolean'],
        ]);

        $components = $this->gradebook->replaceStructure($courseSection, $request->user(), $data['components']);

        return response()->json(['message' => 'Đã cập nhật cơ cấu điểm của lớp.', 'components' => $components]);
    }

    public function saveDraft(Request $request, CourseSection $courseSection): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:5000'],
            'items.*.enrollment_id' => ['required', 'integer', 'min:1'],
            'items.*.component_id' => ['required', 'integer', 'min:1'],
            'items.*.score' => ['present', 'nullable', 'numeric'],
        ]);

        return $this->respond($request, $courseSection, fn () => $this->gradebook->saveDraft($courseSection, $request->user(), $data['items']),
            'Đã lưu nháp điểm thành phần thành công.');
    }

    /**
     * API JSON trả kết quả; form HTML của giao diện FE (teacher/grade/gradebook) được chuyển về trang kèm thông báo
     * `success` / `error` trong session.
     */
    private function respond(Request $request, CourseSection $courseSection, callable $save, string $message): JsonResponse|RedirectResponse
    {
        try {
            $count = $save();
        } catch (BusinessRuleException $e) {
            if ($request->expectsJson()) {
                throw $e;
            }

            return back()->with('error', $e->getMessage());
        }

        if (! $request->expectsJson()) {
            return back()->with('success', "{$message} ({$count} điểm).");
        }

        return response()->json([
            'message' => $message,
            'saved_count' => $count,
            'gradebook' => $this->gradebook->gradebook($courseSection, $request->user()),
        ]);
    }

    /** Sổ điểm Excel: danh sách sinh viên, điểm đã có và cột kết quả tính bằng công thức (xem GradebookSpreadsheet). */
    public function template(Request $request, CourseSection $courseSection): BinaryFileResponse
    {
        $path = $this->spreadsheet->write($courseSection, $request->user());

        return response()->download($path, 'so-diem-'.$courseSection->code.'.xlsx')->deleteFileAfterSend(true);
    }

    /** Nhập lại sổ điểm Excel đã điền: lưu nháp mọi ô điểm thành phần (ô trống = chưa có điểm). */
    public function import(Request $request, CourseSection $courseSection): JsonResponse|RedirectResponse
    {
        $this->gradebook->authorizeGrading($courseSection, $request->user());
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx', 'max:'.config('grades.import_max_kb')]]);

        return $this->respond($request, $courseSection, fn () => $this->gradebook->saveDraft($courseSection, $request->user(),
            $this->spreadsheet->read($courseSection, $request->user(), $request->file('file')->getRealPath())),
            'Đã nhập Excel và lưu nháp điểm thành công.');
    }
}
