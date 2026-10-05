<?php

namespace Tests\Support;

use App\Support\Models\StandardModel;

/** Ví dụ model có trường nhạy cảm: che hoặc bỏ qua khi ghi nhật ký kiểm toán. */
class DemoSensitiveItem extends StandardModel
{
    protected $table = 'demo_items';

    protected $guarded = [];

    protected $hidden = ['secret_token'];

    protected array $auditMasked = ['id_number'];

    protected array $auditExclude = ['viewed_at'];
}
