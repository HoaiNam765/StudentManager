<?php

use App\Modules\Auth\AuthServiceProvider;
use App\Modules\Curriculum\CurriculumServiceProvider;
use App\Modules\Enrollment\EnrollmentServiceProvider;
use App\Modules\Faculty\FacultyServiceProvider;
use App\Modules\Grade\GradeServiceProvider;
use App\Modules\Notification\NotificationServiceProvider;
use App\Modules\Room\RoomServiceProvider;
use App\Modules\SchoolClass\SchoolClassServiceProvider;
use App\Modules\Student\StudentServiceProvider;
use App\Modules\Subject\SubjectServiceProvider;
use App\Modules\System\SystemServiceProvider;
use App\Modules\Teacher\TeacherServiceProvider;
use App\Modules\Timetable\TimetableServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    SystemServiceProvider::class,
    RoomServiceProvider::class,
    FacultyServiceProvider::class,
    TeacherServiceProvider::class,
    SchoolClassServiceProvider::class,
    AuthServiceProvider::class,
    SubjectServiceProvider::class,
    CurriculumServiceProvider::class,
    StudentServiceProvider::class,
    TimetableServiceProvider::class,
    EnrollmentServiceProvider::class,
    GradeServiceProvider::class,
    NotificationServiceProvider::class,
];
