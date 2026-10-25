<?php

namespace App\Modules\Attendance\Services;

use App\Models\User;
use App\Modules\Attendance\Enums\AttendanceStatus;
use App\Modules\Attendance\Models\AttendanceChangeRequest;
use App\Modules\Attendance\Models\AttendanceLock;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Models\AttendanceSummary;
use App\Modules\Attendance\Models\TeachingLog;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Subject\Enums\ComponentKind;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Enums\SessionStatus;
use App\Modules\Timetable\Models\ClassSession;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Điểm danh theo buổi học (#142; FR-ATT-001, 002, 003, 005, 011, 012, 013; BR-ATT-01, 05, 07):
 * - Danh sách điểm danh = sinh viên đăng ký còn hiệu lực của lớp (không gồm đã hủy / rút).
 * - Chỉ giảng viên được phân công điểm danh; Phòng Đào tạo (ATT.approve toàn trường) điều chỉnh có lý do.
 * - Không điểm danh buổi chưa diễn ra, buổi nghỉ / hủy; buổi dạy bù điểm danh như buổi thường.
 * - Giảng viên sửa trong `edit_days` ngày; quá hạn thì gửi yêu cầu cho Phòng Đào tạo duyệt (không tự duyệt).
 * - Sổ đã khóa chỉ Phòng Đào tạo sửa, kèm lý do. Mọi lần sửa ghi nhật ký kiểm toán (StandardModel) và `change_reason`.
 */
class AttendanceService extends BaseService
{
    public function __construct(
        private readonly AccessControl $access,
        private readonly AttendanceCalculator $calculator,
    ) {}

    // ---- Quyền ---------------------------------------------------------------------------------------------------

    public function authorizeView(CourseSection $section, User $user): void
    {
        abort_unless($this->access->allowsOn($user, 'ATT', PermissionAction::View, $section), 403,
            'Bạn không có quyền xem điểm danh lớp này. Hãy chọn lớp thuộc phạm vi của bạn.');
    }

    public function isInstructor(CourseSection $section, User $user): bool
    {
        return SectionInstructor::query()->where('course_section_id', $section->id)
            ->whereHas('lecturer', fn ($q) => $q->where('user_id', $user->id))->exists();
    }

    /** Phòng Đào tạo: điều chỉnh ngoài khung giờ / sổ đã khóa, duyệt yêu cầu, khóa sổ, miễn cấm thi. */
    public function isOffice(User $user): bool
    {
        return $this->access->allowsGlobally($user, 'ATT', PermissionAction::Approve);
    }

    private function authorizeRecord(CourseSection $section, User $user): void
    {
        $office = $this->isOffice($user);
        abort_unless($office || ($this->access->allowsOn($user, 'ATT', PermissionAction::Create, $section) && $this->isInstructor($section, $user)), 403,
            'Chỉ giảng viên được phân công lớp này mới điểm danh (BR-ATT-01). Liên hệ Phòng Đào tạo nếu cần phân công.');
    }

    private function authorizeOffice(User $user): void
    {
        abort_unless($this->isOffice($user), 403, 'Chỉ Phòng Đào tạo được thực hiện thao tác này (BR-ATT-07).');
    }

    // ---- Trạng thái buổi / sổ -------------------------------------------------------------------------------------

    public function isLocked(CourseSection $section): bool
    {
        return AttendanceLock::query()->where('course_section_id', $section->id)->where('locked', true)->exists();
    }

    /** @return array{open: bool, within_edit: bool, reason: ?string} */
    public function window(ClassSession $session): array
    {
        $config = config('studentmanager.attendance');
        $today = CarbonImmutable::now(config('studentmanager.display_timezone'))->startOfDay();
        $date = CarbonImmutable::parse($session->date->toDateString(), config('studentmanager.display_timezone'));

        if (! $session->status->countsForAttendance()) {
            return ['open' => false, 'within_edit' => false, 'reason' => "Buổi học {$session->status->label()} nên không điểm danh (BR-ATT-05)."];
        }

        if ($date->subDays((int) $config['open_days_before'])->gt($today)) {
            return ['open' => false, 'within_edit' => false, 'reason' => 'Buổi học chưa diễn ra nên chưa mở điểm danh (BR-ATT-05).'];
        }

        return ['open' => true, 'within_edit' => $date->diffInDays($today) <= (int) $config['edit_days'], 'reason' => null];
    }

    // ---- Danh sách buổi, phiếu điểm danh ---------------------------------------------------------------------------

    /** @return list<array<string, mixed>> các buổi của lớp kèm số liệu điểm danh, sổ đầu bài */
    public function sessions(CourseSection $section, User $user): array
    {
        $this->authorizeView($section, $user);
        $counts = AttendanceRecord::query()->where('course_section_id', $section->id)
            ->toBase()->selectRaw('class_session_id, status, count(*) as n')->groupBy('class_session_id', 'status')->get()
            ->groupBy('class_session_id');
        $logs = TeachingLog::query()->whereIn('class_session_id', ClassSession::query()->select('id')->where('course_section_id', $section->id))
            ->get()->keyBy('class_session_id');

        return ClassSession::query()->with('room:id,code')->where('course_section_id', $section->id)->orderBy('date')->orderBy('period_from')->get()
            ->map(function (ClassSession $s) use ($counts, $logs) {
                $byStatus = ($counts->get($s->id) ?? collect())->pluck('n', 'status');
                $window = $this->window($s);
                $log = $logs->get($s->id);

                return [
                    'id' => $s->id,
                    'date' => $s->date->toDateString(),
                    'week' => $s->week,
                    'period_from' => $s->period_from,
                    'period_to' => $s->period_to,
                    'room' => $s->room?->code,
                    'status' => $s->status->value,
                    'status_label' => $s->status->label(),
                    'off_reason' => $s->off_reason,
                    'open' => $window['open'],
                    'within_edit' => $window['within_edit'],
                    'closed_reason' => $window['reason'],
                    'recorded' => (int) $byStatus->sum(),
                    'counts' => collect(AttendanceStatus::values())->mapWithKeys(fn ($v) => [$v => (int) ($byStatus[$v] ?? 0)])->all(),
                    'teaching_log' => $log === null ? null : $log->only('periods', 'content') + ['confirmed_at' => $log->confirmed_at->toIso8601String()],
                ];
            })->values()->all();
    }

    /** Phiếu điểm danh của một buổi (FR-ATT-001): sinh viên đăng ký còn hiệu lực, trạng thái đã ghi. */
    public function sheet(ClassSession $session, User $user): array
    {
        $section = $session->section;
        $this->authorizeView($section, $user);
        $records = AttendanceRecord::query()->where('class_session_id', $session->id)->get()->keyBy('enrollment_id');
        $pending = AttendanceChangeRequest::query()->where('status', AttendanceChangeRequest::PENDING)
            ->whereIn('attendance_record_id', $records->pluck('id'))->pluck('attendance_record_id')->flip();
        $window = $this->window($session);
        $locked = $this->isLocked($section);
        $office = $this->isOffice($user);
        $instructor = $this->isInstructor($section, $user);

        return [
            'session' => [
                'id' => $session->id,
                'date' => $session->date->toDateString(),
                'week' => $session->week,
                'period_from' => $session->period_from,
                'period_to' => $session->period_to,
                'status' => $session->status->value,
                'status_label' => $session->status->label(),
            ],
            'section' => ['id' => $section->id, 'code' => $section->code, 'subject' => $section->subject?->only('id', 'code', 'name')],
            'window' => $window,
            'locked' => $locked,
            'can' => [
                // Giảng viên ghi khi phiên mở, trong khung sửa, sổ chưa khóa; Phòng Đào tạo ghi có lý do
                'record' => $window['open'] && (($instructor && $window['within_edit'] && ! $locked) || $office),
                'request_change' => $window['open'] && $instructor && (! $window['within_edit'] || $locked) && ! $office,
                'reason_required' => $office && (! $window['within_edit'] || $locked),
            ],
            'statuses' => collect(AttendanceStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()])->all(),
            'rows' => Enrollment::query()->active()->where('course_section_id', $section->id)
                ->with('student:id,student_code,full_name,photo_path')
                ->get()->sortBy(fn (Enrollment $e) => $e->student?->student_code)->values()
                ->map(fn (Enrollment $e) => [
                    'enrollment_id' => $e->id,
                    'student' => $e->student?->only('id', 'student_code', 'full_name') + [
                        'photo_url' => $e->student?->photo_path ? asset('storage/'.$e->student->photo_path) : null,
                    ],
                    'record' => ($r = $records->get($e->id)) === null ? null : [
                        'id' => $r->id,
                        'status' => $r->status->value,
                        'status_label' => $r->status->label(),
                        'minutes_late' => $r->minutes_late,
                        'note' => $r->note,
                        'pending_change' => $pending->has($r->id),
                    ],
                ])->all(),
        ];
    }

    // ---- Ghi điểm danh ----------------------------------------------------------------------------------------------

    /**
     * Ghi / sửa điểm danh một buổi (FR-ATT-002, 003). `items`: [{enrollment_id, status, minutes_late?, note?}].
     *
     * @return int số dòng đã ghi hoặc đổi
     */
    public function record(ClassSession $session, User $actor, array $items, ?string $reason = null): int
    {
        $section = $session->section;
        $this->authorizeRecord($section, $actor);
        $window = $this->window($session);

        if (! $window['open']) {
            $this->fail($window['reason'], 'Chọn buổi học đã diễn ra và không bị nghỉ / hủy.');
        }

        $office = $this->isOffice($actor);
        $locked = $this->isLocked($section);

        if (! $office && $locked) {
            $this->fail('Sổ điểm danh lớp này đã khóa.', 'Gửi yêu cầu sửa để Phòng Đào tạo duyệt (BR-ATT-07).');
        }

        if (! $office && ! $window['within_edit']) {
            $days = (int) config('studentmanager.attendance.edit_days');
            $this->fail("Đã quá {$days} ngày kể từ buổi học nên không sửa trực tiếp được.", 'Gửi yêu cầu sửa kèm lý do để Phòng Đào tạo duyệt (FR-ATT-005).');
        }

        if ($office && ($locked || ! $window['within_edit']) && blank($reason)) {
            $this->fail('Sửa điểm danh quá hạn hoặc đã khóa sổ cần ghi lý do.', 'Nhập lý do điều chỉnh (BR-ATT-07).');
        }

        if ($items === []) {
            $this->fail('Danh sách điểm danh đang trống.', 'Chọn trạng thái cho ít nhất một sinh viên.');
        }

        $enrollments = Enrollment::query()->active()->where('course_section_id', $section->id)->get()->keyBy('id');

        foreach ($items as $item) {
            if (! $enrollments->has((int) $item['enrollment_id'])) {
                $this->fail('Có sinh viên không còn trong lớp (đã hủy hoặc rút học phần).', 'Tải lại danh sách điểm danh rồi lưu lại (BR-ATT-05).');
            }
        }

        $changed = $this->transaction(function () use ($items, $session, $section, $enrollments, $actor, $reason): int {
            $existing = AttendanceRecord::query()->where('class_session_id', $session->id)->lockForUpdate()->get()->keyBy('enrollment_id');
            $count = 0;

            foreach ($items as $item) {
                $status = AttendanceStatus::from($item['status']);
                $values = [
                    'status' => $status,
                    'minutes_late' => $status === AttendanceStatus::Late ? (int) ($item['minutes_late'] ?? 0) : null,
                    'note' => $item['note'] ?? null,
                ];
                $record = $existing->get((int) $item['enrollment_id']);

                if ($record === null) {
                    AttendanceRecord::create($values + [
                        'class_session_id' => $session->id,
                        'enrollment_id' => (int) $item['enrollment_id'],
                        'student_id' => $enrollments[(int) $item['enrollment_id']]->student_id,
                        'course_section_id' => $section->id,
                        'recorded_by' => $actor->id,
                        'recorded_at' => now(),
                    ]);
                    $count++;

                    continue;
                }

                $record->fill($values);

                if ($record->isDirty()) {
                    $record->fill(['change_reason' => $reason, 'recorded_by' => $actor->id, 'recorded_at' => now()])->save();
                    $count++;
                }
            }

            return $count;
        });

        $this->calculator->recalculate($enrollments->only(array_map(fn ($i) => (int) $i['enrollment_id'], $items))->values());

        return $changed;
    }

    // ---- Yêu cầu sửa quá hạn ----------------------------------------------------------------------------------------

    public function requestChange(AttendanceRecord $record, User $actor, string $toStatus, ?int $minutesLate, string $reason): AttendanceChangeRequest
    {
        $section = $record->section;
        abort_unless($this->isInstructor($section, $actor) && $this->access->allowsOn($actor, 'ATT', PermissionAction::Update, $section), 403,
            'Chỉ giảng viên được phân công lớp này mới gửi yêu cầu sửa điểm danh.');
        $to = AttendanceStatus::from($toStatus);

        if ($to === $record->status && ($to !== AttendanceStatus::Late || $minutesLate === $record->minutes_late)) {
            $this->fail('Trạng thái đề nghị trùng với trạng thái hiện tại.', 'Chọn trạng thái khác.');
        }

        if (AttendanceChangeRequest::query()->where('attendance_record_id', $record->id)->where('status', AttendanceChangeRequest::PENDING)->exists()) {
            $this->fail('Bản ghi này đã có yêu cầu sửa đang chờ duyệt.', 'Chờ Phòng Đào tạo xử lý yêu cầu trước.');
        }

        return AttendanceChangeRequest::create([
            'attendance_record_id' => $record->id,
            'requested_by' => $actor->id,
            'from_status' => $record->status,
            'to_status' => $to,
            'minutes_late' => $to === AttendanceStatus::Late ? (int) $minutesLate : null,
            'reason' => $reason,
            'status' => AttendanceChangeRequest::PENDING,
        ]);
    }

    public function decideChange(AttendanceChangeRequest $request, User $actor, bool $approve, ?string $note): AttendanceChangeRequest
    {
        $this->authorizeOffice($actor);

        if ($request->status !== AttendanceChangeRequest::PENDING) {
            $this->fail('Yêu cầu này đã được xử lý.', 'Tải lại danh sách yêu cầu.');
        }

        if ($request->requested_by === $actor->id) {
            $this->fail('Không tự duyệt yêu cầu của chính mình.', 'Nhờ cán bộ Phòng Đào tạo khác duyệt.');
        }

        if (! $approve && blank($note)) {
            $this->fail('Từ chối cần ghi lý do.', 'Nhập lý do để giảng viên biết cách xử lý.');
        }

        $this->transaction(function () use ($request, $actor, $approve, $note): void {
            if ($approve) {
                $request->record->fill([
                    'status' => $request->to_status,
                    'minutes_late' => $request->to_status === AttendanceStatus::Late ? $request->minutes_late : null,
                    'change_reason' => "Duyệt yêu cầu #{$request->id}: {$request->reason}",
                    'recorded_by' => $actor->id,
                    'recorded_at' => now(),
                ])->save();
            }

            $request->fill([
                'status' => $approve ? AttendanceChangeRequest::APPROVED : AttendanceChangeRequest::REJECTED,
                'decided_by' => $actor->id,
                'decided_at' => now(),
                'decision_note' => $note,
            ])->save();
        });

        if ($approve) {
            $this->calculator->recalculate(Enrollment::query()->whereKey($request->record->enrollment_id)->get());
        }

        return $request->refresh();
    }

    // ---- Khóa sổ, miễn cấm thi ---------------------------------------------------------------------------------------

    public function lock(CourseSection $section, User $actor): AttendanceLock
    {
        $this->authorizeOffice($actor);
        $this->calculator->recalculateSection($section);

        return AttendanceLock::query()->updateOrCreate(['course_section_id' => $section->id], [
            'locked' => true, 'locked_by' => $actor->id, 'locked_at' => now(), 'unlock_reason' => null,
        ]);
    }

    public function unlock(CourseSection $section, User $actor, string $reason): AttendanceLock
    {
        $this->authorizeOffice($actor);
        $lock = AttendanceLock::query()->where('course_section_id', $section->id)->where('locked', true)->first();

        if ($lock === null) {
            $this->fail('Sổ điểm danh lớp này chưa khóa.', 'Không cần mở khóa.');
        }

        $lock->fill(['locked' => false, 'unlock_reason' => $reason])->save();

        return $lock;
    }

    /** Ngoại lệ cấm thi do Phòng Đào tạo xét, có lý do (BR-ATT-03). */
    public function exempt(AttendanceSummary $summary, User $actor, bool $exempted, ?string $reason): AttendanceSummary
    {
        $this->authorizeOffice($actor);

        if ($exempted && blank($reason)) {
            $this->fail('Miễn cấm thi cần ghi lý do.', 'Nhập lý do và số văn bản (nếu có).');
        }

        $summary->fill(['exempted' => $exempted, 'exemption_reason' => $exempted ? $reason : null])->save();
        $this->calculator->recalculate(Enrollment::query()->whereKey($summary->enrollment_id)->get());

        return $summary->refresh();
    }

    // ---- Sổ đầu bài, điểm chuyên cần ---------------------------------------------------------------------------------

    /** Xác nhận buổi đã dạy: số tiết thực dạy, nội dung (FR-ATT-012). Buổi "dự kiến" chuyển sang "đã dạy". */
    public function confirmTeaching(ClassSession $session, User $actor, int $periods, string $content): TeachingLog
    {
        $section = $session->section;
        abort_unless($this->isInstructor($section, $actor), 403, 'Chỉ giảng viên được phân công mới ghi sổ đầu bài lớp này.');
        $window = $this->window($session);

        if (! $window['open']) {
            $this->fail($window['reason'], 'Chọn buổi học đã diễn ra.');
        }

        $max = $session->period_to - $session->period_from + 1;

        if ($periods < 1 || $periods > $max) {
            $this->fail("Số tiết thực dạy phải từ 1 đến {$max}.", 'Nhập số tiết theo buổi học.');
        }

        $lecturer = Lecturer::query()->where('user_id', $actor->id)->firstOrFail();

        return $this->transaction(function () use ($session, $lecturer, $periods, $content): TeachingLog {
            if ($session->status === SessionStatus::Planned) {
                $session->fill(['status' => SessionStatus::Taught])->save();
            }

            return TeachingLog::query()->updateOrCreate(['class_session_id' => $session->id], [
                'lecturer_id' => $lecturer->id, 'periods' => $periods, 'content' => $content, 'confirmed_at' => now(),
            ]);
        });
    }

    /** Chuyển điểm chuyên cần vào thành phần "Chuyên cần" của sổ điểm (nháp) (FR-ATT-008). */
    public function pushScores(CourseSection $section, User $actor, GradebookService $gradebook): int
    {
        $component = $gradebook->components($section)
            ->first(fn (SectionGradeComponent $c) => $c->kind === ComponentKind::Attendance);

        if ($component === null) {
            $this->fail('Cơ cấu điểm lớp này không có thành phần Chuyên cần.', 'Khởi tạo hoặc chỉnh cơ cấu điểm có thành phần Chuyên cần trước.');
        }

        $this->calculator->recalculateSection($section);
        $items = AttendanceSummary::query()->where('course_section_id', $section->id)
            ->whereIn('enrollment_id', Enrollment::query()->active()->where('course_section_id', $section->id)->select('id'))
            ->get()->map(fn (AttendanceSummary $s) => [
                'enrollment_id' => $s->enrollment_id,
                'component_id' => $component->id,
                'score' => $s->attendance_score,
            ])->all();

        return $gradebook->saveDraft($section, $actor, $items);
    }
}
