<?php

namespace App\Modules\Timetable\Services;

use App\Modules\Timetable\Enums\Shift;
use App\Modules\Timetable\Models\Period;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Support\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Khung tiết học (FR-TTB-001): dùng chung hoặc riêng từng cơ sở. Cơ sở nào đã khai báo khung riêng thì dùng khung đó,
 * không thì dùng khung chung. Số tiết không trùng trong cùng khung, giờ không chồng nhau và tăng dần theo số tiết.
 * Tiết đã có lịch học thì không đổi số tiết / ca, không xóa; cơ sở đã có lịch thì không tạo khung riêng.
 */
class PeriodService extends BaseService
{
    /** @return Collection<int, Period> khung tiết áp dụng cho cơ sở (khóa = số tiết) */
    public function periodsFor(?int $campusId): Collection
    {
        if ($campusId !== null && Period::query()->where('campus_id', $campusId)->exists()) {
            return Period::query()->where('campus_id', $campusId)->orderBy('number')->get()->keyBy('number');
        }

        return Period::query()->whereNull('campus_id')->orderBy('number')->get()->keyBy('number');
    }

    /** @param array{campus_id?: ?int, number: int, starts_at: string, ends_at: string, shift: string} $data */
    public function create(array $data): Period
    {
        $data = $this->normalize($data);

        // Tiết riêng đầu tiên của một cơ sở sẽ thay khung chung cho cả cơ sở: không làm khi cơ sở đã có lịch học
        $startsCampusFrame = $data['campus_id'] !== null && ! Period::query()->where('campus_id', $data['campus_id'])->exists();

        if ($startsCampusFrame && $this->rulesAt($data['campus_id'])->exists()) {
            $this->fail(
                'Cơ sở này đã có lớp xếp lịch theo khung tiết chung; tạo khung riêng sẽ làm sai các lịch đó.',
                'Sửa khung tiết chung, hoặc khai báo khung riêng trước khi xếp lịch cho cơ sở.'
            );
        }

        $this->assertFits($data);

        return Period::create($data);
    }

    /** @param array{campus_id?: ?int, number?: int, starts_at?: string, ends_at?: string, shift?: string} $data */
    public function update(Period $period, array $data): Period
    {
        $data = $this->normalize([
            'campus_id' => array_key_exists('campus_id', $data) ? $data['campus_id'] : $period->campus_id,
            'number' => $data['number'] ?? $period->number,
            'starts_at' => $data['starts_at'] ?? $period->startTime(),
            'ends_at' => $data['ends_at'] ?? $period->endTime(),
            'shift' => $data['shift'] ?? $period->shift->value,
        ]);

        $structural = $data['number'] !== $period->number || $data['shift'] !== $period->shift->value || $data['campus_id'] !== $period->campus_id;

        if ($structural && $this->isUsed($period)) {
            $this->fail(
                "Tiết {$period->number} đã có lớp học phần xếp lịch nên không đổi số tiết, ca hoặc cơ sở được.",
                'Chỉ sửa giờ bắt đầu – kết thúc, hoặc khai báo tiết mới.'
            );
        }

        $this->assertFits($data, $period->id);
        $period->update($data);

        return $period->refresh();
    }

    public function delete(Period $period): void
    {
        if ($this->isUsed($period)) {
            $this->fail("Tiết {$period->number} đã có lớp học phần xếp lịch nên không xóa được.", 'Chuyển các lịch học sang tiết khác trước khi xóa.');
        }

        $period->delete();
    }

    /** Có dòng lịch dùng số tiết này: tiết chung tính mọi cơ sở (an toàn), tiết riêng chỉ tính cơ sở của nó. */
    private function isUsed(Period $period): bool
    {
        return $this->rulesAt($period->campus_id)
            ->where('period_from', '<=', $period->number)
            ->where('period_to', '>=', $period->number)
            ->exists();
    }

    /** Dòng lịch ở một cơ sở (null = mọi dòng lịch). */
    private function rulesAt(?int $campusId): Builder
    {
        return ScheduleRule::query()->when($campusId !== null, fn (Builder $q) => $q->whereHas(
            'room.building',
            fn (Builder $b) => $b->where('campus_id', $campusId),
        ));
    }

    /** @return array{campus_id: ?int, number: int, starts_at: string, ends_at: string, shift: string} */
    private function normalize(array $data): array
    {
        return [
            'campus_id' => isset($data['campus_id']) ? (int) $data['campus_id'] : null,
            'number' => (int) $data['number'],
            'starts_at' => substr($data['starts_at'], 0, 5),
            'ends_at' => substr($data['ends_at'], 0, 5),
            'shift' => $data['shift'] instanceof Shift ? $data['shift']->value : $data['shift'],
        ];
    }

    private function assertFits(array $data, ?int $ignoreId = null): void
    {
        if ($data['starts_at'] >= $data['ends_at']) {
            $this->fail("Tiết {$data['number']}: giờ bắt đầu {$data['starts_at']} phải trước giờ kết thúc {$data['ends_at']}.", 'Nhập lại giờ theo dạng HH:MM.');
        }

        $others = Period::query()
            ->when($data['campus_id'] === null, fn ($q) => $q->whereNull('campus_id'), fn ($q) => $q->where('campus_id', $data['campus_id']))
            ->when($ignoreId !== null, fn ($q) => $q->whereKeyNot($ignoreId))
            ->get();

        foreach ($others as $other) {
            if ($other->number === $data['number']) {
                $this->fail("Khung tiết này đã có tiết {$data['number']}.", 'Sửa tiết đã có hoặc dùng số tiết khác.');
            }

            if ($data['starts_at'] < $other->endTime() && $other->startTime() < $data['ends_at']) {
                $this->fail(
                    "Tiết {$data['number']} ({$data['starts_at']}–{$data['ends_at']}) chồng giờ với tiết {$other->number} ({$other->startTime()}–{$other->endTime()}).",
                    'Chỉnh giờ để các tiết không chồng nhau.'
                );
            }

            if (($other->number < $data['number']) !== ($other->startTime() < $data['starts_at'])) {
                $this->fail(
                    "Tiết {$data['number']} bắt đầu lúc {$data['starts_at']} không đúng thứ tự so với tiết {$other->number} ({$other->startTime()}).",
                    'Số tiết phải tăng dần theo giờ trong ngày.'
                );
            }
        }
    }
}
