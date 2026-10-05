<?php

namespace App\Modules\System\Contracts;

use Illuminate\Http\UploadedFile;

/**
 * Giao diện mỗi module cần cài đặt để đăng ký một Importer vào trung tâm import (FR-SYS-007).
 *
 * Ví dụ đăng ký trong AppServiceProvider hoặc ModuleServiceProvider:
 *
 *     $this->app->make(\App\Modules\System\Services\ImportRegistry::class)
 *         ->register('student', new StudentImporter());
 *
 * Hoặc bind lazy qua ImportRegistry::registerLazy():
 *
 *     ImportRegistry::registerLazy('teacher', fn () => new TeacherImporter());
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
     * Đọc file và trả về mảng các dòng, mỗi dòng là array key-value theo cột.
     *
     * @return array<int, array<string, mixed>>
     */
    public function parseRows(string $filePath, string $disk): array;

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
     * Trả về [model_type, model_id] để lưu vào import_rows.saved_model_* (dùng hoàn tác).
     *
     * @param  array<string, mixed>  $rawRow
     * @return array{type: string, id: int|string}
     */
    public function saveRow(int $rowNumber, array $rawRow): array;

    /**
     * Hoàn tác một dòng đã lưu.
     * Được gọi khi người dùng chọn hoàn tác theo lô (BR-SYS-07).
     * Trả về false nếu dòng không thể hoàn tác (đã có dữ liệu phụ thuộc).
     */
    public function rollbackRow(string $modelType, int|string $modelId): bool;

    /**
     * Danh sách vai trò được phép dùng importer này.
     * Trả về [] nghĩa là chỉ ADMIN.
     *
     * @return list<string>
     */
    public function allowedRoles(): array;
}
