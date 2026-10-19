<?php

return [
    // FR-GRD-002, BR-GRD-10: ngưỡng và bước điểm có thể cấu hình.
    'min_score' => (float) env('GRD_MIN_SCORE', 0),
    'max_score' => (float) env('GRD_MAX_SCORE', 10),
    'score_step' => (float) env('GRD_SCORE_STEP', 0.1),
    'import_max_rows' => (int) env('GRD_IMPORT_MAX_ROWS', 5000),
    'import_max_kb' => (int) env('GRD_IMPORT_MAX_KB', 10240),


    
    'round_digits' => (int) env('GRD_ROUND_DIGITS', 1),
    'pass_score' => (float) env('GRD_PASS_SCORE', 4.0),

    'default_scale' => [
        ['min' => 8.5, 'letter' => 'A', 'point4' => 4.0],
        ['min' => 7.0, 'letter' => 'B', 'point4' => 3.0],
        ['min' => 5.5, 'letter' => 'C', 'point4' => 2.0],
        ['min' => 4.0, 'letter' => 'D', 'point4' => 1.0],
        ['min' => 0.0, 'letter' => 'F', 'point4' => 0.0],
    ],

];
