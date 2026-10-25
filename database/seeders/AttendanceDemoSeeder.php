<?php

namespace Database\Seeders;

use App\Modules\AcademicYear\Models\Term;
use App\Modules\Attendance\Enums\AttendanceStatus;
use App\Modules\Attendance\Models\AttendanceRecord;
use App\Modules\Attendance\Services\AttendanceCalculator;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Timetable\Models\ClassSession;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dữ liệu demo điểm danh (#142–#144) cho học kỳ đang diễn ra: mỗi lớp học phần đang học chưa có buổi học thì sinh 15
 * buổi theo tuần (thứ và ca xoay vòng theo lớp), điểm danh các buổi đã qua (đa số có mặt, một ít vắng / có phép / đi
 * muộn; vài sinh viên vắng nhiều để có cảnh báo và cấm thi), tính lại tổng hợp. Chạy lại thì bỏ qua lớp đã có buổi học.
 *
 *     php artisan db:seed --class=AttendanceDemoSeeder
 */
class AttendanceDemoSeeder extends Seeder
{
    public function run(AttendanceCalculator $calculator): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Không chạy dữ liệu demo trên môi trường production (BR-SYS-10).');

            return;
        }

        $tz = config('studentmanager.display_timezone');
        $today = CarbonImmutable::now($tz)->startOfDay();
        $term = Term::query()->whereDate('start_date', '<=', $today->toDateString())->whereDate('end_date', '>=', $today->toDateString())->first();

        if ($term === null) {
            $this->command?->warn('Không có học kỳ đang diễn ra, bỏ qua dữ liệu demo điểm danh.');

            return;
        }

        mt_srand(2610);
        $sections = CourseSection::query()->where('term_id', $term->id)->where('status', SectionStatus::InProgress->value)
            ->whereNotIn('id', ClassSession::query()->select('course_section_id'))
            ->orderBy('id')->get();
        $recorded = 0;

        foreach ($sections->values() as $index => $section) {
            DB::transaction(function () use ($section, $index, $term, $today, $tz, &$recorded): void {
                $instructor = SectionInstructor::query()->where('course_section_id', $section->id)->with('lecturer:id,user_id')->first();
                $start = CarbonImmutable::parse($term->start_date->toDateString(), $tz)->startOfWeek()->addDays($index % 6);
                $periodFrom = [1, 4, 7, 10][intdiv($index, 6) % 4];
                $enrollments = Enrollment::query()->active()->where('course_section_id', $section->id)->get();
                // Hai sinh viên mỗi lớp vắng nhiều để có cảnh báo / cấm thi
                $frequentAbsent = $enrollments->pluck('id')->shuffle()->take(2)->all();

                foreach (range(1, 15) as $week) {
                    $date = $start->addWeeks($week - 1);
                    $session = ClassSession::create([
                        'course_section_id' => $section->id,
                        'date' => $date->toDateString(),
                        'week' => $week,
                        'period_from' => $periodFrom,
                        'period_to' => $periodFrom + 2,
                        'status' => $date->lt($today) ? 'taught' : 'planned',
                    ]);

                    if (! $date->lt($today)) {
                        continue;
                    }

                    foreach ($enrollments as $enrollment) {
                        $roll = mt_rand(1, 100);
                        $status = in_array($enrollment->id, $frequentAbsent, true)
                            ? ($roll <= 30 ? AttendanceStatus::Absent : AttendanceStatus::Present)
                            : match (true) {
                                $roll <= 4 => AttendanceStatus::Absent,
                                $roll <= 7 => AttendanceStatus::Excused,
                                $roll <= 10 => AttendanceStatus::Late,
                                default => AttendanceStatus::Present,
                            };

                        AttendanceRecord::create([
                            'class_session_id' => $session->id,
                            'enrollment_id' => $enrollment->id,
                            'student_id' => $enrollment->student_id,
                            'course_section_id' => $section->id,
                            'status' => $status,
                            'minutes_late' => $status === AttendanceStatus::Late ? mt_rand(5, 25) : null,
                            'recorded_by' => $instructor?->lecturer?->user_id,
                            'recorded_at' => $date->setTime(9, 0),
                        ]);
                        $recorded++;
                    }
                }
            });

            $calculator->recalculateSection($section);
        }

        $this->command?->info("Dữ liệu demo điểm danh: {$sections->count()} lớp học phần, {$recorded} lượt điểm danh.");
    }
}
