<?php

namespace App\Modules\System\Jobs;

use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Services\ImportRegistry;
use App\Modules\System\Services\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Job kiểm tra file import lớn chạy nền (NFR-PERF-04).
 * 5.000 dòng phải hoàn tất trong 60 giây.
 */
class ValidateImportBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Thời gian tối đa (giây) trước khi queue coi là timeout */
    public int $timeout = 120;

    /** Số lần thử lại khi gặp lỗi không mong muốn */
    public int $tries = 1;

    public function __construct(public readonly int $batchId) {}

    public function handle(ImportService $importService, ImportRegistry $registry): void
    {
        $batch = ImportBatch::findOrFail($this->batchId);
        $importer = $registry->get($batch->importer);
        $rows = $importer->parseRows($batch->path, $batch->disk);

        $importService->doValidate($batch, $rows);
    }

    public function failed(Throwable $exception): void
    {
        $batch = ImportBatch::find($this->batchId);
        if ($batch) {
            $batch->update([
                'status' => \App\Modules\System\Enums\ImportStatus::Failed,
                'error_summary' => 'Lỗi khi kiểm tra file: ' . $exception->getMessage(),
                'finished_at' => now(),
            ]);
        }
    }
}
