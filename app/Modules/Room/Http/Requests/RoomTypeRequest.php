<?php

namespace App\Modules\Room\Http\Requests;

/** Tạo (POST) hoặc sửa (PUT) loại phòng. Mã loại cố định vì TTB dùng để kiểm tra loại buổi học. */
class RoomTypeRequest extends FacilityRequest
{
    public function rules(): array
    {
        $creating = $this->isMethod('post');

        return [
            'code' => [$creating ? 'required' : 'prohibited', 'string', 'max:20', self::CODE_RULE],
            'name' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => [$creating ? 'prohibited' : 'sometimes', 'in:active,inactive'],
        ];
    }
}
