<?php

return [
    'retake_policy' => env('GRD_RETAKE_POLICY', 'highest'), // highest | latest
    'classification' => [
        ['min' => 3.6, 'name' => 'Xuất sắc'], ['min' => 3.2, 'name' => 'Giỏi'],
        ['min' => 2.5, 'name' => 'Khá'], ['min' => 2.0, 'name' => 'Trung bình'],
        ['min' => 1.0, 'name' => 'Yếu'], ['min' => 0.0, 'name' => 'Kém'],
    ],
];
