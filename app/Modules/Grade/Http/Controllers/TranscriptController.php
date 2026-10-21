<?php

namespace App\Modules\Grade\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Services\TranscriptService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Student\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class TranscriptController extends Controller
{
    public function __construct(private readonly TranscriptService $transcript, private readonly AccessControl $access) {}

    public function mine(Request $request)
    {
        $student = Student::where('user_id', $request->user()->id)->firstOrFail();
        $data = $request->validate(['term_id' => ['nullable', 'integer', 'exists:terms,id'], 'lang' => ['nullable', 'in:vi,en']]);

        return Pdf::loadView('pdf.grade-transcript', ['report' => $this->transcript->personal($student, $data['term_id'] ?? null), 'lang' => $data['lang'] ?? 'vi'])
            ->download('bang-diem-'.$student->student_code.'.pdf');
    }

    public function gradebook(Request $request, CourseSection $courseSection)
    {
        $user = $request->user();
        $canExport = $this->access->allowsOn($user, 'GRD', PermissionAction::Export, $courseSection)
            || ($user->hasRole('LEC') && $this->access->allowsOn($user, 'GRD', PermissionAction::View, $courseSection))
            || ($user->hasRole('DEAN') && $this->access->allowsOn($user, 'GRD', PermissionAction::View, $courseSection));
        abort_unless($canExport, 403, 'Bạn không có quyền xuất sổ điểm lớp học phần này.');
        if ($user->hasRole('LEC') && ! ($user->hasRole('ACAD') || $user->hasRole('DEAN') || $user->hasRole('EXAM'))) {
            abort_unless(SectionInstructor::where('course_section_id', $courseSection->id)
                ->whereHas('lecturer', fn ($q) => $q->where('user_id', $user->id))->exists(), 403, 'Bạn không được phân công lớp này.');
        }
        $data = $request->validate(['format' => ['required', 'in:pdf,xlsx']]);
        $report = $this->transcript->section($courseSection);
        if ($data['format'] === 'pdf') {
            return Pdf::loadView('pdf.grade-section', ['report' => $report])->download('so-diem-'.$courseSection->code.'.pdf');
        }
        $directory = storage_path('app/tmp');
        if (! is_dir($directory)) {
            mkdir($directory, 0700, true);
        }
        $file = tempnam($directory, 'grade_');
        $writer = new Writer;
        try {
            $writer->openToFile($file);
            $writer->addRow(Row::fromValues(['MSSV', 'Họ tên', 'Điểm 10', 'Điểm chữ', 'Điểm hệ 4', 'Đạt']));
            foreach ($report['rows'] as $entry) {
                $writer->addRow(Row::fromValues([$entry['student_code'], $entry['student_name'], $entry['score_10'], $entry['letter'], $entry['score_4'], $entry['passed'] === null ? '' : ($entry['passed'] ? 'Đạt' : 'Không đạt')]));
            }
        } finally {
            $writer->close();
        }

        return response()->download($file, 'so-diem-'.$courseSection->code.'.xlsx')->deleteFileAfterSend(true);
    }
}
