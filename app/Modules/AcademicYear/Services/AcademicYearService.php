<?php

namespace App\Modules\AcademicYear\Services;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Models\TermType;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AcademicYearService extends BaseService
{
    public function createAcademicYear(array $data): AcademicYear
    {
        return $this->transaction(function () use ($data): AcademicYear {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);

            if ($endDate->lt($startDate)) {
                $this->fail(
                    'Ngày kết thúc năm học không hợp lệ.',
                    'Vui lòng chọn ngày kết thúc sau ngày bắt đầu.'
                );
            }

            $this->validateAcademicYearOverlap(
                $startDate,
                $endDate
            );

            return app(AuditLogger::class)->withReason(
                'Tạo năm học.',
                fn () => AcademicYear::create($data)
            );
        });
    }

    public function updateAcademicYear(
        AcademicYear $academicYear,
        array $data
    ): AcademicYear {
        return $this->transaction(function () use (
            $academicYear,
            $data
        ): AcademicYear {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);

            if ($endDate->lt($startDate)) {
                $this->fail(
                    'Ngày kết thúc năm học không hợp lệ.',
                    'Vui lòng chọn ngày kết thúc sau ngày bắt đầu.'
                );
            }

            $exists = AcademicYear::query()
                ->where('id', '!=', $academicYear->id)
                ->whereDate('start_date', '<=', $endDate)
                ->whereDate('end_date', '>=', $startDate)
                ->exists();

            if ($exists) {
                $this->fail(
                    'Thời gian năm học bị chồng với một năm học khác.',
                    'Vui lòng chọn khoảng thời gian không trùng với năm học đã tồn tại.'
                );
            }

            $hasTermOutsideAcademicYear = $academicYear->terms()
                ->where(function (Builder $query) use ($startDate, $endDate): void {
                    $query
                        ->whereDate('start_date', '<', $startDate)
                        ->orWhereDate('end_date', '>', $endDate);
                })
                ->exists();

            if ($hasTermOutsideAcademicYear) {
                $this->fail(
                    'Khoảng thời gian năm học không bao phủ hết các học kỳ đã tạo.',
                    'Vui lòng chọn ngày bắt đầu và ngày kết thúc bao phủ toàn bộ các học kỳ hiện có.'
                );
            }

            return app(AuditLogger::class)->withReason(
                'Cập nhật năm học.',
                function () use ($academicYear, $data): AcademicYear {
                    $academicYear->update($data);

                    return $academicYear->fresh();
                }
            );
        });
    }

    private function validateAcademicYearOverlap(
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $exists = AcademicYear::query()
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->exists();

        if ($exists) {
            $this->fail(
                'Thời gian năm học bị chồng với một năm học khác.',
                'Vui lòng chọn khoảng thời gian không trùng với năm học đã tồn tại.'
            );
        }
    }

    public function createTerm(array $data): Term
    {
        return $this->transaction(function () use ($data): Term {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);

            if ($endDate->lt($startDate)) {
                $this->fail(
                    'Ngày kết thúc học kỳ không hợp lệ.',
                    'Vui lòng chọn ngày kết thúc sau ngày bắt đầu.'
                );
            }

            $academicYear = AcademicYear::query()
                ->lockForUpdate()
                ->find($data['academic_year_id']);

            if ($academicYear === null) {
                $this->fail(
                    'Năm học không tồn tại.',
                    'Vui lòng chọn một năm học hợp lệ.'
                );
            }

            if (
                $startDate->lt($academicYear->start_date) ||
                $endDate->gt($academicYear->end_date)
            ) {
                $this->fail(
                    'Thời gian học kỳ nằm ngoài thời gian của năm học.',
                    'Vui lòng chọn thời gian học kỳ nằm trong năm học đã chọn.'
                );
            }

            $this->validateTermOverlap(
                $data['academic_year_id'],
                $data['type'],
                $startDate,
                $endDate
            );

            $data['status'] ??= TermStatus::PLANNED->value;

            return app(AuditLogger::class)->withReason(
                'Tạo học kỳ.',
                fn () => Term::create($data)
            );
        });
    }

    private function validateTermOverlap(
        int $academicYearId,
        string $type,
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $query = Term::query()
            ->where('academic_year_id', $academicYearId)
            ->where(function (Builder $query) use (
                $startDate,
                $endDate
            ): void {
                $query
                    ->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
            });

        if ($type === TermType::MAIN->value) {
            $query->whereIn('type', [
                TermType::MAIN->value,
                TermType::SUMMER->value,
            ]);
        } else {
            $query->where(
                'type',
                TermType::MAIN->value
            );
        }

        if ($query->exists()) {
            $this->fail(
                'Thời gian học kỳ bị chồng với một học kỳ đã tồn tại.',
                'Vui lòng chọn khoảng thời gian không trùng với học kỳ khác trong năm học.'
            );
        }
    }

    public function getCurrentTerm(): ?Term
    {
        return Term::query()
            ->where('type', TermType::MAIN->value)
            ->where('status', TermStatus::IN_PROGRESS->value)
            ->orderBy('start_date')
            ->first();
    }

    public function setCurrentTerm(Term $term): Term
    {
        return $this->transaction(function () use ($term): Term {
            $term = Term::query()
                ->lockForUpdate()
                ->findOrFail($term->id);

            if ($term->type !== TermType::MAIN) {
                $this->fail(
                    'Học kỳ hè không thể được đặt làm học kỳ hiện hành.',
                    'Vui lòng chọn một học kỳ chính.'
                );
            }

            if ($term->status === TermStatus::IN_PROGRESS) {
                return $term;
            }

            if ($term->status !== TermStatus::REGISTRATION) {
                $this->fail(
                    'Học kỳ không thể chuyển sang trạng thái đang diễn ra.',
                    'Hãy chuyển học kỳ sang trạng thái Đăng ký trước.'
                );
            }

            if (now()->lt($term->start_date)) {
                $this->fail(
                    'Chưa đến ngày bắt đầu học kỳ.',
                    'Chỉ được bắt đầu học kỳ từ ngày bắt đầu đã cấu hình.'
                );
            }

            $currentTerm = Term::query()
                ->where('type', TermType::MAIN->value)
                ->where('status', TermStatus::IN_PROGRESS->value)
                ->lockForUpdate()
                ->first();

            if ($currentTerm !== null && $currentTerm->isNot($term)) {
                $this->fail(
                    'Đã có một học kỳ chính đang diễn ra.',
                    'Hãy kết thúc học kỳ hiện hành trước khi đặt học kỳ mới.'
                );
            }

            return app(AuditLogger::class)->withReason(
                'Đặt học kỳ hiện hành.',
                function () use ($term): Term {
                    $term->update([
                        'status' => TermStatus::IN_PROGRESS->value,
                    ]);

                    return $term->fresh();
                }
            );
        });
    }

    public function changeStatus(
        Term $term,
        TermStatus $newStatus
    ): Term {
        return $this->transaction(function () use (
            $term,
            $newStatus
        ): Term {
            $term = Term::query()
                ->lockForUpdate()
                ->findOrFail($term->id);

            $allowedTransitions = [
                TermStatus::PLANNED->value => [
                    TermStatus::REGISTRATION->value,
                ],

                TermStatus::REGISTRATION->value => [],

                TermStatus::IN_PROGRESS->value => [
                    TermStatus::EXAM_GRADING->value,
                ],

                TermStatus::EXAM_GRADING->value => [
                    TermStatus::COMPLETED->value,
                ],

                TermStatus::COMPLETED->value => [],

                TermStatus::LOCKED->value => [],
            ];

            // BR-ACY-03: chuyển sang Đang diễn ra chỉ đi qua setCurrentTerm (có kiểm tra học kỳ chính khác)
            if ($newStatus === TermStatus::IN_PROGRESS) {
                $this->fail(
                    'Không thể đổi trạng thái trực tiếp sang Đang diễn ra.',
                    'Hãy dùng thao tác "Đặt học kỳ hiện hành" để bắt đầu học kỳ.'
                );
            }

            $currentStatus = $term->status->value;

            if (! in_array(
                $newStatus->value,
                $allowedTransitions[$currentStatus] ?? [],
                true
            )) {
                $this->fail(
                    'Không thể chuyển trạng thái học kỳ theo quy trình hiện tại.',
                    'Vui lòng thực hiện các trạng thái theo đúng thứ tự nghiệp vụ.'
                );
            }

            if (
                $currentStatus === TermStatus::IN_PROGRESS->value
                && $newStatus === TermStatus::EXAM_GRADING
                && now()->lt($term->end_date)
            ) {
                $this->fail(
                    'Chưa đến ngày kết thúc học kỳ.',
                    'Chỉ được chuyển sang Thi và nhập điểm sau ngày kết thúc học kỳ.'
                );
            }

            return app(AuditLogger::class)->withReason(
                'Đổi trạng thái học kỳ.',
                function () use ($term, $newStatus): Term {
                    $term->update([
                        'status' => $newStatus->value,
                    ]);

                    return $term->fresh();
                }
            );
        });
    }

    public function deleteAcademicYear(
        AcademicYear $academicYear
    ): void {
        // BR-ACY-04: không xóa, chỉ khóa. Năm học luôn bị chặn, dù đã có học kỳ hay chưa.
        $this->fail(
            'Không cho phép xóa năm học.',
            'Năm học là dữ liệu nghiệp vụ và không được xóa khỏi hệ thống.'
        );
    }

    public function deleteTerm(Term $term): void
    {
        $this->fail(
            'Không cho phép xóa học kỳ.',
            'Học kỳ chỉ được khóa theo quy trình nghiệp vụ của hệ thống.'
        );
    }
}
