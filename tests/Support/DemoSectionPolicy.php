<?php

namespace Tests\Support;

use App\Modules\Auth\Policies\ModulePolicy;

/** Ví dụ policy của module: chỉ khai báo mã module, mọi quyết định theo ma trận phân quyền. */
class DemoSectionPolicy extends ModulePolicy
{
    protected string $module = 'GRD';
}
