<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tệp xuất dữ liệu có thể chứa dữ liệu nhạy cảm: dọn tệp quá hạn giữ mỗi ngày
Schedule::command('exports:prune')->dailyAt('02:00')->withoutOverlapping();
