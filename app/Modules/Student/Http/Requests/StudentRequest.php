<?php

namespace App\Modules\Student\Http\Requests;

use App\Modules\Student\Enums\IdType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Tạo (POST) hoặc sửa (PUT) hồ sơ sinh viên. Kiểm tra định dạng ở đây (BR-STU-03); trùng giấy tờ / email trường, tuổi,
 * ngành – chuyên ngành, danh mục và lý do sửa trường nhạy cảm do StudentService kiểm tra.
 */
class StudentRequest extends FormRequest
{
    public const PHONE_RULE = 'regex:/^(0|\+84)[0-9]{9,10}$/';

    protected function prepareForValidation(): void
    {
        foreach (['phone'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => preg_replace('/[\s.\-]/', '', $this->input($field))]);
            }
        }
    }

    public function rules(): array
    {
        $creating = $this->isMethod('post');
        $required = $creating ? ['required'] : ['sometimes', 'required'];
        $optional = ['sometimes', 'nullable'];

        return [
            'full_name' => [...$required, 'string', 'max:255'],
            'gender_id' => [...$optional, 'integer'],
            'date_of_birth' => [...$required, 'date_format:Y-m-d', 'before:today'],
            'place_of_birth' => [...$optional, 'string', 'max:255'],
            'ethnicity_id' => [...$optional, 'integer'],
            'religion_id' => [...$optional, 'integer'],
            'nationality_id' => [...$optional, 'integer'],
            'id_type' => ['sometimes', Rule::enum(IdType::class)],
            'national_id' => [...$optional, 'string', 'max:20', $this->input('id_type') === IdType::Passport->value ? 'regex:/^[A-Za-z0-9]{6,20}$/' : 'regex:/^[0-9]{12}$/'],
            'national_id_issued_on' => [...$optional, 'date_format:Y-m-d', 'before_or_equal:today'],
            'national_id_issued_by' => [...$optional, 'string', 'max:255'],
            'school_email' => [...$optional, 'email', 'max:255'],
            'personal_email' => [...$optional, 'email', 'max:255'],
            'phone' => [...$optional, 'string', self::PHONE_RULE],
            'permanent_address' => [...$optional, 'string', 'max:500'],
            'permanent_unit_id' => [...$optional, 'integer', 'exists:administrative_units,id'],
            'temporary_address' => [...$optional, 'string', 'max:500'],
            'temporary_unit_id' => [...$optional, 'integer', 'exists:administrative_units,id'],
            'major_id' => [...$required, 'integer', 'exists:majors,id'],
            'specialization_id' => [...$optional, 'integer', 'exists:specializations,id'],
            'training_type_id' => [...$required, 'integer', 'exists:training_types,id'],
            'cohort' => [...$required, 'integer', 'between:1990,2100'],
            'admission_date' => [...$optional, 'date_format:Y-m-d'],
            'priority_group_id' => [...$optional, 'integer'],
            'priority_area_id' => [...$optional, 'integer'],
            'note' => [...$optional, 'string', 'max:1000'],
            'reason' => [$creating ? 'prohibited' : 'sometimes', 'nullable', 'string', 'max:500'],
            'student_code' => ['prohibited'],
            'status' => ['prohibited'],
        ];
    }

    /** Dữ liệu hồ sơ (bỏ `reason`). */
    public function profile(): array
    {
        return collect($this->validated())->except('reason')->all();
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'họ và tên',
            'gender_id' => 'giới tính',
            'date_of_birth' => 'ngày sinh',
            'place_of_birth' => 'nơi sinh',
            'ethnicity_id' => 'dân tộc',
            'religion_id' => 'tôn giáo',
            'nationality_id' => 'quốc tịch',
            'id_type' => 'loại giấy tờ',
            'national_id' => 'số giấy tờ',
            'national_id_issued_on' => 'ngày cấp',
            'national_id_issued_by' => 'nơi cấp',
            'school_email' => 'email trường',
            'personal_email' => 'email cá nhân',
            'phone' => 'số điện thoại',
            'permanent_address' => 'địa chỉ thường trú',
            'permanent_unit_id' => 'xã / phường thường trú',
            'temporary_address' => 'địa chỉ tạm trú',
            'temporary_unit_id' => 'xã / phường tạm trú',
            'major_id' => 'ngành',
            'specialization_id' => 'chuyên ngành',
            'training_type_id' => 'hệ đào tạo',
            'cohort' => 'khóa',
            'admission_date' => 'ngày nhập học',
            'priority_group_id' => 'đối tượng ưu tiên',
            'priority_area_id' => 'khu vực ưu tiên',
            'note' => 'ghi chú',
            'reason' => 'lý do',
        ];
    }

    public function messages(): array
    {
        return [
            'national_id.regex' => $this->input('id_type') === IdType::Passport->value
                ? 'Số hộ chiếu gồm 6–20 chữ cái không dấu hoặc chữ số.'
                : 'Số CCCD gồm đúng 12 chữ số.',
            'phone.regex' => 'Số điện thoại phải bắt đầu bằng 0 hoặc +84 và có 10–11 chữ số (ví dụ 0912345678).',
            'student_code.prohibited' => 'MSSV được cấp khi xác nhận nhập học và không sửa được (BR-STU-01).',
            'status.prohibited' => 'Đổi trạng thái sinh viên dùng thao tác chuyển trạng thái theo quyết định (BR-STU-05).',
            'reason.prohibited' => 'Lý do chỉ dùng khi sửa hồ sơ.',
        ];
    }
}
