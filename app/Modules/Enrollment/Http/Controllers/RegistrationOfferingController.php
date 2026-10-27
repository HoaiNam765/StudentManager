<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\OfferingLecturers;
use App\Modules\Enrollment\Services\RegistrationOfferingService;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Subject\Models\Subject;
use App\Modules\Timetable\Enums\WeekParity;
use App\Modules\Timetable\Models\ScheduleRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistrationOfferingController extends Controller
{
    public function __construct(private readonly RegistrationOfferingService $offerings) {}

    public function index(RegistrationPeriod $registrationPeriod): JsonResponse
    {
        $period = $registrationPeriod->load('framework.items.subject', 'framework.faculty', 'framework.cohort', 'term');
        abort_if($period->framework === null, 404);
        $sections = $period->sections()->with('subject', 'instructors.lecturer')->where('status', '!=', 'cancelled')->orderBy('code')->get();
        $rules = ScheduleRule::query()->with('room')->whereIn('course_section_id', $sections->pluck('id'))->get()->groupBy('course_section_id');

        return response()->json([
            'period_id' => $period->id, 'editable' => $period->status->value === 'draft',
            'framework' => [
                'id' => $period->framework->id, 'faculty' => $period->framework->faculty->name,
                'cohort' => $period->framework->cohort->code,
                'items' => $period->framework->items->map(fn ($item) => ['subject' => $item->subject->only('id', 'code', 'name', 'credits'), 'max_size' => $item->max_size]),
            ],
            'term' => $period->term->only('id', 'name', 'weeks'),
            'sections' => $sections->map(fn ($section) => [
                'id' => $section->id, 'code' => $section->code, 'subject' => $section->subject->only('id', 'code', 'name', 'credits'),
                'min_size' => $section->min_size, 'max_size' => $section->max_size, 'section_type' => $section->section_type,
                'lecturer' => $section->instructors->first()?->lecturer?->only('id', 'code', 'full_name'),
                'schedules' => ($rules->get($section->id) ?? collect())->map(fn ($rule) => $rule->only('day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'week_parity', 'room_id') + ['description' => $rule->describe(), 'room' => $rule->room?->code]),
            ]),
            'subjects' => Subject::query()->where('status', 'active')->orderBy('code')->get(['id', 'code', 'name', 'credits']),
            'rooms' => Room::query()->orderBy('code')->get(['id', 'code', 'capacity']),
        ]);
    }

    public function lecturers(Request $request, RegistrationPeriod $registrationPeriod, OfferingLecturers $availability): JsonResponse
    {
        abort_if($registrationPeriod->framework === null, 404);
        $data = $request->validate($this->scheduleRules() + ['ignore_section_id' => ['nullable', 'integer']]);
        $rows = $availability->inFaculty($registrationPeriod->framework->faculty_id)->orderBy('full_name')->get();

        return response()->json($rows->map(function ($lecturer) use ($availability, $registrationPeriod, $data) {
            $reason = $availability->conflict($lecturer, $registrationPeriod->term, $data['schedule'], $data['ignore_section_id'] ?? null);

            return $lecturer->only('id', 'code', 'full_name') + ['available' => $reason === null, 'reason' => $reason];
        }));
    }

    public function store(Request $request, RegistrationPeriod $registrationPeriod): JsonResponse
    {
        $section = $this->offerings->save($registrationPeriod, $this->validated($request));

        return response()->json(['id' => $section->id, 'code' => $section->code], 201);
    }

    public function update(Request $request, RegistrationPeriod $registrationPeriod, CourseSection $courseSection): JsonResponse
    {
        $section = $this->offerings->save($registrationPeriod, $this->validated($request), $courseSection);

        return response()->json(['id' => $section->id, 'code' => $section->code]);
    }

    public function destroy(RegistrationPeriod $registrationPeriod, CourseSection $courseSection): JsonResponse
    {
        $this->offerings->delete($registrationPeriod, $courseSection);

        return response()->json(['deleted' => true]);
    }

    public function announce(Request $request, RegistrationPeriod $registrationPeriod): JsonResponse
    {
        $period = $this->offerings->announce($registrationPeriod, $request->user());

        return response()->json(['preview_at' => $period->preview_at]);
    }

    private function validated(Request $request): array
    {
        return $request->validate($this->scheduleRules() + [
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
            'min_size' => ['required', 'integer', 'min:1'], 'max_size' => ['required', 'integer', 'min:1'],
            'section_type' => ['sometimes', Rule::in(['theory', 'practice', 'mixed'])],
            'confirm_overload' => ['sometimes', 'boolean'],
        ]);
    }

    private function scheduleRules(): array
    {
        return [
            'schedule' => ['required', 'array'],
            'schedule.day_of_week' => ['required', 'integer', 'between:1,7'],
            'schedule.period_from' => ['required', 'integer', 'between:1,20'],
            'schedule.period_to' => ['required', 'integer', 'between:1,20', 'gte:schedule.period_from'],
            'schedule.week_from' => ['required', 'integer', 'between:1,53'],
            'schedule.week_to' => ['required', 'integer', 'between:1,53', 'gte:schedule.week_from'],
            'schedule.week_parity' => ['required', Rule::enum(WeekParity::class)],
            'schedule.room_id' => ['required', 'integer', 'exists:rooms,id'],
        ];
    }
}
