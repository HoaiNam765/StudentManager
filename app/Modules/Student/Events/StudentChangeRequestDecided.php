<?php

namespace App\Modules\Student\Events;

use App\Modules\Student\Models\StudentChangeRequest;
use Illuminate\Foundation\Events\Dispatchable;

/** EV-STU-03 (phụ lục A): yêu cầu sửa thông tin của sinh viên đã được duyệt hoặc từ chối; NOT báo cho sinh viên khi có. */
class StudentChangeRequestDecided
{
    use Dispatchable;

    public function __construct(public readonly StudentChangeRequest $request) {}
}
