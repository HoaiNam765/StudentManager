<?php

namespace App\Modules\Report\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Services\TranscriptService;
use App\Modules\SchoolClass\Models\AdvisorAssignment;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\StudentCurriculumService;
use Illuminate\Support\Str;

/** Adapts the existing academic services to the student portal's Blade templates. */
class StudentPortalData
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly TranscriptService $transcripts,
        private readonly StudentCurriculumService $curricula,
    ) {}

    public function for(User $user): array
    {
        $profile = Student::query()->with('major', 'adminClassMembers.adminClass')->where('user_id', $user->id)->first();
        $today = now(config('studentmanager.display_timezone'));
        $term = Term::query()->with('academicYear')->whereDate('start_date', '<=', $today->toDateString())
            ->whereDate('end_date', '>=', $today->toDateString())->orderByDesc('start_date')->first();
        $dashboard = $profile === null ? [] : $this->dashboard->student($user);
        $curriculum = $profile === null ? null : $this->curricula->current($profile);
        $curriculum?->load('blocks.items.subject', 'items.subject', 'items.electiveGroup');
        $report = $profile === null ? ['terms' => []] : $this->transcripts->personal($profile);
        $results = collect($report['terms'])->flatMap(fn ($term) => collect($term['rows'])
            ->map(fn ($row) => $row + ['semester' => $term['name']]));
        $passed = $results->where('is_passed', true)->keyBy('subject_code');
        $enrollments = $profile === null || $term === null ? collect() : Enrollment::query()
            ->with('subject')->where('student_id', $profile->id)->where('term_id', $term->id)
            ->whereIn('status', ['registered', 'confirmed'])->get();
        $earned = $dashboard['credits_earned'] ?? null;
        $required = $curriculum?->total_credits;
        $percent = $required > 0 && $earned !== null ? min(100, round($earned * 100 / $required, 1)) : 0;
        $currentCredits = $enrollments->unique('subject_id')->sum(fn ($e) => $e->subject?->credits ?? 0);
        $currentPercent = $required > 0 ? min(100 - $percent, round($currentCredits * 100 / $required, 1)) : 0;
        $membership = $profile?->adminClassMembers()->current()->with('adminClass')->first();

        return [
            'profile' => $profile,
            'advisor' => $membership === null ? null : AdvisorAssignment::query()->current()
                ->where('admin_class_id', $membership->admin_class_id)->with('lecturer')->first()?->lecturer,
            'curriculum' => $curriculum,
            'termLabel' => $term ? $term->name.' • '.$term->academicYear?->name : 'Chưa có học kỳ hiện tại',
            'todayLabel' => $today->translatedFormat('l, d/m/Y'),
            'student' => [
                'name' => $profile?->full_name ?? $user->name,
                'initials' => Str::of($profile?->full_name ?? $user->name)->trim()->explode(' ')->filter()->take(-2)
                    ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode(''),
                'student_id' => $profile?->student_code ?? 'Chưa liên kết hồ sơ',
                'major' => $profile?->major?->name ?? '—',
                'cohort' => $profile?->cohort ?? '—',
                'class' => $membership?->adminClass?->code ?? 'Chưa phân lớp',
                'credits_earned' => $earned,
                'credits_total' => $required,
                'credits_pct' => $percent,
                'credits_remaining' => $dashboard['credits_remaining'] ?? null,
                'current_courses_count' => $enrollments->count(),
                'current_credits' => $currentCredits,
                'current_pct' => $currentPercent,
                'remaining_pct' => max(0, round(100 - $percent - $currentPercent, 1)),
                'gpa_4' => $dashboard['cumulative_gpa'] ?? null,
                'classification' => $dashboard['classification'] ?? 'Chưa có kết quả',
            ],
            'todayClasses' => collect($dashboard['today_schedule'] ?? [])->map(fn ($s) => [
                'time' => $s['time'], 'status' => $s['status'], 'status_label' => $s['status_label'],
                'course_name' => $s['subject'], 'room' => $s['room'] ?? '—', 'lecturer' => $s['lecturer'] ?? '—',
            ])->all(),
            'recentGrades' => $results->reverse()->take(4)->map(fn ($r) => [
                'course_name' => $r['subject_name'], 'credits' => $r['credits'], 'semester' => $r['semester'],
                'score' => $r['score10'], 'letter' => $r['letter'] ?? '—',
            ])->values()->all(),
            'notifications' => $dashboard['notifications'] ?? ['unread' => 0, 'latest' => []],
            'blocks' => $curriculum?->blocks->map(function ($block) use ($passed) {
                $credits = $block->items->unique('subject_id')->filter(fn ($item) => $passed->has($item->subject?->code))
                    ->sum(fn ($item) => $item->subject?->credits ?? 0);

                return ['name' => $block->name, 'earned' => $credits, 'required' => $block->required_credits,
                    'percent' => $block->required_credits > 0 ? min(100, round($credits * 100 / $block->required_credits, 1)) : 0];
            })->all() ?? [],
            'curriculumRows' => $curriculum?->items->sortBy('planned_term')->map(fn ($item) => [
                'block_id' => $item->curriculum_block_id,
                'code' => $item->subject?->code, 'name' => $item->subject?->name, 'credits' => $item->subject?->credits,
                'term' => $item->planned_term, 'type' => $item->isElective() ? 'Tự chọn' : 'Bắt buộc',
                'passed' => $passed->has($item->subject?->code),
                'studying' => $enrollments->contains('subject_id', $item->subject_id),
                'score' => $results->where('subject_code', $item->subject?->code)->last()['score10'] ?? null,
                'letter' => $results->where('subject_code', $item->subject?->code)->last()['letter'] ?? null,
            ])->values()->all() ?? [],
        ];
    }
}
