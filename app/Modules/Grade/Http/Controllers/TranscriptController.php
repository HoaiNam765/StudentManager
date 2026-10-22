<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\Grade\Services\TranscriptService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\Response;

/** Bảng điểm cá nhân (FR-GRD-010) và sổ điểm lớp học phần (FR-GRD-011): xem JSON, tải PDF / Excel. */
class TranscriptController extends Controller
{
    public function __construct(
        private readonly TranscriptService $transcript,
        private readonly AccessControl $access,
        private readonly GradebookService $gradebook,
    ) {}

    /** Sinh viên: bảng điểm của mình (`format=pdf` để tải, `lang=en` bản tiếng Anh). */
    public function mine(Request $request): JsonResponse|Response
    {
        $student = Student::query()->where('user_id', $request->user()->id)->firstOrFail();

        return $this->personal($request, $student);
    }

    /** Sinh viên: tải bảng điểm PDF (liên kết "Tải bảng điểm" của giao diện FE student/grade/grades). */
    public function minePdf(Request $request): Response
    {
        $request->merge(['format' => 'pdf']);

        return $this->mine($request);
    }

    /** Cán bộ có GRD.view: bảng điểm của một sinh viên trong phạm vi dữ liệu sinh viên của mình (STU.view). */
    public function student(Request $request, Student $student): JsonResponse|Response
    {
        abort_unless($this->access->allowsOn($request->user(), 'STU', PermissionAction::View, $student), 403,
            'Sinh viên này ngoài phạm vi dữ liệu của bạn.');

        return $this->personal($request, $student);
    }

    /** Sổ điểm lớp: giảng viên được phân công, lãnh đạo khoa / Phòng Đào tạo trong phạm vi GRD.view. */
    public function gradebook(Request $request, CourseSection $courseSection): JsonResponse|Response
    {
        $this->gradebook->authorizeView($courseSection, $request->user());
        $data = $request->validate(['format' => ['nullable', 'in:json,pdf,xlsx']]);
        $format = $data['format'] ?? 'json';

        if ($format === 'json') {
            return response()->json($this->transcript->section($courseSection));
        }

        if ($format === 'pdf') {
            return response($this->transcript->sectionPdf($courseSection), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="so-diem-'.$courseSection->code.'.pdf"',
            ]);
        }

        return $this->sectionXlsx($courseSection);
    }

    private function personal(Request $request, Student $student): JsonResponse|Response
    {
        $data = $request->validate([
            'term_id' => ['nullable', 'integer', 'exists:terms,id'],
            'format' => ['nullable', 'in:json,pdf'],
            'lang' => ['nullable', 'in:vi,en'],
        ]);
        $termId = isset($data['term_id']) ? (int) $data['term_id'] : null;

        if (($data['format'] ?? 'json') === 'json') {
            return response()->json($this->transcript->personal($student, $termId));
        }

        return response($this->transcript->personalPdf($student, $termId, $data['lang'] ?? 'vi'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="bang-diem-'.$student->student_code.'.pdf"',
        ]);
    }

    private function sectionXlsx(CourseSection $section): Response
    {
        $report = $this->transcript->section($section);
        $directory = storage_path('app/tmp');

        if (! is_dir($directory)) {
            mkdir($directory, 0700, true);
        }

        $file = tempnam($directory, 'grade_');
        $writer = new Writer;

        try {
            $writer->openToFile($file);
            $writer->addRow(Row::fromValues(array_merge(
                ['MSSV', 'Họ tên'],
                array_map(fn (array $c) => $c['name'].' ('.$c['weight'].'%)', $report['components']),
                ['Hệ 10', 'Chữ', 'Hệ 4', 'Kết quả'],
            )));

            foreach ($report['rows'] as $row) {
                $writer->addRow(Row::fromValues(array_merge(
                    [$row['student_code'], $row['student_name']],
                    array_map(fn ($s) => $s === null ? null : (float) $s, $row['scores']),
                    [
                        $row['score10'] === null ? null : (float) $row['score10'],
                        $row['letter'],
                        $row['point4'] === null ? null : (float) $row['point4'],
                        $row['special_label'] ?? ($row['is_passed'] === null ? 'Chưa đủ dữ liệu' : ($row['is_passed'] ? 'Đạt' : 'Không đạt')),
                    ],
                )));
            }
        } finally {
            $writer->close();
        }

        return response()->download($file, 'so-diem-'.$section->code.'.xlsx')->deleteFileAfterSend(true);
    }
}
