<?php

namespace App\Modules\Timetable\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Enums\SessionStatus;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Một buổi học cụ thể của lớp học phần (FR-TTB-005). Phạm vi dữ liệu theo lớp học phần. */
class ClassSession extends StandardModel implements HasDataScope
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date' => 'immutable_date',
            'week' => 'integer',
            'period_from' => 'integer',
            'period_to' => 'integer',
            'status' => SessionStatus::class,
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(ScheduleRule::class, 'schedule_rule_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function makeupFor(): BelongsTo
    {
        return $this->belongsTo(self::class, 'makeup_for_id');
    }

    public function makeups(): HasMany
    {
        return $this->hasMany(self::class, 'makeup_for_id');
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        $query->whereIn(
            $this->qualifyColumn('course_section_id'),
            tap(CourseSection::query()->select('id'), fn (Builder $q) => (new CourseSection)->applyDataScope($q, $scope, $user)),
        );
    }
}
