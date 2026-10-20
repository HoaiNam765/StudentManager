<?php

namespace App\Modules\Grade\Services;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\System\Services\PolicyResolver;
use App\Support\Services\BaseService;
use Illuminate\Support\Facades\DB;

/**
 * Tính điểm học phần (BR-GRD-01..03, 11; UAT-07): tổng (điểm thành phần × trọng số), làm tròn `grades.round_digits`
 * (mặc định 1 chữ số, nửa lên); quy đổi chữ / hệ 4 theo `grading.scale` và đạt khi ≥ `grading.pass_score` của bộ quy
 * chế áp dụng cho khóa sinh viên, đồng thời đạt điểm sàn các thành phần bắt buộc (trượt sàn → F). Thiếu thành phần bắt
 * buộc → `incomplete`. Học phần Đạt / Không đạt chỉ trả đạt hay không, không có điểm số.
 */
class CourseGradeService extends BaseService
{
    public function __construct(private readonly PolicyResolver $policies) {}

    public function calculate(Enrollment $enrollment): array
    {
        $enrollment->loadMissing('student', 'section.subject');

        $components = SectionGradeComponent::query()
            ->where('course_section_id', $enrollment->course_section_id)
            ->orderBy('sort_order')
            ->get();

        if ($components->isEmpty()) {
            $this->fail(
                'Lớp chưa có cơ cấu điểm.',
                'Khởi tạo cơ cấu điểm trước khi tính.'
            );
        }

        $totalWeight = $components->sum(
            fn ($c) => (float) $c->weight
        );

        if (abs($totalWeight - 100) > 0.001) {
            $this->fail(
                'Tổng trọng số phải bằng 100%.',
                'Kiểm tra lại cơ cấu điểm lớp.'
            );
        }

        $entries = GradeEntry::query()
            ->where('enrollment_id', $enrollment->id)
            ->whereIn(
                'section_grade_component_id',
                $components->pluck('id')
            )
            ->get()
            ->keyBy('section_grade_component_id');

        $total = 0.0;
        $floorPassed = true;

        foreach ($components as $component) {
            $score = $entries->get($component->id)?->score;

            if ($score === null && $component->is_required) {
                return $this->incomplete();
            }

            if ($score === null) {
                continue;
            }

            $score = (float) $score;

            if ($score < 0 || $score > 10) {
                $this->fail(
                    'Điểm thành phần ngoài khoảng 0–10.',
                    'Kiểm tra lại điểm đã nhập.'
                );
            }

            $total += $score * (float) $component->weight / 100;

            if (
                $component->is_required
                && $component->min_score !== null
                && $score < (float) $component->min_score
            ) {
                $floorPassed = false;
            }
        }

        $digits = (int) config('grades.round_digits', 1);
        $score10 = round($total, $digits, PHP_ROUND_HALF_UP);

        $cohort = (int) $enrollment->student->cohort;
        $passScore = (float) $this->policies->value('grading.pass_score', $cohort);
        $passed = $score10 >= $passScore && $floorPassed;

        // Học phần đánh giá Đạt/Không đạt không có
        // điểm chữ, điểm hệ 4 hoặc điểm số công bố.
        if ($enrollment->section->subject->isPassFailOnly()) {
            return [
                'score10' => null,
                'letter' => null,
                'point4' => null,
                'is_passed' => $passed,
                'status' => 'calculated',
            ];
        }

        // Thang quy đổi của bộ quy chế áp dụng cho khóa (mặc định phụ lục C: A 8,5 / B 7,0 / C 5,5 / D 4,0 / F)
        $grade = collect((array) $this->policies->value('grading.scale', $cohort))
            ->sortByDesc(fn (array $band) => (float) $band['min'])
            ->map(fn (array $band) => (object) ['min_score' => (float) $band['min'], 'letter' => $band['letter'], 'point4' => (float) $band['gpa']])
            ->first(fn (object $band) => $score10 >= $band->min_score);

        if (! $grade) {
            $this->fail(
                "Không có mức quy đổi cho điểm {$score10}.",
                'Kiểm tra thang quy đổi (grading.scale) trong bộ quy chế áp dụng cho khóa '.$cohort.'.'
            );
        }

        // Trượt vì điểm sàn => kết quả F.
        if (! $passed) {
            $grade = (object) [
                'letter' => 'F',
                'point4' => 0.0,
            ];
        }

        return [
            'score10' => $score10,
            'letter' => $grade->letter,
            'point4' => (float) $grade->point4,
            'is_passed' => $passed,
            'status' => 'calculated',
        ];
    }

    public function save(Enrollment $enrollment): array
    {
        return DB::transaction(function () use ($enrollment) {
            $result = $this->calculate($enrollment);

            $record = CourseResult::query()->firstOrNew([
                'enrollment_id' => $enrollment->id,
            ]);

            $record->fill($result);
            $record->save();

            return $result;
        });
    }

    private function incomplete(): array
    {
        return [
            'score10' => null,
            'letter' => null,
            'point4' => null,
            'is_passed' => null,
            'status' => 'incomplete',
        ];
    }
}
