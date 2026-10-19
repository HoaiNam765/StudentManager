<?php

namespace App\Modules\Timetable;

use App\Modules\Room\Models\Campus;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Contracts\SectionStudents;
use App\Modules\Timetable\Models\ClassSession;
use App\Modules\Timetable\Models\Period;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Policies\TimetablePolicy;
use App\Modules\Timetable\Services\NoSectionStudents;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/** Thời khóa biểu (TTB): khung tiết học, mẫu lịch lớp học phần. */
class TimetableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ENR bind lại khi có đăng ký học phần
        $this->app->bindIf(SectionStudents::class, NoSectionStudents::class);
    }

    public function boot(): void
    {
        Gate::policy(Period::class, TimetablePolicy::class);
        Gate::policy(ScheduleRule::class, TimetablePolicy::class);
        Gate::policy(ClassSession::class, TimetablePolicy::class);

        $references = app(ReferenceRegistry::class);
        // Dòng lịch / tiết đã xóa không còn ý nghĩa nghiệp vụ nên không tính
        $notDeleted = fn ($q) => $q->whereNull('deleted_at');

        $references->register(Campus::class, 'periods', 'campus_id', 'khung tiết học riêng của cơ sở', constraint: $notDeleted);
        $references->register(Room::class, 'schedule_rules', 'room_id', 'lịch học', constraint: $notDeleted);
        $references->register(CourseSection::class, 'schedule_rules', 'course_section_id', 'dòng lịch học', constraint: $notDeleted);
        $references->register(Room::class, 'class_sessions', 'room_id', 'buổi học', constraint: $notDeleted);
    }
}
