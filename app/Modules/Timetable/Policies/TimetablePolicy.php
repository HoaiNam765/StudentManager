<?php

namespace App\Modules\Timetable\Policies;

use App\Modules\Auth\Policies\ModulePolicy;

/** Khung tiết, mẫu lịch: theo ma trận phân quyền mục "TTB" (ACAD toàn trường; DEAN, LEC xem / đề xuất trong phạm vi). */
class TimetablePolicy extends ModulePolicy
{
    protected string $module = 'TTB';
}
