<?php

namespace App\Modules\AcademicYear\Models;

enum TermStatus: string
{
    case PLANNED = 'planned';
    case REGISTRATION = 'registration';
    case IN_PROGRESS = 'in_progress';
    case EXAM_GRADING = 'exam_grading';
    case COMPLETED = 'completed';
    case LOCKED = 'locked';
}
