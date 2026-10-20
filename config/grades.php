<?php

return [
    // FR-GRD-002, BR-GRD-10: ngưỡng và bước điểm có thể cấu hình.
    'min_score' => (float) env('GRD_MIN_SCORE', 0),
    'max_score' => (float) env('GRD_MAX_SCORE', 10),
    'score_step' => (float) env('GRD_SCORE_STEP', 0.1),
    'import_max_rows' => (int) env('GRD_IMPORT_MAX_ROWS', 5000),
    'import_max_kb' => (int) env('GRD_IMPORT_MAX_KB', 10240),
];
