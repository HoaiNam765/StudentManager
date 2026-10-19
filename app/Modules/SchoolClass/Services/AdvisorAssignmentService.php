<?php

namespace App\Modules\SchoolClass\Services;

use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdvisorAssignment;
use App\Modules\Teacher\Models\Lecturer;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/** Phân công cố vấn học tập theo thời gian (FR-CLS-004, BR-CLS-07). */
class AdvisorAssignmentService extends BaseService
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function assign(AdminClass $class, Lecturer $lecturer, string $effectiveFrom, ?string $reason = null): AdvisorAssignment
    {
        $date = CarbonImmutable::parse($effectiveFrom)->toDateString();

        if (! $class->isActive()) {
            $this->fail("Lớp {$class->code} đang đóng.", 'Chỉ phân công cố vấn cho lớp hành chính đang hoạt động.');
        }

        if (! $lecturer->isAssignable()) {
            $this->fail("Giảng viên {$lecturer->code} không còn đang làm việc.", 'Chọn giảng viên đang làm việc (BR-CLS-07).');
        }

        return $this->audit->withReason($reason ?: 'Phân công cố vấn học tập', function () use ($class, $lecturer, $date, $reason): AdvisorAssignment {
            return $this->transaction(function () use ($class, $lecturer, $date, $reason): AdvisorAssignment {
                $class = AdminClass::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
                $lecturer = Lecturer::query()->whereKey($lecturer->id)->lockForUpdate()->firstOrFail();

                if (! $class->isActive()) {
                    $this->fail("Lớp {$class->code} đang đóng.", 'Chỉ phân công cố vấn cho lớp hành chính đang hoạt động.');
                }

                if (! $lecturer->isAssignable()) {
                    $this->fail("Giảng viên {$lecturer->code} không còn đang làm việc.", 'Chọn giảng viên đang làm việc (BR-CLS-07).');
                }

                $current = AdvisorAssignment::query()->where('admin_class_id', $class->id)
                    ->effectiveOn($date)->lockForUpdate()->first();
                $scheduled = AdvisorAssignment::query()->where('admin_class_id', $class->id)
                    ->whereDate('effective_from', '>', $date)
                    ->where(fn ($period) => $period->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date))
                    ->exists();

                if ($current?->lecturer_id === $lecturer->id) {
                    $this->fail("Giảng viên {$lecturer->code} đã là cố vấn hiện hành của lớp {$class->code}.", 'Chọn một giảng viên khác hoặc sửa ngày hiệu lực của phân công hiện tại.');
                }

                if ($current?->effective_to !== null && $current->effective_to->toDateString() < $this->today()) {
                    $this->fail('Ngày hiệu lực đã nằm trong một phân công cố vấn đã kết thúc.', 'Chọn ngày sau thời gian cố vấn đã lưu; không sửa ngược lịch sử.');
                }

                if ($scheduled) {
                    $this->fail('Lớp đã có phân công cố vấn bắt đầu sau ngày hiệu lực được chọn.', 'Chọn ngày sau phân công đã lên lịch hoặc hủy lịch đó trước.');
                }

                if ($current !== null && $current->effective_from->toDateString() >= $date) {
                    $this->fail(
                        "Phân công cố vấn của lớp {$class->code} đã bắt đầu từ {$current->effective_from->format('d/m/Y')}.",
                        'Chọn ngày thay đổi sau ngày bắt đầu phân công.'
                    );
                }

                $current?->update([
                    'effective_to' => CarbonImmutable::parse($date)->subDay()->toDateString(),
                    'end_reason' => $reason,
                ]);

                return AdvisorAssignment::create([
                    'admin_class_id' => $class->id,
                    'lecturer_id' => $lecturer->id,
                    'effective_from' => $date,
                    'reason' => $reason,
                ])->load('lecturer');
            });
        });
    }

    public function end(AdvisorAssignment $assignment, string $effectiveTo, ?string $reason = null): AdvisorAssignment
    {
        $date = CarbonImmutable::parse($effectiveTo)->toDateString();
        if ($assignment->effective_to !== null) {
            $this->fail('Phân công cố vấn này đã kết thúc.', 'Chỉ kết thúc phân công đang còn hiệu lực; xem lịch sử lớp để tra cứu.');
        }

        if ($date < $assignment->effective_from->toDateString()) {
            $this->fail('Ngày kết thúc cố vấn trước ngày bắt đầu phân công.', 'Chọn ngày kết thúc bằng hoặc sau ngày bắt đầu.');
        }

        return $this->audit->withReason($reason ?: 'Kết thúc phân công cố vấn', function () use ($assignment, $date, $reason): AdvisorAssignment {
            return $this->transaction(function () use ($assignment, $date, $reason): AdvisorAssignment {
                $assignment = AdvisorAssignment::query()->whereKey($assignment->id)->lockForUpdate()->firstOrFail();
                if ($assignment->effective_to !== null) {
                    $this->fail('Phân công cố vấn vừa được kết thúc bởi thao tác khác.', 'Tải lại lịch sử cố vấn của lớp.');
                }
                $assignment->update(['effective_to' => $date, 'end_reason' => $reason]);

                return $assignment->refresh();
            });
        });
    }

    /** @return array<string, mixed> */
    public function preview(AdminClass $class, Lecturer $lecturer, string $effectiveFrom): array
    {
        $date = CarbonImmutable::parse($effectiveFrom)->toDateString();
        $current = AdvisorAssignment::query()->where('admin_class_id', $class->id)
            ->effectiveOn($date)->with('lecturer')->first();
        $scheduled = AdvisorAssignment::query()->where('admin_class_id', $class->id)
            ->whereDate('effective_from', '>', $date)
            ->where(fn ($period) => $period->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date))
            ->exists();

        if (! $class->isActive()) {
            $reason = 'Lớp hành chính đang đóng.';
        } elseif (! $lecturer->isAssignable()) {
            $reason = 'Giảng viên không còn đang làm việc (BR-CLS-07).';
        } elseif ($current?->effective_to !== null && $current->effective_to->toDateString() < $this->today()) {
            $reason = 'Ngày hiệu lực nằm trong một phân công cố vấn đã kết thúc.';
        } elseif ($scheduled) {
            $reason = 'Lớp đã có phân công cố vấn bắt đầu sau ngày hiệu lực được chọn.';
        } elseif ($current?->lecturer_id === $lecturer->id) {
            $reason = 'Giảng viên đã là cố vấn hiện hành.';
        } elseif ($current !== null && $current->effective_from->toDateString() >= $date) {
            $reason = 'Ngày hiệu lực phải sau ngày bắt đầu phân công hiện tại.';
        } else {
            $reason = null;
        }

        return [
            'admin_class' => ['id' => $class->id, 'code' => $class->code, 'name' => $class->name],
            'current_advisor' => $current?->lecturer?->only('id', 'code', 'full_name'),
            'new_advisor' => $lecturer->only('id', 'code', 'full_name'),
            'effective_from' => $date,
            'will_replace' => $current !== null,
            'allowed' => $reason === null,
            'reason' => $reason,
        ];
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
