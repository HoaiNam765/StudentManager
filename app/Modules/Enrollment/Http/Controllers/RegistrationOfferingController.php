<?php

namespace App\Modules\Enrollment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\OfferingLecturers;
use App\Modules\Enrollment\Services\RegistrationOfferingService;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Models\TrainingFrameworkItem;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Enums\WeekParity;
use App\Modules\Timetable\Models\ScheduleRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Đợt đăng ký theo chương trình khung (cả học kỳ): bảng Khoa × Khóa (chọn kỳ CTĐT từng ô), lớp + giảng viên của khoa
 * từng khung, lịch học từng lớp.
 */
class RegistrationOfferingController extends Controller
{
    public function __construct(private readonly RegistrationOfferingService $offerings) {}

    public function index(RegistrationPeriod $registrationPeriod): JsonResponse
    {
        abort_unless($registrationPeriod->usesFrameworks(), 404);
        $period = $registrationPeriod->load('term');
        $matrix = $this->offerings->matrix($period);
        $sections = $period->sections()->with('subject', 'instructors.lecturer', 'trainingFrameworkItem:id,training_framework_id')
            ->where('status', '!=', 'cancelled')->orderBy('code')->get();
        $rules = ScheduleRule::query()->with('room')->whereIn('course_section_id', $sections->pluck('id'))->get()->groupBy('course_section_id');
        $frameworks = TrainingFramework::query()->with('items.subject', 'faculty', 'cohort')
            ->whereIn('id', collect($matrix['cells'])->pluck('framework.id')->filter())->get()->keyBy('id');

        return response()->json([
            'period_id' => $period->id, 'editable' => $period->status->value === 'draft',
            'term' => $period->term->only('id', 'name', 'weeks'),
            'faculties' => collect($matrix['faculties'])->map(fn ($f) => $f->only('id', 'code', 'name'))->values(),
            'cohorts' => collect($matrix['cohorts'])->map(fn ($c) => $c->only('id', 'code', 'start_year'))->values(),
            'cells' => collect($matrix['cells'])->map(fn (array $c) => [
                'faculty_id' => $c['faculty']->id, 'cohort_id' => $c['cohort']->id, 'students' => $c['students'],
                'suggested_term' => $c['suggested_term'], 'planned_term' => $c['planned_term'],
                'framework_id' => $c['framework']?->id, 'items' => $c['items'], 'sections' => $c['sections'], 'unscheduled' => $c['unscheduled'],
            ])->values(),
            'frameworks' => $frameworks->values()->map(fn (TrainingFramework $f) => [
                'id' => $f->id, 'faculty' => $f->faculty->only('id', 'code', 'name'), 'cohort' => $f->cohort->only('id', 'code'),
                'planned_term' => $f->planned_term,
                'items' => $f->items->sortBy(fn (TrainingFrameworkItem $i) => [! $i->compulsory, $i->subject->code])->values()->map(fn (TrainingFrameworkItem $i) => [
                    'id' => $i->id, 'subject' => $i->subject->only('id', 'code', 'name', 'credits'), 'compulsory' => $i->compulsory,
                    'planned_sections' => $i->planned_sections, 'max_size' => $i->max_size,
                ]),
            ]),
            'sections' => $sections->map(fn ($section) => [
                'id' => $section->id, 'code' => $section->code, 'subject' => $section->subject->only('id', 'code', 'name', 'credits'),
                'framework_id' => $section->trainingFrameworkItem?->training_framework_id,
                'min_size' => $section->min_size, 'max_size' => $section->max_size, 'section_type' => $section->section_type,
                'lecturer' => $section->instructors->first()?->lecturer?->only('id', 'code', 'full_name'),
                'schedules' => ($rules->get($section->id) ?? collect())->map(fn ($rule) => $rule->only('day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'week_parity', 'room_id') + ['description' => $rule->describe(), 'room' => $rule->room?->code]),
            ]),
            'subjects' => Subject::query()->where('status', 'active')->orderBy('code')->get(['id', 'code', 'name', 'credits']),
            'rooms' => Room::query()->orderBy('code')->get(['id', 'code', 'capacity']),
            'default_max_size' => (int) config('studentmanager.course_section.default_max_size', 60),
        ]);
    }

    /** Chọn kỳ CTĐT cho ô khoa – khóa (tạo / dùng khung, môn theo CTĐT, gắn vào đợt). */
    public function setTerm(Request $request, RegistrationPeriod $registrationPeriod): JsonResponse
    {
        $data = $request->validate([
            'faculty_id' => ['required', 'integer', 'exists:faculties,id'],
            'cohort_id' => ['required', 'integer', 'exists:cohorts,id'],
            'planned_term' => ['required', 'integer', 'between:1,12'],
        ], [], ['faculty_id' => 'khoa', 'cohort_id' => 'khóa', 'planned_term' => 'kỳ']);
        $framework = $this->offerings->setTerm($registrationPeriod, $data['faculty_id'], $data['cohort_id'], $data['planned_term']);

        return response()->json(['framework_id' => $framework->id, 'items' => $framework->items()->count()]);
    }

    public function detach(RegistrationPeriod $registrationPeriod, TrainingFramework $trainingFramework): JsonResponse
    {
        $this->offerings->detach($registrationPeriod, $trainingFramework);

        return response()->json(['detached' => true]);
    }

    /** Mở lớp cho môn của một khung trong đợt: số lớp, sĩ số, giảng viên của khoa từng lớp. */
    public function addClasses(Request $request, RegistrationPeriod $registrationPeriod, TrainingFramework $trainingFramework): JsonResponse
    {
        $data = $request->validate([
            'classes' => ['required', 'array', 'min:1', 'max:200'],
            'classes.*.item_id' => ['required', 'integer'],
            'classes.*.lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
            'classes.*.max_size' => ['nullable', 'integer', 'min:1'],
            'confirm_overload' => ['sometimes', 'boolean'],
        ], [], ['classes' => 'lớp', 'classes.*.lecturer_id' => 'giảng viên', 'classes.*.max_size' => 'sĩ số tối đa']);
        $opened = $this->offerings->addClasses($registrationPeriod, $trainingFramework, $data['classes'], (bool) ($data['confirm_overload'] ?? false));

        return response()->json(['opened' => collect($opened)->map(fn (CourseSection $s) => $s->only('id', 'code'))->values()], 201);
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

    /** Giảng viên của khoa (khung của lớp) còn trống lịch đã chọn. */
    public function lecturers(Request $request, RegistrationPeriod $registrationPeriod, OfferingLecturers $availability): JsonResponse
    {
        abort_unless($registrationPeriod->usesFrameworks(), 404);
        $data = $request->validate($this->scheduleRules() + [
            'framework_id' => ['required', 'integer'],
            'ignore_section_id' => ['nullable', 'integer'],
        ]);
        $framework = $registrationPeriod->frameworks()->whereKey($data['framework_id'])->firstOrFail();
        $rows = $availability->inFaculty($framework->faculty_id)->orderBy('full_name')->get();

        return response()->json($rows->map(function ($lecturer) use ($availability, $registrationPeriod, $data) {
            $reason = $availability->conflict($lecturer, $registrationPeriod->term, $data['schedule'], $data['ignore_section_id'] ?? null);

            return $lecturer->only('id', 'code', 'full_name') + ['available' => $reason === null, 'reason' => $reason];
        }));
    }

    public function store(Request $request, RegistrationPeriod $registrationPeriod): JsonResponse
    {
        $section = $this->offerings->save($registrationPeriod, $this->validated($request, true));

        return response()->json(['id' => $section->id, 'code' => $section->code], 201);
    }

    public function update(Request $request, RegistrationPeriod $registrationPeriod, CourseSection $courseSection): JsonResponse
    {
        $section = $this->offerings->save($registrationPeriod, $this->validated($request, false), $courseSection);

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

    private function validated(Request $request, bool $creating): array
    {
        return $request->validate($this->scheduleRules() + [
            'framework_id' => [$creating ? 'required' : 'sometimes', 'integer'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'lecturer_id' => ['required', 'integer', 'exists:lecturers,id'],
            'min_size' => ['required', 'integer', 'min:1'], 'max_size' => ['required', 'integer', 'min:1'],
            'section_type' => ['sometimes', Rule::in(['theory', 'practice', 'mixed'])],
            'confirm_overload' => ['sometimes', 'boolean'],
        ], [], ['framework_id' => 'khoa – khóa', 'subject_id' => 'học phần', 'lecturer_id' => 'giảng viên']);
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
