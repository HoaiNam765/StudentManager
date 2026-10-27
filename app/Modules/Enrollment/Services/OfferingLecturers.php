<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Faculty\Models\Department;
use App\Modules\Room\Models\Room;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\PeriodService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/** Cùng một phép kiểm tra cho danh sách chọn giảng viên và lúc ghi lớp trong giao dịch. */
class OfferingLecturers
{
    public function inFaculty(int $facultyId): Builder
    {
        return Lecturer::query()->where('status', 'working')->where(fn ($q) => $q
            ->whereHas('department', fn ($q) => $q->where('faculty_id', $facultyId))
            ->orWhereHas('units', fn ($q) => $q->effectiveOn(now()->toDateString())->where(fn ($q) => $q
                ->where(fn ($q) => $q->where('unit_type', 'faculty')->where('unit_id', $facultyId))
                ->orWhere(fn ($q) => $q->where('unit_type', 'department')->whereIn('unit_id', Department::query()->where('faculty_id', $facultyId)->select('id'))))));
    }

    public function conflict(Lecturer $lecturer, Term $term, array $schedule, ?int $ignoreSection = null): ?string
    {
        $candidate = new ScheduleRule($schedule + ['week_parity' => 'all']);
        $dates = $this->dates($term, $candidate);
        [$from, $to] = $this->times($candidate);
        $others = ScheduleRule::query()->with('section.term', 'room.building')
            ->when($ignoreSection, fn ($q) => $q->where('course_section_id', '!=', $ignoreSection))
            ->whereHas('section', fn ($q) => $q->active()->whereHas('instructors', fn ($q) => $q->where('lecturer_id', $lecturer->id)))
            ->get();
        foreach ($others as $other) {
            [$otherFrom, $otherTo] = $this->times($other);
            if ($from < $otherTo && $otherFrom < $to && array_intersect($dates, $this->dates($other->section->term, $other)) !== []) {
                return "Giảng viên {$lecturer->full_name} trùng lịch lớp {$other->section->code}: {$other->describe()}.";
            }
        }

        return null;
    }

    private function dates(Term $term, ScheduleRule $rule): array
    {
        $monday = CarbonImmutable::parse($term->start_date)->startOfWeek();

        return array_map(fn ($week) => $monday->addWeeks($week - 1)->addDays($rule->day_of_week - 1)->toDateString(), $rule->weeks());
    }

    private function times(ScheduleRule $rule): array
    {
        $campus = $rule->room_id ? Room::query()->with('building')->find($rule->room_id)?->building?->campus_id : null;
        $periods = app(PeriodService::class)->periodsFor($campus);

        return [$periods->get($rule->period_from)?->startTime() ?? '00:00', $periods->get($rule->period_to)?->endTime() ?? '23:59'];
    }
}
