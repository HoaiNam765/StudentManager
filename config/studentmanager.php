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

        // Quên mật khẩu: số lần gửi biểu mẫu mỗi phút từ một địa chỉ IP (chống dò tài khoản, gửi email hàng loạt)
        'password_reset_per_minute' => (int) env('AUTH_PASSWORD_RESET_PER_MINUTE', 5),

        // "Ghi nhớ đăng nhập" có hiệu lực tối đa N ngày; 0 = tắt (FR-AUTH-007). Quản trị viên đổi được ở tham số hệ thống
        'remember_days' => (int) env('AUTH_REMEMBER_DAYS', 30),
        // Hết hạn phiên khi không hoạt động: dùng `session.lifetime` (SESSION_LIFETIME, phút; tham số hệ thống `session.lifetime`)

        // Đăng nhập bất thường (EV-AUTH-02): IP và thiết bị đều chưa từng đăng nhập thành công trong N ngày gần đây; 0 = tắt
        'unusual_login_lookback_days' => (int) env('AUTH_UNUSUAL_LOGIN_LOOKBACK_DAYS', 90),

        // Sinh viên thôi học, buộc thôi học, chuyển trường: ngừng tài khoản sau N ngày (BR-AUTH-07); 0 = ngừng ngay
        'student_deactivate_after_days' => (int) env('AUTH_STUDENT_DEACTIVATE_AFTER_DAYS', 30),

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
    /*
    |--------------------------------------------------------------------------
    | Khoa, bộ môn (module FAC)
    |--------------------------------------------------------------------------
    | BR-FAC-04: trưởng khoa, trưởng bộ môn phải là giảng viên hoặc cán bộ đang hoạt động thuộc đơn vị đó,
    | trừ khi bật ngoại lệ. Lớp kiểm tra thật do TCH cài (LeaderEligibility).
    */
    // Giảng viên (TCH): dung lượng ảnh hồ sơ tối đa (KB)
    'teacher' => [
        'photo_max_kb' => (int) env('TEACHER_PHOTO_MAX_KB', 2048),

        // Số tiết tối đa trong ngày, dùng kiểm tra lịch bận (khung tiết học chính thức thuộc TTB, FR-TTB-001)
        'periods_per_day' => (int) env('TEACHER_PERIODS_PER_DAY', 15),
    ],

    // Sinh viên (STU): khoảng tuổi khi nhập học (BR-STU-03)
    'student' => [
        'min_age' => (int) env('STUDENT_MIN_AGE', 16),
        'max_age' => (int) env('STUDENT_MAX_AGE', 60),
        // Tệp quyết định chuyển trạng thái (FR-STU-010): disk riêng tư, dung lượng tối đa (KB)
        'decision_disk' => env('STUDENT_DECISION_DISK', 'local'),
        'decision_max_kb' => (int) env('STUDENT_DECISION_MAX_KB', 5120),
        // MSSV khi xác nhận nhập học (BR-STU-01): 2 số cuối khóa + N số cuối mã ngành + số thứ tự trong khóa – ngành
        'code_major_digits' => (int) env('STUDENT_CODE_MAJOR_DIGITS', 4),
        'code_sequence_digits' => (int) env('STUDENT_CODE_SEQUENCE_DIGITS', 4),
        // Email trường cấp cho sinh viên: {MSSV}@{domain}
        'email_domain' => env('STUDENT_EMAIL_DOMAIN', 'student.studentmanager.test'),
    ],

    'faculty' => [
        'allow_leader_outside_unit' => (bool) env('FACULTY_ALLOW_LEADER_OUTSIDE_UNIT', false),
    ],

    // Học phần (SUB, FR-SUB-002): giới hạn tín chỉ và định mức số tiết / giờ tự học dùng làm giá trị gợi ý
    'subject' => [
        'max_credits' => (int) env('SUBJECT_MAX_CREDITS', 20),
        'periods_per_credit' => [
            'theory' => (int) env('SUBJECT_THEORY_PERIODS_PER_CREDIT', 15),
            'practice' => (int) env('SUBJECT_PRACTICE_PERIODS_PER_CREDIT', 30),
        ],
        'self_study_hours_per_credit' => (int) env('SUBJECT_SELF_STUDY_HOURS_PER_CREDIT', 30),

        // Đề cương (FR-SUB-006, BR-SUB-08): bắt buộc được duyệt trước khi áp dụng cho lớp mới; tệp PDF tối đa (KB), lưu ở disk riêng tư
        'syllabus_requires_approval' => (bool) env('SYLLABUS_REQUIRES_APPROVAL', true),
        'syllabus_max_kb' => (int) env('SYLLABUS_MAX_KB', 10240),
        'syllabus_disk' => env('SYLLABUS_DISK', 'local'),
    ],

    // Lớp (CLS): khóa mặc định kéo dài N năm; sĩ số mặc định và tối đa của lớp hành chính (FR-CLS-001, 002)
    'school_class' => [
        'default_cohort_years' => (int) env('CLASS_DEFAULT_COHORT_YEARS', 4),
        'default_max_size' => (int) env('CLASS_DEFAULT_MAX_SIZE', 60),
        'admin_class_max_size' => (int) env('CLASS_ADMIN_CLASS_MAX_SIZE', 120),
    ],

    // Lớp học phần (CLS, FR-CLS-009, 011; BR-CLS-02): sĩ số mặc định, mức tối đa; định mức số tiết một giảng viên trong học kỳ
    'course_section' => [
        'default_min_size' => (int) env('SECTION_DEFAULT_MIN_SIZE', 15),
        'default_max_size' => (int) env('SECTION_DEFAULT_MAX_SIZE', 60),
        'max_size' => (int) env('SECTION_MAX_SIZE', 200),
        'max_periods_per_term' => (int) env('SECTION_MAX_PERIODS_PER_TERM', 400),
    ],

    // Đăng ký học phần (ENR): khung tín chỉ riêng (BR-ENR-02; khung thường tính từ quy chế registration.load_*_ratio),
    // cùng học phần hai lớp (BR-ENR-04), chặn khi nợ quá hạn (BR-ENR-09)
    'enrollment' => [
        'probation_max_credits' => (int) env('ENR_PROBATION_MAX_CREDITS', 14),
        'good_standing_extra_credits' => (int) env('ENR_GOOD_STANDING_EXTRA_CREDITS', 3),
        'final_term_max_credits' => (int) env('ENR_FINAL_TERM_MAX_CREDITS', 30),
        'allow_same_subject_twice' => (bool) env('ENR_ALLOW_SAME_SUBJECT_TWICE', false),
        'block_on_debt' => (bool) env('ENR_BLOCK_ON_DEBT', false),
        'debt_threshold' => (int) env('ENR_DEBT_THRESHOLD', 0),
    ],

    // Thời khóa biểu (TTB): buổi học không kết thúc muộn hơn giờ này (BR-TTB-03)
    'timetable' => [
        'latest_end' => env('TIMETABLE_LATEST_END', '21:00'),
        // Đổi lịch sau khi công bố phải báo trước tối thiểu số giờ này, trừ khi khẩn cấp (BR-TTB-05)
        'change_notice_hours' => (int) env('TIMETABLE_CHANGE_NOTICE_HOURS', 24),
        // Loại phòng dùng cho lớp thực hành (BR-ROM-03)
        'practice_room_types' => array_filter(explode(',', env('TIMETABLE_PRACTICE_ROOM_TYPES', 'MT,TN'))),
        // Xung đột chặn (block) hay chỉ cảnh báo (warn) (FR-TTB-003)
        'conflicts' => [
            'room' => env('TIMETABLE_CONFLICT_ROOM', 'block'),
            'lecturer' => env('TIMETABLE_CONFLICT_LECTURER', 'block'),
            'student' => env('TIMETABLE_CONFLICT_STUDENT', 'warn'),
            'room_type' => env('TIMETABLE_CONFLICT_ROOM_TYPE', 'block'),
        ],
    ],

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
