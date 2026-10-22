<?php

// Thông báo (NOT; BA 6.12). Kênh P1: chỉ trong ứng dụng; email / SMS ở P2.
return [
    // Số người nhận ghi mỗi lô khi gửi hàng loạt (BR-NOT-07)
    'chunk_size' => (int) env('NOT_CHUNK_SIZE', 500),

    // Số thông báo mỗi trang ở trung tâm thông báo
    'per_page' => (int) env('NOT_PER_PAGE', 20),

    // Chuông thông báo hỏi lại số chưa đọc sau mỗi … giây
    'poll_seconds' => (int) env('NOT_POLL_SECONDS', 60),

    // Thông báo đã hết hạn được giữ thêm … ngày rồi lệnh `notifications:prune` mới xóa (BR-NOT-06)
    'retention_days' => (int) env('NOT_RETENTION_DAYS', 365),

    // Nhóm thông báo hiển thị (lọc ở trung tâm thông báo)
    'categories' => [
        'academic' => 'Học vụ',
        'enrollment' => 'Đăng ký học phần',
        'timetable' => 'Lịch học',
        'grade' => 'Điểm',
        'account' => 'Tài khoản',
        'announcement' => 'Thông báo chung',
        'general' => 'Khác',
    ],
];
