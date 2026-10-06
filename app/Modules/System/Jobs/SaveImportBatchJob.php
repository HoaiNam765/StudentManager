<?php

namespace App\Modules\System\Jobs;

use App\Modules\System\Enums\ImportStatus;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Services\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Job lưu file import lớn chạy nền (NFR-PERF-04). Chạy với tư cách người đã bấm lưu để nhật ký ghi đúng người (GC-03).
 */
class SaveImportBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Thời gian tối đa (giây) của job; lấy từ cấu hình (GC-12) */
    public int $timeout;

    public int $tries = 1;

    public function __construct(
        public readonly int $batchId,
        public readonly string $mode,
        public readonly ?int $userId = null,
    ) {
        $this->timeout = (int) config('studentmanager.import.job_timeout_seconds');
    }

    public function handle(ImportService $importService): void
    {
        $batch = ImportBatch::query()->find($this->batchId);

        // Lô đã bị xóa hoặc không còn ở trạng thái đang lưu (job chạy trùng): bỏ qua, không lưu lần hai
        if ($batch === null || $batch->status !== ImportStatus::Saving) {
            return;
        }

        $importService->runAs($this->userId, fn () => $importService->doSave($batch, $this->mode));
    }

    public function failed(Throwable $exception): void
    {
        $service = app(ImportService::class);

        $service->runAs(
            $this->userId,
            fn () => $service->failBatchById($this->batchId, 'Lỗi khi lưu dữ liệu: '.$exception->getMessage().' Không có dòng nào được lưu.')
        );
    }
}
