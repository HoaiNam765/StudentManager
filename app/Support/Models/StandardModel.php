<?php

namespace App\Support\Models;

use App\Support\Audit\Auditable;
use App\Support\Concerns\HasStandardFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model nền cho dữ liệu nghiệp vụ: có người tạo/sửa (GC-11), xóa mềm (GC-02)
 * và tự ghi nhật ký kiểm toán khi tạo, sửa, xóa, khôi phục (GC-03).
 *
 * Thêm các trait tùy loại dữ liệu:
 *   - HasActiveStatus      cho danh mục có Hoạt động / Ngừng hoạt động
 *   - HasEffectivePeriod   cho dữ liệu có hiệu lực theo thời gian
 *   - HasVietnameseSearch  cho dữ liệu cần tìm không dấu
 *
 *     class Faculty extends StandardModel
 *     {
 *         use HasActiveStatus, HasVietnameseSearch;
 *
 *         protected array $searchable = ['code', 'name'];
 *     }
 */
abstract class StandardModel extends Model
{
    use Auditable;
    use HasStandardFields;
    use SoftDeletes;
}
