<?php

namespace App\Modules\Grade\Enums;

/** Trạng thái bảng điểm của lớp học phần (FR-GRD-004). Sinh viên chỉ thấy điểm khi Đã công bố / Đã khóa (BR-GRD-08). */
enum GradePublicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case FacultyApproved = 'faculty_approved';
    case Confirmed = 'confirmed';
    case Published = 'published';
    case Locked = 'locked';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nháp',
            self::Submitted => 'Đã nộp',
            self::FacultyApproved => 'Khoa đã duyệt',
            self::Confirmed => 'Đã xác nhận',
            self::Published => 'Đã công bố',
            self::Locked => 'Đã khóa',
            self::Returned => 'Bị trả lại',
        };
    }

    public function public(): bool
    {
        return in_array($this, [self::Published, self::Locked], true);
    }

    /** Giảng viên còn sửa điểm trực tiếp được (BR-GRD-06). */
    public function editable(): bool
    {
        return in_array($this, [self::Draft, self::Returned], true);
    }

    public function allows(self $next, bool $facultyRequired): bool
    {
        return match ($this) {
            self::Draft => $next === self::Submitted,
            self::Submitted => in_array($next, $facultyRequired ? [self::FacultyApproved, self::Returned] : [self::Confirmed, self::Returned], true),
            self::FacultyApproved => in_array($next, [self::Confirmed, self::Returned], true),
            self::Confirmed => in_array($next, [self::Published, self::Returned], true),
            self::Published => $next === self::Locked,
            self::Returned => in_array($next, [self::Draft, self::Submitted], true),
            self::Locked => false,
        };
    }
}
