<?php

namespace App\Modules\Subject\Enums;

/** Loại thành phần điểm (FR-SUB-005). Thi cuối kỳ có trọng số tối thiểu theo quy chế (BR-SUB-04). */
enum ComponentKind: string
{
    case Attendance = 'attendance';
    case Assignment = 'assignment';
    case Midterm = 'midterm';
    case Practical = 'practical';
    case FinalExam = 'final_exam';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Attendance => 'Chuyên cần',
            self::Assignment => 'Bài tập',
            self::Midterm => 'Giữa kỳ',
            self::Practical => 'Thực hành',
            self::FinalExam => 'Thi cuối kỳ',
            self::Other => 'Khác',
        };
    }
}
