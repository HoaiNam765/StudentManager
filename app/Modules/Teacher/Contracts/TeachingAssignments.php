<?php

namespace App\Modules\Teacher\Contracts;

use App\Modules\Teacher\Models\Lecturer;

/**
 * Lớp học phần giảng viên đang dạy, phải chuyển cho người khác trước khi ghi nhận nghỉ việc / nghỉ hưu (BR-TCH-02).
 *
 * Chưa có module CLS nên bản mặc định (NoTeachingAssignments) trả về rỗng; CLS (#107) cài lớp thật và bind
 * trong ServiceProvider của mình:
 *
 *     $this->app->bind(TeachingAssignments::class, SectionTeachingAssignments::class);
 */
interface TeachingAssignments
{
    /**
     * Các lớp đang dạy hoặc đã được phân công mà chưa kết thúc, mỗi phần tử là một dòng mô tả để hiển thị
     * (ví dụ "INT201.01 – Cấu trúc dữ liệu và giải thuật (HK1 2026–2027)").
     *
     * @return list<string>
     */
    public function activeSections(Lecturer $lecturer): array;

    /**
     * Số lớp học phần đang hoạt động và tổng số tiết được phân công trong học kỳ, theo từng giảng viên.
     *
     * @param  list<int>  $lecturerIds
     * @return array<int, array{sections: int, periods: int}>
     */
    public function termLoads(array $lecturerIds, int $termId): array;
}
