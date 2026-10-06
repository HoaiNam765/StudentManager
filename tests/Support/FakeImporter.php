<?php

namespace Tests\Support;

use App\Modules\System\Contracts\ImporterContract;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Importer giả cho test trung tâm import: file CSV có cột `code`, `qty`; lưu vào bảng `fake_import_targets`.
 *
 *  - `code` bắt buộc, `qty` là số nguyên dương;
 *  - trùng `code` đã có trong bảng → lỗi lúc lưu (unique), dòng vẫn "hợp lệ" ở bước kiểm tra;
 *  - `code` bắt đầu bằng "LOCK" → bản ghi sau khi lưu bị đánh dấu có dữ liệu phụ thuộc (không hoàn tác được).
 */
class FakeImporter implements ImporterContract
{
    public const TABLE = 'fake_import_targets';

    public static function createTable(): void
    {
        Schema::create(self::TABLE, function ($table): void {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedInteger('qty');
            $table->boolean('has_children')->default(false);
        });
    }

    /** Nội dung CSV gồm tiêu đề và các dòng `code,qty`. */
    public static function csv(array $rows): string
    {
        $lines = ['code,qty'];

        foreach ($rows as [$code, $qty]) {
            $lines[] = $code.','.$qty;
        }

        return implode("\n", $lines)."\n";
    }

    public function key(): string
    {
        return 'fake';
    }

    public function label(): string
    {
        return 'Dữ liệu giả cho test';
    }

    public function description(): string
    {
        return 'Cột code, qty.';
    }

    public function templateUrl(): ?string
    {
        return null;
    }

    public function validateFile(UploadedFile $file): void {}

    public function parseRows(string $filePath, string $disk): iterable
    {
        $stream = Storage::disk($disk)->readStream($filePath);
        $header = null;

        while (($line = fgetcsv($stream)) !== false) {
            if ($line === [null]) {
                continue;
            }

            if ($header === null) {
                $header = $line;

                continue;
            }

            yield array_combine($header, $line);
        }

        fclose($stream);
    }

    public function validateRow(int $rowNumber, array $rawRow): array
    {
        $errors = [];

        if (($rawRow['code'] ?? '') === '') {
            $errors[] = ['column' => 'code', 'message' => 'Mã không được để trống.'];
        }

        if (! ctype_digit((string) ($rawRow['qty'] ?? '')) || (int) $rawRow['qty'] < 1) {
            $errors[] = ['column' => 'qty', 'message' => 'Số lượng phải là số nguyên dương.'];
        }

        return $errors;
    }

    public function saveRow(int $rowNumber, array $rawRow): array
    {
        if (str_starts_with($rawRow['code'], 'BUSINESS')) {
            throw new BusinessRuleException('Mã này bị cấm theo quy tắc nghiệp vụ.', 'Dùng mã khác.');
        }

        $id = DB::table(self::TABLE)->insertGetId([
            'code' => $rawRow['code'],
            'qty' => (int) $rawRow['qty'],
            'has_children' => str_starts_with($rawRow['code'], 'LOCK'),
        ]);

        return ['type' => self::TABLE, 'id' => $id];
    }

    public function canRollbackRow(string $modelType, int|string $modelId): bool
    {
        return ! DB::table(self::TABLE)->where('id', $modelId)->where('has_children', true)->exists();
    }

    public function rollbackRow(string $modelType, int|string $modelId): bool
    {
        return DB::table(self::TABLE)->where('id', $modelId)->delete() > 0;
    }
}
