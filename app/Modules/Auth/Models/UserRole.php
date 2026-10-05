<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Vai trò của người dùng trong một khoảng thời gian (valid_from – valid_to, để trống là không giới hạn).
 */
class UserRole extends Pivot
{
    public $incrementing = true;

    protected $table = 'user_roles';

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_to' => 'date',
        ];
    }
}
