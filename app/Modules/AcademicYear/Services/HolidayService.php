<?php

namespace App\Modules\AcademicYear\Services;

use App\Modules\AcademicYear\Models\Holiday;
use App\Modules\AcademicYear\Models\Term;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

/**
 * Ngày nghỉ (FR-ACY-005) và lịch ngày nghỉ cho TTB:
 *
 *     $holidays->daysOff('2026-09-01', '2027-01-10', $term);   // ['2026-09-01', '2026-09-02', ...]
 *     $holidays->isDayOff('2026-09-02', $term);                // true → buổi học ngày này đánh dấu Nghỉ
 *
 * Ngày nghỉ đã qua giữ làm lịch sử: không sửa, không xóa (TTB đã dựa vào để sinh buổi học).
 */
class HolidayService extends BaseService
{
    /** @param  array{name: string, type: string, starts_on: string, ends_on: string, term_id?: ?int, note?: ?string}  $data */
    public function create(array $data): Holiday
    {
        $this->assertRange($data['starts_on'], $data['ends_on']);
        $this->assertWithinTerm($data);

        return Holiday::create($data);
    }

    /** @param  array{name?: string, type?: string, starts_on?: string, ends_on?: string, term_id?: ?int, note?: ?string}  $data */
    public function update(Holiday $holiday, array $data): Holiday
    {
        $this->assertNotPast($holiday);

        $merged = array_merge([
            'starts_on' => $holiday->starts_on->toDateString(),
            'ends_on' => $holiday->ends_on->toDateString(),
            'term_id' => $holiday->term_id,
        ], $data);

        $this->assertRange($merged['starts_on'], $merged['ends_on']);
        $this->assertWithinTerm($merged);

        $holiday->update($data);

        return $holiday->refresh();
    }

    public function delete(Holiday $holiday): void
    {
        $this->assertNotPast($holiday);

        $holiday->delete();
    }

    /** @return list<string> các ngày nghỉ (Y-m-d) trong [$from, $to], gồm nghỉ toàn trường và nghỉ riêng của học kỳ */
    public function daysOff(CarbonInterface|string $from, CarbonInterface|string $to, ?Term $term = null): array
    {
        $start = CarbonImmutable::parse($from)->toDateString();
        $end = CarbonImmutable::parse($to)->toDateString();
        $days = [];

        $holidays = Holiday::query()->applicableTo($term?->id)->overlapping($start, $end)->get();

        foreach ($holidays as $holiday) {
            $first = max($start, $holiday->starts_on->toDateString());
            $last = min($end, $holiday->ends_on->toDateString());

            foreach (CarbonPeriod::create($first, $last) as $day) {
                $days[$day->toDateString()] = true;
            }
        }

        $days = array_keys($days);
        sort($days);

        return $days;
    }

    public function isDayOff(CarbonInterface|string $date, ?Term $term = null): bool
    {
        return $this->daysOff($date, $date, $term) !== [];
    }

    private function assertRange(string $startsOn, string $endsOn): void
    {
        if (CarbonImmutable::parse($endsOn)->lt(CarbonImmutable::parse($startsOn))) {
            $this->fail('Ngày kết thúc nghỉ phải sau hoặc bằng ngày bắt đầu.', 'Chọn lại khoảng ngày nghỉ.');
        }
    }

    /** @param  array<string, mixed>  $data */
    private function assertWithinTerm(array $data): void
    {
        if (empty($data['term_id'])) {
            return;
        }

        $term = Term::query()->findOrFail($data['term_id']);

        if ($data['starts_on'] < $term->start_date->toDateString() || $data['ends_on'] > $term->end_date->toDateString()) {
            $this->fail(
                "Ngày nghỉ riêng của học kỳ phải nằm trong {$term->name} (".$term->start_date->format('d/m/Y').' – '.$term->end_date->format('d/m/Y').').',
                'Chọn lại ngày, hoặc bỏ chọn học kỳ để áp dụng toàn trường.'
            );
        }
    }

    private function assertNotPast(Holiday $holiday): void
    {
        if ($holiday->starts_on->toDateString() <= now(config('studentmanager.display_timezone'))->toDateString()) {
            $this->fail('Ngày nghỉ đã bắt đầu hoặc đã qua nên không sửa, không xóa được.', 'Ngày nghỉ đã qua được giữ làm lịch sử vì thời khóa biểu đã dựa vào; thêm ngày nghỉ mới nếu cần.');
        }
    }
}
