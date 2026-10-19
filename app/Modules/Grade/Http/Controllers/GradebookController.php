<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Subject\Enums\ComponentKind;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class GradebookController extends Controller
{
    public function __construct(private readonly GradebookService $gradebook) {}

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

    public function saveDraft(Request $request, CourseSection $courseSection): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:5000'],
            'items.*.enrollment_id' => ['required', 'integer', 'min:1'],
            'items.*.component_id' => ['required', 'integer', 'min:1'],
            'items.*.score' => ['present', 'nullable', 'numeric'],
        ]);
        $count = $this->gradebook->saveDraft($courseSection, $request->user(), $data['items']);

        return response()->json([
            'message' => 'Đã lưu nháp điểm thành phần thành công.',
            'saved_count' => $count,
            'gradebook' => $this->gradebook->gradebook($courseSection, $request->user()),
        ]);
    }

    public function template(Request $request, CourseSection $courseSection): BinaryFileResponse
    {
        $data = $this->gradebook->gradebook($courseSection, $request->user());
        $dir = storage_path('app/tmp');
        File::ensureDirectoryExists($dir);
        $path = tempnam($dir, 'grd_');
        if ($path === false) {
            throw new BusinessRuleException('Không tạo được file mẫu Excel.', 'Kiểm tra quyền ghi thư mục storage/app/tmp.');
        }
        $writer = new Writer;
        try {
            $writer->openToFile($path);
            $writer->addRow($this->row(['enrollment_id', 'student_code', 'component_id', 'component', 'score']));
            foreach ($data['students'] as $student) {
                foreach ($data['components'] as $component) {
                    $score = collect($student['scores'])->firstWhere('component_id', $component['id'])['score'] ?? null;
                    $writer->addRow($this->row([
                        $student['enrollment_id'], $student['student']['student_code'] ?? '',
                        $component['id'], $component['name'], $score === null ? '' : (float) $score,
                    ]));
                }
            }
        } finally {
            $writer->close();
        }

        return response()->download($path, 'mau-nhap-diem-lop-'.$courseSection->id.'.xlsx')
            ->deleteFileAfterSend(true);
    }

    public function import(Request $request, CourseSection $courseSection): JsonResponse
    {
        $this->gradebook->authorizeGrading($courseSection, $request->user());
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx', 'max:'.config('grades.import_max_kb')]]);
        $file = $request->file('file');
        $reader = new Reader;
        $rows = [];
        $line = 0;
        $max = (int) config('grades.import_max_rows');

        try {
            $reader->open($file->getRealPath());
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $line++;
                    $cols = $row->toArray();
                    if ($line === 1) {
                        $head = array_map(fn ($v) => strtolower(trim((string) $v)), array_slice($cols, 0, 5));
                        if (array_slice($head, 0, 5) !== ['enrollment_id', 'student_code', 'component_id', 'component', 'score']) {
                            throw new BusinessRuleException('File không đúng mẫu cột nhập điểm.',
                                'Tải lại mẫu Excel từ chức năng xuất mẫu, không sửa tên hoặc thứ tự cột.');
                        }

                        continue;
                    }
                    if (count(array_filter($cols, fn ($v) => $v !== null && $v !== '')) === 0) {
                        continue;
                    }
                    if (count($rows) >= $max) {
                        throw new BusinessRuleException('File có quá nhiều dòng.',
                            "Giới hạn là {$max} dòng. Chia thành nhiều file rồi nhập lần lượt.");
                    }
                    if (! is_numeric($cols[0] ?? null) || ! is_numeric($cols[2] ?? null)) {
                        throw new BusinessRuleException("Dòng {$line} có ID đăng ký hoặc thành phần không hợp lệ.",
                            'Giữ nguyên hai cột ID được tạo trong file mẫu.');
                    }
                    $rows[] = [
                        'enrollment_id' => (int) $cols[0],
                        'component_id' => (int) $cols[2],
                        'score' => $cols[4] ?? null,
                    ];
                }
                break; // Chỉ đọc sheet đầu tiên.
            }
        } catch (BusinessRuleException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BusinessRuleException('Không đọc được file Excel.',
                'Kiểm tra file .xlsx có mở được bằng Excel và lưu lại theo đúng mẫu.');
        } finally {
            $reader->close();
        }

        if ($line === 0) {
            throw new BusinessRuleException('File Excel trống.', 'Tải file mẫu và điền điểm vào cột score.');
        }
        $count = $this->gradebook->saveDraft($courseSection, $request->user(), $rows);

        return response()->json([
            'message' => 'Đã nhập Excel và lưu nháp điểm thành công.',
            'saved_count' => $count,
            'gradebook' => $this->gradebook->gradebook($courseSection, $request->user()),
        ]);
    }

    private function row(array $values): Row
    {
        return new Row(array_map(fn ($value) => Cell::fromValue($value), $values));
    }
}
