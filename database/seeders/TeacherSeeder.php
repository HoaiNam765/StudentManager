<?php

namespace Database\Seeders;

use App\Modules\Faculty\Models\Department;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Services\LecturerService;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu giảng viên (docs/BA.md mục 11.2: 20 giảng viên). Chỉ là dữ liệu giả (NFR-DAT-02): họ tên ghép ngẫu nhiên,
 * email ở tên miền thử nghiệm, không có CCCD. Mỗi hồ sơ có tài khoản (tên đăng nhập = mã giảng viên, vai trò LEC,
 * mật khẩu tạm gửi qua mailer `log`). Chạy lặp lại an toàn theo mã.
 */
class TeacherSeeder extends Seeder
{
    /** [mã, họ tên, mã bộ môn, chức danh, học vị, chuyên môn] */
    public const LECTURERS = [
        ['GV0001', 'Nguyễn Văn An', 'BM-KTPM', 'senior_lecturer', 'doctor', 'Kỹ thuật phần mềm'],
        ['GV0002', 'Trần Thị Bình', 'BM-KTPM', 'lecturer', 'master', 'Kiểm thử phần mềm'],
        ['GV0003', 'Lê Hoàng Cường', 'BM-KTPM', 'lecturer', 'master', 'Phát triển ứng dụng web'],
        ['GV0004', 'Phạm Minh Đức', 'BM-HTTT', 'associate_professor', 'doctor', 'Cơ sở dữ liệu'],
        ['GV0005', 'Hoàng Thị Giang', 'BM-HTTT', 'lecturer', 'master', 'Trí tuệ nhân tạo'],
        ['GV0006', 'Vũ Quốc Hùng', 'BM-HTTT', 'lecturer', 'engineer', 'Hệ thống thông tin quản lý'],
        ['GV0007', 'Đặng Thu Hà', 'BM-MMT', 'senior_lecturer', 'doctor', 'Mạng máy tính'],
        ['GV0008', 'Bùi Văn Khánh', 'BM-MMT', 'lecturer', 'master', 'An toàn thông tin'],
        ['GV0009', 'Đỗ Thị Lan', 'BM-QTKD', 'senior_lecturer', 'doctor', 'Quản trị chiến lược'],
        ['GV0010', 'Hồ Văn Long', 'BM-QTKD', 'lecturer', 'master', 'Marketing'],
        ['GV0011', 'Ngô Thị Mai', 'BM-QTKD', 'lecturer', 'master', 'Kinh tế học'],
        ['GV0012', 'Dương Văn Nam', 'BM-KTKT', 'lecturer', 'master', 'Kế toán tài chính'],
        ['GV0013', 'Lý Thị Oanh', 'BM-KTKT', 'senior_lecturer', 'doctor', 'Kiểm toán'],
        ['GV0014', 'Trịnh Văn Phúc', 'BM-TA', 'lecturer', 'master', 'Ngôn ngữ học tiếng Anh'],
        ['GV0015', 'Mai Thị Quỳnh', 'BM-TA', 'lecturer', 'master', 'Biên – phiên dịch'],
        ['GV0016', 'Cao Văn Sơn', 'BM-TA', 'principal_lecturer', 'doctor', 'Giảng dạy tiếng Anh'],
        ['GV0017', 'Tạ Thị Thảo', 'BM-TT', 'lecturer', 'master', 'Ngôn ngữ Trung Quốc'],
        ['GV0018', 'Lương Văn Tuấn', 'BM-TT', 'lecturer', 'bachelor', 'Hán ngữ'],
        ['GV0019', 'Phan Thị Uyên', 'BM-KTPM', 'lecturer', 'master', 'Phát triển ứng dụng di động'],
        ['GV0020', 'Châu Văn Vinh', 'BM-HTTT', 'professor', 'doctor', 'Học máy'],
    ];

    public function run(): void
    {
        $departments = Department::query()->pluck('id', 'code');
        $service = app(LecturerService::class);

        foreach (self::LECTURERS as $i => [$code, $name, $department, $title, $degree, $expertise]) {
            if (Lecturer::withTrashed()->where('code', $code)->exists() || ! isset($departments[$department])) {
                continue;
            }

            $service->create([
                'code' => $code,
                'full_name' => $name,
                'email' => strtolower($code).'@studentmanager.test',
                'department_id' => $departments[$department],
                'academic_title' => $title,
                'academic_degree' => $degree,
                'expertise' => $expertise,
                'hired_on' => sprintf('%d-09-01', 2010 + $i % 15),
            ]);
        }
    }
}
