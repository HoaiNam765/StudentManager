<?php

namespace App\Modules\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Một tham số trong bộ quy chế; giá trị có thể là số hoặc bảng (thang điểm, xếp loại). */
class PolicyItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    public function policySet(): BelongsTo
    {
        return $this->belongsTo(PolicySet::class);
    }
}
