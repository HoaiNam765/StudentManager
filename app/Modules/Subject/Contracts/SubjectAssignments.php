<?php

namespace App\Modules\Subject\Contracts;

use App\Models\User;

/**
 * Học phần mà người dùng "phụ trách" theo phạm vi SECTION của giảng viên (BA 4.3: lớp học phần được phân công).
 * `Subject::applyDataScope()` dùng để lọc học phần và đề cương giảng viên được xem, được sửa.
 *
 * Chưa có lớp học phần (CLS) nên TCH tạm cài `DepartmentSubjectAssignments`: học phần thuộc bộ môn chính hoặc bộ môn
 * kiêm nhiệm của giảng viên. CLS (#107) bind lại bằng "học phần của các lớp được phân công":
 *
 *     $this->app->bind(SubjectAssignments::class, SectionSubjectAssignments::class);
 */
interface SubjectAssignments
{
    /** @return list<int> id học phần */
    public function subjectIdsFor(User $user): array;
}
