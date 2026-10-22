<?php

namespace App\Modules\Notification\Support;

/**
 * Danh mục sự kiện thông báo theo BA phụ lục A (mã, tên, module nguồn, người nhận, kênh, bắt buộc) kèm mẫu mặc định.
 * Seeder `NotificationEventSeeder` nạp vào `event_subscriptions`; ADMIN sửa mẫu / bật tắt sau đó không bị seeder ghi đè.
 *
 * Mẫu dùng biến `{{ten_bien}}`. Theo BR-NOT-03, mẫu **không** chứa điểm, số nợ, CCCD — chỉ nhắc và liên kết yêu cầu đăng
 * nhập. `wired` = module nguồn đã phát sự kiện ở P1; sự kiện chưa có module (FEE, EXM, ATT…) vẫn nạp sẵn cấu hình.
 * Kênh: `in_app` (trong ứng dụng), `email`. P1 chỉ gửi `in_app`; email làm ở P2 (FR-NOT-002).
 */
final class EventCatalog
{
    /** @var array<string, array{name: string, module: string, recipients: string, channels: list<string>, mandatory: bool, category: string, title: string, body: string, link?: ?string, wired?: bool}> */
    public const EVENTS = [
        'EV-AUTH-01' => ['name' => 'Yêu cầu đặt lại mật khẩu', 'module' => 'AUTH', 'recipients' => 'Chủ tài khoản', 'channels' => ['email'], 'mandatory' => true, 'category' => 'account',
            'title' => 'Yêu cầu đặt lại mật khẩu', 'body' => 'Có yêu cầu đặt lại mật khẩu cho tài khoản của bạn.'],
        'EV-AUTH-02' => ['name' => 'Tài khoản bị khóa tạm hoặc có đăng nhập bất thường', 'module' => 'AUTH', 'recipients' => 'Chủ tài khoản, ADMIN', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'account', 'wired' => true,
            'title' => 'Cảnh báo bảo mật tài khoản', 'body' => '{{noi_dung}} Nếu không phải bạn, hãy đổi mật khẩu và báo Phòng Đào tạo.', 'link' => '/tai-khoan/phien-dang-nhap'],
        'EV-AUTH-03' => ['name' => 'Thay đổi vai trò hoặc quyền', 'module' => 'AUTH', 'recipients' => 'Người bị ảnh hưởng', 'channels' => ['in_app'], 'mandatory' => true, 'category' => 'account', 'wired' => true,
            'title' => 'Vai trò tài khoản đã thay đổi', 'body' => 'Tài khoản của bạn vừa được {{thay_doi}} vai trò "{{vai_tro}}". Đăng nhập lại nếu không thấy chức năng mới.', 'link' => '/tai-khoan/cai-dat'],
        'EV-STU-01' => ['name' => 'Xác nhận nhập học, cấp MSSV và tài khoản', 'module' => 'STU', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic', 'wired' => true,
            'title' => 'Chào mừng tân sinh viên', 'body' => 'Bạn đã được xác nhận nhập học với MSSV {{mssv}}. Hãy kiểm tra hồ sơ và thời khóa biểu trên cổng sinh viên.', 'link' => '/student'],
        'EV-STU-02' => ['name' => 'Thay đổi trạng thái sinh viên (bảo lưu, thôi học…)', 'module' => 'STU', 'recipients' => 'STU, ADV', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic', 'wired' => true,
            'title' => 'Thay đổi trạng thái học tập', 'body' => 'Trạng thái học tập của sinh viên {{ho_ten}} ({{mssv}}) đã chuyển sang "{{trang_thai}}" theo quyết định {{so_quyet_dinh}}. Đăng nhập để xem chi tiết.'],
        'EV-STU-03' => ['name' => 'Kết quả duyệt yêu cầu chỉnh sửa hồ sơ', 'module' => 'STU', 'recipients' => 'STU', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'academic', 'wired' => true,
            'title' => 'Kết quả yêu cầu sửa hồ sơ', 'body' => 'Yêu cầu sửa thông tin hồ sơ của bạn đã được {{ket_qua}}. Đăng nhập để xem chi tiết.', 'link' => '/student'],
        'EV-ENR-01' => ['name' => 'Mở hoặc đóng đợt đăng ký học phần', 'module' => 'ENR', 'recipients' => 'STU thuộc đối tượng', 'channels' => ['in_app', 'email'], 'mandatory' => false, 'category' => 'enrollment',
            'title' => 'Đợt đăng ký học phần', 'body' => '{{noi_dung}}', 'link' => '/student/dang-ky-hoc-phan'],
        'EV-ENR-02' => ['name' => 'Xác nhận đăng ký, hủy hoặc rút học phần', 'module' => 'ENR', 'recipients' => 'STU', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'enrollment', 'wired' => true,
            'title' => 'Đã {{thao_tac}} học phần {{hoc_phan}}', 'body' => 'Bạn đã {{thao_tac}} học phần {{hoc_phan}}, lớp {{lop}}. Xem lại kết quả đăng ký nếu không phải bạn thực hiện.', 'link' => '/student/ket-qua-dang-ky'],
        'EV-ENR-03' => ['name' => 'Lớp học phần bị hủy hoặc thay đổi quan trọng', 'module' => 'CLS', 'recipients' => 'STU đã đăng ký, LEC', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'enrollment', 'wired' => true,
            'title' => 'Lớp {{lop}} bị hủy', 'body' => 'Lớp học phần {{lop}} ({{hoc_phan}}) đã bị hủy. Lý do: {{ly_do}}. Sinh viên đăng ký lớp khác hoặc liên hệ Phòng Đào tạo.', 'link' => '/student/ket-qua-dang-ky'],
        'EV-ENR-04' => ['name' => 'Có chỗ trống (danh sách chờ)', 'module' => 'ENR', 'recipients' => 'STU trong danh sách chờ', 'channels' => ['in_app', 'email'], 'mandatory' => false, 'category' => 'enrollment',
            'title' => 'Lớp {{lop}} có chỗ trống', 'body' => 'Lớp học phần {{lop}} vừa có chỗ trống. Đăng nhập để đăng ký.', 'link' => '/student/dang-ky-hoc-phan'],
        'EV-TTB-01' => ['name' => 'Công bố thời khóa biểu', 'module' => 'TTB', 'recipients' => 'STU, LEC', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'timetable', 'wired' => true,
            'title' => 'Đã công bố thời khóa biểu {{hoc_ky}}', 'body' => 'Thời khóa biểu {{hoc_ky}} đã được công bố. Đăng nhập để xem lịch học của bạn.'],
        'EV-TTB-02' => ['name' => 'Thay đổi lịch học, phòng, nghỉ, dạy bù', 'module' => 'TTB', 'recipients' => 'STU, LEC của lớp', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'timetable', 'wired' => true,
            'title' => 'Lịch học lớp {{lop}} thay đổi', 'body' => 'Lịch học lớp {{lop}} ({{hoc_phan}}) vừa thay đổi. Lý do: {{ly_do}}. Đăng nhập để xem lịch mới.'],
        'EV-TTB-03' => ['name' => 'Nhắc lịch học ngày mai', 'module' => 'TTB', 'recipients' => 'STU, LEC', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'timetable',
            'title' => 'Lịch học ngày mai', 'body' => '{{noi_dung}}'],
        'EV-ATT-01' => ['name' => 'Vắng chạm ngưỡng cảnh báo', 'module' => 'ATT', 'recipients' => 'STU, ADV', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Cảnh báo vắng học', 'body' => '{{noi_dung}}'],
        'EV-ATT-02' => ['name' => 'Vắng vượt ngưỡng cấm thi', 'module' => 'ATT', 'recipients' => 'STU, ADV, ACAD', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Vắng vượt ngưỡng cấm thi', 'body' => '{{noi_dung}}'],
        'EV-GRD-01' => ['name' => 'Điểm được công bố (không kèm điểm trong nội dung)', 'module' => 'GRD', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => false, 'category' => 'grade', 'wired' => true,
            'title' => 'Đã có điểm học phần {{hoc_phan}}', 'body' => 'Điểm học phần {{hoc_phan}} (lớp {{lop}}) đã được công bố. Đăng nhập để xem kết quả.', 'link' => '/student/ket-qua-hoc-tap'],
        'EV-GRD-02' => ['name' => 'Điểm được điều chỉnh sau công bố', 'module' => 'GRD', 'recipients' => 'STU, LEC', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'grade', 'wired' => true,
            'title' => 'Điểm học phần {{hoc_phan}} đã được điều chỉnh', 'body' => 'Điểm học phần {{hoc_phan}} (lớp {{lop}}) của sinh viên {{mssv}} đã được điều chỉnh theo yêu cầu được duyệt. Đăng nhập để xem.', 'link' => '/student/ket-qua-hoc-tap'],
        'EV-GRD-03' => ['name' => 'Nhắc hạn nhập hoặc nộp điểm', 'module' => 'GRD', 'recipients' => 'LEC', 'channels' => ['in_app', 'email'], 'mandatory' => false, 'category' => 'grade',
            'title' => 'Nhắc hạn nộp điểm', 'body' => '{{noi_dung}}', 'link' => '/teacher'],
        'EV-GRD-04' => ['name' => 'Kết quả phúc khảo', 'module' => 'GRD', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'grade',
            'title' => 'Kết quả phúc khảo', 'body' => 'Đã có kết quả phúc khảo học phần {{hoc_phan}}. Đăng nhập để xem.', 'link' => '/student/ket-qua-hoc-tap'],
        'EV-FEE-01' => ['name' => 'Phát hành thông báo học phí', 'module' => 'FEE', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Thông báo học phí {{hoc_ky}}', 'body' => 'Đã có thông báo học phí {{hoc_ky}}. Đăng nhập để xem số tiền và hạn nộp.'],
        'EV-FEE-02' => ['name' => 'Xác nhận thanh toán và biên lai', 'module' => 'FEE', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => false, 'category' => 'academic',
            'title' => 'Đã ghi nhận thanh toán', 'body' => 'Khoản thanh toán học phí của bạn đã được ghi nhận. Đăng nhập để xem biên lai.'],
        'EV-FEE-03' => ['name' => 'Nhắc hạn nộp hoặc quá hạn', 'module' => 'FEE', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Nhắc hạn nộp học phí', 'body' => 'Học phí {{hoc_ky}} sắp đến hạn hoặc đã quá hạn. Đăng nhập để xem chi tiết.'],
        'EV-FEE-04' => ['name' => 'Hoàn tiền hoặc điều chỉnh công nợ', 'module' => 'FEE', 'recipients' => 'STU', 'channels' => ['in_app'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Điều chỉnh học phí', 'body' => 'Công nợ học phí của bạn vừa được điều chỉnh. Đăng nhập để xem chi tiết.'],
        'EV-EXM-01' => ['name' => 'Công bố lịch thi và danh sách phòng thi', 'module' => 'EXM', 'recipients' => 'STU, giám thị', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'timetable',
            'title' => 'Đã công bố lịch thi', 'body' => 'Lịch thi {{hoc_ky}} đã được công bố. Đăng nhập để xem.'],
        'EV-EXM-02' => ['name' => 'Không đủ điều kiện dự thi', 'module' => 'EXM', 'recipients' => 'STU, ADV', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Không đủ điều kiện dự thi', 'body' => '{{noi_dung}}'],
        'EV-EXM-03' => ['name' => 'Nhắc lịch thi hoặc lịch coi thi', 'module' => 'EXM', 'recipients' => 'STU, giám thị', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'timetable',
            'title' => 'Nhắc lịch thi', 'body' => '{{noi_dung}}'],
        'EV-EXM-04' => ['name' => 'Thay đổi lịch thi', 'module' => 'EXM', 'recipients' => 'STU, giám thị', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'timetable',
            'title' => 'Lịch thi thay đổi', 'body' => '{{noi_dung}}'],
        'EV-DOC-01' => ['name' => 'Hồ sơ thiếu, không hợp lệ hoặc cần bổ sung', 'module' => 'DOC', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Hồ sơ cần bổ sung', 'body' => '{{noi_dung}}'],
        'EV-DOC-02' => ['name' => 'Giấy tờ đã sẵn sàng', 'module' => 'DOC', 'recipients' => 'STU', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'academic',
            'title' => 'Giấy tờ đã sẵn sàng', 'body' => '{{noi_dung}}'],
        'EV-REQ-01' => ['name' => 'Đơn đã được tiếp nhận', 'module' => 'REQ', 'recipients' => 'STU', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'academic',
            'title' => 'Đơn đã được tiếp nhận', 'body' => '{{noi_dung}}'],
        'EV-REQ-02' => ['name' => 'Có đơn chờ duyệt hoặc sắp quá SLA', 'module' => 'REQ', 'recipients' => 'Người duyệt', 'channels' => ['in_app', 'email'], 'mandatory' => false, 'category' => 'academic',
            'title' => 'Có đơn chờ duyệt', 'body' => '{{noi_dung}}'],
        'EV-REQ-03' => ['name' => 'Kết quả xử lý đơn', 'module' => 'REQ', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Kết quả xử lý đơn', 'body' => '{{noi_dung}}'],
        'EV-GRA-01' => ['name' => 'Cảnh báo học vụ', 'module' => 'GRA', 'recipients' => 'STU, ADV, DEAN', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Cảnh báo học vụ', 'body' => '{{noi_dung}}'],
        'EV-GRA-02' => ['name' => 'Kết quả xét tốt nghiệp', 'module' => 'GRA', 'recipients' => 'STU', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'academic',
            'title' => 'Kết quả xét tốt nghiệp', 'body' => '{{noi_dung}}'],
        'EV-ACY-01' => ['name' => 'Mốc học vụ sắp đến', 'module' => 'ACY', 'recipients' => 'Cán bộ liên quan', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'academic',
            'title' => 'Mốc học vụ sắp đến', 'body' => '{{noi_dung}}'],
        'EV-SYS-01' => ['name' => 'Lỗi nghiêm trọng hoặc sao lưu thất bại', 'module' => 'SYS', 'recipients' => 'ADMIN', 'channels' => ['in_app', 'email'], 'mandatory' => true, 'category' => 'account',
            'title' => 'Sự cố hệ thống', 'body' => '{{noi_dung}}'],
        'EV-SCH-01' => ['name' => 'Kết quả xét học bổng, điểm rèn luyện', 'module' => 'SCH', 'recipients' => 'STU', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'academic',
            'title' => 'Kết quả xét học bổng, rèn luyện', 'body' => '{{noi_dung}}'],
        'EV-EVA-01' => ['name' => 'Mở đợt khảo sát hoặc nhắc hoàn thành', 'module' => 'EVA', 'recipients' => 'STU', 'channels' => ['in_app'], 'mandatory' => false, 'category' => 'academic',
            'title' => 'Khảo sát đang mở', 'body' => '{{noi_dung}}'],
    ];
}
