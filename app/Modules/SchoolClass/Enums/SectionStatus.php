<?php

namespace App\Modules\SchoolClass\Enums;

/**
 * Vòng đời lớp học phần (FR-CLS-012): Dự kiến → Mở đăng ký → Đóng đăng ký → Đang học → Kết thúc → Đã khóa;
 * hủy được ở ba bước đầu.
 */
enum SectionStatus: string
{
    case Planned = 'planned';
    case RegistrationOpen = 'registration_open';
    case RegistrationClosed = 'registration_closed';
    case InProgress = 'in_progress';
    case Finished = 'finished';
    case Locked = 'locked';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Dự kiến',
            self::RegistrationOpen => 'Mở đăng ký',
            self::RegistrationClosed => 'Đóng đăng ký',
            self::InProgress => 'Đang học',
            self::Finished => 'Kết thúc',
            self::Locked => 'Đã khóa',
            self::Cancelled => 'Đã hủy',
        };
    }

    /** Bước tiếp theo hợp lệ (không tính hủy). */
    public function next(): ?self
    {
        return match ($this) {
            self::Planned => self::RegistrationOpen,
            self::RegistrationOpen => self::RegistrationClosed,
            self::RegistrationClosed => self::InProgress,
            self::InProgress => self::Finished,
            self::Finished => self::Locked,
            default => null,
        };
    }

    public function cancellable(): bool
    {
        return in_array($this, [self::Planned, self::RegistrationOpen, self::RegistrationClosed], true);
    }

    /** Lớp còn hoạt động (chưa kết thúc, chưa khóa, chưa hủy): giảng viên đang được phân công dạy. */
    public function isActive(): bool
    {
        return in_array($this, [self::Planned, self::RegistrationOpen, self::RegistrationClosed, self::InProgress], true);
    }

    /** Còn sửa được cấu hình lớp (sĩ số, hình thức…). */
    public function isEditable(): bool
    {
        return in_array($this, [self::Planned, self::RegistrationOpen], true);
    }

    /** @return list<string> */
    public static function activeValues(): array
    {
        return array_map(fn (self $s) => $s->value, array_filter(self::cases(), fn (self $s) => $s->isActive()));
    }
}
