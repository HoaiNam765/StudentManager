<?php

namespace App\Modules\Auth\Enums;

/** Loại hồ sơ chính của tài khoản (BR-AUTH-01) và vai trò mặc định khi tạo tài khoản (BA mục 4.1). */
enum ProfileType: string
{
    case Student = 'student';
    case Teacher = 'teacher';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Sinh viên',
            self::Teacher => 'Giảng viên',
            self::Staff => 'Cán bộ, nhân viên',
        };
    }

    /** Vai trò gán sẵn khi tạo tài khoản; nhân viên thì chọn vai trò theo phòng ban. */
    public function defaultRole(): ?string
    {
        return match ($this) {
            self::Student => 'STU',
            self::Teacher => 'LEC',
            self::Staff => null,
        };
    }
}
