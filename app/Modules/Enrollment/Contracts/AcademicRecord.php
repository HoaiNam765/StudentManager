<?php

namespace App\Modules\Enrollment\Contracts;

use App\Modules\Student\Models\Student;

/**
 * Kết quả học tập ENR cần để xét điều kiện đăng ký (BR-ENR-02, 03, 07). Mặc định chưa có điểm (EmptyAcademicRecord);
 * GRD (#120, #125) bind cài đặt thật theo quy tắc đạt BR-GRD-03.
 */
interface AcademicRecord
{
    /** @return list<int> học phần sinh viên đã đạt */
    public function passedSubjectIds(Student $student): array;

    /** @return list<int> học phần sinh viên đã học (có kết quả, đạt hoặc không) — dùng cho điều kiện "học trước" */
    public function attemptedSubjectIds(Student $student): array;

    /** Đang bị cảnh báo học vụ (khung tín chỉ riêng, BR-ENR-02). */
    public function onProbation(Student $student): bool;

    /** Học lực từ khá trở lên ở học kỳ gần nhất (được đăng ký thêm, BR-ENR-02). */
    public function goodStanding(Student $student): bool;
}
