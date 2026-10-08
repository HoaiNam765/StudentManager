<?php

namespace App\Modules\System\Services;

use App\Models\User;
use App\Modules\System\Enums\ImportRowStatus;
use App\Modules\System\Enums\ImportStatus;
use App\Modules\System\Jobs\SaveImportBatchJob;
use App\Modules\System\Jobs\ValidateImportBatchJob;
use App\Modules\System\Models\ImportBatch;
use App\Modules\System\Models\ImportRow;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Services\BaseService;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
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
 * File lớn (>= ngưỡng cấu hình) được dispatch sang hàng đợi (NFR-PERF-04). Mọi ngưỡng lấy từ
 * config('studentmanager.import') (GC-12).
 *
 * Chống chạy trùng: chuyển trạng thái lô bằng cập nhật có điều kiện (claim), chỉ một yêu cầu thắng.
 */
class ImportService extends BaseService
{
    /** Số lần thử lại khi mã lô bị trùng do hai yêu cầu tải lên cùng lúc */
    private const CODE_ATTEMPTS = 5;

    public function __construct(
        private readonly ImportRegistry $registry,
        private readonly AuditLogger $audit,
    ) {}

    // -------------------------------------------------------------------------
    // Bước 1: Tải file và tạo lô
    // -------------------------------------------------------------------------

    /**
     * Tải file lên và tạo ImportBatch mới (trạng thái pending).
     *
     * @throws BusinessRuleException
     */
    public function upload(string $importerKey, UploadedFile $file, User $user): ImportBatch
    {
        $importer = $this->registry->get($importerKey);
        $importer->validateFile($file);

        $disk = $this->disk();
        $path = $file->store("imports/{$importerKey}/".now()->format('Ymd'), $disk);

        if ($path === false) {
            $this->fail(
                'Không thể lưu file tải lên.',
                'Kiểm tra dung lượng đĩa và quyền ghi thư mục storage.'
            );
        }

        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                return $this->transaction(function () use ($importerKey, $file, $disk, $path, $user): ImportBatch {
                    $batch = ImportBatch::create([
                        'code' => $this->nextCode($importerKey),
                        'importer' => $importerKey,
                        'original_filename' => $file->getClientOriginalName(),
                        'disk' => $disk,
                        'path' => $path,
                        'status' => ImportStatus::Pending,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);

                    $this->audit->record(AuditEvent::Created, $batch, [], [
                        'importer' => $importerKey,
                        'original_filename' => $batch->original_filename,
                    ], 'Tải file import lên.');

                    return $batch;
                });
            } catch (UniqueConstraintViolationException $e) {
                // Mã lô trùng do yêu cầu khác vừa tạo: tính lại mã và thử lại
                if ($attempt < self::CODE_ATTEMPTS) {
                    continue;
                }

                Storage::disk($disk)->delete($path);

                throw $e;
            } catch (Throwable $e) {
                Storage::disk($disk)->delete($path);

                throw $e;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Bước 2: Kiểm tra từng dòng
    // -------------------------------------------------------------------------

    /**
     * Kiểm tra từng dòng của lô. File từ ngưỡng cấu hình trở lên chạy nền bằng job.
     * Lô được "chiếm" (pending → validating) trước khi đọc file nên hai yêu cầu cùng lúc không dispatch trùng;
     * file rỗng hoặc đọc lỗi thì lô chuyển sang Failed, không bao giờ kẹt ở "Đang kiểm tra".
     *
     * @throws BusinessRuleException
     */
    public function validate(ImportBatch $batch, User $user): ImportBatch
    {
        $this->assertStatus($batch, [ImportStatus::Pending]);
        $this->claim($batch, ImportStatus::Pending, ImportStatus::Validating, [
            'started_at' => now(),
            'finished_at' => null,
            'progress' => 0,
            'error_summary' => null,
        ], $user);

        $threshold = $this->asyncThreshold();
        $rows = [];
        $count = 0;

        try {
            $importer = $this->registry->get($batch->importer);

            // Chỉ đọc đủ số dòng cần để biết file lớn hay nhỏ; Importer trả Generator thì không nạp cả file
            foreach ($importer->parseRows($batch->path, $batch->disk) as $index => $row) {
                $rows[$index] = $row; // giữ khóa để đánh đúng số dòng

                if (++$count >= $threshold) {
                    break;
                }
            }
        } catch (Throwable $e) {
            $this->markFailed($batch, 'Không đọc được file: '.$this->describe($e));

            throw $e;
        }

        if ($count === 0) {
            $message = 'File không có dữ liệu.';
            $this->markFailed($batch, $message);

            $this->fail($message, 'Hãy tải file mẫu, điền dữ liệu rồi tải lên lại.');
        }

        // File lớn → chạy nền (NFR-PERF-04)
        if ($count >= $threshold) {
            $this->dispatchJob($batch, new ValidateImportBatchJob($batch->id, $user->id));

            return $batch->refresh();
        }

        // File nhỏ → xử lý đồng bộ
        $this->doValidate($batch, $rows, $count);

        return $batch->refresh();
    }

    /**
     * Phần kiểm tra chạy trong job nền: đếm số dòng để tính tiến trình rồi kiểm tra theo luồng.
     */
    public function validateInBackground(ImportBatch $batch): void
    {
        $importer = $this->registry->get($batch->importer);

        $total = $this->countRows($importer->parseRows($batch->path, $batch->disk));

        $this->doValidate($batch, $importer->parseRows($batch->path, $batch->disk), $total);
    }

    /**
     * Kiểm tra từng dòng và ghi vào import_rows theo lô (insert nhiều dòng một lần).
     *
     * @param  iterable<int, array<string, mixed>>  $rows
     */
    public function doValidate(ImportBatch $batch, iterable $rows, ?int $total = null): void
    {
        try {
            $importer = $this->registry->get($batch->importer);
            $chunk = $this->chunkSize();

            // Kiểm tra lại (hoặc job chạy lại) thì bỏ kết quả cũ
            ImportRow::query()->where('import_batch_id', $batch->id)->delete();

            $processed = 0;
            $valid = 0;
            $invalid = 0;
            $buffer = [];

            foreach ($rows as $index => $rawRow) {
                // Dòng 1 là tiêu đề. Importer bỏ qua dòng trống thì trả khóa là vị trí thật (xem SpreadsheetImporter)
                // để số dòng báo lỗi khớp với file; Importer khác trả khóa 0, 1, 2… như trước.
                $rowNumber = $index + 2;
                $errors = $importer->validateRow($rowNumber, $rawRow);
                $isValid = $errors === [];

                $isValid ? $valid++ : $invalid++;
                $processed++;

                $buffer[] = $this->rowRecord($batch, $rowNumber, $rawRow, $isValid, $errors);

                if (count($buffer) >= $chunk) {
                    ImportRow::query()->insert($buffer);
                    $buffer = [];

                    if ($total !== null && $total > 0) {
                        $batch->update(['progress' => min(49, (int) floor($processed / $total * 50))]);
                    }
                }
            }

            if ($buffer !== []) {
                ImportRow::query()->insert($buffer);
            }

            $batch->update([
                'status' => ImportStatus::Validated,
                'total_rows' => $processed,
                'valid_rows' => $valid,
                'invalid_rows' => $invalid,
                'progress' => 50,
            ]);

            $this->audit->record(
                AuditEvent::Updated,
                $batch,
                ['status' => ImportStatus::Validating->value],
                [
                    'status' => ImportStatus::Validated->value,
                    'total_rows' => $processed,
                    'valid_rows' => $valid,
                    'invalid_rows' => $invalid,
                ],
                'Kiểm tra dữ liệu import.'
            );
        } catch (Throwable $e) {
            $this->markFailed($batch, 'Lỗi khi kiểm tra file: '.$this->describe($e));

            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Bước 3: Xem trước kết quả
    // -------------------------------------------------------------------------

    /**
     * Lấy dữ liệu xem trước: thống kê + danh sách dòng có lỗi.
     *
     * @return array{batch: ImportBatch, error_rows: Collection}
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
     *   all_valid  — chỉ lưu các dòng hợp lệ, bỏ qua dòng lỗi (GC-07); dòng hợp lệ nhưng lỗi lúc lưu được ghi lỗi
     *                riêng, các dòng khác vẫn được lưu
     *   all        — lưu tất cả; có bất kỳ lỗi nào thì không lưu dòng nào
     *
     * @param  'all_valid'|'all'  $mode
     *
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
                'Không thể lưu toàn bộ vì có '.$batch->invalid_rows.' dòng lỗi.',
                "Hãy chọn 'Chỉ lưu dòng hợp lệ' hoặc sửa các dòng lỗi rồi tải lại file."
            );
        }

        $this->claim($batch, ImportStatus::Validated, ImportStatus::Saving, [
            'save_mode' => $mode,
            'finished_at' => null,
        ], $user);

        // File lớn → chạy nền
        if ($batch->total_rows >= $this->asyncThreshold()) {
            $this->dispatchJob($batch, new SaveImportBatchJob($batch->id, $mode, $user->id));

            return $batch->refresh();
        }

        // File nhỏ → xử lý đồng bộ
        $this->doSave($batch, $mode);

        return $batch->refresh();
    }

    /**
     * Thực hiện lưu từng dòng (đồng bộ hoặc từ job), toàn bộ trong một giao dịch: hoặc lưu xong cả lô,
     * hoặc không dòng nào được lưu. Mỗi dòng chạy trong một điểm lưu (savepoint) riêng nên với "all_valid"
     * một dòng lỗi chỉ làm hỏng chính dòng đó.
     *
     * Lưu ý: tiến trình được ghi trong giao dịch nên chỉ thấy sau khi lưu xong (khác bước kiểm tra).
     */
    public function doSave(ImportBatch $batch, string $mode): void
    {
        $importer = $this->registry->get($batch->importer);
        $chunk = $this->chunkSize();
        $totalRows = max(1, $batch->total_rows);
        $processed = 0;
        $saved = 0;
        $failed = 0;

        try {
            $this->transaction(function () use ($batch, $importer, $mode, $chunk, $totalRows, &$processed, &$saved, &$failed): void {
                $batch->rows()->orderBy('id')->chunkById($chunk, function ($rows) use ($batch, $importer, $mode, $totalRows, &$processed, &$saved, &$failed): void {
                    /** @var ImportRow $row */
                    foreach ($rows as $row) {
                        $processed++;

                        if ($row->status === ImportRowStatus::Invalid) {
                            $row->markSkipped();

                            continue;
                        }

                        if ($row->status !== ImportRowStatus::Valid) {
                            continue;
                        }

                        try {
                            $result = DB::transaction(fn () => $importer->saveRow($row->row_number, $row->raw_data));
                        } catch (Throwable $e) {
                            if ($mode === 'all') {
                                throw $e;
                            }

                            $row->markInvalid([[
                                'column' => '*',
                                'message' => $this->rowSaveError($e),
                            ]]);
                            $failed++;

                            continue;
                        }

                        $row->markSaved($result['type'], $result['id']);
                        $saved++;
                    }

                    $batch->update(['progress' => min(99, 50 + (int) floor($processed / $totalRows * 50))]);
                });

                $batch->update([
                    'status' => ImportStatus::Saved,
                    'saved_rows' => $saved,
                    'valid_rows' => $batch->valid_rows - $failed,
                    'invalid_rows' => $batch->invalid_rows + $failed,
                    'error_summary' => $failed > 0
                        ? "{$failed} dòng hợp lệ nhưng không lưu được; xem lỗi theo dòng."
                        : null,
                    'progress' => 100,
                    'finished_at' => now(),
                ]);

                $this->audit->record(
                    AuditEvent::Imported,
                    $batch,
                    ['status' => ImportStatus::Saving->value],
                    [
                        'status' => ImportStatus::Saved->value,
                        'save_mode' => $mode,
                        'saved_rows' => $saved,
                        'failed_rows' => $failed,
                        'total_rows' => $batch->total_rows,
                    ],
                    'Lưu dữ liệu import.'
                );
            });
        } catch (Throwable $e) {
            $this->markFailed($batch, 'Lỗi khi lưu dữ liệu: '.$this->describe($e).' Không có dòng nào được lưu.');

            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Bước 5: Hoàn tác
    // -------------------------------------------------------------------------

    /**
     * Hoàn tác toàn lô: xóa các bản ghi đã lưu nếu chưa có dữ liệu phụ thuộc (BR-SYS-07).
     * Kiểm tra trước cho cả lô (canRollbackRow, không xóa gì), rồi mới xóa trong một giao dịch:
     * không bao giờ hoàn tác dở dang.
     *
     * @throws BusinessRuleException
     */
    public function rollback(ImportBatch $batch, User $user): ImportBatch
    {
        $importer = $this->registry->get($batch->importer);
        $chunk = $this->chunkSize();

        $this->transaction(function () use ($batch, $importer, $user, $chunk): void {
            // Khóa lô để hai yêu cầu hoàn tác cùng lúc không chạy song song
            $locked = ImportBatch::query()->whereKey($batch->id)->lockForUpdate()->firstOrFail();

            if (! $locked->canRollback()) {
                $this->fail(
                    'Lô này không thể hoàn tác (trạng thái: '.$locked->status->label().').',
                    'Chỉ có thể hoàn tác lô đã lưu thành công.'
                );
            }

            $cannotRollback = [];
            $savedCount = 0;

            $locked->savedRows()->orderBy('id')->chunkById($chunk, function ($rows) use ($importer, &$cannotRollback, &$savedCount): void {
                foreach ($rows as $row) {
                    $savedCount++;

                    if (! $importer->canRollbackRow($row->saved_model_type, $row->saved_model_id)) {
                        $cannotRollback[] = $row->row_number;
                    }
                }
            });

            if ($cannotRollback !== []) {
                $lines = implode(', ', array_slice($cannotRollback, 0, 10));
                $suffix = count($cannotRollback) > 10 ? '…' : '';

                $this->fail(
                    'Không thể hoàn tác vì '.count($cannotRollback)." dòng đã có dữ liệu phụ thuộc (dòng {$lines}{$suffix}).",
                    'Hãy xử lý các dữ liệu phụ thuộc trước khi hoàn tác.'
                );
            }

            $locked->savedRows()->orderBy('id')->chunkById($chunk, function ($rows) use ($importer): void {
                /** @var ImportRow $row */
                foreach ($rows as $row) {
                    if (! $importer->rollbackRow($row->saved_model_type, $row->saved_model_id)) {
                        $this->fail(
                            "Không thể hoàn tác dòng {$row->row_number}.",
                            'Dữ liệu vừa thay đổi; không có dòng nào bị xóa. Tải lại trang rồi thử lại.'
                        );
                    }

                    $row->update([
                        'status' => ImportRowStatus::Pending,
                        'saved_model_type' => null,
                        'saved_model_id' => null,
                    ]);
                }
            });

            $locked->update([
                'status' => ImportStatus::RolledBack,
                'saved_rows' => 0,
                'updated_by' => $user->id,
                'finished_at' => now(),
            ]);

            $this->audit->record(
                AuditEvent::RolledBack,
                $locked,
                ['status' => ImportStatus::Saved->value, 'saved_rows' => $savedCount],
                ['status' => ImportStatus::RolledBack->value],
                'Hoàn tác lô import.'
            );
        });

        return $batch->refresh();
    }

    // -------------------------------------------------------------------------
    // Xóa lô
    // -------------------------------------------------------------------------

    /**
     * Xóa mềm lô khỏi lịch sử. Không xóa lô đang chạy, và không xóa lô đã lưu dữ liệu (phải hoàn tác trước).
     *
     * @throws BusinessRuleException
     */
    public function delete(ImportBatch $batch, User $user): void
    {
        $this->transaction(function () use ($batch, $user): void {
            $locked = ImportBatch::query()->whereKey($batch->id)->lockForUpdate()->firstOrFail();

            if (in_array($locked->status, [ImportStatus::Validating, ImportStatus::Saving], true)) {
                $this->fail(
                    "Lô import {$locked->code} đang được xử lý.",
                    'Chờ lô xử lý xong rồi xóa.'
                );
            }

            if ($locked->status === ImportStatus::Saved) {
                $this->fail(
                    "Lô import {$locked->code} đã lưu dữ liệu vào hệ thống.",
                    'Hãy hoàn tác lô trước nếu muốn xóa khỏi lịch sử.'
                );
            }

            $locked->update(['updated_by' => $user->id]);
            $locked->delete();

            $this->audit->record(
                AuditEvent::Deleted,
                $locked,
                ['status' => $locked->status->value],
                [],
                'Xóa lô import khỏi lịch sử.'
            );
        });
    }

    // -------------------------------------------------------------------------
    // Job nền
    // -------------------------------------------------------------------------

    /**
     * Chạy $callback với người dùng $userId là người thực hiện, để nhật ký kiểm toán ghi đúng người
     * khi xử lý trong job nền (không có phiên đăng nhập). Trả lại người dùng cũ khi xong.
     */
    public function runAs(?int $userId, Closure $callback): mixed
    {
        $user = $userId === null ? null : User::query()->find($userId);

        if ($user === null) {
            return $callback();
        }

        $previous = Auth::user();
        Auth::setUser($user);

        try {
            return $callback();
        } finally {
            $previous !== null ? Auth::setUser($previous) : Auth::forgetUser();
        }
    }

    /** Đánh dấu lô thất bại theo id (dùng khi job nền báo lỗi). */
    public function failBatchById(int $batchId, string $summary): void
    {
        $batch = ImportBatch::query()->find($batchId);

        if ($batch !== null) {
            $this->markFailed($batch, $summary);
        }
    }

    // -------------------------------------------------------------------------
    // Hàm phụ trợ
    // -------------------------------------------------------------------------

    private function disk(): string
    {
        return (string) config('studentmanager.import.disk');
    }

    private function asyncThreshold(): int
    {
        return (int) config('studentmanager.import.async_threshold');
    }

    private function chunkSize(): int
    {
        return max(1, (int) config('studentmanager.import.chunk_size'));
    }

    /**
     * Sinh mã lô: IMP-{IMPORTER}-{YYYYMMDD}-{XXXX}. Tính cả lô đã xóa mềm vì mã vẫn nằm trong chỉ mục unique;
     * nếu hai yêu cầu sinh trùng thì upload() thử lại.
     */
    private function nextCode(string $importerKey): string
    {
        $prefix = 'IMP-'.strtoupper(substr($importerKey, 0, 6)).'-'.now()->format('Ymd').'-';

        $last = ImportBatch::withTrashed()
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->value('code');

        $seq = $last === null ? 1 : ((int) Str::afterLast($last, '-')) + 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Chuyển trạng thái lô bằng cập nhật có điều kiện: chỉ một yêu cầu thắng, các yêu cầu đồng thời còn lại bị từ chối
     * (không dispatch job trùng, không lưu hai lần).
     *
     * @param  array<string, mixed>  $values
     */
    private function claim(ImportBatch $batch, ImportStatus $from, ImportStatus $to, array $values, User $user): void
    {
        $updated = ImportBatch::query()
            ->whereKey($batch->getKey())
            ->where('status', $from->value)
            ->update([...$values, 'status' => $to->value, 'updated_by' => $user->id]);

        $batch->refresh();

        if ($updated === 0) {
            $this->fail(
                "Lô import {$batch->code} đang được xử lý bởi yêu cầu khác (trạng thái: {$batch->status->label()}).",
                'Tải lại trang để xem trạng thái mới nhất, không gửi lại thao tác này.'
            );
        }
    }

    /**
     * Đẩy job nền; nếu không đẩy được thì lô không được kẹt ở trạng thái đang xử lý.
     */
    private function dispatchJob(ImportBatch $batch, ValidateImportBatchJob|SaveImportBatchJob $job): void
    {
        try {
            $jobId = Bus::dispatch($job);
        } catch (Throwable $e) {
            $this->markFailed($batch, 'Không đưa được công việc vào hàng đợi: '.$this->describe($e));

            throw $e;
        }

        // Với hàng đợi đồng bộ, job đã chạy xong ở dòng trên; chỉ ghi mã job khi hàng đợi thật trả về mã
        if (is_scalar($jobId) && $jobId) {
            ImportBatch::query()->whereKey($batch->getKey())->update(['job_id' => (string) $jobId]);
        }
    }

    /**
     * Đặt lô sang Failed nếu đang kiểm tra hoặc đang lưu; không ghi đè lô đã xong (Validated, Saved…).
     */
    private function markFailed(ImportBatch $batch, string $summary): void
    {
        $updated = ImportBatch::query()
            ->whereKey($batch->getKey())
            ->whereIn('status', [ImportStatus::Validating->value, ImportStatus::Saving->value])
            ->update([
                'status' => ImportStatus::Failed->value,
                'error_summary' => Str::limit($summary, 1000, '…'),
                'finished_at' => now(),
            ]);

        $batch->refresh();

        if ($updated > 0) {
            $this->audit->record(
                AuditEvent::Updated,
                $batch,
                [],
                ['status' => ImportStatus::Failed->value, 'error_summary' => $batch->error_summary],
                'Import thất bại.'
            );
        }
    }

    private function describe(Throwable $e): string
    {
        return $e instanceof BusinessRuleException ? $e->userMessage() : $e->getMessage();
    }

    /** Thông báo lỗi theo dòng khi lưu: lỗi nghiệp vụ nêu nguyên văn, lỗi hệ thống không để lộ chi tiết kỹ thuật. */
    private function rowSaveError(Throwable $e): string
    {
        if ($e instanceof BusinessRuleException) {
            return $e->userMessage();
        }

        report($e);

        return 'Không lưu được dòng này do lỗi dữ liệu (ví dụ trùng khóa). Kiểm tra lại dữ liệu của dòng rồi nhập lại riêng dòng này.';
    }

    /** @param  iterable<int, array<string, mixed>>  $rows */
    private function countRows(iterable $rows): int
    {
        return is_array($rows) ? count($rows) : iterator_count($rows);
    }

    /**
     * Một bản ghi import_rows để insert theo lô (insert không qua cast nên tự mã hóa JSON).
     *
     * @param  array<string, mixed>  $rawRow
     * @param  array<int, array{column: string, message: string}>  $errors
     * @return array<string, mixed>
     */
    private function rowRecord(ImportBatch $batch, int $rowNumber, array $rawRow, bool $isValid, array $errors): array
    {
        return [
            'import_batch_id' => $batch->id,
            'row_number' => $rowNumber,
            'raw_data' => json_encode($rawRow, JSON_UNESCAPED_UNICODE),
            'status' => ($isValid ? ImportRowStatus::Valid : ImportRowStatus::Invalid)->value,
            'errors' => $isValid ? null : json_encode($errors, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ];
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
                "Lô import {$batch->code} ở trạng thái '{$batch->status->label()}', không thể thực hiện thao tác này.",
                "Thao tác này chỉ được phép khi lô ở trạng thái: {$allowedLabels}."
            );
        }
    }
}
