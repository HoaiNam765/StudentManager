<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\PeriodService;
use App\Modules\Timetable\Services\SectionStartDates;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

/**
 * Kết quả đăng ký, thời khóa biểu cá nhân, phiếu đăng ký PDF (FR-ENR-010) và theo dõi – thống kê (FR-ENR-013).
 */
class EnrollmentReport
{
    public function __construct(
        private readonly PeriodService $periods,
        private readonly CreditLimits $limits,
        private readonly SectionStartDates $startDates,
    ) {}

    /** Học kỳ mặc định: học kỳ có đợt đăng ký mới nhất (đã công bố), không có thì học kỳ mới nhất. */
    public function defaultTerm(): ?Term
    {
        $termId = RegistrationPeriod::query()->where('status', '!=', PeriodStatus::Draft)->orderByDesc('opens_at')->value('term_id');

        return $termId ? Term::query()->find($termId) : Term::query()->orderByDesc('start_date')->first();
    }

    /**
     * Học kỳ sinh viên có đăng ký (còn hiệu lực hoặc đã rút) cùng học kỳ mặc định, mới nhất trước, nhóm theo năm học:
     * `[{id, name, terms: [{id, name}]}]`.
     *
     * @return list<array<string, mixed>>
     */
    public function terms(Student $student): array
    {
        $ids = Enrollment::query()->where('student_id', $student->id)
            ->whereIn('status', [...EnrollmentStatus::activeValues(), EnrollmentStatus::Withdrawn->value])
            ->distinct()->pluck('term_id')
            ->push($this->defaultTerm()?->id)
            ->filter()->unique();

        return Term::query()->with('academicYear:id,name')->whereIn('id', $ids)->orderByDesc('start_date')->get()
            ->groupBy('academic_year_id')
            ->map(fn (Collection $terms) => [
                'id' => $terms->first()->academicYear?->id,
                'name' => $terms->first()->academicYear?->name,
                'terms' => $terms->map(fn (Term $term) => $term->only('id', 'name'))->values()->all(),
            ])
            ->values()->all();
    }

    /**
     * Kết quả đăng ký của sinh viên trong học kỳ: các học phần (còn hiệu lực và đã rút), tổng tín chỉ, thời khóa biểu tuần.
     *
     * @return array<string, mixed>
     */
    public function result(Student $student, Term $term): array
    {
        $term->loadMissing('academicYear');
        $enrollments = Enrollment::query()
            ->with(['subject:id,code,name,credits', 'section:id,code,room_id'])
            ->where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->whereIn('status', [...EnrollmentStatus::activeValues(), EnrollmentStatus::Withdrawn->value])
            ->get()
            ->sortBy(fn (Enrollment $e) => $e->subject->code)
            ->values();

        $active = $enrollments->filter(fn (Enrollment $e) => $e->status->isActive());
        $instructors = SectionInstructor::query()->with('lecturer:id,full_name')
            ->whereIn('course_section_id', $enrollments->pluck('course_section_id'))->get()->groupBy('course_section_id');
        $rules = ScheduleRule::query()->with('room.building')
            ->whereIn('course_section_id', $active->pluck('course_section_id'))->get()->groupBy('course_section_id');
        $period = RegistrationPeriod::query()->where('term_id', $term->id)->where('status', '!=', PeriodStatus::Draft)->orderByDesc('opens_at')->first();
        $starts = $this->startDates->for($enrollments->pluck('course_section_id'));

        return [
            'student' => [
                'id' => $student->id,
                'student_code' => $student->student_code,
                'full_name' => $student->full_name,
                'major' => $student->major?->name,
                'cohort' => $student->cohort,
            ],
            'term' => [
                'id' => $term->id,
                'name' => $term->name,
                'academic_year' => $term->academicYear?->name,
                'start_date' => $term->start_date->toDateString(),
                'end_date' => $term->end_date->toDateString(),
            ],
            'total_credits' => $active->sum(fn (Enrollment $e) => (int) $e->subject->credits),
            'credit_limits' => $period ? $this->limits->for($student, $period) : null,
            'items' => $enrollments->map(fn (Enrollment $e) => [
                'id' => $e->id,
                'subject' => $e->subject->only('code', 'name', 'credits'),
                'section' => $e->section->code,
                'type_label' => Enrollment::TYPES[$e->type] ?? $e->type,
                'status' => $e->status->value,
                'status_label' => $e->status->label(),
                'counts_for_gpa' => $e->status !== EnrollmentStatus::Withdrawn,
                'instructors' => ($instructors->get($e->course_section_id) ?? collect())->map(fn (SectionInstructor $i) => $i->lecturer->full_name)->values(),
                'schedule' => ($rules->get($e->course_section_id) ?? collect())->map(fn (ScheduleRule $r) => $r->describe().($r->room ? " – phòng {$r->room->code}" : ''))->values(),
                'starts_on' => ($starts[$e->course_section_id] ?? null)?->toDateString(),
                'registered_at' => $e->registered_at->setTimezone(config('studentmanager.display_timezone'))->format('d/m/Y H:i'),
            ])->all(),
            'timetable' => $this->timetable($active, $rules),
        ];
    }

    /** Phiếu đăng ký học phần PDF (FR-ENR-010): phông Unicode cấu hình `studentmanager.export.pdf_font`. */
    public function slipPdf(Student $student, Term $term): string
    {
        $data = $this->result($student, $term);
        $data['school'] = config('studentmanager.school.name');
        $data['printed_at'] = now(config('studentmanager.display_timezone'))->format('H:i d/m/Y');
        $data['font'] = config('studentmanager.export.pdf_font');

        return Pdf::loadView('pdf.enrollment-slip', $data)->setPaper('a4')->setOptions([
            'defaultFont' => $data['font'],
            'isRemoteEnabled' => false,
            'isPhpEnabled' => false,
        ])->output();
    }

    /**
     * Thống kê đăng ký của học kỳ (FR-ENR-013): từng lớp (đã đăng ký / tối thiểu / tối đa, đầy hoặc thiếu), sinh viên
     * Đang học chưa đăng ký hoặc dưới mức tín chỉ tối thiểu.
     *
     * @return array<string, mixed>
     */
    public function stats(Term $term, ?callable $sectionScope = null): array
    {
        $sections = CourseSection::query()
            ->with('subject:id,code,name,credits')
            ->where('term_id', $term->id)
            ->where('status', '!=', SectionStatus::Cancelled)
            ->when($sectionScope !== null, $sectionScope)
            ->orderBy('code')
            ->get();
        $counts = Enrollment::query()->active()->whereIn('course_section_id', $sections->pluck('id'))
            ->groupBy('course_section_id')->selectRaw('course_section_id, COUNT(*) as n')->pluck('n', 'course_section_id');
        $sections->each(fn (CourseSection $s) => $s->setAttribute('enrolled', (int) ($counts[$s->id] ?? 0)));

        $sectionRows = $sections->map(fn (CourseSection $s) => [
            'id' => $s->id,
            'code' => $s->code,
            'subject' => $s->subject->only('code', 'name'),
            'status_label' => $s->status->label(),
            'enrolled' => $s->enrolled,
            'min_size' => $s->min_size,
            'max_size' => $s->max_size,
            'state' => $s->enrolled >= $s->max_size ? 'full' : ($s->enrolled < $s->min_size ? 'under_min' : 'ok'),
        ]);

        $period = RegistrationPeriod::query()->where('term_id', $term->id)->where('status', '!=', PeriodStatus::Draft)->orderByDesc('opens_at')->first();
        $credits = Enrollment::query()->active()->where('term_id', $term->id)
            ->join('subjects', 'subjects.id', '=', 'enrollments.subject_id')
            ->groupBy('enrollments.student_id')
            ->selectRaw('enrollments.student_id, SUM(subjects.credits) as credits')
            ->pluck('credits', 'student_id');

        $notRegistered = [];
        $underMin = [];

        foreach (Student::query()->where('status', StudentStatus::Studying)->orderBy('student_code')->get() as $student) {
            $total = (int) ($credits[$student->id] ?? 0);
            $row = ['id' => $student->id, 'student_code' => $student->student_code, 'full_name' => $student->full_name, 'credits' => $total];

            if ($total === 0) {
                $notRegistered[] = $row;

                continue;
            }

            $min = $period ? $this->limits->for($student, $period)['min'] : 0;

            if ($total < $min) {
                $underMin[] = [...$row, 'min_credits' => $min];
            }
        }

        return [
            'term' => ['id' => $term->id, 'name' => $term->name],
            'summary' => [
                'sections' => $sections->count(),
                'enrollments' => $sections->sum('enrolled'),
                'full_sections' => $sectionRows->where('state', 'full')->count(),
                'under_min_sections' => $sectionRows->where('state', 'under_min')->count(),
                'students_registered' => $credits->count(),
                'students_not_registered' => count($notRegistered),
                'students_under_min_credits' => count($underMin),
            ],
            'sections' => $sectionRows->values(),
            'students_not_registered' => $notRegistered,
            'students_under_min_credits' => $underMin,
        ];
    }

    /**
     * Thời khóa biểu tuần theo thứ, có giờ học theo khung tiết của cơ sở.
     *
     * @param  Collection<int, Enrollment>  $active
     * @param  Collection<int, Collection<int, ScheduleRule>>  $rules
     * @return list<array<string, mixed>>
     */
    private function timetable(Collection $active, Collection $rules): array
    {
        $rows = [];

        foreach ($active as $enrollment) {
            foreach ($rules->get($enrollment->course_section_id) ?? [] as $rule) {
                $frame = $this->periods->periodsFor($rule->room?->building?->campus_id);
                $rows[] = [
                    'day_of_week' => $rule->day_of_week,
                    'day_label' => ScheduleRule::DAYS[$rule->day_of_week],
                    'periods' => "{$rule->period_from}–{$rule->period_to}",
                    'time' => ($frame->get($rule->period_from)?->startTime() ?? '?').'–'.($frame->get($rule->period_to)?->endTime() ?? '?'),
                    'weeks' => "{$rule->week_from}–{$rule->week_to}".($rule->week_parity->value === 'all' ? '' : ' ('.mb_strtolower($rule->week_parity->label()).')'),
                    'subject' => $enrollment->subject->name,
                    'section' => $enrollment->section->code,
                    'room' => $rule->room?->code,
                    'sort' => $rule->day_of_week * 100 + $rule->period_from,
                ];
            }
        }

        usort($rows, fn ($a, $b) => $a['sort'] <=> $b['sort']);

        return array_map(fn ($r) => array_diff_key($r, ['sort' => true]), $rows);
    }
}
