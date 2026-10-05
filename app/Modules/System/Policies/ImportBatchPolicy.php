<?php

namespace App\Modules\System\Policies;

use App\Models\User;
use App\Modules\System\Models\ImportBatch;
use App\Support\Policies\DenyByDefaultPolicy;

/**
 * Policy cho ImportBatch (GC-08, NFR-SEC-04, NFR-SEC-05).
 * Mặc định từ chối; ghi nhật ký khi từ chối (qua DenyByDefaultPolicy).
 *
 * Kiểm tra quyền ở máy chủ cho từng vai trò:
 *  - ADMIN  : toàn quyền
 *  - ACAD   : xem và quản lý import thuộc phạm vi học vụ
 */
class ImportBatchPolicy extends DenyByDefaultPolicy
{
    /** Xem danh sách lô import */
    public function viewAny(User $user): bool
    {
        return $user->can('SYS.view');
    }

    /** Xem chi tiết lô import */
    public function view(User $user, ImportBatch $batch): bool
    {
        return $user->can('SYS.view') && $this->ownOrAdmin($user, $batch);
    }

    /** Tạo lô import mới (upload file) */
    public function create(User $user): bool
    {
        return $user->can('SYS.import');
    }

    /** Kích hoạt kiểm tra (validate) */
    public function validate(User $user, ImportBatch $batch): bool
    {
        return $user->can('SYS.import') && $this->ownOrAdmin($user, $batch);
    }

    /** Xem kết quả kiểm tra (preview) */
    public function preview(User $user, ImportBatch $batch): bool
    {
        return $user->can('SYS.view') && $this->ownOrAdmin($user, $batch);
    }

    /** Lưu lô */
    public function save(User $user, ImportBatch $batch): bool
    {
        return $user->can('SYS.import') && $this->ownOrAdmin($user, $batch);
    }

    /** Hoàn tác lô */
    public function rollback(User $user, ImportBatch $batch): bool
    {
        return $user->can('SYS.import') && $this->ownOrAdmin($user, $batch);
    }

    /** Xóa lô (xóa mềm) — chỉ ADMIN */
    public function delete(User $user, ImportBatch $batch): bool
    {
        return $user->can('SYS.delete');
    }

    /** Chỉ cho phép người tạo hoặc ADMIN thao tác lô */
    private function ownOrAdmin(User $user, ImportBatch $batch): bool
    {
        return $batch->created_by === $user->id || $user->hasRole('ADMIN');
    }
}
