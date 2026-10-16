<?php

namespace App\Modules\Teacher\Enums;

/** Chức danh của giảng viên (FR-TCH-001). */
enum AcademicTitle: string
{
    case Lecturer = 'lecturer';
    case SeniorLecturer = 'senior_lecturer';
    case PrincipalLecturer = 'principal_lecturer';
    case AssociateProfessor = 'associate_professor';
    case Professor = 'professor';

    public function label(): string
    {
        return match ($this) {
            self::Lecturer => 'Giảng viên',
            self::SeniorLecturer => 'Giảng viên chính',
            self::PrincipalLecturer => 'Giảng viên cao cấp',
            self::AssociateProfessor => 'Phó giáo sư',
            self::Professor => 'Giáo sư',
        };
    }
}
