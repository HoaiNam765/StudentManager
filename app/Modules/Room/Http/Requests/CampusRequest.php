<?php

namespace App\Modules\Room\Http\Requests;

/** Tạo (POST) hoặc sửa (PUT) cơ sở. */
class CampusRequest extends FacilityRequest
{
    public function rules(): array
    {
        $creating = $this->isMethod('post');

        return [
            'code' => [$creating ? 'required' : 'sometimes', 'string', 'max:20', self::CODE_RULE],
            'name' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => [$creating ? 'prohibited' : 'sometimes', 'in:active,inactive'],
        ];
    }
}
