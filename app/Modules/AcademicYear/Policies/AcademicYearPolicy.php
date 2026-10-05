<?php

namespace App\Modules\AcademicYear\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

class AcademicYearPolicy extends ModulePolicy
{
    protected string $module = 'ACY';
}
