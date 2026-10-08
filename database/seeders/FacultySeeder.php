<?php

namespace Database\Seeders;

use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu cơ cấu tổ chức (docs/BA.md mục 11.2: 3 khoa, 6 ngành) và hệ đào tạo. Mã ngành theo danh mục
 * ngành đào tạo trình độ đại học (7 chữ số). Chạy lặp lại an toàn theo mã.
 */
class FacultySeeder extends Seeder
{
    public const TRAINING_TYPES = [
        'CQ' => 'Chính quy',
        'LT' => 'Liên thông',
        'VB2' => 'Văn bằng hai',
        'VLVH' => 'Vừa làm vừa học',
    ];

    /** mã khoa => [tên, tên tiếng Anh, [mã bộ môn => tên], [mã ngành => [tên, tổng tín chỉ, số học kỳ, [mã chuyên ngành => tên]]]] */
    public const FACULTIES = [
        'CNTT' => ['Khoa Công nghệ thông tin', 'Faculty of Information Technology', [
            'BM-KTPM' => 'Bộ môn Kỹ thuật phần mềm',
            'BM-HTTT' => 'Bộ môn Hệ thống thông tin',
            'BM-MMT' => 'Bộ môn Mạng máy tính',
        ], [
            '7480201' => ['Công nghệ thông tin', 150, 8, ['CNTT-TTNT' => 'Trí tuệ nhân tạo', 'CNTT-ATTT' => 'An toàn thông tin']],
            '7480103' => ['Kỹ thuật phần mềm', 150, 8, []],
        ]],
        'KT' => ['Khoa Kinh tế', 'Faculty of Economics', [
            'BM-QTKD' => 'Bộ môn Quản trị kinh doanh',
            'BM-KTKT' => 'Bộ môn Kế toán – Kiểm toán',
        ], [
            '7340101' => ['Quản trị kinh doanh', 125, 8, ['QTKD-MKT' => 'Marketing']],
            '7340301' => ['Kế toán', 125, 8, []],
        ]],
        'NN' => ['Khoa Ngoại ngữ', 'Faculty of Foreign Languages', [
            'BM-TA' => 'Bộ môn Tiếng Anh',
            'BM-TT' => 'Bộ môn Tiếng Trung',
        ], [
            '7220201' => ['Ngôn ngữ Anh', 130, 8, ['NNA-TM' => 'Tiếng Anh thương mại']],
            '7220204' => ['Ngôn ngữ Trung Quốc', 130, 8, []],
        ]],
    ];

    public function run(): void
    {
        foreach (self::TRAINING_TYPES as $code => $name) {
            TrainingType::firstOrCreate(['code' => $code], ['name' => $name, 'is_default' => $code === 'CQ']);
        }

        foreach (self::FACULTIES as $code => [$name, $nameEn, $departments, $majors]) {
            $faculty = Faculty::firstOrCreate(['code' => $code], ['name' => $name, 'name_en' => $nameEn]);

            foreach ($departments as $departmentCode => $departmentName) {
                Department::firstOrCreate(['code' => $departmentCode], ['faculty_id' => $faculty->id, 'name' => $departmentName]);
            }

            foreach ($majors as $majorCode => [$majorName, $credits, $terms, $specializations]) {
                $major = Major::firstOrCreate(['code' => (string) $majorCode], [
                    'faculty_id' => $faculty->id,
                    'name' => $majorName,
                    'education_level' => 'undergraduate',
                    'total_credits' => $credits,
                    'standard_terms' => $terms,
                ]);

                foreach ($specializations as $specializationCode => $specializationName) {
                    Specialization::firstOrCreate(['code' => $specializationCode], ['major_id' => $major->id, 'name' => $specializationName]);
                }
            }
        }
    }
}
