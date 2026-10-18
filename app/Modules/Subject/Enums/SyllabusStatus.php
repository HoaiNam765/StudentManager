<?php

namespace App\Modules\Subject\Enums;

/** Trạng thái một phiên bản đề cương (FR-SUB-006, BR-SUB-08). */
enum SyllabusStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Superseded = 'superseded';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nháp',
            self::Submitted => 'Chờ duyệt',
            self::Approved => 'Đã duyệt',
            self::Rejected => 'Bị trả lại',
            self::Superseded => 'Đã thay thế',
        };
    }

    /** Còn sửa được nội dung (nháp hoặc bị trả lại). */
    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Rejected], true);
    }
}
