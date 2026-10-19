<?php

namespace App\Modules\SchoolClass\Services;

use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Gán, chuyển và gỡ sinh viên khỏi lớp hành chính (FR-CLS-003; BR-CLS-08; GC-07).
 * Mọi thao tác ghi đều khóa lớp/sinh viên, kiểm tra lại sĩ số trong giao dịch và giữ nguyên lịch sử.
 */
class ClassRosterService extends BaseService
{
    public function __construct(private readonly AuditLogger $audit) {}

    /** @param  list<int>  $studentIds
     * @return array{assignments: list<array<string, mixed>>, errors: list<array<string, mixed>>}
     */
    public function previewAssignment(AdminClass $class, array $studentIds, string $effectiveFrom): array
    {
        $date = CarbonImmutable::parse($effectiveFrom)->toDateString();
        $this->assertActiveClass($class);
        $students = $this->students($studentIds);
        $errors = [];
        $rows = [];
        $class->loadMissing('cohort:id,start_year');
        $size = AdminClassMember::query()->effectiveOn($date)->where('admin_class_id', $class->id)->count();
        $seats = max(0, $class->max_size - $size);

        foreach ($students as $student) {
            $error = $this->placementError($student, $class);
            $current = $this->membershipOn($student, $date);
            $scheduled = $this->membershipStartingAfter($student, $date);

            if ($error !== null) {
                $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => $error];

                continue;
            }

            if ($current !== null && $current->effective_to !== null) {
                $today = $this->today();
                if ($current->effective_to->toDateString() < $today) {
                    $errors[] = [
                        'student_id' => $student->id,
                        'student_code' => $student->student_code,
                        'reason' => 'Ngày hiệu lực đã nằm trong lịch sử phân lớp đã kết thúc.',
                    ];

                    continue;
                }
            }

            if ($current?->admin_class_id === $class->id) {
                $rows[] = $this->assignmentRow($student, $class, 'unchanged');

                continue;
            }

            if ($scheduled !== null && $scheduled->effective_from->toDateString() > $date) {
                $errors[] = [
                    'student_id' => $student->id,
                    'student_code' => $student->student_code,
                    'reason' => 'Sinh viên đã có phân lớp bắt đầu sau ngày hiệu lực được chọn.',
                ];

                continue;
            }

            if ($current !== null && $current->effective_from->toDateString() >= $date) {
                $errors[] = [
                    'student_id' => $student->id,
                    'student_code' => $student->student_code,
                    'reason' => 'Ngày chuyển phải sau ngày bắt đầu phân lớp hiện hành.',
                ];

                continue;
            }

            if ($seats < 1) {
                $errors[] = [
                    'student_id' => $student->id,
                    'student_code' => $student->student_code,
                    'reason' => "Lớp {$class->code} đã đủ sĩ số tối đa {$class->max_size}.",
                ];

                continue;
            }

            $seats--;
            $rows[] = $this->assignmentRow($student, $class, $current === null ? 'assign' : 'transfer', $current?->adminClass?->code);
        }

        return ['assignments' => $rows, 'errors' => $errors];
    }

    /** @param  list<int>  $studentIds
     * @return list<AdminClassMember>
     */
    public function assign(AdminClass $class, array $studentIds, string $effectiveFrom, ?string $reason = null): array
    {
        $date = CarbonImmutable::parse($effectiveFrom)->toDateString();
        $this->assertActiveClass($class);
        $this->students($studentIds);

        return $this->audit->withReason($reason ?: 'Gán sinh viên vào lớp hành chính', function () use ($class, $studentIds, $date, $reason): array {
            return $this->transaction(function () use ($class, $studentIds, $date, $reason): array {
                $class = AdminClass::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
                $this->assertActiveClass($class);
                Student::query()->whereIn('id', $studentIds)->orderBy('id')->lockForUpdate()->get();
                $plan = $this->previewAssignment($class, $studentIds, $date);
                $this->assertPreviewCanApply($plan);

                $members = [];
                foreach ($plan['assignments'] as $row) {
                    if ($row['action'] === 'unchanged') {
                        continue;
                    }

                    $student = Student::query()->findOrFail($row['student_id']);
                    $old = $this->membershipOn($student, $date);

                    if ($old !== null) {
                        $old->update([
                            'effective_to' => CarbonImmutable::parse($date)->subDay()->toDateString(),
                            'end_reason' => $reason,
                        ]);
                    }

                    $members[] = AdminClassMember::create([
                        'admin_class_id' => $class->id,
                        'student_id' => $student->id,
                        'effective_from' => $date,
                        'reason' => $reason,
                    ]);
                }

                return $members;
            });
        });
    }

    /**
     * Tự chia sinh viên theo khóa/ngành/hệ, lần lượt lấp đầy lớp đang hoạt động theo mã lớp (FR-CLS-003).
     *
     * @param  list<int>  $studentIds
     * @return array{assignments: list<array<string, mixed>>, errors: list<array<string, mixed>>}
     */
    public function previewAutomaticPlacement(array $studentIds, string $effectiveFrom): array
    {
        $date = CarbonImmutable::parse($effectiveFrom)->toDateString();
        $students = $this->students($studentIds);
        $classes = AdminClass::query()->active()->with('cohort:id,start_year')->orderBy('code')->get();
        $seats = [];
        foreach ($classes as $class) {
            $seats[$class->id] = max(0, $class->max_size - AdminClassMember::query()->effectiveOn($date)->where('admin_class_id', $class->id)->count());
        }

        $rows = [];
        $errors = [];
        foreach ($students as $student) {
            if (! in_array($student->status, [StudentStatus::Studying, StudentStatus::Suspended], true)) {
                $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => 'Chỉ xếp lớp cho sinh viên đang học hoặc bảo lưu.'];

                continue;
            }

            $current = $this->membershipOn($student, $date);
            $scheduled = $this->membershipStartingAfter($student, $date);
            $matching = $classes->filter(fn (AdminClass $class) => $class->major_id === $student->major_id
                && $class->training_type_id === $student->training_type_id
                && $class->cohort->start_year === $student->cohort);

            if ($current !== null && $matching->contains('id', $current->admin_class_id)) {
                if ($current->effective_to !== null && $current->effective_to->toDateString() < $this->today()) {
                    $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => 'Ngày hiệu lực đã nằm trong lịch sử phân lớp đã kết thúc.'];

                    continue;
                }

                $rows[] = $this->assignmentRow($student, $current->adminClass, 'unchanged');

                continue;
            }

            if ($current !== null && $current->effective_to !== null && $current->effective_to->toDateString() < $this->today()) {
                $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => 'Ngày hiệu lực đã nằm trong lịch sử phân lớp đã kết thúc.'];

                continue;
            }

            if ($scheduled !== null) {
                $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => 'Sinh viên đã có phân lớp bắt đầu sau ngày hiệu lực được chọn.'];

                continue;
            }

            $target = $matching->first(fn (AdminClass $class) => $seats[$class->id] > 0);
            if ($target === null) {
                $errors[] = [
                    'student_id' => $student->id,
                    'student_code' => $student->student_code,
                    'reason' => 'Không có lớp hành chính cùng khóa, ngành, hệ còn chỗ.',
                ];

                continue;
            }

            if ($current !== null && $current->effective_from->toDateString() >= $date) {
                $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => 'Ngày chuyển phải sau ngày bắt đầu phân lớp hiện hành.'];

                continue;
            }

            $seats[$target->id]--;
            $rows[] = $this->assignmentRow($student, $target, $current === null ? 'assign' : 'transfer', $current?->adminClass?->code);
        }

        return ['assignments' => $rows, 'errors' => $errors];
    }

    /** @param  list<int>  $studentIds
     * @return list<AdminClassMember>
     */
    public function assignAutomatically(array $studentIds, string $effectiveFrom, ?string $reason = null): array
    {
        $date = CarbonImmutable::parse($effectiveFrom)->toDateString();

        return $this->audit->withReason($reason ?: 'Tự xếp lớp theo khóa, ngành và sĩ số', function () use ($studentIds, $date, $reason): array {
            return $this->transaction(function () use ($studentIds, $date, $reason): array {
                $this->students($studentIds);
                Student::query()->whereIn('id', $studentIds)->orderBy('id')->lockForUpdate()->get();
                AdminClass::query()->active()->orderBy('id')->lockForUpdate()->get();
                $plan = $this->previewAutomaticPlacement($studentIds, $date);
                $this->assertPreviewCanApply($plan);
                $members = [];

                foreach ($plan['assignments'] as $row) {
                    if ($row['action'] === 'unchanged') {
                        continue;
                    }

                    $student = Student::query()->findOrFail($row['student_id']);
                    $class = AdminClass::query()->findOrFail($row['admin_class_id']);
                    $old = $this->membershipOn($student, $date);
                    if ($old !== null) {
                        $old->update([
                            'effective_to' => CarbonImmutable::parse($date)->subDay()->toDateString(),
                            'end_reason' => $reason,
                        ]);
                    }

                    $members[] = AdminClassMember::create([
                        'admin_class_id' => $class->id,
                        'student_id' => $student->id,
                        'effective_from' => $date,
                        'reason' => $reason,
                    ]);
                }

                return $members;
            });
        });
    }

    /** Chọn lớp còn chỗ và ghi thành viên khi xác nhận nhập học (#102). */
    public function assignForAdmission(Student $student, string $effectiveFrom): AdminClassMember
    {
        return $this->assignAutomatically([$student->id], $effectiveFrom, 'Xếp lớp khi nhập học')[0];
    }

    /** @param  list<int>  $studentIds
     * @return array{removals: list<array<string, mixed>>, errors: list<array<string, mixed>>}
     */
    public function previewRemoval(AdminClass $class, array $studentIds, string $effectiveTo): array
    {
        $date = CarbonImmutable::parse($effectiveTo)->toDateString();
        $students = $this->students($studentIds);
        $rows = [];
        $errors = [];

        foreach ($students as $student) {
            $membership = $this->membershipOn($student, $date);
            if ($membership === null || $membership->admin_class_id !== $class->id) {
                $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => "Sinh viên không thuộc lớp {$class->code} vào ngày {$date}."];

                continue;
            }

            if ($membership->effective_from->toDateString() > $date) {
                $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu phân lớp.'];

                continue;
            }

            if ($membership->effective_to !== null && $membership->effective_to->toDateString() < $this->today()) {
                $errors[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'reason' => 'Phân lớp này đã kết thúc; không thể gỡ lại trong lịch sử.'];

                continue;
            }

            $rows[] = ['student_id' => $student->id, 'student_code' => $student->student_code, 'full_name' => $student->full_name, 'effective_to' => $date];
        }

        return ['removals' => $rows, 'errors' => $errors];
    }

    /** @param  list<int>  $studentIds
     * @return list<AdminClassMember>
     */
    public function remove(AdminClass $class, array $studentIds, string $effectiveTo, ?string $reason = null): array
    {
        $date = CarbonImmutable::parse($effectiveTo)->toDateString();

        return $this->audit->withReason($reason ?: 'Gỡ sinh viên khỏi lớp hành chính', function () use ($class, $studentIds, $date, $reason): array {
            return $this->transaction(function () use ($class, $studentIds, $date, $reason): array {
                $class = AdminClass::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
                Student::query()->whereIn('id', $studentIds)->orderBy('id')->lockForUpdate()->get();
                $plan = $this->previewRemoval($class, $studentIds, $date);
                $this->assertPreviewCanApply($plan);
                $members = [];

                foreach ($plan['removals'] as $row) {
                    $member = AdminClassMember::query()->where('student_id', $row['student_id'])
                        ->where('admin_class_id', $class->id)->effectiveOn($date)->lockForUpdate()->firstOrFail();
                    $member->update(['effective_to' => $date, 'end_reason' => $reason]);
                    $members[] = $member->refresh();
                }

                return $members;
            });
        });
    }

    /** @param  list<int>  $studentIds */
    private function students(array $studentIds): Collection
    {
        if ($studentIds === []) {
            $this->fail('Chưa chọn sinh viên.', 'Chọn ít nhất một sinh viên để tiếp tục.');
        }

        $ids = array_values(array_unique(array_map('intval', $studentIds)));
        $students = Student::query()->whereIn('id', $ids)->orderBy('id')->get();

        if ($students->count() !== count($ids)) {
            $this->fail('Danh sách có sinh viên không tồn tại hoặc đã bị xóa.', 'Tải lại danh sách sinh viên và chọn lại.');
        }

        return $students;
    }

    private function membershipOn(Student $student, string $date): ?AdminClassMember
    {
        return AdminClassMember::query()->with('adminClass')
            ->where('student_id', $student->id)
            ->effectiveOn($date)
            ->orderByDesc('effective_from')
            ->first();
    }

    private function membershipStartingAfter(Student $student, string $date): ?AdminClassMember
    {
        return AdminClassMember::query()->where('student_id', $student->id)
            ->whereDate('effective_from', '>', $date)
            ->where(fn ($period) => $period->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date))
            ->orderByDesc('effective_from')
            ->first();
    }

    private function placementError(Student $student, AdminClass $class): ?string
    {
        if (! in_array($student->status, [StudentStatus::Studying, StudentStatus::Suspended], true)) {
            return "Sinh viên đang ở trạng thái {$student->status->label()}; chỉ xếp lớp cho sinh viên đang học hoặc bảo lưu.";
        }

        if ($class->cohort->start_year !== $student->cohort || $class->major_id !== $student->major_id || $class->training_type_id !== $student->training_type_id) {
            return 'Lớp không cùng khóa, ngành và hệ đào tạo với sinh viên.';
        }

        return null;
    }

    private function assertActiveClass(AdminClass $class): void
    {
        if (! $class->isActive()) {
            $this->fail("Lớp {$class->code} đang đóng.", 'Chọn một lớp hành chính đang hoạt động.');
        }
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }

    /** @param  array<string, mixed>  $plan */
    private function assertPreviewCanApply(array $plan): void
    {
        if ($plan['errors'] !== []) {
            $summary = implode(' ', array_map(fn (array $error) => ($error['student_code'] ?? 'Sinh viên').' — '.$error['reason'], $plan['errors']));
            $this->fail("Không thể ghi vì bước xem trước phát hiện lỗi: {$summary}", 'Sửa danh sách hoặc sĩ số lớp rồi xem trước và thực hiện lại; không có thay đổi nào được lưu.');
        }
    }

    /** @return array<string, mixed> */
    private function assignmentRow(Student $student, AdminClass $class, string $action, ?string $fromClass = null): array
    {
        return [
            'student_id' => $student->id,
            'student_code' => $student->student_code,
            'full_name' => $student->full_name,
            'action' => $action,
            'from_class' => $fromClass,
            'admin_class_id' => $class->id,
            'admin_class_code' => $class->code,
        ];
    }
}
