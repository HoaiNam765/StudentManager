<?php

namespace App\Modules\Room\Http\Requests;

use App\Modules\Room\Enums\RoomStatus;
use Illuminate\Validation\Rule;

/** Tạo (POST) hoặc sửa (PUT) phòng. Quy tắc sức chứa, tầng, mã do RoomService kiểm tra (BR-ROM-01). */
class RoomRequest extends FacilityRequest
{
    public function rules(): array
    {
        $creating = $this->isMethod('post');

        return [
            'building_id' => [$creating ? 'required' : 'sometimes', 'integer', 'exists:buildings,id'],
            'room_type_id' => [$creating ? 'required' : 'sometimes', 'integer', 'exists:room_types,id'],
            'code' => [$creating ? 'required' : 'sometimes', 'string', 'max:30', self::CODE_RULE],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'floor' => ['sometimes', 'integer', 'between:-5,100'],
            'capacity' => [$creating ? 'required' : 'sometimes', 'integer', 'min:1', 'max:5000'],
            'exam_capacity' => ['sometimes', 'integer', 'min:0', 'max:5000'],
            'equipment' => ['sometimes', 'nullable', 'array', 'max:50'],
            'equipment.*' => ['string', 'max:100'],
            'note' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => [$creating ? 'prohibited' : 'sometimes', Rule::enum(RoomStatus::class)],
        ];
    }
}
