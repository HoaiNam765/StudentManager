<?php

namespace App\Modules\System\Models;

use App\Support\Concerns\HasStandardFields;
use Illuminate\Database\Eloquent\Model;

/**
 * Một tham số hệ thống (FR-SYS-001). Không dùng StandardModel: SettingService tự ghi nhật ký trước – sau
 * theo giá trị đang hiệu lực (kể cả khi trước đó đang dùng giá trị mặc định), tránh ghi hai lần.
 * Đọc và ghi qua App\Modules\System\Services\SettingService, không sửa trực tiếp.
 */
class SystemSetting extends Model
{
    use HasStandardFields;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }
}
