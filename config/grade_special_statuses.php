<?php

return [
    'absent_unexcused' => ['symbol' => 'V', 'gpa' => 'zero'],
    'absent_excused' => ['symbol' => 'HT', 'gpa' => 'exclude'],
    'exam_banned' => ['symbol' => 'CT', 'gpa' => 'zero'],
    'deferred' => ['symbol' => 'H', 'gpa' => 'exclude'],
    'exempt' => ['symbol' => 'M', 'gpa' => 'exclude'],
    'recognized' => ['symbol' => 'CN', 'gpa' => 'exclude'],
    'incomplete' => ['symbol' => 'I', 'gpa' => 'exclude'],
    'withdrawn' => ['symbol' => 'R', 'gpa' => 'exclude'],
];
