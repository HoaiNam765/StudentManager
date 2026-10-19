<?php

namespace App\Modules\Student\Events;

use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentStatusHistory;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * EV-STU-02 (phụ lục A): sinh viên đổi trạng thái theo quyết định. Phát trong giao dịch, nghe đồng bộ: ENR hủy đăng ký
 * chưa chốt của kỳ chưa bắt đầu (BR-STU-10). NOT gửi thông báo bắt buộc cho STU, ADV khi có.
 */
class StudentStatusChanged
{
    use Dispatchable;

    public function __construct(
        public readonly Student $student,
        public readonly StudentStatus $from,
        public readonly StudentStatus $to,
        public readonly StudentStatusHistory $history,
    ) {}
}
