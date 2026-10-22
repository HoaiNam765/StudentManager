<?php

namespace App\Modules\Grade\Services;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\SchoolClass\Models\CourseSection;
use Illuminate\Validation\ValidationException;

/** Temporary #120-compatible calculation; replace with canonical #120 service after merge. */
class CourseResultService
{
    public function calculate(CourseSection $section): int
    {
        $components = SectionGradeComponent::where('course_section_id', $section->id)->orderBy('sort_order')->get();
        if ($components->isEmpty() || abs($components->sum('weight') - 100.0) > 0.001) {
            throw ValidationException::withMessages(['grades' => 'Cơ cấu điểm chưa hợp lệ hoặc tổng trọng số không bằng 100%.']);
        }
        $count = 0;
        foreach (Enrollment::query()->active()->where('course_section_id', $section->id)->cursor() as $enrollment) {
            $grades = GradeEntry::query()->where('enrollment_id', $enrollment->id)
                ->whereIn('section_grade_component_id', $components->pluck('id'))->get()->keyBy('section_grade_component_id');
            $score = 0.0;
            $complete = true;
            $passesFloors = true;
            foreach ($components as $component) {
                $value = $grades->get($component->id)?->score;
                if ($component->is_required && $value === null) {
                    $complete = false;
                    break;
                }
                if ($value !== null) {
                    $score += (float) $value * (float) $component->weight / 100;
                    if ($component->min_score !== null && (float) $value < (float) $component->min_score) {
                        $passesFloors = false;
                    }
                }
            }
            if (! $complete) {
                throw ValidationException::withMessages(['grades' => "Sinh viên đăng ký #{$enrollment->id} còn thiếu điểm bắt buộc."]);
            }
            $score = round($score, (int) config('grade_lifecycle.score_digits', 1));
            $passScore = (float) config('grade_lifecycle.pass_score', 4);
            $passed = $passesFloors && $score >= $passScore;

            $subject = $enrollment->subject ?? $section->subject;
            $isPassFailOnly = $subject && method_exists($subject, 'isPassFailOnly') && $subject->isPassFailOnly();

            if ($isPassFailOnly) {
                CourseResult::updateOrCreate(['enrollment_id' => $enrollment->id], [
                    'score_10' => null,
                    'letter' => null,
                    'score_4' => null,
                    'passed' => $passed,
                ]);
            } else {
                $scale = collect(config('grade_lifecycle.scale'))->first(fn ($row) => $score >= $row['min']);
                if (! $scale) {
                    throw ValidationException::withMessages(['grades' => 'Bảng quy đổi điểm chưa được cấu hình.']);
                }

                // BR-GRD-03: Trượt vì điểm sàn hoặc dưới điểm đạt => kết quả F, hệ 4 = 0.0
                $letter = $passed ? $scale['letter'] : 'F';
                $score4 = $passed ? (float) $scale['four'] : 0.0;

                CourseResult::updateOrCreate(['enrollment_id' => $enrollment->id], [
                    'score_10' => $score,
                    'letter' => $letter,
                    'score_4' => $score4,
                    'passed' => $passed,
                ]);
            }
            $count++;
        }

        return $count;
    }
}
