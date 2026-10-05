<?php

namespace App\Modules\System\Services;

use App\Models\User;
use App\Modules\System\Enums\ImportRowStatus;
use App\Modules\System\Enums\ImportStatus;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Models\ImportRow;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Dịch vụ lõi của Trung tâm Import (FR-SYS-007, BR-SYS-07, GC-07).
 *
 * Quy trình:
 *   1. upload()     — tải file, kiểm tra cơ bản, tạo ImportBatch
 *   2. validate()   — kiểm tra từng dòng theo quy tắc của Importer
 *   3. preview()    — xem trước kết quả kiểm tra
 *   4. save()       — lưu (all_valid | all)
 *   5. rollback()   — hoàn tác theo lô nếu chưa có dữ liệu phụ thuộc
 *
 * File lớn (>= ngưỡng cấu hình) được dispatch sang hàng đợi (NFR-PERF-04).
 */
class ImportService extends BaseService
{
    /** Disk mặc định để lưu file import (lấy từ cấu hình, GC-12) */
    private string $disk;

    /** Số dòng tối đa xử lý đồng bộ, vượt ngưỡng sẽ chạy nền (NFR-PERF-04) */
    private int $asyncThreshold;

    public function __construct(
        private readonly ImportRegistry $registry,
        private readonly AuditLogger $audit,
    ) {
        $this->disk = config('studentmanager.import.disk', 'local');
        $this->asyncThreshold = (int) config('studentmanager.import.async_threshold', 500);
    }

    // -------------------------------------------------------------------------
    // Bước 1: Tải file và tạo lô
    // -------------------------------------------------------------------------

    /**
     * Tải file lên và tạo ImportBatch mới (trạng thái pending).
     * Ném BusinessRuleException nếu file không đúng định dạng.
     *
     * @throws BusinessRuleException
     */
    public function upload(string $importerKey, UploadedFile $file, User $user): ImportBatch
    {
        $importer = $this->registry->get($importerKey);
        $importer->validateFile($file);

        $path = $file->store("imports/{$importerKey}/" . now()->format('Ymd'), $this->disk);

        if ($path === false) {
            throw new BusinessRuleException(
                'Không thể lưu file tải lên.',
                'Kiểm tra dung lượng đĩa và quyền ghi thư mục storage.'
            );
        }

        return $this->transaction(function () use ($importerKey, $file, $path, $user): ImportBatch {
            $batch = ImportBatch::create([
                'code' => $this->generateCode($importerKey),
                'importer' => $importerKey,
                'original_filename' => $file->getClientOriginalName(),
                'disk' => $this->disk,
                'path' => $path,
                'status' => ImportStatus::Pending,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $this->audit->record(AuditEvent::Created, $batch);

            return $batch;
        });
    }

    // -------------------------------------------------------------------------
    // Bước 2: Kiểm tra từng dòng
    // -------------------------------------------------------------------------

    /**
     * Kiểm tra từng dòng của lô. Nếu file lớn hơn ngưỡng, dispatch job chạy nền.
     * Trả về $batch đã cập nhật.
     *
     * @throws BusinessRuleException
     */
    public function validate(ImportBatch $batch): ImportBatch
    {
        $this->assertStatus($batch, [ImportStatus::Pending]);

        $importer = $this->registry->get($batch->importer);
        $rows = $importer->parseRows($batch->path, $batch->disk);
        $totalRows = count($rows);

        $batch->update([
            'status' => ImportStatus::Validating,
            'total_rows' => $totalRows,
            'started_at' => now(),
        ]);

        if ($totalRows === 0) {
            $this->fail('File không có dữ liệu.', 'Hãy tải file mẫu, điền dữ liệu và thử lại.');
        }

        // File lớn → chạy nền (NFR-PERF-04)
        if ($totalRows >= $this->asyncThreshold) {
            $job = \App\Modules\System\Jobs\ValidateImportBatchJob::dispatch($batch->id);
            $batch->update(['job_id' => (string) $job->getJobId()]);

            return $batch->refresh();
        }

        // File nhỏ → xử lý đồng bộ
        $this->doValidate($batch, $rows);

        return $batch->refresh();
    }

    /**
     * Thực hiện kiểm tra từng dòng (đồng bộ hoặc từ job).
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function doValidate(ImportBatch $batch, array $rows): void
    {
        $importer = $this->registry->get($batch->importer);
        $total = count($rows);
        $validCount = 0;
        $invalidCount = 0;

        // Xóa các dòng cũ nếu validate lại
        ImportRow::where('import_batch_id', $batch->id)->delete();

        foreach ($rows as $index => $rawRow) {
            $rowNumber = $index + 2; // dòng 1 là header
            $errors = $importer->validateRow($rowNumber, $rawRow);

            $status = empty($errors) ? ImportRowStatus::Valid : ImportRowStatus::Invalid;
            if ($status === ImportRowStatus::Valid) {
                $validCount++;
            } else {
                $invalidCount++;
            }

            ImportRow::create([
                'import_batch_id' => $batch->id,
                'row_number' => $rowNumber,
                'raw_data' => $rawRow,
                'status' => $status,
                'errors' => empty($errors) ? null : $errors,
            ]);

            // Cập nhật tiến trình mỗi 50 dòng
            if (($index + 1) % 50 === 0 || ($index + 1) === $total) {
                $progress = (int) round((($index + 1) / $total) * 50); // 0–50% cho validate
                $batch->update(['progress' => $progress]);
            }
        }

        $batch->update([
            'status' => ImportStatus::Validated,
            'valid_rows' => $validCount,
            'invalid_rows' => $invalidCount,
            'total_rows' => $total,
            'progress' => 50,
        ]);
    }

    // -------------------------------------------------------------------------
    // Bước 3: Xem trước kết quả
    // -------------------------------------------------------------------------

    /**
     * Lấy dữ liệu xem trước: thống kê + danh sách dòng có lỗi.
     *
     * @return array{batch: ImportBatch, error_rows: \Illuminate\Database\Eloquent\Collection}
     */
    public function preview(ImportBatch $batch): array
    {
        return [
            'batch' => $batch->refresh(),
            'error_rows' => $batch->invalidRows()->orderBy('row_number')->get(),
        ];
    }

    // -------------------------------------------------------------------------
    // Bước 4: Lưu
    // -------------------------------------------------------------------------

    /**
     * Lưu lô import theo chế độ:
     *   all_valid  — chỉ lưu các dòng hợp lệ, bỏ qua dòng lỗi (GC-07)
     *   all        — lưu tất cả; nếu có bất kỳ lỗi nào thì rollback toàn bộ
     *
     * @param  'all_valid'|'all'  $mode
     * @throws BusinessRuleException
     */
    public function save(ImportBatch $batch, string $mode, User $user): ImportBatch
    {
        $this->assertStatus($batch, [ImportStatus::Validated]);

        if (! in_array($mode, ['all_valid', 'all'], true)) {
            $this->fail("Chế độ lưu '{$mode}' không hợp lệ.", "Dùng 'all_valid' hoặc 'all'.");
        }

        if ($batch->valid_rows === 0) {
            $this->fail(
                'Không có dòng hợp lệ để lưu.',
                'Kiểm tra lại file và sửa các lỗi trước khi lưu.'
            );
        }

        if ($mode === 'all' && $batch->invalid_rows > 0) {
            $this->fail(
                'Không thể lưu toàn bộ vì có ' . $batch->invalid_rows . ' dòng lỗi.',
                "Hãy chọn 'Chỉ lưu dòng hợp lệ' hoặc sửa các dòng lỗi rồi tải lại file."
            );
        }

        $batch->update([
            'status' => ImportStatus::Saving,
            'save_mode' => $mode,
            'updated_by' => $user->id,
        ]);

        $total = $batch->total_rows;

        // File lớn → chạy nền
        if ($total >= $this->asyncThreshold) {
            $job = \App\Modules\System\Jobs\SaveImportBatchJob::dispatch($batch->id, $mode);
            $batch->update(['job_id' => (string) $job->getJobId()]);

            return $batch->refresh();
        }

        // File nhỏ → xử lý đồng bộ
        $this->doSave($batch, $mode);

        return $batch->refresh();
    }

    /**
     * Thực hiện lưu từng dòng (đồng bộ hoặc từ job).
     */
    public function doSave(ImportBatch $batch, string $mode): void
    {
        $importer = $this->registry->get($batch->importer);
        $rows = $batch->rows()->orderBy('row_number')->get();
        $total = $rows->count();
        $savedCount = 0;

        try {
            $this->transaction(function () use ($batch, $importer, $rows, $mode, $total, &$savedCount): void {
                foreach ($rows as $index => $row) {
                    if ($row->status === ImportRowStatus::Invalid) {
                        // all_valid: bỏ qua dòng lỗi; all: không thể tới đây (đã check trước)
                        $row->markSkipped();
                        continue;
                    }

                    if ($row->status !== ImportRowStatus::Valid) {
                        continue;
                    }

                    $result = $importer->saveRow($row->row_number, $row->raw_data);
                    $row->markSaved($result['type'], $result['id']);
                    $savedCount++;

                    // Cập nhật tiến trình 50–100%
                    if (($index + 1) % 50 === 0 || ($index + 1) === $total) {
                        $progress = 50 + (int) round((($index + 1) / $total) * 50);
                        $batch->update(['progress' => min(99, $progress)]);
                    }
                }

                $batch->update([
                    'status' => ImportStatus::Saved,
                    'saved_rows' => $savedCount,
                    'progress' => 100,
                    'finished_at' => now(),
                ]);
            });
        } catch (BusinessRuleException $e) {
            $batch->update([
                'status' => ImportStatus::Failed,
                'error_summary' => $e->userMessage(),
                'finished_at' => now(),
            ]);
            throw $e;
        } catch (Throwable $e) {
            $batch->update([
                'status' => ImportStatus::Failed,
                'error_summary' => 'Lỗi hệ thống khi lưu dữ liệu: ' . $e->getMessage(),
                'finished_at' => now(),
            ]);
            throw $e;
        }

        $this->audit->record(
            AuditEvent::Created,
            $batch,
            [],
            [
                'save_mode' => $mode,
                'saved_rows' => $savedCount,
                'total_rows' => $total,
            ]
        );
    }

    // -------------------------------------------------------------------------
    // Bước 5: Hoàn tác
    // -------------------------------------------------------------------------

    /**
     * Hoàn tác toàn lô: xóa các bản ghi đã lưu nếu chưa có dữ liệu phụ thuộc (BR-SYS-07).
     *
     * @throws BusinessRuleException
     */
    public function rollback(ImportBatch $batch, User $user): ImportBatch
    {
        if (! $batch->canRollback()) {
            $this->fail(
                'Lô này không thể hoàn tác (trạng thái: ' . $batch->status->label() . ').',
                'Chỉ có thể hoàn tác lô đã lưu thành công.'
            );
        }

        $importer = $this->registry->get($batch->importer);
        $savedRows = $batch->savedRows()->get();
        $cannotRollback = [];

        // Kiểm tra trước: dòng nào có dữ liệu phụ thuộc?
        foreach ($savedRows as $row) {
            if (! $importer->rollbackRow($row->saved_model_type, $row->saved_model_id)) {
                $cannotRollback[] = $row->row_number;
            }
        }

        if (! empty($cannotRollback)) {
            $lines = implode(', ', array_slice($cannotRollback, 0, 10));
            $suffix = count($cannotRollback) > 10 ? '…' : '';
            $this->fail(
                'Không thể hoàn tác vì ' . count($cannotRollback) . " dòng đã có dữ liệu phụ thuộc (dòng {$lines}{$suffix}).",
                'Hãy xử lý các dữ liệu phụ thuộc trước khi hoàn tác.'
            );
        }

        $this->transaction(function () use ($batch, $savedRows, $importer, $user): void {
            foreach ($savedRows as $row) {
                $importer->rollbackRow($row->saved_model_type, $row->saved_model_id);
                $row->update(['status' => ImportRowStatus::Pending]);
            }

            $batch->update([
                'status' => ImportStatus::RolledBack,
                'saved_rows' => 0,
                'updated_by' => $user->id,
                'finished_at' => now(),
            ]);

            $this->audit->record(
                AuditEvent::Deleted,
                $batch,
                ['saved_rows' => $savedRows->count()],
                ['status' => ImportStatus::RolledBack->value]
            );
        });

        return $batch->refresh();
    }

    // -------------------------------------------------------------------------
    // Hàm phụ trợ
    // -------------------------------------------------------------------------

    /**
     * Sinh mã lô: IMP-{IMPORTER}-{YYYYMMDD}-{XXXX}
     */
    private function generateCode(string $importerKey): string
    {
        $prefix = 'IMP-' . strtoupper(substr($importerKey, 0, 6)) . '-' . now()->format('Ymd') . '-';
        $last = ImportBatch::where('code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('code');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Kiểm tra trạng thái lô, ném exception nếu không đúng.
     *
     * @param  list<ImportStatus>  $allowed
     */
    private function assertStatus(ImportBatch $batch, array $allowed): void
    {
        if (! in_array($batch->status, $allowed, true)) {
            $allowedLabels = implode(', ', array_map(fn ($s) => $s->label(), $allowed));
            $this->fail(
                "Lô import #{$batch->code} ở trạng thái '{$batch->status->label()}', không thể thực hiện thao tác này.",
                "Thao tác này chỉ được phép khi lô ở trạng thái: {$allowedLabels}."
            );
        }
    }
}
