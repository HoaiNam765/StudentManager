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

    /*
    |--------------------------------------------------------------------------
    | Đăng nhập và mật khẩu (module AUTH)
    |--------------------------------------------------------------------------
    | Giá trị mặc định theo docs/BA.md: BR-AUTH-06 (khóa tạm), BR-AUTH-10 (mật khẩu tạm),
    | FR-AUTH-005 (chính sách mật khẩu), NFR-SEC-03 (giới hạn tần suất). Không viết cứng trong mã (GC-12).
    */
    'auth' => [
        // Khóa tạm sau N lần sai liên tiếp trong khoảng thời gian, khóa trong M phút
        'lockout_max_attempts' => (int) env('AUTH_LOCKOUT_MAX_ATTEMPTS', 5),
        'lockout_window_minutes' => (int) env('AUTH_LOCKOUT_WINDOW_MINUTES', 15),
        'lockout_minutes' => (int) env('AUTH_LOCKOUT_MINUTES', 15),

        // Giới hạn số lần gửi biểu mẫu đăng nhập mỗi phút (chống dò mật khẩu hàng loạt)
        'throttle_per_minute' => (int) env('AUTH_THROTTLE_PER_MINUTE', 10),
        'throttle_per_ip_per_minute' => (int) env('AUTH_THROTTLE_PER_IP_PER_MINUTE', 30),

        // Mật khẩu tạm do hệ thống hoặc quản trị viên cấp hết hạn sau N ngày nếu chưa dùng
        'temporary_password_days' => (int) env('AUTH_TEMPORARY_PASSWORD_DAYS', 7),

        'password' => [
            'min_length' => (int) env('AUTH_PASSWORD_MIN_LENGTH', 8),
            'mixed_case' => (bool) env('AUTH_PASSWORD_MIXED_CASE', true),
            'numbers' => (bool) env('AUTH_PASSWORD_NUMBERS', true),
            'symbols' => (bool) env('AUTH_PASSWORD_SYMBOLS', false),
            // Không được trùng N mật khẩu gần nhất (tính cả mật khẩu hiện tại)
            'history' => (int) env('AUTH_PASSWORD_HISTORY', 3),
            // Hết hạn sau N ngày, buộc đổi; để trống là không hết hạn
            'expires_days' => env('AUTH_PASSWORD_EXPIRES_DAYS') !== null ? (int) env('AUTH_PASSWORD_EXPIRES_DAYS') : null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Trung tâm import (module SYS)
    |--------------------------------------------------------------------------
    | FR-SYS-007, BR-SYS-07, GC-07, NFR-PERF-04 (5.000 dòng không quá 60 giây). Không viết cứng trong mã (GC-12).
    */
    'import' => [
        // Disk lưu file tải lên
        'disk' => env('IMPORT_DISK', 'local'),

        // Dung lượng file tối đa (KB)
        'max_file_kb' => (int) env('IMPORT_MAX_FILE_KB', 10240),

        // Từ số dòng này trở lên thì kiểm tra/lưu chạy nền (hàng đợi)
        'async_threshold' => (int) env('IMPORT_ASYNC_THRESHOLD', 500),

        // Số dòng mỗi lần ghi vào CSDL và mỗi lần cập nhật tiến trình
        'chunk_size' => (int) env('IMPORT_CHUNK_SIZE', 500),

        // Thời gian tối đa (giây) của một job nền
        'job_timeout_seconds' => (int) env('IMPORT_JOB_TIMEOUT_SECONDS', 120),
    ],

];
