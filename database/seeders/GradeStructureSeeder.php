<?php

namespace Database\Seeders;

use App\Modules\Subject\Enums\KnowledgeBlock;
use App\Modules\Subject\Models\GradeComponentTemplate;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\GradeStructureService;
use Illuminate\Database\Seeder;

/**
 * Cơ cấu điểm mẫu cho các học phần chưa có (dữ liệu giả, đúng BR-SUB-04 với quy chế mặc định): lý thuyết 10/30/60,
 * có thực hành 10/30/60 (thực hành thay giữa kỳ), thực tập và đồ án / khóa luận 30/20/50. Chạy lặp lại an toàn.
 */
class GradeStructureSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(GradeStructureService::class);
        $done = GradeComponentTemplate::query()->distinct()->pluck('subject_id')->flip();

        Subject::query()->orderBy('code')->get()->each(function (Subject $subject) use ($service, $done): void {
            if (! $done->has($subject->id)) {
                $service->replace($subject, $this->structureFor($subject));
            }
        });
    }

    /** @return list<array<string, mixed>> */
    private function structureFor(Subject $subject): array
    {
        if (in_array($subject->knowledge_block, [KnowledgeBlock::Internship, KnowledgeBlock::Thesis], true)) {
            return [
                ['name' => 'Đánh giá của người hướng dẫn', 'kind' => 'other', 'weight' => 30],
                ['name' => 'Báo cáo, phản biện', 'kind' => 'other', 'weight' => 20],
                ['name' => 'Bảo vệ trước hội đồng', 'kind' => 'final_exam', 'weight' => 50, 'min_score' => 4],
            ];
        }

        return [
            ['name' => 'Chuyên cần', 'kind' => 'attendance', 'weight' => 10],
            $subject->practice_credits > 0
                ? ['name' => 'Thực hành', 'kind' => 'practical', 'weight' => 30, 'min_score' => 4]
                : ['name' => 'Giữa kỳ', 'kind' => 'midterm', 'weight' => 30],
            ['name' => 'Thi cuối kỳ', 'kind' => 'final_exam', 'weight' => 60, 'min_score' => 1],
        ];
    }
}
