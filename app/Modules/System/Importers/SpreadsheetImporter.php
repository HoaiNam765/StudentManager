<?php

namespace App\Modules\System\Importers;

use App\Modules\System\Contracts\ImporterContract;
use App\Support\Exceptions\BusinessRuleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenSpout\Reader\CSV\Options as CsvOptions;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Options as XlsxOptions;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

/**
 * Nền cho Importer đọc file Excel (.xlsx) hoặc CSV (UTF-8): dòng 1 là tiêu đề cột, các dòng sau là dữ liệu.
 * Đọc theo luồng bằng OpenSpout (không nạp cả file vào bộ nhớ) và chỉ đọc sheet đầu tiên.
 *
 * Lớp con khai báo các cột bằng `columns()` (tên cột = tiêu đề trong file, viết thường) rồi cài
 * `validateRow`, `saveRow`, `canRollbackRow`, `rollbackRow`. Mỗi dòng trả về dạng ['code' => '01', ...];
 * cột thiếu giá trị là chuỗi rỗng. Dòng trống hoàn toàn được bỏ qua.
 *
 * Lưu ý: ô Excel định dạng số làm mất số 0 ở đầu (mã "01" thành 1); file mẫu nên để cột mã ở dạng Text.
 */
abstract class SpreadsheetImporter implements ImporterContract
{
    /**
     * Cột bắt buộc và cột tùy chọn trong dòng tiêu đề.
     *
     * @return array{required: list<string>, optional?: list<string>}
     */
    abstract protected function columns(): array;

    public function templateUrl(): ?string
    {
        return null;
    }

    public function validateFile(UploadedFile $file): void
    {
        $extension = Str::lower($file->getClientOriginalExtension());

        if (! in_array($extension, ['xlsx', 'csv'], true)) {
            throw new BusinessRuleException(
                'Chỉ đọc được file Excel .xlsx hoặc CSV.',
                'Mở file bằng Excel rồi lưu lại dưới dạng "Excel Workbook (.xlsx)" hoặc "CSV UTF-8", sau đó tải lên lại.'
            );
        }
    }

    public function parseRows(string $filePath, string $disk): iterable
    {
        $path = Storage::disk($disk)->path($filePath);

        // Giữ dòng trống để đếm đúng số dòng trong file: lỗi báo "dòng 7" phải là dòng 7 khi mở bằng Excel
        if ($this->isXlsx($path)) {
            $options = new XlsxOptions;
            $options->SHOULD_PRESERVE_EMPTY_ROWS = true;
            $reader = new XlsxReader($options);
        } else {
            $options = new CsvOptions;
            $options->SHOULD_PRESERVE_EMPTY_ROWS = true;
            $reader = new CsvReader($options);
        }

        $reader->open($path);

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $header = null;
                $line = 0;

                foreach ($sheet->getRowIterator() as $row) {
                    $line++;
                    $values = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $row->toArray());

                    if ($header === null) {
                        if ($this->isBlank($values)) {
                            continue;
                        }

                        $header = $this->readHeader($values);
                        $headerLine = $line;

                        continue;
                    }

                    if ($this->isBlank($values)) {
                        continue;
                    }

                    $record = [];

                    foreach ($header as $index => $column) {
                        $value = $values[$index] ?? '';
                        $record[$column] = $value === null ? '' : (is_string($value) ? $value : (string) $value);
                    }

                    // Khóa = vị trí dòng dữ liệu tính cả dòng trống; trung tâm import đổi thành số dòng trong file
                    yield $line - $headerLine - 1 => $record;
                }

                // Chỉ đọc sheet đầu tiên
                break;
            }
        } finally {
            $reader->close();
        }
    }

    /** @param  list<mixed>  $values */
    private function isBlank(array $values): bool
    {
        return array_filter($values, fn ($value) => $value !== null && $value !== '') === [];
    }

    /**
     * @param  list<mixed>  $values
     * @return array<int, string> vị trí cột => tên cột
     */
    private function readHeader(array $values): array
    {
        $header = [];

        foreach ($values as $index => $value) {
            $name = Str::lower(trim(str_replace("\u{FEFF}", '', (string) $value)));

            if ($name !== '') {
                $header[$index] = $name;
            }
        }

        $columns = $this->columns();
        $missing = array_values(array_diff($columns['required'], $header));

        if ($missing !== []) {
            throw new BusinessRuleException(
                'File thiếu cột: '.implode(', ', $missing).'.',
                'Dòng đầu tiên của file phải là tiêu đề cột: '.implode(', ', [...$columns['required'], ...($columns['optional'] ?? [])])
                .' (các cột '.implode(', ', $columns['optional'] ?? []).' có thể bỏ trống).'
            );
        }

        return $header;
    }

    /** File .xlsx là gói zip (bắt đầu bằng "PK"); tên file đã lưu không đáng tin vì CSV có thể bị đổi đuôi thành .txt. */
    private function isXlsx(string $path): bool
    {
        $handle = fopen($path, 'rb');
        $signature = $handle === false ? '' : (string) fread($handle, 4);

        if ($handle !== false) {
            fclose($handle);
        }

        return $signature === "PK\x03\x04";
    }
}
