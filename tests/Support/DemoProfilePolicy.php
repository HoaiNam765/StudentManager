<?php

namespace Tests\Support;

use App\Modules\Auth\Policies\ModulePolicy;

class DemoProfilePolicy extends ModulePolicy
{
    protected string $module = 'STU';
}
