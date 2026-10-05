<?php

namespace Tests\Support;

use App\Support\Concerns\HasEffectivePeriod;
use App\Support\Models\StandardModel;

/** Ví dụ dữ liệu có hiệu lực theo thời gian (kiểu khung giá, nhiệm kỳ). */
class DemoRate extends StandardModel
{
    use HasEffectivePeriod;

    protected $table = 'demo_rates';

    protected $guarded = [];
}
