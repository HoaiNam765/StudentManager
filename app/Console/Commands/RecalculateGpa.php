<?php

namespace App\Console\Commands;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Services\GpaService;
use App\Modules\Student\Models\Student;
use Illuminate\Console\Command;

/** Tính lại GPA học kỳ / tích lũy (FR-GRD-009) — sau khi đổi bộ quy chế, chính sách học lại hoặc nhập dữ liệu cũ. */
class RecalculateGpa extends Command
{
    protected $signature = 'grades:recalculate-gpa {student_code? : MSSV, bỏ trống = mọi sinh viên có điểm}';

    protected $description = 'Tính lại GPA học kỳ, tích lũy và xếp loại học lực';

    public function handle(GpaService $gpa): int
    {
        $query = Student::query()->whereIn('id', Enrollment::query()->select('student_id'));

        if ($code = $this->argument('student_code')) {
            $query->where('student_code', $code);
        }

        $count = 0;
        $query->each(function (Student $student) use ($gpa, &$count): void {
            $gpa->recalculate($student);
            $count++;
        });

        $this->info("Đã tính lại GPA của {$count} sinh viên.");

        return self::SUCCESS;
    }
}
