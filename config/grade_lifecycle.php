<?php

// Quy trình duyệt điểm (FR-GRD-004). Thang quy đổi và điểm đạt nằm trong bộ quy chế theo khóa (grading.scale,
// grading.pass_score — PolicyResolver), không đặt ở đây.
return [
    // Có bước khoa duyệt (DEAN) giữa "Đã nộp" và "Đã xác nhận" không
    'faculty_approval_required' => (bool) env('GRD_FACULTY_APPROVAL', true),
];
