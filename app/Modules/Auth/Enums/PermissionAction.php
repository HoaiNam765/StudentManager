<?php

namespace App\Modules\Auth\Enums;

use InvalidArgumentException;

/**
 * Hành động trong ma trận phân quyền (docs/BA.md mục 4.2):
 * C tạo · R xem · U sửa · D xóa mềm · A phê duyệt / xác nhận / khóa · X xuất dữ liệu.
 */
enum PermissionAction: string
{
    case Create = 'create';
    case View = 'view';
    case Update = 'update';
    case Delete = 'delete';
    case Approve = 'approve';
    case Export = 'export';

    public static function fromLetter(string $letter): self
    {
        return match ($letter) {
            'C' => self::Create,
            'R' => self::View,
            'U' => self::Update,
            'D' => self::Delete,
            'A' => self::Approve,
            'X' => self::Export,
            default => throw new InvalidArgumentException("Ký hiệu quyền không hợp lệ: {$letter}"),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Create => 'Tạo',
            self::View => 'Xem',
            self::Update => 'Sửa',
            self::Delete => 'Xóa',
            self::Approve => 'Duyệt / xác nhận / khóa',
            self::Export => 'Xuất dữ liệu',
        };
    }
}
