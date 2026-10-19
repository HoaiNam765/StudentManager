<?php

namespace App\Modules\Timetable\Events;

use App\Modules\AcademicYear\Models\Term;
use Illuminate\Foundation\Events\Dispatchable;

/** EV-TTB-01 (phụ lục A): công bố thời khóa biểu học kỳ; NOT gửi thông báo cho STU, LEC khi có module thông báo. */
class TimetablePublished
{
    use Dispatchable;

    public function __construct(public readonly Term $term) {}
}
