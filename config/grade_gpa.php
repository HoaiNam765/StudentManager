<?php

// GPA (BR-GRD-04; FR-GRD-009). Xếp loại học lực lấy từ bộ quy chế theo khóa (classification.bands — PolicyResolver),
// không đặt ở đây.
return [
    // GPA tích lũy lấy kết quả cao nhất của mỗi học phần (highest, mặc định) hay lần học gần nhất (latest) — Q-12
    'retake_policy' => env('GRD_RETAKE_POLICY', 'highest'),
];
