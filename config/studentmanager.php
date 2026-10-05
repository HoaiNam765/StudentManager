<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Múi giờ hiển thị
    |--------------------------------------------------------------------------
    | Dữ liệu thời gian lưu theo UTC (config 'app.timezone'); hiển thị cho người
    | dùng theo múi giờ này (UTC+7). Xem GC-09 và NFR-LOC-01 trong docs/BA.md.
    */
    'display_timezone' => env('APP_DISPLAY_TIMEZONE', 'Asia/Ho_Chi_Minh'),

    'formats' => [
        'date' => 'd/m/Y',
        'datetime' => 'd/m/Y H:i',
    ],

    'currency_symbol' => '₫',

];
