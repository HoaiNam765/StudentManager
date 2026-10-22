<?php

namespace App\Modules\Grade\Models\Concerns;

/**
 * Cho phép đọc thuộc tính theo tên cũ mà view của nhóm FE đang dùng (ví dụ `score_10` thay cho `score10`), không đổi cột.
 * Chỉ đọc; ghi vẫn dùng tên cột thật. Model khai báo `LEGACY_ALIASES = ['tên cũ' => 'tên cột']`.
 */
trait HasLegacyAttributeAliases
{
    public function getAttribute($key)
    {
        if (is_string($key) && isset(static::LEGACY_ALIASES[$key])) {
            return parent::getAttribute(static::LEGACY_ALIASES[$key]);
        }

        return parent::getAttribute($key);
    }
}
