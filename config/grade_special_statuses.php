<?php

/*
 * Trạng thái điểm đặc biệt (FR-GRD-012; BR-GRD-07). Mỗi trạng thái: nhãn, ký hiệu hiện trên bảng điểm, cách tính GPA
 * ('zero' = tính như 0 điểm, 'exclude' = không tính), và `final_zero` = ghi 0 cho thành phần thi cuối kỳ rồi tính điểm
 * bình thường (vắng thi không lý do, cấm thi); `earns_credit` = vẫn cộng tín chỉ tích lũy dù không tính GPA (miễn, công
 * nhận). Trạng thái loại trừ không cần đủ điểm thành phần khi nộp.
 * Đổi ký hiệu / cách tính theo quy chế của trường tại đây.
 */
return [
    'absent_unexcused' => ['label' => 'Vắng thi không lý do', 'symbol' => 'V', 'gpa' => 'zero', 'final_zero' => true],
    'exam_banned' => ['label' => 'Cấm thi', 'symbol' => 'CT', 'gpa' => 'zero', 'final_zero' => true],
    'absent_excused' => ['label' => 'Vắng thi có lý do (hoãn thi)', 'symbol' => 'HT', 'gpa' => 'exclude', 'final_zero' => false],
    'deferred' => ['label' => 'Hoãn thi', 'symbol' => 'H', 'gpa' => 'exclude', 'final_zero' => false],
    'exempt' => ['label' => 'Miễn học', 'symbol' => 'M', 'gpa' => 'exclude', 'final_zero' => false, 'earns_credit' => true],
    'recognized' => ['label' => 'Công nhận', 'symbol' => 'CN', 'gpa' => 'exclude', 'final_zero' => false, 'earns_credit' => true],
    'incomplete' => ['label' => 'Chưa đủ dữ liệu', 'symbol' => 'I', 'gpa' => 'exclude', 'final_zero' => false],
];
