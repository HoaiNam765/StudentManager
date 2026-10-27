<?php

namespace App\Modules\Enrollment\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\MilestoneType;
use App\Modules\AcademicYear\Services\MilestoneService;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Events\EnrollmentAdjusted;
use App\Modules\Enrollment\Events\EnrollmentChanged;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Support\Audit\AuditLogger;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Đăng ký, hủy, rút, đổi lớp (FR-ENR-003, 005..008; BR-ENR-04..06, 11).
 *
 * Chống tranh chấp (FR-ENR-008, BR-ENR-05): trong một giao dịch, khóa dòng sinh viên rồi dòng lớp (`lockForUpdate`,
 * luôn theo thứ tự này, các lớp theo id tăng dần để không deadlock), kiểm tra điều kiện (EnrollmentRules) trên dữ liệu
 * vừa khóa rồi mới ghi. Hai yêu cầu vào chỗ cuối chạy lần lượt: yêu cầu sau thấy lớp đã đủ và bị từ chối. Unique
 * (sinh viên, lớp còn hiệu lực) ở CSDL chặn bấm trùng.
 *
 * Mốc (BR-ENR-06, giờ máy chủ): đăng ký / hủy tự do khi đợt của sinh viên đang mở hoặc chưa qua "Hạn hủy học phần" (ACY);
 * sau đó tới hết ngày "Hạn rút học phần" thì chỉ rút (không tính GPA); sau hạn rút thì không rút được.
 * Mỗi lần thử ghi `enrollment_logs` kèm IP và lý do (FR-ENR-017), kể cả khi bị từ chối.
 */
class EnrollmentService extends BaseService
{
    public function __construct(
        private readonly EnrollmentRules $rules,
        private readonly RegistrationAccess $access,
        private readonly MilestoneService $milestones,
        private readonly AuditLogger $audit,
    ) {}

    public function register(
        Student $student,
        CourseSection $section,
        ?RegistrationPeriod $period = null,
        string $type = EnrollmentRules::TYPE_NORMAL,
        ?User $actor = null,
        ?string $ip = null,
        array $skip = [],
        ?string $overrideReason = null,
    ): Enrollment {
        return $this->attempt('register', $student, $section, $actor, $ip, function () use ($student, $section, $period, $type, $actor, $skip, $overrideReason): Enrollment {
            return $this->transaction(function () use ($student, $section, $period, $type, $actor, $skip, $overrideReason): Enrollment {
                $student = Student::query()->lockForUpdate()->findOrFail($student->id);
                $section = CourseSection::query()->lockForUpdate()->findOrFail($section->id);
                $period ??= $this->periodFor($student, $section);

                $this->assertAllowed($this->rules->check($student, $section, $period, $type, null, $skip));
                $enrollment = $this->create($student, $section, $period, $type, $actor, $skip !== [] ? $overrideReason : null);

                // Sau khi chốt, cán bộ đăng ký thay: đồng bộ sang FEE (BR-ENR-08)
                if ($enrollment->status === EnrollmentStatus::Confirmed) {
                    EnrollmentAdjusted::dispatch($enrollment, 'register', (string) $overrideReason);
                }

                return $enrollment;
            });
        });
    }

    /**
     * Hủy trong thời hạn đăng ký: giải phóng chỗ (FR-ENR-005). Sau khi chốt, sinh viên không tự hủy được; cán bộ
     * (`$staff`) hủy cả đăng ký Đã chốt, bắt buộc lý do, ghi nhật ký và đồng bộ FEE (BR-ENR-08, FR-ENR-009).
     */
    public function cancel(Enrollment $enrollment, ?User $actor = null, ?string $ip = null, bool $staff = false, ?string $reason = null): Enrollment
    {
        return $this->attempt('cancel', $enrollment->student, $enrollment->section, $actor, $ip, function () use ($enrollment, $staff, $reason): Enrollment {
            return $this->transaction(function () use ($enrollment, $staff, $reason): Enrollment {
                $enrollment = Enrollment::query()->lockForUpdate()->findOrFail($enrollment->id);

                if ($enrollment->status === EnrollmentStatus::Confirmed && ! $staff) {
                    $this->fail('Đăng ký học kỳ này đã chốt nên bạn không tự hủy được (BR-ENR-08).', 'Rút học phần trong hạn rút, hoặc liên hệ Phòng Đào tạo để xử lý kèm lý do.');
                }

                if ($staff) {
                    if (trim((string) $reason) === '') {
                        $this->fail('Cán bộ hủy đăng ký thay phải ghi lý do.', 'Nhập lý do hủy (FR-ENR-009, BR-ENR-08).');
                    }

                    if (! in_array($enrollment->status, [EnrollmentStatus::Registered, EnrollmentStatus::Confirmed], true)) {
                        $this->assertTransition($enrollment, EnrollmentStatus::Cancelled);
                    }

                    $wasConfirmed = $enrollment->status === EnrollmentStatus::Confirmed;
                    $this->audit->withReason("Hủy đăng ký thay: {$reason}", fn () => $enrollment->update(['status' => EnrollmentStatus::Cancelled, 'cancelled_at' => $this->access->now()]));

                    if ($wasConfirmed) {
                        EnrollmentAdjusted::dispatch($enrollment, 'cancel', $reason);
                    }

                    return $enrollment;
                }

                $this->assertTransition($enrollment, EnrollmentStatus::Cancelled);

                if (! $staff && ! $this->canCancelNow($enrollment)) {
                    $this->fail(
                        'Đã qua thời hạn hủy học phần '.$this->deadlineText($enrollment, MilestoneType::CancelDeadline).'.',
                        'Sau hạn hủy chỉ còn rút học phần (trước hạn rút), hoặc liên hệ Phòng Đào tạo.'
                    );
                }

                $enrollment->update(['status' => EnrollmentStatus::Cancelled, 'cancelled_at' => $this->access->now()]);

                return $enrollment;
            });
        });
    }

    /** Rút học phần sau hạn hủy, trước hết ngày hạn rút (FR-ENR-006, BR-ENR-06): không tính GPA. */
    public function withdraw(Enrollment $enrollment, ?User $actor = null, ?string $ip = null, bool $staff = false): Enrollment
    {
        return $this->attempt('withdraw', $enrollment->student, $enrollment->section, $actor, $ip, function () use ($enrollment, $staff): Enrollment {
            return $this->transaction(function () use ($enrollment, $staff): Enrollment {
                $enrollment = Enrollment::query()->lockForUpdate()->findOrFail($enrollment->id);
                $this->assertTransition($enrollment, EnrollmentStatus::Withdrawn);

                if (! $staff) {
                    if ($this->canCancelNow($enrollment)) {
                        $this->fail('Học phần vẫn còn trong thời hạn hủy.', 'Dùng chức năng hủy đăng ký (không ghi nhận rút học phần).');
                    }

                    $deadline = $this->milestones->date($enrollment->term, MilestoneType::WithdrawDeadline);

                    if ($deadline === null || $this->access->now()->gt($this->endOfDay($deadline))) {
                        $this->fail(
                            'Đã qua hạn rút học phần'.($deadline ? ' '.$deadline->format('d/m/Y') : ' (học kỳ chưa khai báo hạn rút)').'.',
                            'Sau hạn rút không rút được học phần (BR-ENR-06); liên hệ Phòng Đào tạo nếu có lý do đặc biệt.'
                        );
                    }
                }

                $enrollment->update(['status' => EnrollmentStatus::Withdrawn, 'withdrawn_at' => $this->access->now()]);

                return $enrollment;
            });
        });
    }

    /**
     * Đổi sang lớp khác của cùng học phần trong một giao dịch (FR-ENR-007): giữ chỗ lớp cũ tới khi lớp mới ghi nhận
     * thành công; lỗi ở bất kỳ bước nào thì vẫn ở lớp cũ.
     */
    public function change(Enrollment $enrollment, CourseSection $target, ?User $actor = null, ?string $ip = null): Enrollment
    {
        return $this->attempt('change', $enrollment->student, $target, $actor, $ip, function () use ($enrollment, $target, $actor): Enrollment {
            return $this->transaction(function () use ($enrollment, $target, $actor): Enrollment {
                $student = Student::query()->lockForUpdate()->findOrFail($enrollment->student_id);
                $ids = collect([$enrollment->course_section_id, $target->id])->sort()->values();
                $locked = CourseSection::query()->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                $enrollment = Enrollment::query()->lockForUpdate()->findOrFail($enrollment->id);
                $target = $locked->get($target->id);
                $old = $locked->get($enrollment->course_section_id);

                if ($enrollment->status !== EnrollmentStatus::Registered) {
                    $this->fail("Đăng ký đang {$enrollment->status->label()} nên không đổi lớp được.", 'Chỉ đổi lớp khi đăng ký chưa chốt; sau khi chốt liên hệ Phòng Đào tạo.');
                }

                if ($target->id === $old->id) {
                    $this->fail("Bạn đang ở lớp {$old->code}.", 'Chọn lớp khác của cùng học phần.');
                }

                if ($target->subject_id !== $old->subject_id) {
                    $this->fail("Lớp {$target->code} không cùng học phần với lớp {$old->code}.", 'Chỉ đổi sang lớp khác của cùng học phần; học phần khác thì hủy rồi đăng ký mới.');
                }

                $period = $this->periodFor($student, $target);
                $this->assertAllowed($this->rules->check($student, $target, $period, $enrollment->type, $old));

                $enrollment->update(['status' => EnrollmentStatus::Cancelled, 'cancelled_at' => $this->access->now()]);

                return $this->create($student, $target, $period, $enrollment->type, $actor);
            });
        });
    }

    public function canCancelNow(Enrollment $enrollment): bool
    {
        if ($enrollment->status !== EnrollmentStatus::Registered) {
            return false;
        }

        $student = $enrollment->student;
        $open = $this->access->openPeriodsFor($student)->contains('term_id', $enrollment->term_id);

        if ($open) {
            return true;
        }

        $deadline = $this->milestones->date($enrollment->term, MilestoneType::CancelDeadline);

        return $deadline !== null && $this->access->now()->lte($this->endOfDay($deadline));
    }

    private function create(Student $student, CourseSection $section, RegistrationPeriod $period, string $type, ?User $actor, ?string $overrideReason = null): Enrollment
    {
        try {
            return Enrollment::create([
                'student_id' => $student->id,
                'course_section_id' => $section->id,
                'term_id' => $section->term_id,
                'subject_id' => $section->subject_id,
                'registration_period_id' => $period->id,
                'type' => $type,
                'status' => $this->isFinalized($section->term_id) ? EnrollmentStatus::Confirmed : EnrollmentStatus::Registered,
                'source' => $actor !== null && $actor->id !== $student->user_id ? 'staff' : 'student',
                'registered_at' => $this->access->now(),
                'override_reason' => $overrideReason,
                'approved_by' => $overrideReason !== null ? $actor?->id : null,
            ]);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'enrollments_student_active_section_unique')) {
                $this->fail("Bạn đã đăng ký lớp {$section->code}.", 'Tải lại trang để xem kết quả đăng ký.');
            }

            throw $e;
        }
    }

    /** Học kỳ đã chốt đăng ký (#113): có đợt ở trạng thái Đã chốt. */
    public function isFinalized(int $termId): bool
    {
        return RegistrationPeriod::query()->where('term_id', $termId)->where('status', PeriodStatus::Finalized)->exists();
    }

    /** Đợt đang mở cho sinh viên ở học kỳ của lớp; cán bộ đăng ký thay ngoài đợt dùng đợt gần nhất của học kỳ. */
    private function periodFor(Student $student, CourseSection $section): RegistrationPeriod
    {
        if ($section->registration_period_id !== null) {
            return RegistrationPeriod::query()->findOrFail($section->registration_period_id);
        }

        $period = $this->access->openPeriodsFor($student)->whereNull('training_framework_id')->firstWhere('term_id', $section->term_id)
            ?? RegistrationPeriod::query()->whereNull('training_framework_id')->where('term_id', $section->term_id)->where('status', '!=', 'draft')->orderByDesc('opens_at')->first();

        if ($period === null) {
            $this->fail("Học kỳ của lớp {$section->code} chưa có đợt đăng ký nào.", 'Phòng Đào tạo cần mở đợt đăng ký cho học kỳ này (FR-ENR-001).');
        }

        return $period;
    }

    /** @param list<string> $reasons */
    private function assertAllowed(array $reasons): void
    {
        if ($reasons !== []) {
            throw new BusinessRuleException(implode(' ', $reasons), 'Xem lý do ở trên để khắc phục rồi đăng ký lại.', $reasons);
        }
    }

    private function assertTransition(Enrollment $enrollment, EnrollmentStatus $to): void
    {
        if (! $enrollment->status->canTransitionTo($to)) {
            $this->fail("Đăng ký đang {$enrollment->status->label()}, không chuyển sang {$to->label()} được.", 'Tải lại trang để xem trạng thái mới nhất.');
        }
    }

    /**
     * Ghi nhật ký mỗi lần thử, cả khi thất bại (ngoài giao dịch đã hoàn tác).
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    private function attempt(string $action, Student $student, ?CourseSection $section, ?User $actor, ?string $ip, callable $callback): mixed
    {
        try {
            $result = $callback();
            $this->log($action, $student, $section, true, null, $actor, $ip);

            if ($result instanceof Enrollment) {
                EnrollmentChanged::dispatch($result, $action); // EV-ENR-02
            }

            return $result;
        } catch (BusinessRuleException $e) {
            $this->log($action, $student, $section, false, $e->reasons ?: [$e->getMessage()], $actor, $ip);

            throw $e;
        }
    }

    private function log(string $action, Student $student, ?CourseSection $section, bool $success, ?array $reasons, ?User $actor, ?string $ip): void
    {
        DB::table('enrollment_logs')->insert([
            'student_id' => $student->id,
            'course_section_id' => $section?->id,
            'action' => $action,
            'success' => $success,
            'reasons' => $reasons === null ? null : json_encode($reasons, JSON_UNESCAPED_UNICODE),
            'ip' => $ip,
            'user_id' => $actor?->id,
            'created_at' => $this->access->now(),
        ]);
    }

    private function endOfDay(CarbonImmutable $date): CarbonImmutable
    {
        return CarbonImmutable::parse($date->toDateString().' 23:59:59', config('studentmanager.display_timezone'))->utc();
    }

    private function deadlineText(Enrollment $enrollment, MilestoneType $type): string
    {
        $date = $this->milestones->date($enrollment->term, $type);

        return $date ? '('.$date->format('d/m/Y').')' : '(đợt đăng ký đã đóng)';
    }
}
