<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Services\SectionMergeService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Support\Exports\ExportColumn;
use App\Support\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Xử lý lớp sau đăng ký (FR-CLS-014; BR-CLS-05, 09): lớp thiếu sĩ số, gộp lớp, xuất danh sách sinh viên của lớp
 * (cho điểm danh, nhập điểm, danh sách thi).
 */
class SectionEnrollmentController extends Controller
{
    public function underMin(Term $term, CourseSectionService $sections): JsonResponse
    {
        $rows = $sections->underMinSections($term);

        return response()->json(['term' => $term->only('id', 'name'), 'count' => count($rows), 'data' => $rows]);
    }

    public function merge(Request $request, CourseSection $courseSection, SectionMergeService $merger): JsonResponse
    {
        $data = $request->validate([
            'target_id' => ['required', 'integer', 'exists:course_sections,id'],
            'reason' => ['required', 'string', 'max:500'],
        ], [], ['target_id' => 'lớp đích', 'reason' => 'lý do']);

        return response()->json($merger->merge($courseSection, CourseSection::query()->findOrFail($data['target_id']), $data['reason'], $request->user()));
    }

    /** Xuất danh sách sinh viên đang đăng ký của lớp (Excel / CSV / PDF), lọc theo quyền xuất ENR (FR-CLS-014). */
    public function export(Request $request, CourseSection $courseSection, ExportService $exports): Response|JsonResponse
    {
        $query = Enrollment::query()
            ->with('student:id,student_code,full_name,school_email,date_of_birth')
            ->where('course_section_id', $courseSection->id)
            ->active()
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->orderBy('students.student_code')
            ->select('enrollments.*');

        $result = $exports->export($query, [
            new ExportColumn('student.student_code', 'MSSV'),
            new ExportColumn('student.full_name', 'Họ và tên'),
            new ExportColumn('student.date_of_birth', 'Ngày sinh'),
            new ExportColumn('student.school_email', 'Email trường'),
            new ExportColumn('type_label', 'Loại đăng ký'),
        ], 'ENR', $request->user(), $request->string('format', 'xlsx')->toString(), "danh-sach-lop-{$courseSection->code}");

        return $result->isQueued()
            ? response()->json(['id' => $result->requestId, 'row_count' => $result->rowCount], 202)
            : $result->download;
    }
}
