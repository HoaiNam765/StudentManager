<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\AvailableSections;
use App\Modules\Enrollment\Services\CreditLimits;
use App\Modules\Enrollment\Services\RegistrationAccess;
use App\Modules\Student\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Danh sách lớp được chọn (FR-ENR-002): sinh viên xem của mình (cổng sinh viên); cán bộ xem thay một sinh viên.
 * Không gửi `period_id` thì lấy đợt đang mở đầu tiên mà sinh viên thuộc đối tượng.
 */
class RegistrationOptionsController extends Controller
{
    public function __construct(
        private readonly RegistrationAccess $access,
        private readonly AvailableSections $sections,
        private readonly CreditLimits $limits,
    ) {}

    public function mine(Request $request): JsonResponse
    {
        return $this->respond($request, $this->access->studentOf($request->user()));
    }

    public function forStudent(Request $request, Student $student): JsonResponse
    {
        Gate::authorize('view', $student);

        return $this->respond($request, $student);
    }

    private function respond(Request $request, Student $student): JsonResponse
    {
        $open = $this->access->openPeriodsFor($student);
        $visible = $this->access->visiblePeriodsFor($student);
        $period = $request->filled('period_id')
            ? $visible->firstWhere('id', $request->integer('period_id'))
            : ($open->first() ?? $visible->first());
        abort_if($request->filled('period_id') && $period === null, 404);

        if ($period === null) {
            return response()->json([
                'student' => $student->only('id', 'student_code', 'full_name'),
                'period' => null,
                'problems' => ['Chưa có danh sách học phần được thông báo cho khóa của bạn.'],
                'sections' => [],
            ]);
        }

        $tz = config('studentmanager.display_timezone');

        return response()->json([
            'student' => $student->only('id', 'student_code', 'full_name'),
            'server_time' => $this->access->now()->setTimezone($tz)->format('Y-m-d H:i:s'),
            'period' => [
                'id' => $period->id,
                'name' => $period->name,
                'status' => $period->status->value,
                'is_open' => $period->isOpenAt($this->access->now()),
                'preview_only' => $period->status->value === 'draft',
                'term_id' => $period->term_id,
                'term' => $period->term->name,
                'opens_at' => $period->opens_at->setTimezone($tz)->format('Y-m-d H:i'),
                'closes_at' => $period->closes_at->setTimezone($tz)->format('Y-m-d H:i'),
                'min_credits' => $period->min_credits,
                'max_credits' => $period->max_credits,
            ],
            'open_periods' => $open->map(fn (RegistrationPeriod $p) => $p->only('id', 'name'))->values(),
            'visible_periods' => $visible->map(fn (RegistrationPeriod $p) => $p->only('id', 'name'))->values(),
            'credit_limits' => $this->limits->for($student, $period),
            'problems' => $this->access->problems($period, $student),
            'sections' => $this->sections->listFor($student, $period),
        ]);
    }
}
