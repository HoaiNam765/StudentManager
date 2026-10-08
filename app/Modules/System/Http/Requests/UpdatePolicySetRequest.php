<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Sửa thông tin bộ quy chế đang soạn; mã và phiên bản cố định, trạng thái đổi bằng thao tác ban hành. */
class UpdatePolicySetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['prohibited'],
            'version' => ['prohibited'],
            'status' => ['prohibited'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'cohort_from' => ['sometimes', 'nullable', 'integer', 'between:1990,2100'],
            'cohort_to' => ['sometimes', 'nullable', 'integer', 'between:1990,2100'],
            'effective_from' => ['sometimes', 'required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tên bộ quy chế',
            'description' => 'mô tả',
            'cohort_from' => 'khóa bắt đầu áp dụng',
            'cohort_to' => 'khóa kết thúc áp dụng',
            'effective_from' => 'ngày hiệu lực',
        ];
    }

    public function messages(): array
    {
        return [
            'code.prohibited' => 'Không đổi được mã bộ quy chế; tạo bộ mới nếu cần mã khác.',
            'version.prohibited' => 'Phiên bản do hệ thống đánh số.',
            'status.prohibited' => 'Dùng thao tác "Ban hành" để đổi trạng thái.',
        ];
    }
}
