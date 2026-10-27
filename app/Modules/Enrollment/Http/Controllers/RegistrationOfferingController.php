<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Enums\RegistrationRound;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\OfferingLecturers;
use App\Modules\Enrollment\Services\RegistrationOfferingService;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;
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

    /** Giảng viên của khoa trong khung, kèm số tiết đã nhận trong học kỳ để chia lớp (chưa có lịch nên chưa xét trùng). */
    public function frameworkLecturers(TrainingFramework $trainingFramework, OfferingLecturers $availability, CourseSectionService $sections): JsonResponse
    {
        $limit = (int) config('studentmanager.course_section.max_periods_per_term', 400);

        return response()->json($availability->inFaculty($trainingFramework->faculty_id)->orderBy('full_name')->get()
            ->map(fn (Lecturer $lecturer) => $lecturer->only('id', 'code', 'full_name') + [
                'load' => $sections->loadInTerm($lecturer, $trainingFramework->term_id), 'limit' => $limit,
            ]));
    }

    public function storeFromFramework(Request $request, TrainingFramework $trainingFramework): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'round' => ['required', Rule::enum(RegistrationRound::class)],
            'opens_at' => ['required', 'date_format:Y-m-d H:i'],
            'closes_at' => ['required', 'date_format:Y-m-d H:i'],
            'min_credits' => ['nullable', 'integer', 'min:0', 'max:60'],
            'max_credits' => ['nullable', 'integer', 'min:1', 'max:60'],
            'classes' => ['required', 'array', 'min:1', 'max:200'],
            'classes.*.item_id' => ['required', 'integer'],
            'classes.*.lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
            'classes.*.max_size' => ['nullable', 'integer', 'min:1'],
            'confirm_overload' => ['sometimes', 'boolean'],
        ], [], [
            'name' => 'tên đợt', 'round' => 'vòng', 'opens_at' => 'thời gian mở', 'closes_at' => 'thời gian đóng',
            'min_credits' => 'tín chỉ tối thiểu', 'max_credits' => 'tín chỉ tối đa', 'classes' => 'lớp',
            'classes.*.lecturer_id' => 'giảng viên', 'classes.*.max_size' => 'sĩ số tối đa',
        ]);
        $period = $this->offerings->createFromFramework(
            $trainingFramework,
            array_intersect_key($data, array_flip(['name', 'round', 'opens_at', 'closes_at', 'min_credits', 'max_credits'])),
            $data['classes'],
            (bool) ($data['confirm_overload'] ?? false),
        );

        return response()->json([
            'id' => $period->id, 'term_id' => $period->term_id, 'name' => $period->name,
            'sections' => $period->sections()->count(),
        ], 201);
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
