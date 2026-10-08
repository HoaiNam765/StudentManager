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

    // Tên trường hiển thị ở giao diện (đăng nhập, menu, chân trang)
    'school' => [
        'name' => env('SCHOOL_NAME', 'Trường Đại học Công Thương TP. Hồ Chí Minh'),
        'short_name' => env('SCHOOL_SHORT_NAME', 'Trường ĐH Công Thương'),
        'abbr' => env('SCHOOL_ABBR', 'HUIT'),
    ],

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

    /*
    |--------------------------------------------------------------------------
    | Xuất dữ liệu (GC-01, NFR-CMP-02, NFR-PERF-03)
    |--------------------------------------------------------------------------
    | Trên ngưỡng đồng bộ thì xuất nền; các tác vụ lớn không giữ toàn bộ bảng trong bộ nhớ.
    */
    'export' => [
        'disk' => env('EXPORT_DISK', 'local'),
        'sync_threshold' => (int) env('EXPORT_SYNC_THRESHOLD', 50000),
        'job_timeout_seconds' => (int) env('EXPORT_JOB_TIMEOUT_SECONDS', 300),
        'pdf_font' => env('EXPORT_PDF_FONT', 'DejaVu Sans'),

        // PDF dựng cả bảng trong bộ nhớ nên tốn hơn Excel/CSV rất nhiều (đo trên máy dev, 3 cột:
        // 1.000 dòng ≈ 3,6 giây/190 MB, 2.000 dòng ≈ 12 giây/480 MB, 3.000 dòng ≈ 25 giây/920 MB;
        // Excel 50.000 dòng ≈ 2 giây/22 MB). PDF từ ngưỡng này chạy nền; quá mức trần thì từ chối.
        'pdf_sync_threshold' => (int) env('EXPORT_PDF_SYNC_THRESHOLD', 300),
        'pdf_max_rows' => (int) env('EXPORT_PDF_MAX_ROWS', 2000),

        // Số ngày giữ tệp xuất chạy nền; sau đó lệnh exports:prune xóa tệp và đặt yêu cầu sang expired
        'retention_days' => (int) env('EXPORT_RETENTION_DAYS', 7),
    ],

];
