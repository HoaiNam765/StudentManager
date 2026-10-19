<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Student\Models\Student;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Services\TimetablePublication;
use App\Modules\Timetable\Services\TimetableView;

class DashboardService
{
    public function __construct(
        private readonly TimetableView $timetable,
        private readonly TimetablePublication $publication,
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
            'deadlines' => [],
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
            'pending_grade_sections' => null,
            'deadlines' => [],
            'notifications' => [],
        ];
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
