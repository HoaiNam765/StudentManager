<?php

namespace App\Modules\System\Models;

use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Một giá trị của danh mục dùng chung. Mã ổn định: không đổi sau khi tạo; đã được tham chiếu thì
 * chỉ ngừng sử dụng, không xóa (BR-SYS-09). Module nào lưu khóa ngoại tới bảng này phải đăng ký với
 * App\Support\References\ReferenceRegistry để quy tắc trên biết tới.
 */
class LookupValue extends StandardModel
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LookupCategory::class, 'lookup_category_id');
    }
}
