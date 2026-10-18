<?php

use App\Modules\Auth\AuthServiceProvider;
use App\Modules\Curriculum\CurriculumServiceProvider;
use App\Modules\Faculty\FacultyServiceProvider;
use App\Modules\Room\RoomServiceProvider;
use App\Modules\Student\StudentServiceProvider;
use App\Modules\Subject\SubjectServiceProvider;
use App\Modules\System\SystemServiceProvider;
use App\Modules\Teacher\TeacherServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    SystemServiceProvider::class,
    RoomServiceProvider::class,
    FacultyServiceProvider::class,
    TeacherServiceProvider::class,
    AuthServiceProvider::class,
    SubjectServiceProvider::class,
    CurriculumServiceProvider::class,
    StudentServiceProvider::class,
];
