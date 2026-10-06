<?php

namespace App\Modules\System\Http\Requests;

use App\Modules\System\Models\ImportBatch;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation khi tải file import lên (bước 1).
 * Kiểm tra định dạng và dung lượng ở tầng HTTP trước khi vào Service.
 */
class UploadImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ImportBatch::class);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'importer' => ['required', 'string', 'max:100'],
            'file' => [
                'required',
                'file',
                // Ngưỡng dung lượng lấy từ cấu hình (GC-12, NFR-MNT-03)
                'max:'.config('studentmanager.import.max_file_kb'),
                'mimes:xlsx,xls,csv',
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'importer.required' => 'Vui lòng chọn loại import.',
            'file.required' => 'Vui lòng chọn file để tải lên.',
            'file.max' => 'Kích thước file vượt quá giới hạn cho phép ('.config('studentmanager.import.max_file_kb').' KB).',
            'file.mimes' => 'Chỉ chấp nhận file định dạng Excel (.xlsx, .xls) hoặc CSV (.csv).',
        ];
    }
}
