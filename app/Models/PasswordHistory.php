<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Mật khẩu (đã băm) từng dùng, để chặn đặt lại N mật khẩu gần nhất (FR-AUTH-005). */
class PasswordHistory extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = [];

    protected $hidden = ['password'];
}
