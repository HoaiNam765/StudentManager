<?php

namespace App\Modules\System\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * Giao diện mỗi module cần cài đặt để đăng ký một Importer vào trung tâm import (FR-SYS-007).
 *
 * Ví dụ đăng ký trong AppServiceProvider hoặc ModuleServiceProvider:
 *
 *     $this->app->make(\App\Modules\System\Services\ImportRegistry::class)
 *         ->register(new StudentImporter());
 *
 * Hoặc bind lazy qua ImportRegistry::registerLazy():
 *
 *     $registry->registerLazy('teacher', fn () => new TeacherImporter());
 *
 * Quyền dùng trung tâm import theo module SYS (xem ImportBatchPolicy): xem, tạo, xóa/hoàn tác.
 */
interface ImporterContract
{
    /**
     * Định danh duy nhất của Importer, ví dụ: 'student', 'teacher', 'subject'.
     * Dùng làm giá trị cột import_batches.importer.
     */
    public function key(): string;

    /**
     * Tên hiển thị tiếng Việt, ví dụ: 'Danh sách sinh viên'.
     */
    public function label(): string;

    /**
     * Mô tả ngắn về mẫu và cách dùng.
     */
    public function description(): string;

    /**
     * Đường dẫn hoặc URL tải file mẫu (template).
     * Trả về null nếu không có file mẫu.
     */
    public function templateUrl(): ?string;

    /**
     * Kiểm tra tính hợp lệ của file trước khi xử lý từng dòng.
     * Ném BusinessRuleException nếu file không hợp lệ.
     */
    public function validateFile(UploadedFile $file): void;

    /**
     * Đọc file và trả về các dòng, mỗi dòng là array key-value theo cột (không gồm dòng tiêu đề).
     *
     * Nên trả về Generator (yield từng dòng) để file lớn không bị nạp hết vào bộ nhớ (NFR-PERF-04):
     * trung tâm import chỉ đọc đủ số dòng cần để quyết định chạy nền, rồi đọc lại theo luồng trong job.
     *
     * Khóa của mỗi dòng là vị trí dòng dữ liệu (0 = dòng ngay sau tiêu đề); trung tâm import báo lỗi ở "dòng khóa + 2".
     * Importer bỏ qua dòng trống thì vẫn trả khóa theo vị trí thật để số dòng báo lỗi khớp với file (SpreadsheetImporter làm sẵn).
     *
     * @return iterable<int, array<string, mixed>>
     */
    public function parseRows(string $filePath, string $disk): iterable;

    /**
     * Kiểm tra một dòng dữ liệu thô.
     * Trả về mảng lỗi [{column, message}] hoặc [] nếu hợp lệ.
     *
     * @param  array<string, mixed>  $rawRow
     * @return array<int, array{column: string, message: string}>
     */
    public function validateRow(int $rowNumber, array $rawRow): array;

    /**
     * Lưu một dòng hợp lệ vào CSDL trong giao dịch của lô.
     * Trả về [type, id] để lưu vào import_rows.saved_model_* (dùng hoàn tác).
     * Ném BusinessRuleException (có cách khắc phục) nếu dòng không lưu được; khi chọn "Chỉ lưu dòng hợp lệ"
     * dòng đó được ghi lỗi và các dòng còn lại vẫn được lưu.
     *
     * @param  array<string, mixed>  $rawRow
     * @return array{type: string, id: int|string}
     */
    public function saveRow(int $rowNumber, array $rawRow): array;

    /**
     * Dòng đã lưu này có thể hoàn tác không? Chỉ kiểm tra, KHÔNG được thay đổi dữ liệu (BR-SYS-07).
     * Trả về false nếu đã có dữ liệu phụ thuộc.
     */
    public function canRollbackRow(string $modelType, int|string $modelId): bool;

    /**
     * Hoàn tác một dòng đã lưu (xóa bản ghi). Chỉ được gọi sau khi canRollbackRow() trả về true cho cả lô,
     * và nằm trong giao dịch của lô: ném lỗi hoặc trả false thì toàn bộ lô được giữ nguyên.
     */
    public function rollbackRow(string $modelType, int|string $modelId): bool;
}
