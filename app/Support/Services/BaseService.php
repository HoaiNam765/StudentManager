<?php

namespace App\Support\Services;

use App\Support\Exceptions\BusinessRuleException;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Lớp nền cho Service của các module. Quy tắc nghiệp vụ lõi viết ở Service
 * (không viết trong Controller) để kiểm thử đơn vị được (NFR-MNT-02).
 *
 *     namespace App\Modules\Faculty\Services;
 *
 *     class FacultyService extends BaseService
 *     {
 *         public function deactivate(Faculty $faculty): void
 *         {
 *             if ($faculty->departments()->active()->exists()) {
 *                 $this->fail(
 *                     'Khoa còn bộ môn đang hoạt động.',
 *                     'Hãy chuyển hoặc ngừng các bộ môn trước.'
 *                 );
 *             }
 *
 *             $this->transaction(fn () => $faculty->deactivate());
 *         }
 *     }
 */
abstract class BaseService
{
    /** Chạy trong giao dịch nguyên tử: lỗi ở bước nào thì hoàn tác toàn bộ. */
    protected function transaction(Closure $callback, int $attempts = 1): mixed
    {
        return DB::transaction($callback, $attempts);
    }

    /** Từ chối vì vi phạm quy tắc nghiệp vụ, kèm cách khắc phục. */
    protected function fail(string $message, ?string $hint = null): never
    {
        throw new BusinessRuleException($message, $hint);
    }
}
