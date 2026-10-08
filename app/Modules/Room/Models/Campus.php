<?php

namespace App\Modules\Room\Models;

use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Cơ sở đào tạo (FR-ROM-001). */
class Campus extends StandardModel
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }
}
