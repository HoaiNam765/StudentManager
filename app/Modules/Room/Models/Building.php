<?php

namespace App\Modules\Room\Models;

use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Tòa nhà thuộc một cơ sở (FR-ROM-001). */
class Building extends StandardModel
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    protected function casts(): array
    {
        return ['floors' => 'integer'];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
