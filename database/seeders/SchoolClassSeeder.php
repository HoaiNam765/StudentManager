<?php

namespace Database\Seeders;

use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Khóa và lớp hành chính mẫu (CLS): khóa K2023–K2026; khóa 2026 mỗi ngành một lớp hệ chính quy, mã dạng 26DH + chữ đầu
 * tên ngành + 01 (ví dụ 26DHCNTT01). Chạy lặp lại an toàn.
 */
class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([2023, 2024, 2025, 2026] as $year) {
            Cohort::firstOrCreate(['start_year' => $year], [
                'code' => "K{$year}",
                'name' => "Khóa {$year}–".($year + 4),
                'expected_end_year' => $year + 4,
            ]);
        }

        $cohort = Cohort::query()->where('start_year', 2026)->first();
        $type = TrainingType::query()->where('code', 'CQ')->first();

        if ($cohort === null || $type === null) {
            return;
        }

        foreach (Major::query()->orderBy('code')->get() as $major) {
            $initials = collect(preg_split('/\s+/u', Str::ascii($major->name)) ?: [])
                ->filter()
                ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
                ->implode('');

            AdminClass::firstOrCreate(['code' => "26DH{$initials}01"], [
                'name' => "Lớp {$major->name} K2026 - 01",
                'cohort_id' => $cohort->id,
                'major_id' => $major->id,
                'training_type_id' => $type->id,
                'max_size' => 60,
            ]);
        }
    }
}
