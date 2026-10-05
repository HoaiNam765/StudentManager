# Danh sách màn hình cần thiết kế

Nguồn: `docs/BA.md` mục 8.2. Màn hình được chia theo giai đoạn phát hành (BA mục 5.6): **P1** làm trước, **P2 trở đi** để sau. Tick `[x]` khi wireframe hoặc mockup của màn hình đã được duyệt.

## Khung dùng chung (làm đầu tiên)

- [ ] Bảng màu, phông chữ, khoảng cách
- [ ] Thành phần dùng chung: nút, ô nhập, bảng dữ liệu, nhãn trạng thái, hộp thoại xác nhận, thông báo, trạng thái đang tải / rỗng / lỗi
- [ ] Khung Cổng Sinh viên (ưu tiên điện thoại)
- [ ] Khung Cổng Giảng viên
- [ ] Khung Cổng Quản trị / Văn phòng

## Trang công khai

| Xong | Màn hình | Module | Giai đoạn |
|---|---|---|---|
| [ ] | Đăng nhập | AUTH | P1 |
| [ ] | Quên mật khẩu, đặt lại mật khẩu | AUTH | P1 |
| [ ] | Xác thực văn bản bằng mã QR | DOC | P2 |

## Cổng Sinh viên

| Xong | Màn hình | Module | Giai đoạn |
|---|---|---|---|
| [ ] | Trang chủ (dashboard cá nhân) | RPT | P1 |
| [ ] | Hồ sơ cá nhân và yêu cầu chỉnh sửa | STU | P1 |
| [ ] | Đăng ký học phần (chọn lớp, thời khóa biểu tạm, cảnh báo trùng lịch, tổng tín chỉ) | ENR | P1 |
| [ ] | Kết quả đăng ký và phiếu đăng ký | ENR | P1 |
| [ ] | Thời khóa biểu (tuần, học kỳ) | TTB | P1 |
| [ ] | Kết quả học tập (điểm, GPA, bảng điểm) | GRD | P1 |
| [ ] | Chương trình đào tạo và tiến độ học tập | CUR | P1 (phần tiến độ ở mức Should, FR-CUR-008) |
| [ ] | Thông báo | NOT | P1 |
| [ ] | Cài đặt tài khoản (mật khẩu, phiên đăng nhập, tùy chọn thông báo) | AUTH, NOT | P1 |
| [ ] | Lịch thi | EXM | P2 |
| [ ] | Chuyên cần | ATT | P2 |
| [ ] | Học phí và thanh toán | FEE | P2 |
| [ ] | Hồ sơ và giấy tờ | DOC | P2 |
| [ ] | Đơn từ (gửi, theo dõi tiến độ) | REQ | P3 |
| [ ] | Học bổng và điểm rèn luyện | SCH | P4 |
| [ ] | Khảo sát chất lượng | EVA | P4 |

## Cổng Giảng viên

| Xong | Màn hình | Module | Giai đoạn |
|---|---|---|---|
| [ ] | Trang chủ (lịch hôm nay, việc cần làm) | TCH, RPT | P1 |
| [ ] | Lịch dạy | TTB | P1 |
| [ ] | Lớp học phần đang dạy và danh sách sinh viên | CLS, TCH | P1 |
| [ ] | Nhập điểm và nộp điểm | GRD | P1 |
| [ ] | Gửi thông báo cho lớp | NOT | P1 |
| [ ] | Đề cương học phần | SUB | P1 |
| [ ] | Hồ sơ cá nhân và lịch bận | TCH | P1 |
| [ ] | Lịch coi thi | EXM | P2 |
| [ ] | Điểm danh | ATT | P2 |
| [ ] | Lớp cố vấn học tập | TCH, GRA | P3 |
| [ ] | Duyệt đơn | REQ | P3 |
| [ ] | Kết quả khảo sát | EVA | P4 |

## Cổng Quản trị / Văn phòng

| Xong | Nhóm màn hình | Module | Giai đoạn |
|---|---|---|---|
| [ ] | Dashboard theo vai trò | RPT | P1 |
| [ ] | Danh mục: khoa – bộ môn – ngành; năm học – học kỳ; phòng học; học phần; chương trình đào tạo | FAC, ACY, ROM, SUB, CUR | P1 |
| [ ] | Sinh viên (danh sách, hồ sơ, import, trạng thái); giảng viên | STU, TCH | P1 |
| [ ] | Khóa, lớp hành chính; mở lớp học phần | CLS | P1 |
| [ ] | Xếp thời khóa biểu; quản lý đăng ký học phần (đợt, ngoại lệ, chốt) | TTB, ENR | P1 |
| [ ] | Quản lý điểm | GRD | P1 |
| [ ] | Thông báo (soạn, mẫu, lịch sử) | NOT | P1 |
| [ ] | Quản trị hệ thống (người dùng, vai trò – quyền, cấu hình, nhật ký) | AUTH, SYS | P1 |
| [ ] | Điểm danh và chuyên cần; kỳ thi (lịch, phòng, giám thị) | ATT, EXM | P2 |
| [ ] | Học phí (khoản thu, hóa đơn, thanh toán, công nợ) | FEE | P2 |
| [ ] | Hồ sơ và giấy tờ | DOC | P2 |
| [ ] | Đơn từ (hộp thư duyệt); học vụ và tốt nghiệp; báo cáo đầy đủ | REQ, GRA, RPT | P3 |
| [ ] | Học bổng – rèn luyện – kỷ luật; khảo sát | SCH, EVA | P4 |

Quản trị hệ thống ở P1 chỉ gồm phần cơ bản (cấu hình, nhật ký); sao lưu và import đầy đủ làm ở P3 (BA mục 5.6).
