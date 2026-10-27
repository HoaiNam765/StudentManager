<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\MilestoneType;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermMilestone;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\RegistrationAccess;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Notification\Services\NotificationCenter;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\StudentCurriculumService;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Services\TimetablePublication;
use App\Modules\Timetable\Services\TimetableView;
use Carbon\CarbonImmutable;

/**
 * Dữ liệu dashboard của sinh viên và giảng viên (FR-RPT-009, 010; FR-TCH-009) — chỉ dữ liệu của chính người dùng (OWN).
 *
 * - Lịch hôm nay: buổi học cụ thể (TTB) của học kỳ đang diễn ra, chỉ khi thời khóa biểu đã công bố.
 * - Hạn chót: các mốc lịch học vụ (ACY) sắp tới trong `studentmanager.dashboard.deadline_days` ngày, theo vai trò; sinh
 *   viên thêm hạn đóng của đợt đăng ký đang mở cho mình.
 * - Giảng viên: lớp được phân công đang trong giai đoạn nhập điểm (Đang học / Kết thúc).
 * - GPA từng học kỳ và tích lũy, tín chỉ tích lũy / còn thiếu theo CTĐT hiện hành (GRD #125, đã lưu khi công bố điểm).
 * - Thông báo chưa đọc mới nhất (NOT #115, tối đa 5) và số chưa đọc.
 */
class DashboardService
{
    private const STUDENT_MILESTONES = [
        MilestoneType::RegistrationOpen, MilestoneType::RegistrationClose, MilestoneType::CancelDeadline,
        MilestoneType::WithdrawDeadline, MilestoneType::TuitionDeadline, MilestoneType::ExamStart, MilestoneType::GradePublish,
    ];

    private const TEACHER_MILESTONES = [
        MilestoneType::ClassesStart, MilestoneType::TeachingEnd, MilestoneType::ExamStart, MilestoneType::ExamEnd,
        MilestoneType::GradeEntryDeadline, MilestoneType::GradePublish,
    ];

    public function __construct(
        private readonly TimetableView $timetable,
        private readonly TimetablePublication $publication,
        private readonly RegistrationAccess $registration,
    ) {}

    public function student(User $user): array
    {
        $student = Student::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

        return [
            'student' => [
                'id' => $student->id,
                'code' => $student->student_code,
                'name' => $student->full_name,
            ],
            ...$this->academic($student),
            'today_schedule' => $this->schedule(
                'student',
                $student->id
            ),
            'deadlines' => $this->studentDeadlines($student),
            'notifications' => $this->notifications($user),
        ];
    }

    public function teacher(User $user): array
    {
        $lecturer = Lecturer::query()
            ->where('user_id', $user->id)
            ->firstOr(fn () => abort(404, 'Tài khoản chưa gắn với hồ sơ giảng viên.'));

        return [
            'lecturer' => [
                'id' => $lecturer->id,
                'code' => $lecturer->code,
                'name' => $lecturer->full_name,
            ],
            'today_schedule' => $this->schedule(
                'lecturer',
                $lecturer->id
            ),
            'pending_grade_sections' => $this->gradingSections($lecturer),
            'deadlines' => $this->milestones(self::TEACHER_MILESTONES),
            'notifications' => $this->notifications($user),
        ];
    }

    /** GPA theo học kỳ, GPA / tín chỉ tích lũy và tín chỉ còn thiếu so với CTĐT hiện hành. */
    private function academic(Student $student): array
    {
        $terms = TermGpa::query()->with('term:id,name,start_date')->where('student_id', $student->id)->get()
            ->sortBy(fn (TermGpa $t) => $t->term?->start_date?->format('Y-m-d'))
            ->map(fn (TermGpa $t) => ['term' => $t->term?->name, 'gpa' => $t->gpa === null ? null : (float) $t->gpa, 'classification' => $t->classification])
            ->values()->all();
        $cumulative = CumulativeGpa::query()->where('student_id', $student->id)->first();
        $required = app(StudentCurriculumService::class)->current($student)?->total_credits;
        $earned = $cumulative?->credits_earned;

        return [
            'gpa_by_term' => $terms,
            'cumulative_gpa' => $cumulative?->gpa === null ? null : (float) $cumulative->gpa,
            'classification' => $cumulative?->classification,
            'credits_earned' => $earned,
            'credits_remaining' => $required === null ? null : max(0, (int) $required - (int) $earned),
        ];
    }

    /** @return array{unread: int, latest: list<array<string, mixed>>} */
    private function notifications(User $user): array
    {
        $center = app(NotificationCenter::class);

        return [
            'unread' => $center->unreadCount($user),
            'latest' => collect($center->list($user, ['status' => 'unread'])->items())->take(5)
                ->map(fn ($row) => ['id' => $row->notification->id, 'title' => $row->notification->title, 'link' => $row->notification->link, 'sent_at' => $row->notification->sent_at?->toIso8601String()])
                ->values()->all(),
        ];
    }

    /** @return list<array<string, mixed>> */
    private function studentDeadlines(Student $student): array
    {
        $tz = config('studentmanager.display_timezone');
        $periods = $this->registration->openPeriodsFor($student)->map(fn (RegistrationPeriod $p) => [
            'date' => $p->closes_at->setTimezone($tz)->toDateString(),
            'time' => $p->closes_at->setTimezone($tz)->format('H:i'),
            'label' => "Đóng {$p->name}",
            'term' => $p->term?->name,
        ])->all();

        $all = [...$periods, ...$this->milestones(self::STUDENT_MILESTONES)];
        usort($all, fn (array $a, array $b) => strcmp($a['date'], $b['date']));

        return $all;
    }

    /**
     * Mốc lịch học vụ sắp tới (từ hôm nay tới N ngày) thuộc các loại cho trước.
     *
     * @param  list<MilestoneType>  $types
     * @return list<array<string, mixed>>
     */
    private function milestones(array $types): array
    {
        $today = CarbonImmutable::now(config('studentmanager.display_timezone'))->startOfDay();
        $until = $today->addDays((int) config('studentmanager.dashboard.deadline_days', 30));

        return TermMilestone::query()
            ->with('term:id,name')
            ->whereIn('type', array_map(fn (MilestoneType $t) => $t->value, $types))
            ->whereBetween('date', [$today->toDateString(), $until->toDateString()])
            ->orderBy('date')
            ->get()
            ->map(fn (TermMilestone $m) => [
                'date' => $m->date->toDateString(),
                'time' => null,
                'label' => $m->type->label(),
                'term' => $m->term?->name,
            ])->all();
    }

    /** @return list<array<string, mixed>> lớp được phân công đang trong giai đoạn nhập điểm */
    private function gradingSections(Lecturer $lecturer): array
    {
        return CourseSection::query()
            ->with('subject:id,code,name', 'term:id,name')
            ->whereIn('status', [SectionStatus::InProgress, SectionStatus::Finished])
            ->whereHas('instructors', fn ($q) => $q->where('lecturer_id', $lecturer->id))
            ->orderBy('code')
            ->get()
            ->map(fn (CourseSection $s) => [
                'id' => $s->id,
                'code' => $s->code,
                'subject' => $s->subject?->name,
                'term' => $s->term?->name,
                'status_label' => $s->status->label(),
            ])->all();
    }

    private function schedule(string $target, int $id): array
    {
        $today = now(
            config('studentmanager.display_timezone')
        )->toDateString();

        $term = Term::query()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if ($term === null) {
            return [];
        }

        if (! $this->publication->isVisible($term)) {
            return [];
        }

        $range = $this->timetable->range(
            'week',
            $today,
            $term
        );

        return array_values(array_filter(
            $this->timetable->sessions(
                $target,
                $id,
                $term,
                $range['from'],
                $range['to']
            ),
            fn (array $session): bool => $session['date'] === $today
        ));
    }
}
