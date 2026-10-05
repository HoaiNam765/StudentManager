<?php

namespace App\Modules\System\Jobs;

use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Services\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Job lưu file import lớn chạy nền (NFR-PERF-04).
 */
class SaveImportBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 1;

    public function __construct(
        public readonly int $batchId,
        public readonly string $mode,
    ) {}

    public function handle(ImportService $importService): void
    {
        $batch = ImportBatch::findOrFail($this->batchId);
        $importService->doSave($batch, $this->mode);
    }

    public function failed(Throwable $exception): void
    {
        $batch = ImportBatch::find($this->batchId);
        if ($batch) {
            $batch->update([
                'status' => \App\Modules\System\Enums\ImportStatus::Failed,
                'error_summary' => 'Lỗi khi lưu dữ liệu: ' . $exception->getMessage(),
                'finished_at' => now(),
            ]);
        }
    }
}
