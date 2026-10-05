<?php

namespace Tests\Support;

use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;

/**
 * Ví dụ một model danh mục dùng lớp nền. Module thật làm tương tự:
 * kế thừa StandardModel rồi thêm các trait cần dùng.
 */
class DemoItem extends StandardModel
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $table = 'demo_items';

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];
}
