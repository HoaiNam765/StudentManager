<?php

namespace App\Modules\Room\Http\Requests;

/** Tạo (POST) hoặc sửa (PUT) tòa nhà. Không chuyển tòa nhà sang cơ sở khác: tạo tòa nhà mới. */
class BuildingRequest extends FacilityRequest
{
    public function rules(): array
    {
        $creating = $this->isMethod('post');

        return [
            'campus_id' => [$creating ? 'required' : 'prohibited', 'integer', 'exists:campuses,id'],
            'code' => [$creating ? 'required' : 'sometimes', 'string', 'max:20', self::CODE_RULE],
            'name' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'floors' => ['sometimes', 'nullable', 'integer', 'between:1,100'],
            'status' => [$creating ? 'prohibited' : 'sometimes', 'in:active,inactive'],
        ];
    }
}
