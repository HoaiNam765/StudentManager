<?php

namespace App\Modules\System\Models;

use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Loại danh mục dùng chung (FR-SYS-002): giới tính, dân tộc, tôn giáo… Mã loại là hằng số mà các module dùng
 * để lấy danh sách lựa chọn, ví dụ `LookupService::options(LookupCategory::GENDER)`.
 */
class LookupCategory extends StandardModel
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    public const GENDER = 'GENDER';

    public const ETHNICITY = 'ETHNICITY';

    public const RELIGION = 'RELIGION';

    public const NATIONALITY = 'NATIONALITY';

    public const PRIORITY_GROUP = 'PRIORITY_GROUP';

    public const PRIORITY_AREA = 'PRIORITY_AREA';

    public const CONTRACT_TYPE = 'CONTRACT_TYPE';

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }

    public function values(): HasMany
    {
        return $this->hasMany(LookupValue::class);
    }
}
