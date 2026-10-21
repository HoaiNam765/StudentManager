<?php

return [
    'faculty_approval_required' => (bool) env('GRD_FACULTY_APPROVAL', true),
    'score_digits' => (int) env('GRD_RESULT_DIGITS', 1),
    'pass_score' => (float) env('GRD_PASS_SCORE', 4),
    'scale' => [
        ['min' => 8.5, 'letter' => 'A', 'four' => 4.0],
        ['min' => 7.0, 'letter' => 'B', 'four' => 3.0],
        ['min' => 5.5, 'letter' => 'C', 'four' => 2.0],
        ['min' => 4.0, 'letter' => 'D', 'four' => 1.0],
        ['min' => 0.0, 'letter' => 'F', 'four' => 0.0],
    ],
];
