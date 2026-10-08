<?php

namespace App\Modules\AcademicYear\Models;

/**
 * Loại mốc trong lịch học vụ của học kỳ (FR-ACY-004). Module khác đọc mốc bằng
 * `app(MilestoneService::class)->date($term, MilestoneType::WithdrawDeadline)`.
 */
enum MilestoneType: string
{
    case RegistrationOpen = 'registration_open';
    case RegistrationClose = 'registration_close';
    case ClassesStart = 'classes_start';
    case CancelDeadline = 'cancel_deadline';
    case WithdrawDeadline = 'withdraw_deadline';
    case TeachingEnd = 'teaching_end';
    case ExamStart = 'exam_start';
    case ExamEnd = 'exam_end';
    case GradeEntryDeadline = 'grade_entry_deadline';
    case GradePublish = 'grade_publish';
    case TuitionDeadline = 'tuition_deadline';
    case TermEnd = 'term_end';

    public function label(): string
    {
        return match ($this) {
            self::RegistrationOpen => 'Mở đăng ký',
            self::RegistrationClose => 'Đóng đăng ký',
            self::ClassesStart => 'Bắt đầu học',
            self::CancelDeadline => 'Hạn hủy học phần',
            self::WithdrawDeadline => 'Hạn rút học phần',
            self::TeachingEnd => 'Kết thúc giảng dạy',
            self::ExamStart => 'Bắt đầu thi',
            self::ExamEnd => 'Kết thúc thi',
            self::GradeEntryDeadline => 'Hạn nhập điểm',
            self::GradePublish => 'Công bố điểm',
            self::TuitionDeadline => 'Hạn nộp học phí',
            self::TermEnd => 'Kết thúc học kỳ',
        };
    }

    /**
     * Thứ tự bắt buộc (BR-ACY-02): [mốc trước, phép so sánh, mốc sau]. Mốc nào chưa có thì bỏ qua cặp đó.
     * "<" là phải trước hẳn, "<=" là được trùng ngày.
     *
     * @return list<array{0: self, 1: '<'|'<=', 2: self}>
     */
    public static function orderRules(): array
    {
        return [
            [self::RegistrationOpen, '<', self::RegistrationClose],
            [self::RegistrationClose, '<=', self::ClassesStart],
            [self::CancelDeadline, '<', self::WithdrawDeadline],
            [self::WithdrawDeadline, '<', self::TeachingEnd],
            [self::TeachingEnd, '<', self::ExamStart],
            [self::ExamStart, '<=', self::ExamEnd],
            [self::ExamStart, '<', self::GradePublish],
            [self::ExamEnd, '<=', self::GradeEntryDeadline],
            [self::GradeEntryDeadline, '<=', self::GradePublish],
            [self::ClassesStart, '<', self::TeachingEnd],
            [self::GradePublish, '<=', self::TermEnd],
        ];
    }
}
