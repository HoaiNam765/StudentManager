<?php

namespace App\Modules\Timetable\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Enums\WeekParity;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mẫu lịch của lớp học phần (FR-TTB-002): thứ, tiết từ – đến, phòng, các tuần học.
 * Phạm vi dữ liệu theo lớp học phần (DEAN theo khoa, LEC lớp mình dạy).
 */
class ScheduleRule extends StandardModel implements HasDataScope
{
    public const DAYS = [1 => 'Thứ Hai', 2 => 'Thứ Ba', 3 => 'Thứ Tư', 4 => 'Thứ Năm', 5 => 'Thứ Sáu', 6 => 'Thứ Bảy', 7 => 'Chủ nhật'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'period_from' => 'integer',
            'period_to' => 'integer',
            'week_from' => 'integer',
            'week_to' => 'integer',
            'week_parity' => WeekParity::class,
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function periodCount(): int
    {
        return $this->period_to - $this->period_from + 1;
    }

    /** @return list<int> các tuần có học */
    public function weeks(): array
    {
        return array_values(array_filter(range($this->week_from, $this->week_to), fn (int $w) => $this->week_parity->includes($w)));
    }

    public function describe(): string
    {
        return self::DAYS[$this->day_of_week]." tiết {$this->period_from}–{$this->period_to}, tuần {$this->week_from}–{$this->week_to}"
            .($this->week_parity === WeekParity::All ? '' : ' ('.mb_strtolower($this->week_parity->label()).')');
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        $query->whereIn(
            $this->qualifyColumn('course_section_id'),
            tap(CourseSection::query()->select('id'), fn (Builder $q) => (new CourseSection)->applyDataScope($q, $scope, $user)),
        );
    }
}
