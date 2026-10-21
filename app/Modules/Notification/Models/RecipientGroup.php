<?php

namespace App\Modules\Notification\Models;

use App\Support\Models\StandardModel;

/** Nhóm người nhận lưu lại của một người gửi (FR-NOT-015): lưu bộ chọn đối tượng, không lưu danh sách tài khoản. */
class RecipientGroup extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['audience' => 'array'];
    }
}
