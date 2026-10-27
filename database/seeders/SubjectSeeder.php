<?php

namespace Database\Seeders;

use App\Modules\Faculty\Models\Department;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\SubjectService;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu học phần (docs/BA.md mục 11.2: 60 học phần, bổ sung thêm để đủ CTĐT 8 học kỳ của 6 ngành). Chỉ là dữ
 * liệu giả; chạy lặp lại an toàn theo mã.
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

        // Bổ sung cho CTĐT đầy đủ 8 học kỳ (CurriculumSeeder). Không đổi 60 học phần ở trên: test và `uat:p1` dựa vào chúng.
        // Đại cương và cơ sở dùng chung nhiều ngành
        ['BM-LLCT', 'LLCT107', 'Phương pháp nghiên cứu khoa học', 2, 0, 'G', false],
        ['BM-LLCT', 'LLCT108', 'Cơ sở văn hóa Việt Nam', 2, 0, 'G', false],
        ['BM-TOAN', 'TOAN105', 'Kinh tế lượng', 3, 0, 'F', false],
        ['BM-HTTT', 'INT103', 'Tin học đại cương', 2, 0, 'G', false],
        ['BM-HTTT', 'INT104', 'Thực hành tin học đại cương', 1, 1, 'G', false],
        ['BM-QTKD', 'BUS103', 'Kỹ năng mềm', 2, 0, 'G', false],
        ['BM-QTKD', 'BUS104', 'Khởi nghiệp và đổi mới sáng tạo', 2, 0, 'G', false],
        ['BM-TA', 'LIN101', 'Dẫn luận ngôn ngữ học', 2, 0, 'F', false],
        ['BM-TA', 'LIN102', 'Tiếng Việt thực hành', 2, 0, 'G', false],
        ['BM-TA', 'LIN103', 'Giao tiếp liên văn hóa', 3, 0, 'F', false],
        ['BM-TA', 'LIN104', 'Phương pháp giảng dạy ngoại ngữ', 3, 0, 'S', true],
        ['BM-TA', 'LIN105', 'Ngôn ngữ học đối chiếu', 3, 0, 'S', false],
        // Công nghệ thông tin, Kỹ thuật phần mềm (lý thuyết và thực hành tách thành hai học phần)
        ['BM-MMT', 'INT207', 'Thực hành mạng máy tính', 1, 1, 'F', false],
        ['BM-KTPM', 'INT208', 'Lập trình Java', 2, 0, 'F', false],
        ['BM-KTPM', 'INT209', 'Thực hành lập trình Java', 1, 1, 'F', false],
        ['BM-HTTT', 'INT210', 'Hệ quản trị cơ sở dữ liệu', 2, 0, 'F', false],
        ['BM-HTTT', 'INT211', 'Thực hành hệ quản trị cơ sở dữ liệu', 1, 1, 'F', false],
        ['BM-HTTT', 'INT212', 'Lập trình Python', 2, 0, 'F', false],
        ['BM-HTTT', 'INT213', 'Thực hành lập trình Python', 1, 1, 'F', false],
        ['BM-HTTT', 'INT310', 'Cơ sở dữ liệu nâng cao', 2, 0, 'S', false],
        ['BM-HTTT', 'INT311', 'Thực hành cơ sở dữ liệu nâng cao', 1, 1, 'S', false],
        ['BM-HTTT', 'INT312', 'Quản trị dự án công nghệ thông tin', 3, 0, 'S', false],
        ['BM-KTPM', 'INT313', 'Kiến trúc và thiết kế phần mềm', 3, 1, 'S', false],
        ['BM-KTPM', 'INT314', 'Đảm bảo chất lượng phần mềm', 3, 1, 'S', true],
        ['BM-MMT', 'INT315', 'Quản trị mạng', 2, 0, 'S', false],
        ['BM-MMT', 'INT316', 'Thực hành quản trị mạng', 1, 1, 'S', false],
        ['BM-HTTT', 'INT317', 'Khai phá dữ liệu', 3, 1, 'S', true],
        ['BM-MMT', 'INT318', 'An ninh mạng', 3, 1, 'S', true],
        ['BM-HTTT', 'INT319', 'Dữ liệu lớn', 3, 1, 'S', true],
        ['BM-HTTT', 'INT320', 'Thị giác máy tính', 3, 1, 'S', true],
        ['BM-MMT', 'INT321', 'Internet vạn vật', 3, 1, 'S', true],
        ['BM-KTPM', 'INT322', 'Phát triển và vận hành phần mềm (DevOps)', 3, 1, 'S', true],
        ['BM-KTPM', 'INT323', 'Đồ án chuyên ngành', 2, 2, 'S', false],
        ['BM-KTPM', 'INT324', 'Đạo đức nghề nghiệp và pháp luật công nghệ thông tin', 2, 0, 'S', false],
        // Quản trị kinh doanh, Kế toán
        ['BM-QTKD', 'BUS203', 'Luật kinh tế', 2, 0, 'F', false],
        ['BM-QTKD', 'BUS204', 'Thống kê kinh doanh', 3, 0, 'F', false],
        ['BM-QTKD', 'BUS205', 'Quản trị nguồn nhân lực', 3, 0, 'F', false],
        ['BM-QTKD', 'BUS303', 'Quản trị marketing', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS304', 'Quản trị sản xuất và vận hành', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS305', 'Kinh doanh quốc tế', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS306', 'Quản trị dự án', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS307', 'Nghiên cứu marketing', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS308', 'Quản trị bán hàng', 3, 0, 'S', true],
        ['BM-QTKD', 'BUS309', 'Kỹ năng đàm phán trong kinh doanh', 3, 0, 'S', true],
        ['BM-QTKD', 'BUS310', 'Thương mại điện tử', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS311', 'Đề án quản trị kinh doanh', 2, 2, 'S', false],
        ['BM-QTKD', 'BUS312', 'Quản trị chất lượng', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS313', 'Văn hóa doanh nghiệp', 2, 0, 'S', false],
        ['BM-QTKD', 'BUS314', 'Quản trị chuỗi cung ứng', 3, 0, 'S', true],
        ['BM-QTKD', 'BUS315', 'Quản trị rủi ro', 3, 0, 'S', true],
        ['BM-QTKD', 'BUS316', 'Marketing kỹ thuật số', 3, 0, 'S', true],
        ['BM-QTKD', 'BUS317', 'Quản trị thương hiệu', 3, 0, 'S', true],
        ['BM-QTKD', 'BUS318', 'Đạo đức kinh doanh và trách nhiệm xã hội', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS319', 'Quản trị sự thay đổi', 3, 0, 'S', false],
        ['BM-QTKD', 'BUS402', 'Khóa luận tốt nghiệp ngành Quản trị kinh doanh', 7, 7, 'T', false],
        ['BM-KTKT', 'ACC202', 'Tài chính doanh nghiệp', 3, 0, 'F', false],
        ['BM-KTKT', 'ACC203', 'Tài chính – tiền tệ', 3, 0, 'F', false],
        ['BM-KTKT', 'ACC302', 'Kế toán tài chính 2', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC303', 'Kế toán quản trị', 3, 0, 'F', false],
        ['BM-KTKT', 'ACC304', 'Kế toán chi phí', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC305', 'Thuế', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC306', 'Kiểm toán căn bản', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC307', 'Kiểm toán báo cáo tài chính', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC308', 'Phân tích báo cáo tài chính', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC309', 'Hệ thống thông tin kế toán', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC310', 'Kế toán hành chính sự nghiệp', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC311', 'Kế toán quốc tế', 3, 0, 'S', true],
        ['BM-KTKT', 'ACC312', 'Kế toán ngân hàng', 3, 0, 'S', true],
        ['BM-KTKT', 'ACC313', 'Kế toán máy', 2, 0, 'S', false],
        ['BM-KTKT', 'ACC314', 'Thực hành kế toán máy', 1, 1, 'S', false],
        ['BM-KTKT', 'ACC315', 'Chuẩn mực kế toán Việt Nam', 2, 0, 'S', false],
        ['BM-KTKT', 'ACC316', 'Kế toán thuế', 3, 0, 'S', true],
        ['BM-KTKT', 'ACC317', 'Kiểm soát nội bộ', 3, 0, 'S', true],
        ['BM-KTKT', 'ACC318', 'Kiểm toán nội bộ', 3, 0, 'S', true],
        ['BM-KTKT', 'ACC319', 'Đạo đức nghề nghiệp kế toán – kiểm toán', 2, 0, 'S', false],
        ['BM-KTKT', 'ACC320', 'Phân tích hoạt động kinh doanh', 3, 0, 'S', false],
        ['BM-KTKT', 'ACC401', 'Thực tập tốt nghiệp ngành Kế toán', 4, 4, 'I', false],
        ['BM-KTKT', 'ACC402', 'Khóa luận tốt nghiệp ngành Kế toán', 7, 7, 'T', false],
        // Ngôn ngữ Anh
        ['BM-TA', 'ENG204', 'Ngữ pháp tiếng Anh', 3, 0, 'F', false],
        ['BM-TA', 'ENG205', 'Ngữ pháp tiếng Anh nâng cao', 3, 0, 'F', false],
        ['BM-TA', 'ENG206', 'Kỹ năng Nghe – Nói nâng cao', 3, 1, 'F', false],
        ['BM-TA', 'ENG207', 'Văn hóa Anh – Mỹ', 3, 0, 'F', false],
        ['BM-TA', 'ENG208', 'Kỹ năng Đọc – Viết nâng cao', 3, 1, 'F', false],
        ['BM-TA', 'ENG209', 'Từ vựng học tiếng Anh', 3, 0, 'F', false],
        ['BM-TA', 'ENG210', 'Ngữ nghĩa học tiếng Anh', 3, 0, 'F', false],
        ['BM-TA', 'ENG304', 'Cú pháp học tiếng Anh', 3, 0, 'S', false],
        ['BM-TA', 'ENG305', 'Văn học Anh – Mỹ', 3, 0, 'S', false],
        ['BM-TA', 'ENG306', 'Tiếng Anh du lịch – khách sạn', 3, 0, 'S', true],
        ['BM-TA', 'ENG307', 'Tiếng Anh văn phòng', 3, 0, 'S', true],
        ['BM-TA', 'ENG308', 'Kỹ năng thuyết trình tiếng Anh', 3, 1, 'S', false],
        ['BM-TA', 'ENG309', 'Biên dịch nâng cao', 3, 1, 'S', false],
        ['BM-TA', 'ENG310', 'Tiếng Anh tài chính – ngân hàng', 3, 0, 'S', true],
        ['BM-TA', 'ENG311', 'Viết học thuật tiếng Anh', 3, 0, 'S', false],
        ['BM-TA', 'ENG312', 'Phiên dịch nâng cao', 3, 1, 'S', false],
        ['BM-TA', 'ENG313', 'Tiếng Anh pháp lý', 3, 0, 'S', true],
        ['BM-TA', 'ENG314', 'Biên dịch văn bản kinh tế', 3, 1, 'S', true],
        ['BM-TA', 'ENG315', 'Tiếng Anh truyền thông – quảng cáo', 3, 0, 'S', true],
        ['BM-TA', 'ENG316', 'Phân tích diễn ngôn', 3, 0, 'S', false],
        ['BM-TA', 'ENG317', 'Chuyên đề biên – phiên dịch', 3, 1, 'S', false],
        // Ngôn ngữ Trung Quốc
        ['BM-TT', 'CHN103', 'Tiếng Trung tổng hợp 3', 4, 1, 'F', false],
        ['BM-TT', 'CHN104', 'Tiếng Trung tổng hợp 4', 4, 1, 'F', false],
        ['BM-TT', 'CHN105', 'Tiếng Trung tổng hợp 5', 3, 0, 'F', false],
        ['BM-TT', 'CHN202', 'Ngữ âm tiếng Trung', 2, 0, 'F', false],
        ['BM-TT', 'CHN203', 'Nghe – Nói tiếng Trung 1', 2, 1, 'F', false],
        ['BM-TT', 'CHN204', 'Nghe – Nói tiếng Trung 2', 2, 1, 'F', false],
        ['BM-TT', 'CHN205', 'Đọc – Viết tiếng Trung', 3, 1, 'F', false],
        ['BM-TT', 'CHN206', 'Ngữ pháp tiếng Trung hiện đại', 3, 0, 'F', false],
        ['BM-TT', 'CHN207', 'Văn hóa Trung Quốc', 2, 0, 'F', false],
        ['BM-TT', 'CHN208', 'Đất nước học Trung Quốc', 2, 0, 'F', false],
        ['BM-TT', 'CHN209', 'Từ vựng học tiếng Trung', 3, 0, 'F', false],
        ['BM-TT', 'CHN302', 'Tiếng Trung thương mại', 3, 0, 'S', false],
        ['BM-TT', 'CHN303', 'Tiếng Trung du lịch', 3, 0, 'S', true],
        ['BM-TT', 'CHN304', 'Văn học Trung Quốc', 3, 0, 'S', false],
        ['BM-TT', 'CHN305', 'Tiếng Trung văn phòng', 3, 0, 'S', true],
        ['BM-TT', 'CHN306', 'Biên dịch tiếng Trung nâng cao', 3, 1, 'S', false],
        ['BM-TT', 'CHN307', 'Phiên dịch tiếng Trung nâng cao', 3, 1, 'S', false],
        ['BM-TT', 'CHN308', 'Tiếng Trung xuất nhập khẩu', 3, 0, 'S', true],
        ['BM-TT', 'CHN309', 'Tiếng Trung tài chính – ngân hàng', 3, 0, 'S', true],
        ['BM-TT', 'CHN310', 'Luyện thi HSK', 3, 0, 'S', false],
        ['BM-TT', 'CHN311', 'Tiếng Trung khoa học – kỹ thuật', 3, 0, 'S', true],
        ['BM-TT', 'CHN312', 'Biên dịch văn bản hành chính – pháp luật', 3, 1, 'S', false],
        ['BM-TT', 'CHN313', 'Viết học thuật tiếng Trung', 3, 0, 'S', false],
        ['BM-TT', 'CHN401', 'Thực tập tốt nghiệp ngành Ngôn ngữ Trung Quốc', 4, 4, 'I', false],
        ['BM-TT', 'CHN402', 'Khóa luận tốt nghiệp ngành Ngôn ngữ Trung Quốc', 7, 7, 'T', false],
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
