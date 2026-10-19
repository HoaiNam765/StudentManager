<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Services\EnrollmentReport;
use App\Modules\Enrollment\Services\RegistrationAccess;
use App\Modules\Student\Models\Student;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

/**
 * Kết quả đăng ký, thời khóa biểu cá nhân, phiếu PDF (FR-ENR-010) và thống kê (FR-ENR-013).
 * Không gửi `term_id` thì lấy học kỳ của đợt đăng ký mới nhất.
 */
class EnrollmentResultController extends Controller
{
    public function __construct(
        private readonly EnrollmentReport $report,
        private readonly RegistrationAccess $access,
        private readonly AccessControl $acl,
        private readonly AuditLogger $audit,
    ) {}

    public function mine(Request $request): JsonResponse
    {
        return response()->json($this->report->result($this->access->studentOf($request->user()), $this->term($request)));
    }

    public function mineSlip(Request $request): Response
    {
        return $this->slip($this->access->studentOf($request->user()), $this->term($request));
    }

    public function forStudent(Request $request, Student $student): JsonResponse
    {
        Gate::authorize('view', $student);

        return response()->json($this->report->result($student, $this->term($request)));
    }

    public function studentSlip(Request $request, Student $student): Response
    {
        Gate::authorize('view', $student);

        return $this->slip($student, $this->term($request));
    }

    /** Thống kê theo học kỳ; DEAN, LEC chỉ thấy lớp trong phạm vi ENR của mình. */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $scope = $this->acl->allowsGlobally($user, 'ENR', PermissionAction::View)
            ? null
            : fn (Builder $q) => $this->acl->constrain($q, $user, 'ENR', PermissionAction::View);

        $stats = $this->report->stats($this->term($request), $scope);

        // Danh sách sinh viên chưa đăng ký / thiếu tín chỉ là dữ liệu toàn trường: chỉ ENR toàn trường (ACAD…) xem
        if ($scope !== null) {
            unset(
                $stats['students_not_registered'],
                $stats['students_under_min_credits'],
                $stats['summary']['students_registered'],
                $stats['summary']['students_not_registered'],
                $stats['summary']['students_under_min_credits'],
            );
        }

        return response()->json($stats);
    }

    private function slip(Student $student, Term $term): Response
    {
        $pdf = $this->report->slipPdf($student, $term);
        $this->audit->record(AuditEvent::Downloaded, $student, [], ['document' => 'phiếu đăng ký học phần', 'term_id' => $term->id]);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="phieu-dang-ky-'.$student->student_code.'-hk'.$term->id.'.pdf"',
        ]);
    }

    private function term(Request $request): Term
    {
        $term = $request->filled('term_id') ? Term::query()->find($request->integer('term_id')) : $this->report->defaultTerm();
        abort_if($term === null, 404, 'Không tìm thấy học kỳ.');

        return $term;
    }
}
