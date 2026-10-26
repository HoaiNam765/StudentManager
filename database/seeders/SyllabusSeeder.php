<?php

namespace Database\Seeders;

use App\Modules\Subject\Enums\SyllabusStatus;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\Syllabus;
use Illuminate\Database\Seeder;

/**
 * SUB (dữ liệu dùng thử): đề cương mẫu đã duyệt (phiên bản 1) cho mỗi học phần chưa có đề cương, để mở lớp học phần
 * được khi demo (BR-SUB-08 yêu cầu đề cương đã duyệt). Không chạy ở production; chạy lặp lại không tạo trùng.
 */
class SyllabusSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        Subject::query()->whereNotIn('id', Syllabus::withTrashed()->select('subject_id'))->orderBy('code')->get()->each(fn (Subject $subject) => Syllabus::create([
            'subject_id' => $subject->id,
            'version' => 1,
            'objectives' => "Đề cương mẫu (dữ liệu dùng thử) của học phần {$subject->name}.",
            'learning_outcomes' => [['code' => 'CLO1', 'description' => "Nắm được kiến thức cơ bản của học phần {$subject->name}."]],
            'content' => [['unit' => 'Tuần 1–15', 'topic' => 'Nội dung theo kế hoạch giảng dạy', 'hours' => (int) ($subject->theory_periods ?? 0) + (int) ($subject->practice_periods ?? 0)]],
            'status' => SyllabusStatus::Approved,
            'submitted_at' => now(),
            'decided_at' => now(),
            'decision_note' => 'Dữ liệu dùng thử',
        ]));
    }
}
