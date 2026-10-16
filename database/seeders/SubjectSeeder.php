<?php

namespace Database\Seeders;

use App\Modules\Faculty\Models\Department;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\SubjectService;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu học phần (docs/BA.md mục 11.2: 60 học phần). Chỉ là dữ liệu giả; chạy lặp lại an toàn theo mã.
 * Tạo qua SubjectService để dữ liệu mẫu cũng tuân theo quy tắc tín chỉ (BR-SUB-02); số tiết, giờ tự học
 * tính theo định mức trong cấu hình.
 */
class SubjectSeeder extends Seeder
{
    /** Khối kiến thức: G đại cương, F cơ sở ngành, S chuyên ngành, I thực tập, T đồ án / khóa luận. */
    private const BLOCKS = ['G' => 'general', 'F' => 'foundation', 'S' => 'specialized', 'I' => 'internship', 'T' => 'thesis'];

    /** Học phần điều kiện: không tính GPA nhưng phải Đạt để tốt nghiệp (BR-SUB-06). */
    public const CONDITION_SUBJECTS = ['GDTC101', 'GDTC102', 'GDTC103', 'GDQP101', 'GDQP102', 'GDQP103', 'SHCD101'];

    /** [mã bộ môn, mã học phần, tên, tổng tín chỉ, tín chỉ thực hành, khối, tự chọn?] */
    public const SUBJECTS = [
        ['BM-LLCT', 'LLCT101', 'Triết học Mác – Lênin', 3, 0, 'G', false],
        ['BM-LLCT', 'LLCT102', 'Kinh tế chính trị Mác – Lênin', 2, 0, 'G', false],
        ['BM-LLCT', 'LLCT103', 'Chủ nghĩa xã hội khoa học', 2, 0, 'G', false],
        ['BM-LLCT', 'LLCT104', 'Lịch sử Đảng Cộng sản Việt Nam', 2, 0, 'G', false],
        ['BM-LLCT', 'LLCT105', 'Tư tưởng Hồ Chí Minh', 2, 0, 'G', false],
        ['BM-LLCT', 'LLCT106', 'Pháp luật đại cương', 2, 0, 'G', false],
        ['BM-GDTC', 'GDTC101', 'Giáo dục thể chất 1', 1, 1, 'G', false],
        ['BM-GDTC', 'GDTC102', 'Giáo dục thể chất 2', 1, 1, 'G', false],
        ['BM-GDTC', 'GDTC103', 'Giáo dục thể chất 3', 1, 1, 'G', false],
        ['BM-GDTC', 'GDQP101', 'Giáo dục quốc phòng và an ninh 1', 3, 0, 'G', false],
        ['BM-GDTC', 'GDQP102', 'Giáo dục quốc phòng và an ninh 2', 2, 0, 'G', false],
        ['BM-GDTC', 'GDQP103', 'Giáo dục quốc phòng và an ninh 3', 3, 2, 'G', false],
        ['BM-GDTC', 'SHCD101', 'Tuần sinh hoạt công dân – sinh viên', 0, 0, 'G', false],
        ['BM-TOAN', 'TOAN101', 'Giải tích', 3, 0, 'G', false],
        ['BM-TOAN', 'TOAN102', 'Đại số tuyến tính', 3, 0, 'G', false],
        ['BM-TOAN', 'TOAN103', 'Xác suất thống kê', 3, 0, 'G', false],
        ['BM-TOAN', 'TOAN104', 'Toán rời rạc', 3, 0, 'F', false],
        ['BM-TA', 'ENG101', 'Tiếng Anh 1', 3, 0, 'G', false],
        ['BM-TA', 'ENG102', 'Tiếng Anh 2', 3, 0, 'G', false],
        ['BM-TA', 'ENG103', 'Tiếng Anh 3', 3, 0, 'G', false],
        ['BM-TA', 'ENG201', 'Ngữ âm – Âm vị học tiếng Anh', 3, 0, 'F', false],
        ['BM-TA', 'ENG202', 'Kỹ năng Nghe – Nói', 3, 1, 'F', false],
        ['BM-TA', 'ENG203', 'Kỹ năng Đọc – Viết', 3, 1, 'F', false],
        ['BM-TA', 'ENG301', 'Biên dịch', 3, 1, 'S', false],
        ['BM-TA', 'ENG302', 'Phiên dịch', 3, 1, 'S', false],
        ['BM-TA', 'ENG303', 'Tiếng Anh thương mại', 3, 0, 'S', true],
        ['BM-TA', 'ENG401', 'Thực tập tốt nghiệp ngành Ngôn ngữ Anh', 4, 4, 'I', false],
        ['BM-TA', 'ENG402', 'Khóa luận tốt nghiệp ngành Ngôn ngữ Anh', 7, 7, 'T', false],
        ['BM-TT', 'CHN101', 'Tiếng Trung tổng hợp 1', 4, 1, 'F', false],
        ['BM-TT', 'CHN102', 'Tiếng Trung tổng hợp 2', 4, 1, 'F', false],
        ['BM-TT', 'CHN201', 'Hán tự', 2, 0, 'F', false],
        ['BM-TT', 'CHN301', 'Biên – phiên dịch tiếng Trung', 3, 1, 'S', false],
        ['BM-KTPM', 'INT101', 'Nhập môn lập trình', 3, 1, 'F', false],
        ['BM-KTPM', 'INT102', 'Kỹ thuật lập trình', 3, 1, 'F', false],
        ['BM-KTPM', 'INT201', 'Cấu trúc dữ liệu và giải thuật', 4, 1, 'F', false],
        ['BM-KTPM', 'INT202', 'Lập trình hướng đối tượng', 3, 1, 'F', false],
        ['BM-KTPM', 'INT301', 'Công nghệ phần mềm', 3, 1, 'S', false],
        ['BM-KTPM', 'INT302', 'Kiểm thử phần mềm', 3, 1, 'S', false],
        ['BM-KTPM', 'INT303', 'Phát triển ứng dụng web', 3, 1, 'S', false],
        ['BM-KTPM', 'INT304', 'Phát triển ứng dụng di động', 3, 1, 'S', true],
        ['BM-KTPM', 'INT401', 'Thực tập doanh nghiệp', 4, 4, 'I', false],
        ['BM-KTPM', 'INT402', 'Đồ án tốt nghiệp', 10, 10, 'T', false],
        ['BM-HTTT', 'INT203', 'Cơ sở dữ liệu', 3, 1, 'F', false],
        ['BM-HTTT', 'INT204', 'Hệ điều hành', 3, 1, 'F', false],
        ['BM-HTTT', 'INT305', 'Phân tích và thiết kế hệ thống thông tin', 3, 1, 'S', false],
        ['BM-HTTT', 'INT306', 'Trí tuệ nhân tạo', 3, 1, 'S', false],
        ['BM-HTTT', 'INT307', 'Học máy', 3, 1, 'S', true],
        ['BM-MMT', 'INT205', 'Mạng máy tính', 3, 1, 'F', false],
        ['BM-MMT', 'INT206', 'Kiến trúc máy tính', 3, 0, 'F', false],
        ['BM-MMT', 'INT308', 'An toàn thông tin', 3, 1, 'S', false],
        ['BM-MMT', 'INT309', 'Điện toán đám mây', 3, 1, 'S', true],
        ['BM-QTKD', 'ECO101', 'Kinh tế vi mô', 3, 0, 'G', false],
        ['BM-QTKD', 'ECO102', 'Kinh tế vĩ mô', 3, 0, 'G', false],
        ['BM-QTKD', 'BUS201', 'Quản trị học', 3, 0, 'F', false],
        ['BM-QTKD', 'BUS202', 'Marketing căn bản', 3, 0, 'F', false],
        ['BM-QTKD', 'BUS301', 'Quản trị chiến lược', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS302', 'Hành vi người tiêu dùng', 3, 0, 'S', true],
        ['BM-QTKD', 'BUS401', 'Thực tập tốt nghiệp ngành Quản trị kinh doanh', 4, 4, 'I', false],
        ['BM-KTKT', 'ACC201', 'Nguyên lý kế toán', 3, 0, 'F', false],
        ['BM-KTKT', 'ACC301', 'Kế toán tài chính', 3, 0, 'S', false],
    ];

    public function run(): void
    {
        $departments = Department::query()->pluck('id', 'code');
        $service = app(SubjectService::class);

        foreach (self::SUBJECTS as [$department, $code, $name, $credits, $practice, $block, $elective]) {
            if (Subject::withTrashed()->where('code', $code)->exists()) {
                continue;
            }

            $condition = in_array($code, self::CONDITION_SUBJECTS, true);

            $service->create([
                'department_id' => $departments[$department],
                'code' => $code,
                'name' => $name,
                'teaching_language' => str_starts_with($code, 'ENG') ? 'en' : 'vi',
                'credits' => $credits,
                'practice_credits' => $practice,
                'knowledge_block' => self::BLOCKS[$block],
                'requirement' => $elective ? 'elective' : 'compulsory',
                'counts_toward_gpa' => ! $condition,
                'is_graduation_requirement' => $condition,
            ] + ($code === 'SHCD101' ? ['theory_periods' => 30, 'self_study_hours' => 0] : []));
        }
    }
}
