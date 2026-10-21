<?php

namespace App\Modules\Grade\Services;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CumulativeGpa;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Student\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Only published/locked attempts count; never expose draft grades. */
class GpaService
{
    public function recalculate(Student $student): array
    {
        return DB::transaction(function () use ($student) {
            $attempts = Enrollment::query()->where('student_id', $student->id)
                ->whereHas('section', fn ($q) => $q->whereIn('id', DB::table('section_grade_workflows')
                    ->select('course_section_id')->whereIn('status', ['published', 'locked'])->whereNull('deleted_at')))
                ->whereNotIn('status', ['cancelled', 'withdrawn'])
                ->with(['subject:id,code,name,credits,counts_toward_gpa', 'section:id,term_id'])
                ->get();
            $results = DB::table('course_results')->whereIn('enrollment_id', $attempts->pluck('id'))->whereNull('deleted_at')->get()->keyBy('enrollment_id');
            $specialStatuses = config('grade_special_statuses', []);
            $valid = $attempts->filter(function ($e) use ($results, $specialStatuses) {
                if (! ($e->subject?->counts_toward_gpa ?? false) || ! $results->has($e->id)) {
                    return false;
                }
                $res = $results->get($e->id);
                if ($res->special_status !== null) {
                    $statusCfg = $specialStatuses[$res->special_status] ?? null;
                    if (! $statusCfg || ($statusCfg['gpa'] ?? '') === 'exclude') {
                        return false;
                    }
                }

                return $res->score_4 !== null || ($res->special_status !== null && ($specialStatuses[$res->special_status]['gpa'] ?? '') === 'zero');
            });
            $termRows = [];
            foreach ($valid->groupBy('term_id') as $termId => $group) {
                $gpa = $this->weighted($group, $results, $specialStatuses);
                $row = TermGpa::updateOrCreate(['student_id' => $student->id, 'term_id' => $termId], [
                    'gpa' => $gpa, 'credits_attempted' => $group->sum(fn ($e) => $e->subject->credits),
                    'credits_earned' => $group->sum(fn ($e) => $results->get($e->id)->passed ? $e->subject->credits : 0),
                    'classification' => $this->classification($gpa),
                ]);
                $termRows[] = $row;
            }
            // Repeated attempts of the same subject: keep highest or latest according to policy.
            $policy = config('grade_gpa.retake_policy', 'highest');
            if (! in_array($policy, ['highest', 'latest'], true)) {
                throw ValidationException::withMessages(['retake_policy' => 'Cấu hình GRD_RETAKE_POLICY phải là highest hoặc latest.']);
            }
            $selected = $valid->groupBy('subject_id')->map(function ($group) use ($results, $policy) {
                if ($policy === 'latest') {
                    return $group->sortByDesc(fn ($e) => sprintf('%012d%012d', (int) $e->term_id, (int) $e->id))->first();
                }

                return $group->sortByDesc(fn ($e) => sprintf('%06.2f%012d', (float) ($results->get($e->id)->score_4 ?? 0), (int) $e->id))->first();
            });
            $cumulative = $this->weighted($selected, $results, $specialStatuses);
            $cgpa = CumulativeGpa::updateOrCreate(['student_id' => $student->id], [
                'gpa' => $cumulative,
                'credits_attempted' => $selected->sum(fn ($e) => $e->subject->credits),
                'credits_earned' => $selected->sum(fn ($e) => $results->get($e->id)->passed ? $e->subject->credits : 0),
                'classification' => $this->classification($cumulative),
            ]);

            return ['terms' => $termRows, 'cumulative' => $cgpa];
        });
    }

    private function weighted(Collection $attempts, Collection $results, array $specialStatuses = []): ?float
    {
        $credits = $attempts->sum(fn ($e) => $e->subject->credits);
        if ($credits <= 0) {
            return null;
        }
        $totalPoints = $attempts->sum(function ($e) use ($results, $specialStatuses) {
            $res = $results->get($e->id);
            $score4 = (float) ($res->score_4 ?? 0);
            if ($res->special_status !== null && ($specialStatuses[$res->special_status]['gpa'] ?? '') === 'zero') {
                $score4 = 0.0;
            }

            return (float) $e->subject->credits * $score4;
        });

        return round($totalPoints / $credits, 2);
    }

    private function classification(?float $gpa): ?string
    {
        if ($gpa === null) {
            return null;
        }
        foreach (config('grade_gpa.classification', []) as $level) {
            if ($gpa >= $level['min']) {
                return $level['name'];
            }
        }

        return null;
    }
}
