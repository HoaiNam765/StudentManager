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
use App\Modules\Timetable\Models\TermTimetable;
use App\Modules\Timetable\Services\TimetablePublication;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dữ liệu demo điểm danh (#142–#144) cho học kỳ đang diễn ra: mỗi lớp học phần đang học chưa có buổi học thì xếp lịch
 * tuần 1–15 có phòng qua `TimetableSeeder::place` (sinh viên của lớp không bị trùng buổi; buổi sinh tự động, trùng ngày
 * nghỉ thì Nghỉ), điểm danh các buổi đã qua (đa số có mặt, một ít vắng / có phép / đi muộn; vài sinh viên vắng nhiều để
 * có cảnh báo và cấm thi), tính lại tổng hợp, rồi công bố thời khóa biểu học kỳ đó. Chạy lại thì bỏ qua lớp đã có buổi học.
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
        $used = [];
        // Lớp đông trước để còn nhiều buổi / phòng lớn
        $sections = $sections->sortByDesc(fn (CourseSection $s) => Enrollment::query()->active()->where('course_section_id', $s->id)->count())->values();

        foreach ($sections as $index => $section) {
            DB::transaction(function () use ($section, $index, $today, $term, $tz, &$recorded, &$used): void {
                $instructor = SectionInstructor::query()->where('course_section_id', $section->id)->with('lecturer:id,user_id')->first();
                $enrollments = Enrollment::query()->active()->where('course_section_id', $section->id)->get();
                $students = $enrollments->map(fn (Enrollment $e) => "student:{$e->student_id}")->all();

                // DemoDataSeeder tạo lớp thẳng ở trạng thái Đang học, mà mẫu lịch chỉ xếp được trước đó: tạm lùi trạng thái khi
                // xếp (query builder, không ghi nhật ký) rồi trả lại. SessionService chỉ sinh buổi từ "hôm nay" nên đặt đồng
                // hồ về ngày bắt đầu học kỳ để có đủ buổi đã qua.
                CourseSection::query()->whereKey($section->id)->update(['status' => SectionStatus::RegistrationOpen->value]);
                Carbon::setTestNow(CarbonImmutable::parse($term->start_date->toDateString(), $tz));
                try {
                    $placed = TimetableSeeder::place($section->refresh(), $index, $students, $used);
                } finally {
                    Carbon::setTestNow();
                    CourseSection::query()->whereKey($section->id)->update(['status' => SectionStatus::InProgress->value]);
                }

                if ($placed === null) {
                    $this->command?->warn("Không xếp được lịch cho lớp {$section->code}.");

                    return;
                }

                // Hai sinh viên mỗi lớp vắng nhiều để có cảnh báo / cấm thi
                $frequentAbsent = $enrollments->pluck('id')->shuffle()->take(2)->all();
                $sessions = ClassSession::query()->where('course_section_id', $section->id)->where('status', 'planned')
                    ->whereDate('date', '<', $today->toDateString())->orderBy('date')->get();

                foreach ($sessions as $session) {
                    $session->update(['status' => 'taught']);
                    $date = CarbonImmutable::parse($session->date->toDateString());

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

        // Học kỳ đang học thì thời khóa biểu đã công bố: giảng viên thấy lịch dạy, sinh viên thấy lịch chính thức
        if ($sections->isNotEmpty() && ! TermTimetable::query()->where('term_id', $term->id)->exists()) {
            app(TimetablePublication::class)->publish($term);
        }

        $this->command?->info("Dữ liệu demo điểm danh: {$sections->count()} lớp học phần, {$recorded} lượt điểm danh.");
    }
}
