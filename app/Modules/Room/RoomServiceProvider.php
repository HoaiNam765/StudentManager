<?php

namespace App\Modules\Room;

use App\Modules\Room\Models\Building;
use App\Modules\Room\Models\Campus;
use App\Modules\Room\Models\Room;
use App\Modules\Room\Models\RoomMaintenance;
use App\Modules\Room\Models\RoomType;
use App\Modules\Room\Policies\RoomPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module Room (ROM). TTB và EXM lưu khóa ngoại tới `rooms` thì đăng ký với ReferenceRegistry
 * trong ServiceProvider của mình, để phòng đã có lịch sử sử dụng không xóa được (BR-ROM-04):
 *
 *     app(ReferenceRegistry::class)->register(Room::class, 'class_sessions', 'room_id', 'buổi học');
 */
class RoomServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach ([Campus::class, Building::class, RoomType::class, Room::class, RoomMaintenance::class] as $model) {
            Gate::policy($model, RoomPolicy::class);
        }
    }
}
