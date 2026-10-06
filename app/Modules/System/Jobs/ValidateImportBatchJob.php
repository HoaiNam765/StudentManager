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
 * Job kiểm tra file import lớn chạy nền (NFR-PERF-04).
 * 5.000 dòng phải hoàn tất trong 60 giây. Chạy với tư cách người đã bấm kiểm tra để nhật ký ghi đúng người (GC-03).
 */
class ValidateImportBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Thời gian tối đa (giây) của job; lấy từ cấu hình (GC-12) */
    public int $timeout;

    /** Không tự thử lại: lỗi thì lô chuyển sang Failed để người dùng tải lại file */
    public int $tries = 1;

    public function __construct(
        public readonly int $batchId,
        public readonly ?int $userId = null,
    ) {
        $this->timeout = (int) config('studentmanager.import.job_timeout_seconds');
    }

    public function handle(ImportService $importService): void
    {
        $batch = ImportBatch::query()->find($this->batchId);

        // Lô đã bị xóa hoặc không còn ở trạng thái đang kiểm tra (job chạy trùng): bỏ qua
        if ($batch === null || $batch->status !== ImportStatus::Validating) {
            return;
        }

        $importService->runAs($this->userId, fn () => $importService->validateInBackground($batch));
    }

    public function failed(Throwable $exception): void
    {
        $service = app(ImportService::class);

        $service->runAs(
            $this->userId,
            fn () => $service->failBatchById($this->batchId, 'Lỗi khi kiểm tra file: '.$exception->getMessage())
        );
    }
}
