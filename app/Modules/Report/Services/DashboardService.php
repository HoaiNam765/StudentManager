<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\MilestoneType;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermMilestone;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\RegistrationAccess;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
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
 * - GPA, tín chỉ tích lũy (GRD #125), thông báo (NOT #115) trả `null` / rỗng cho tới khi các module đó có.
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
            'gpa_by_term' => null,
            'credits_earned' => null,
            'credits_remaining' => null,
            'today_schedule' => $this->schedule(
                'student',
                $student->id
            ),
            'deadlines' => $this->studentDeadlines($student),
            'notifications' => [],
        ];
    }

    public function teacher(User $user): array
    {
        $lecturer = Lecturer::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

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
            'notifications' => [],
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
