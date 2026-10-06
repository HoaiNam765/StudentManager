<?php

namespace App\Modules\System\Policies;

use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Policies\ModulePolicy;
use App\Modules\System\Models\ImportBatch;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Quyền với lô import (GC-08, NFR-SEC-04, NFR-SEC-05): theo ma trận phân quyền, mục "SYS"
 * (mặc định chỉ ADMIN có Tạo/Xem/Xóa; ACAD chỉ xem), mặc định từ chối.
 *
 *  - Xem, xem trước, tiến trình : SYS.view   (viewAny, view, preview)
 *  - Tải lên, kiểm tra, lưu     : SYS.create (create, validate, save)
 *  - Hoàn tác, xóa lô           : SYS.delete (rollback, delete)
 *
 * Trên từng lô còn kiểm tra phạm vi dữ liệu: quyền phạm vi OWN chỉ áp dụng cho lô do chính mình tạo (chống IDOR).
 */
class ImportBatchPolicy extends ModulePolicy
{
    protected string $module = 'SYS';

    /** Kích hoạt kiểm tra từng dòng */
    public function validate(?Authenticatable $user, ImportBatch $batch): bool
    {
        return $this->access()->allowsOn($user, $this->module, PermissionAction::Create, $batch);
    }

    /** Xem trước kết quả kiểm tra */
    public function preview(?Authenticatable $user, ImportBatch $batch): bool
    {
        return $this->view($user, $batch);
    }

    /** Lưu lô */
    public function save(?Authenticatable $user, ImportBatch $batch): bool
    {
        return $this->access()->allowsOn($user, $this->module, PermissionAction::Create, $batch);
    }

    /** Hoàn tác lô */
    public function rollback(?Authenticatable $user, ImportBatch $batch): bool
    {
        return $this->access()->allowsOn($user, $this->module, PermissionAction::Delete, $batch);
    }
}
