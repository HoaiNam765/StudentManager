<?php

namespace App\Modules\Room\Http\Requests;

/** Lên lịch (POST) hoặc sửa (PUT) lịch bảo trì phòng. */
class RoomMaintenanceRequest extends FacilityRequest
{
    public function rules(): array
    {
        $creating = $this->isMethod('post');

        return [
            'starts_on' => [$creating ? 'required' : 'sometimes', 'date_format:Y-m-d'],
            'ends_on' => [$creating ? 'required' : 'sometimes', 'date_format:Y-m-d'],
            'reason' => [$creating ? 'required' : 'sometimes', 'string', 'max:500'],
        ];
    }
}
