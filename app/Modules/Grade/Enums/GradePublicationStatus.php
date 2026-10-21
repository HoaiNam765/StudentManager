<?php

namespace App\Modules\Grade\Enums;

enum GradePublicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case FacultyApproved = 'faculty_approved';
    case Confirmed = 'confirmed';
    case Published = 'published';
    case Locked = 'locked';
    case Returned = 'returned';

    public function public(): bool
    {
        return in_array($this, [self::Published, self::Locked], true);
    }

    public function allows(self $next, bool $facultyRequired): bool
    {
        return match ($this) {
            self::Draft => $next === self::Submitted,
            self::Submitted => in_array($next, $facultyRequired ? [self::FacultyApproved, self::Returned] : [self::Confirmed, self::Returned], true),
            self::FacultyApproved => in_array($next, [self::Confirmed, self::Returned], true),
            self::Confirmed => in_array($next, [self::Published, self::Returned], true),
            self::Published => $next === self::Locked,
            self::Returned => $next === self::Draft,
            self::Locked => false,
        };
    }
}
