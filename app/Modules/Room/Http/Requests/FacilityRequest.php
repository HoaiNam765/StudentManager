<?php

namespace App\Modules\Room\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

/**
 * Nền cho các biểu mẫu danh mục phòng: chuẩn hóa mã (bỏ khoảng trắng, viết hoa), tên trường tiếng Việt.
 * Lớp con khai báo `rules()`.
 */
abstract class FacilityRequest extends FormRequest
{
    protected const CODE_RULE = 'regex:/^[A-Z0-9][A-Z0-9._\-]*$/';

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge(['code' => Str::upper(trim($this->input('code')))]);
        }
    }

    public function attributes(): array
    {
        return [
            'code' => 'mã',
            'name' => 'tên',
            'address' => 'địa chỉ',
            'campus_id' => 'cơ sở',
            'building_id' => 'tòa nhà',
            'room_type_id' => 'loại phòng',
            'floors' => 'số tầng',
            'floor' => 'tầng',
            'capacity' => 'sức chứa học',
            'exam_capacity' => 'sức chứa thi',
            'equipment' => 'thiết bị',
            'equipment.*' => 'thiết bị',
            'note' => 'ghi chú',
            'description' => 'mô tả',
            'status' => 'tình trạng',
            'starts_on' => 'ngày bắt đầu',
            'ends_on' => 'ngày kết thúc',
            'reason' => 'lý do',
        ];
    }

    public function messages(): array
    {
        return ['code.regex' => 'Mã chỉ gồm chữ in hoa không dấu, số, dấu chấm, gạch ngang và gạch dưới (ví dụ A101, B.201).'];
    }
}
