<?php

namespace App\Modules\AcademicYear\Services;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Models\TermType;
use App\Support\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AcademicYearService extends BaseService
{
    /**
     * Tạo năm học.
     */
    public function createAcademicYear(array $data): AcademicYear
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if ($endDate->lt($startDate)) {
            $this->fail(
                'Ngày kết thúc năm học không hợp lệ.',
                'Vui lòng chọn ngày kết thúc sau ngày bắt đầu.'
            );
        }

        return AcademicYear::create($data);
    }

    /**
     * Tạo học kỳ.
     */
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

            $academicYear = AcademicYear::find($data['academic_year_id']);

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

            return Term::create($data);
        });
    }

    /**
     * BR-ACY-01:
     * - Các kỳ chính không được chồng nhau.
     * - Kỳ hè không được chồng với kỳ chính.
     */
    private function validateTermOverlap(
        int $academicYearId,
        string $type,
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $query = Term::query()
            ->where('academic_year_id', $academicYearId)
            ->where(function (Builder $query) use ($startDate, $endDate): void {
                $query
                    ->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
            });

        if ($type === TermType::MAIN->value) {
            $query->where(function (Builder $query): void {
                $query
                    ->where('type', TermType::MAIN->value)
                    ->orWhere('type', TermType::SUMMER->value);
            });
        } else {
            // Học kỳ hè chỉ cần kiểm tra với học kỳ chính.
            $query->where('type', TermType::MAIN->value);
        }

        if ($query->exists()) {
            $this->fail(
                'Thời gian học kỳ bị chồng với một học kỳ đã tồn tại.',
                'Vui lòng chọn khoảng thời gian không trùng với học kỳ khác trong năm học.'
            );
        }
    }

    /**
     * Lấy học kỳ hiện hành.
     *
     * Theo Issue #75, học kỳ hiện hành là kỳ chính đang diễn ra.
     */
    public function getCurrentTerm(): ?Term
    {
        return Term::query()
            ->where('type', TermType::MAIN->value)
            ->where('status', TermStatus::IN_PROGRESS->value)
            ->orderBy('start_date')
            ->first();
    }

    /**
     * Đặt học kỳ chính thành đang diễn ra.
     *
     * Không tự động chuyển kỳ cũ sang "Kết thúc".
     * Kỳ cũ phải đi qua state machine đúng quy trình.
     */
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

            $term->update([
                'status' => TermStatus::IN_PROGRESS->value,
            ]);

            return $term->fresh();
        });
    }

    /**
     * FR-ACY-006:
     * Chuyển trạng thái theo đúng state machine.
     *
     * Lưu ý:
     * Chuyển sang LOCKED không thực hiện ở ACY.
     * BR-ACY-06 yêu cầu việc khóa phải đi qua SYS.
     */
    public function changeStatus(
        Term $term,
        TermStatus $newStatus
    ): Term {
        $allowedTransitions = [
            TermStatus::PLANNED->value => [
                TermStatus::REGISTRATION->value,
            ],

            TermStatus::REGISTRATION->value => [
                TermStatus::IN_PROGRESS->value,
            ],

            TermStatus::IN_PROGRESS->value => [
                TermStatus::EXAM_GRADING->value,
            ],

            TermStatus::EXAM_GRADING->value => [
                TermStatus::COMPLETED->value,
            ],

            TermStatus::COMPLETED->value => [],

            TermStatus::LOCKED->value => [],
        ];

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

        $term->update([
            'status' => $newStatus->value,
        ]);

        return $term->fresh();
    }

    /**
     * Không cho xóa năm học nếu còn học kỳ liên quan.
     */
    public function deleteAcademicYear(AcademicYear $academicYear): void
    {
        if ($academicYear->terms()->exists()) {
            $this->fail(
                'Không thể xóa năm học vì đã có học kỳ liên quan.',
                'Hãy khóa năm học thay vì xóa dữ liệu đã phát sinh.'
            );
        }

        $academicYear->delete();
    }

    /**
     * Xóa học kỳ khi chưa có dữ liệu phụ thuộc.
     *
     * Hiện tại P1 chưa có các bảng ENR/TTB/GRD/EXM.
     * Khi các module đó được tạo, bổ sung kiểm tra quan hệ tại đây.
     */
    public function deleteTerm(Term $term): void
    {
        if ($term->status === TermStatus::LOCKED) {
            $this->fail(
                'Không thể xóa học kỳ đã khóa.',
                'Học kỳ đã khóa chỉ được mở khóa theo quy trình ngoại lệ của SYS.'
            );
        }

        $term->delete();
    }
}
