<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Logo: ảnh PNG/JPG/WebP tối đa 2 MB. Không nhận SVG vì có thể chứa mã script. */
class UploadLogoRequest extends FormRequest
{
    public function rules(): array
    {
        return ['logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048']];
    }

    public function attributes(): array
    {
        return ['logo' => 'logo'];
    }

    public function messages(): array
    {
        return ['logo.mimes' => 'Logo phải là ảnh PNG, JPG hoặc WebP (không nhận SVG).'];
    }
}
