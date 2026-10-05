<?php

namespace App\Support\Audit;

use LogicException;

class AuditLogImmutableException extends LogicException
{
    public static function cannotModify(): self
    {
        return new self('Nhật ký kiểm toán chỉ được ghi thêm, không được sửa hoặc xóa (BR-SYS-01).');
    }
}
