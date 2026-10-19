<?php

namespace App\Modules\Grade\Services;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\GradeScale;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Support\Services\BaseService;
use Illuminate\Support\Facades\DB;

class CourseGradeService extends BaseService
{
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
            fn($c) => (float) $c->weight
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

        $passScore = (float) config('grades.pass_score', 4.0);
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

        $cohort = (int) $enrollment->student->cohort;

        $scale = GradeScale::query()
            ->where('cohort', $cohort)
            ->orderByDesc('min_score')
            ->get();

        // Nếu khóa chưa có thang riêng, dùng thang mặc định.
        if ($scale->isEmpty()) {
            $scale = collect(config('grades.default_scale'))
                ->map(fn($row) => (object) [
                    'min_score' => $row['min'],
                    'letter' => $row['letter'],
                    'point4' => $row['point4'],
                ]);
        }

        $grade = $scale->first(
            fn($row) => $score10 >= (float) $row->min_score
        );

        if (! $grade) {
            $this->fail(
                'Không có mức quy đổi phù hợp.',
                'Kiểm tra bảng GradeScale của khóa học.'
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
