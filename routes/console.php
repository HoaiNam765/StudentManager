<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tệp xuất dữ liệu có thể chứa dữ liệu nhạy cảm: dọn tệp quá hạn giữ mỗi ngày
Schedule::command('exports:prune')->dailyAt('02:00')->withoutOverlapping();

// Ngừng tài khoản sinh viên thôi học, buộc thôi học, chuyển trường đã tới ngày hẹn (BR-AUTH-07)
Schedule::command('users:apply-deactivations')->dailyAt('00:15')->withoutOverlapping();

// Vai trò DEAN theo nhiệm kỳ lãnh đạo đơn vị: sửa dòng vai trò lệch, ghi nhật ký nhiệm kỳ bắt đầu / kết thúc (BR-FAC-06)
Schedule::command('faculty:sync-leadership')->dailyAt('00:05')->withoutOverlapping();
