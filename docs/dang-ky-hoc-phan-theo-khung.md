# Đăng ký học phần theo chương trình khung

## Quy trình của Phòng Đào tạo

1. Vào **Chương trình khung**, chọn khoa – khóa – học kỳ, thêm hoặc sửa danh sách môn bắt buộc / tự chọn. Trang này chỉ quản lý chương trình; không mở lớp hay gửi thông báo.
2. Vào **Đăng ký học phần**, chọn học kỳ, tạo đợt gắn với chương trình khung tương ứng, nhập giờ mở và đóng theo giờ Việt Nam.
3. Bấm **Học phần / giảng viên / lịch**, thêm môn từ khung hoặc bổ sung môn khác. Nhập phòng, thứ, tiết, tuần học, sĩ số rồi chọn giảng viên. Danh sách chỉ gồm giảng viên đang công tác trong khoa; giảng viên trùng lịch không chọn được. Máy chủ kiểm tra lại khi lưu.
4. Có thể sửa hoặc bỏ lớp khi đợt còn nháp. Các thay đổi này không xóa môn trong chương trình khung. Muốn thay môn của một lớp dự kiến, bỏ lớp rồi thêm môn mới. Lớp đã có nhiều lịch / nhiều giảng viên được chỉnh tại trang thời khóa biểu / lớp học phần để giữ các phân công khác.
5. Bấm **Thông báo xem trước**. Sinh viên thuộc khoa và khóa nhận thông báo; mở thông báo sẽ tới đúng đợt, xem được môn, giảng viên, lịch học nhưng chưa đăng ký được.
6. Bấm **Công bố**. Đợt chuyển sang chờ giờ mở; đúng giờ máy chủ cho phép đăng ký, không cần chạy tác vụ hẹn giờ. Phòng Đào tạo có thể bấm **Mở ngay (sớm hơn lịch)** trước giờ đã hẹn. Khi sinh viên đang mở trang, bấm **Làm mới** để cập nhật trạng thái.

## Ràng buộc

- Đợt và chương trình khung phải cùng học kỳ. Sinh viên phải đúng khoa và khóa; các đối tượng bổ sung của đợt thu hẹp phạm vi này.
- Công bố cần danh sách đã thông báo xem trước, có giảng viên và lịch học hợp lệ. Ngày học phải sau ngày đóng đăng ký.
- Môn đã đạt bị chặn trong đăng ký thường. Cổng sinh viên chưa hỗ trợ đăng ký học cải thiện.
- Lưu lớp, giảng viên và lịch học trong một giao dịch; lỗi sẽ hoàn tác toàn bộ. Xóa lớp khỏi đợt dùng trạng thái hủy để giữ lịch sử.
- Đợt / lớp cũ chưa gắn chương trình khung tiếp tục dùng luồng cũ, không tự chuyển dữ liệu.

## Kiểm tra

`RegistrationOfferingWorkflowTest` kiểm tra qua HTTP: nháp → xem trước → công bố → mở sớm, mở đúng giờ / đóng đúng giờ, phạm vi khoa – khóa, quyền ghi, thông báo, giảng viên ngoài khoa / trùng lịch, hoàn tác khi lỗi, sửa / hủy lớp, và chặn môn đã đạt / học cải thiện. Chạy bằng cơ sở dữ liệu kiểm thử riêng, không dùng dữ liệu đang vận hành.
