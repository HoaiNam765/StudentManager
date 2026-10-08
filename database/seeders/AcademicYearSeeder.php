<?php

namespace Database\Seeders;

use App\Modules\AcademicYear\Models\AcademicYear;
use App\Modules\AcademicYear\Models\Holiday;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Models\TermType;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu năm học 2026–2027: ba học kỳ, lịch học vụ của học kỳ 1 và các ngày nghỉ lễ.
 * Ngày Tết Nguyên đán là ví dụ, cần chỉnh theo lịch nghỉ chính thức. Chạy lặp lại an toàn.
 */
class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::firstOrCreate(['name' => '2026-2027'], ['start_date' => '2026-08-15', 'end_date' => '2027-08-14']);

        $terms = [];

        foreach ([
            ['Học kỳ 1', TermType::MAIN, '2026-09-01', '2027-01-10', 18],
            ['Học kỳ 2', TermType::MAIN, '2027-01-25', '2027-06-13', 18],
            ['Học kỳ hè', TermType::SUMMER, '2027-06-21', '2027-08-08', 7],
        ] as [$name, $type, $start, $end, $weeks]) {
            $terms[$name] = Term::firstOrCreate(
                ['academic_year_id' => $year->id, 'name' => $name],
                ['type' => $type, 'start_date' => $start, 'end_date' => $end, 'weeks' => $weeks, 'status' => TermStatus::PLANNED],
            );
        }

        $milestones = [
            'registration_open' => '2026-08-01',
            'registration_close' => '2026-08-25',
            'classes_start' => '2026-09-07',
            'cancel_deadline' => '2026-09-21',
            'withdraw_deadline' => '2026-11-02',
            'teaching_end' => '2026-12-20',
            'exam_start' => '2026-12-28',
            'exam_end' => '2027-01-08',
            'grade_entry_deadline' => '2027-01-15',
            'grade_publish' => '2027-01-20',
            'tuition_deadline' => '2026-10-15',
            'term_end' => '2027-01-20',
        ];

        foreach ($milestones as $type => $date) {
            $terms['Học kỳ 1']->milestones()->firstOrCreate(['type' => $type], ['date' => $date]);
        }

        foreach ([
            ['Quốc khánh', 'public_holiday', '2026-09-01', '2026-09-02'],
            ['Tết Dương lịch', 'public_holiday', '2027-01-01', '2027-01-01'],
            ['Tết Nguyên đán (ví dụ)', 'public_holiday', '2027-02-04', '2027-02-12'],
            ['Giải phóng miền Nam và Quốc tế Lao động', 'public_holiday', '2027-04-30', '2027-05-01'],
            ['Nghỉ hè', 'summer_break', '2027-08-09', '2027-08-14'],
        ] as [$name, $type, $start, $end]) {
            Holiday::firstOrCreate(['name' => $name, 'starts_on' => $start], ['type' => $type, 'ends_on' => $end]);
        }
    }
}
