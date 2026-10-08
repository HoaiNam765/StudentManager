<?php

use App\Modules\Room\RoomServiceProvider;
use App\Modules\System\SystemServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    SystemServiceProvider::class,
    RoomServiceProvider::class,
];
