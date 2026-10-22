# Nghiệm thu P1 (UAT-01 → UAT-08)

Kịch bản theo BA mục 11.2. Cách chạy lại trên máy:

1. Tạo CSDL tạm (ví dụ `sm_check123`), đặt `DB_DATABASE` trong `.env` của bản sao làm việc.
2. `php artisan migrate:fresh --seed` rồi `php artisan demo:seed` (khoảng 4 phút).
3. `php artisan uat:p1` — chạy mọi kịch bản trong một giao dịch rồi hoàn tác, in kết quả và ghi báo cáo vào `storage/logs/uat-p1-*.md`.

Phạm vi: lệnh gọi trực tiếp các dịch vụ nghiệp vụ (đăng nhập, phân quyền, danh mục, sinh viên, lớp, thời khóa biểu, đăng ký, điểm). Phần "100 yêu cầu đồng thời vào một chỗ" của UAT-06 cần nhiều tiến trình nên không chạy trong lệnh; đã đo khi làm #105 (12 tiến trình song song, chỉ 1 đăng ký thành công).

## Kết quả lần chạy gần nhất

Chạy lúc 23:41 21/10/2026 trên CSDL `sm_check270` bằng `php artisan uat:p1` (mọi thay đổi đã hoàn tác).

| UAT | Kiểm tra | Kết quả | Chi tiết |
|---|---|---|---|
| UAT-01 | Khóa tạm sau 5 lần nhập sai mật khẩu | ĐẠT | Sau 5 lần sai, khóa tới 23:56; mật khẩu đúng cũng bị từ chối khi đang khóa. |
| UAT-01 | Liên kết đặt lại mật khẩu dùng một lần, hết hạn sau 30 phút | ĐẠT | Lần 1 đặt lại được, lần 2 báo liên kết không hợp lệ; sau 31 phút liên kết hết hạn. |
| UAT-02 | Giảng viên chỉ thấy lớp mình dạy | ĐẠT | GV0001 thấy 8 / 93 lớp học phần (đúng các lớp được phân công). |
| UAT-02 | Sinh viên không xem được hồ sơ sinh viên khác | ĐẠT | 2602010001 xem được hồ sơ mình, không xem được 2602010002. |
| UAT-02 | Truy cập trái quyền bị từ chối và ghi nhật ký | ĐẠT | Sinh viên gọi API duyệt điều chỉnh điểm: 403, có nhật ký "Từ chối truy cập". |
| UAT-03 | Chặn vòng lặp tiên quyết | ĐẠT | BUS202 ← BUS302 ← BUS401, thêm BUS401 ← BUS202: Không thêm được: BUS202 là Tiên quyết của BUS401 sẽ tạo vòng lặp BUS401 → BUS202 → BUS302 → BUS401. |
| UAT-03 | Tổng trọng số điểm phải bằng 100% | ĐẠT | 10 + 20 + 60 = 90%: Tổng trọng số các thành phần điểm là 90%, phải đúng 100% (thiếu 10%). |
| UAT-03 | CTĐT thiếu tín chỉ không ban hành được | ĐẠT | CTĐT rỗng yêu cầu 130 tín chỉ: Chưa ban hành được CTĐT CTDT-UAT-7220201: CTĐT chưa có khối kiến thức nào. |
| UAT-04 | Cấp MSSV đúng quy tắc khi xác nhận nhập học | ĐẠT | MSSV 2602010051 = khóa 26 + 4 số cuối mã ngành 0201 + số thứ tự; có tài khoản, Đang học. |
| UAT-04 | Import hồ sơ báo lỗi theo dòng – cột | ĐẠT | Dòng 2 hợp lệ; dòng 3 lỗi ở cột: date_of_birth, major_code. |
| UAT-04 | Bảo lưu hủy các đăng ký chưa chốt | ĐẠT | 2602010051 bảo lưu: đăng ký lớp ACC201.262.01 (chưa chốt) chuyển sang Đã hủy. |
| UAT-05 | Chặn mở đăng ký lớp chưa có giảng viên chính | ĐẠT | Lớp ACC301.UAT.1: Lớp ACC301.UAT.1 chưa có giảng viên chính nên chưa mở đăng ký được. |
| UAT-05 | Phát hiện trùng phòng, trùng giảng viên | ĐẠT | Phòng A101 đã có lớp ACC201.262.01 (Thứ Hai tiết 1–3, tuần 1–15). Giảng viên Dương Văn Nam (GV0012) đang dạy lớp ACC201.262.01 (Thứ Hai tiết 1–3, tuần 1–15). |
| UAT-05 | Phòng không đủ sức chứa | ĐẠT | Phòng B201 (30 chỗ), lớp tối đa 50: Phòng B201 chứa 30 chỗ, ít hơn sĩ số tối đa 50 của lớp (BR-TTB-02). |
| UAT-06 | Chặn thiếu học phần tiên quyết | ĐẠT | CHN102.262.01: Thiếu học phần tiên quyết: Tiếng Trung tổng hợp 1 (CHN101). Phải đạt các học phần này trước (BR-ENR-03). |
| UAT-06 | Chặn lớp hết chỗ | ĐẠT | ACC201.262.01 (tối đa 1, đã có 1): Lớp ACC201.262.01 đã đủ 1 sinh viên. Chọn lớp khác của học phần. |
| UAT-06 | Chặn trùng lịch | ĐẠT | Đã đăng ký BUS201.262.01, chọn CHN102.262.01 cùng giờ: Trùng lịch: CHN102.262.01 (Thứ Ba tiết 6–8, tuần 1–15) trùng giờ với BUS201.262.01 (Thứ Ba tiết 6–8, tuần 1–15). Chọn lớp khác của học phần hoặc hủy lớp đang trùng. \| Thiếu học phần tiên quyết: Tiếng Trung tổng hợp 1 (CHN101). Phải đạt các học phần này trước (BR-ENR-03). |
| UAT-06 | Chặn vượt số tín chỉ tối đa | ĐẠT | Tối đa 9 tín chỉ: Thiếu học phần tiên quyết: Nhập môn lập trình (INT101). Phải đạt các học phần này trước (BR-ENR-03). \| Vượt khối lượng tối đa: sau khi đăng ký là 15 tín chỉ, tối đa 9 tín chỉ (khung thường theo quy chế khóa). Hủy bớt học phần khác trước. |
| UAT-06 | 100 yêu cầu đồng thời vào 1 chỗ cuối | BỎ QUA | Cần nhiều tiến trình song song, không chạy trong một giao dịch; đã đo ở #105 (12 tiến trình, chỉ 1 thành công). |
| UAT-07 | Điểm tổng kết khớp ví dụ BA (9,0 / 7,0 / 6,5 × 10 / 30 / 60 → 6,9 C 2,0) | ĐẠT | Lớp BUS301.UAT.4: 6,9 / C / 2,0 / Đạt. |
| UAT-07 | Sinh viên chỉ thấy điểm đã công bố | ĐẠT | Trước khi công bố, bảng điểm cá nhân trống. |
| UAT-07 | GPA học kỳ 2,50 (B 3 tín chỉ + C 3 tín chỉ), công bố tạo thông báo không kèm điểm | ĐẠT | GPA học kỳ 2.50 (Khá); 2 thông báo công bố, nội dung không có điểm. |
| UAT-08 | Không sửa điểm trực tiếp sau công bố | ĐẠT | Bảng điểm lớp đang ở trạng thái "Đã công bố", không sửa điểm trực tiếp được. |
| UAT-08 | Yêu cầu → duyệt → phiên bản và lịch sử | ĐẠT | Phiên bản 1: 6.90 → 7.50; có thông báo EV-GRD-02. |
