<?php

namespace App\Modules\System\Services;

use App\Modules\System\Models\PolicySet;
use App\Modules\System\Settings\PolicyDefinitions;
use App\Support\Exceptions\BusinessRuleException;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Tra cứu ngưỡng quy chế theo khóa của sinh viên và ngày (FR-SYS-003, BR-SYS-02, GC-12):
 *
 *     $resolver->value('attendance.ban_percent', cohort: 2026, date: '2026-12-01');   // 20
 *     $resolver->setFor(2025)->code;                                                  // bộ đang áp dụng cho K2025 hôm nay
 *
 * Quy tắc chọn: trong các bộ đã ban hành có phạm vi khóa chứa $cohort và ngày hiệu lực không sau $date,
 * lấy bộ có ngày hiệu lực gần nhất (cùng ngày thì phiên bản cao hơn). Nhờ vậy bộ mới cho K2026 từ ngày D
 * không ảnh hưởng K2025, và trước ngày D mọi khóa vẫn dùng bộ cũ.
 *
 * Kết quả được nhớ trong một yêu cầu (đăng ký `scoped`).
 */
class PolicyResolver
{
    /** @var array<string, PolicySet> */
    private array $cache = [];

    public function setFor(int $cohort, CarbonInterface|string|null $date = null): PolicySet
    {
        $day = $this->day($date);
        $cacheKey = "{$cohort}|{$day}";

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $set = PolicySet::query()
            ->published()
            ->forCohort($cohort)
            ->whereDate('effective_from', '<=', $day)
            ->orderByDesc('effective_from')
            ->orderByDesc('version')
            ->with('items')
            ->first();

        if ($set === null) {
            throw new BusinessRuleException(
                "Chưa có bộ quy chế đào tạo áp dụng cho khóa K{$cohort} vào ngày ".Carbon::parse($day)->format('d/m/Y').'.',
                'Quản trị viên hoặc phòng Đào tạo cần ban hành bộ quy chế cho khóa này (Quản trị → Bộ quy chế).'
            );
        }

        return $this->cache[$cacheKey] = $set;
    }

    /** Giá trị một tham số; bộ đã ban hành trước khi có tham số này thì dùng giá trị mặc định. */
    public function value(string $key, int $cohort, CarbonInterface|string|null $date = null): mixed
    {
        if (! array_key_exists($key, PolicyDefinitions::all())) {
            throw new BusinessRuleException("Không có tham số quy chế \"{$key}\".", 'Kiểm tra tên tham số trong App\Modules\System\Settings\PolicyDefinitions.');
        }

        $values = $this->setFor($cohort, $date)->values();

        return array_key_exists($key, $values) ? $values[$key] : PolicyDefinitions::all()[$key]['default'];
    }

    /** @return array<string, mixed> mọi tham số đang áp dụng cho khóa vào ngày */
    public function values(int $cohort, CarbonInterface|string|null $date = null): array
    {
        return array_merge(PolicyDefinitions::defaults(), $this->setFor($cohort, $date)->values());
    }

    public function flush(): void
    {
        $this->cache = [];
    }

    private function day(CarbonInterface|string|null $date): string
    {
        return $date === null
            ? now(config('studentmanager.display_timezone'))->toDateString()
            : Carbon::parse($date)->toDateString();
    }
}
