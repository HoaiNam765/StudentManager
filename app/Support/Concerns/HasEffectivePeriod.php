<?php

namespace App\Support\Concerns;

use App\Support\Exceptions\BusinessRuleException;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Dữ liệu có hiệu lực theo thời gian (GC-06): nhiệm kỳ, khung giá, bộ quy chế…
 * Không ghi đè lịch sử: khi thay đổi thì đóng bản ghi cũ (đặt `effective_to`)
 * rồi tạo bản ghi mới (`supersedeWith`).
 *
 * Bảng cần có cột `effective_from`, `effective_to`: dùng `$table->effectivePeriod()` trong migration.
 * Lưu ý: ràng buộc unique của bảng loại này phải gồm cả `effective_from`.
 */
trait HasEffectivePeriod
{
    public function initializeHasEffectivePeriod(): void
    {
        $this->mergeCasts([
            'effective_from' => 'date',
            'effective_to' => 'date',
        ]);
    }

    /** Các bản ghi đang có hiệu lực vào ngày $date (mặc định: hôm nay theo giờ Việt Nam). */
    public function scopeEffectiveOn(Builder $query, CarbonInterface|string|null $date = null): Builder
    {
        $day = $date === null
            ? now(config('studentmanager.display_timezone'))->toDateString()
            : Carbon::parse($date)->toDateString();

        $from = $this->qualifyColumn('effective_from');
        $to = $this->qualifyColumn('effective_to');

        return $query
            ->whereDate($from, '<=', $day)
            ->where(function (Builder $q) use ($to, $day): void {
                $q->whereNull($to)->orWhereDate($to, '>=', $day);
            });
    }

    /**
     * Thay bản ghi hiện tại bằng bản mới có hiệu lực từ ngày $from.
     * Bản cũ được đóng lại vào ngày hôm trước; lịch sử vẫn còn nguyên.
     *
     * @param  array<string, mixed>  $attributes  Các trường thay đổi so với bản cũ
     */
    public function supersedeWith(array $attributes, CarbonInterface|string $from): static
    {
        $start = Carbon::parse($from)->startOfDay();

        if ($this->effective_from !== null && $start->lessThanOrEqualTo($this->effective_from)) {
            throw new BusinessRuleException(
                'Ngày hiệu lực mới phải sau ngày hiệu lực của bản hiện tại.',
                'Hãy chọn một ngày sau '.$this->effective_from->format('d/m/Y').'.'
            );
        }

        return DB::transaction(function () use ($attributes, $start): static {
            $this->forceFill(['effective_to' => $start->copy()->subDay()->toDateString()])->save();

            $new = $this->replicate(['created_by', 'updated_by', 'deleted_at']);
            $new->forceFill($attributes + [
                'effective_from' => $start->toDateString(),
                'effective_to' => null,
            ])->save();

            return $new;
        });
    }
}
