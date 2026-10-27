# Kịch bản demo — Hệ thống Quản lý Sinh viên

Thời lượng khoảng 30 phút. Đi theo đúng vòng đời một học kỳ: Phòng Đào tạo chuẩn bị → lập chương trình khung, mở lớp, thông báo → xếp lịch, mở đăng ký → sinh viên đăng ký → giảng viên dạy, điểm danh, nhập điểm → sinh viên xem kết quả → nghiệm thu tự động.

Số liệu trong kịch bản lấy từ bộ dữ liệu mẫu (`demo:seed`), đã chạy thử thật ngày 27/10/2026. Dữ liệu mẫu cố định (họ tên, điểm, phân công giảng viên giống nhau mỗi lần dựng lại); riêng thời gian đợt đăng ký tính theo ngày dựng.

---

## 0. Chuẩn bị (trước buổi demo 15 phút)

1. Laragon: **Start All** (bật MySQL).
2. Dựng lại dữ liệu mẫu sạch, khoảng 5 phút, **xóa hết dữ liệu cũ của `student_manager`**:
   ```powershell
   cd C:\laragon\www\StudentManager; php artisan migrate:fresh --seed; php artisan demo:seed; npm.cmd run build; php artisan serve
   ```
3. Mở http://127.0.0.1:8000/login.
4. Mở **hai trình duyệt** (hoặc một cửa sổ thường + một cửa sổ ẩn danh) để cùng lúc đăng nhập cán bộ và sinh viên. Hai tab cùng trình duyệt dùng chung phiên đăng nhập.
5. **Điền sẵn điểm thi cho lớp INT201** (khoảng 3 phút, để phần H demo được trọn luồng duyệt điểm): đăng nhập `GV0001` → **Nhập điểm** (`/teacher/nhap-diem`, mặc định mở lớp `INT201.261.01`) → cột **Thi cuối kỳ** còn 50 ô trống (dữ liệu mẫu đang giữa học kỳ) → gõ điểm, phím Tab để sang ô tiếp → **Lưu nháp điểm** → đăng xuất. Chưa bấm "Nộp điểm".

### Tài khoản (mật khẩu đều là `password`)

| Vai trò | Đăng nhập | Dùng ở phần |
|---|---|---|
| Quản trị hệ thống | `admin` | A, J |
| Phòng Đào tạo | `acad.test` | B, C, D, H, I |
| Trưởng khoa CNTT | `dean.test` | H |
| Giảng viên | `GV0001` (Nguyễn Văn An) | F |
| Sinh viên khóa 2026, ngành Kỹ thuật phần mềm (khoa CNTT) | `2601030001` (Hoàng Văn Mai) | E |
| Sinh viên khóa 2026, ngành Ngôn ngữ Anh | `2602010001` (Nguyễn Văn An) | E |
| Sinh viên khóa 2025, học lực Xuất sắc (GPA 3,94) | `2501010010` (Dương Như Tiên) | G |
| Sinh viên khóa 2025, học lực Kém (bị cảnh báo học vụ) | `2501010001` (Mang Quang Hồng) | G (tùy chọn) |
| Sinh viên lớp INT201.261.01 của GV0001 | `2501030001` (Nhâm Bích Phong) | F, H |
| Cố vấn học tập | `adv.test` (hồ sơ GV9001) | F (tùy chọn) |

### Dữ liệu mẫu có sẵn
- 300 sinh viên (khóa 2025, 2026), 20 giảng viên, 60 học phần, 6 CTĐT, 93 lớp học phần, 4.165 lượt đăng ký, khoảng 14.000 lượt điểm danh.
- Năm học 2025–2026: hai học kỳ **đã khóa**, có điểm, GPA.
- HK1 2026–2027 (01/09/2026 – 10/01/2027): **đang học**.
- HK2 2026–2027 (bắt đầu 25/01/2027): đợt đăng ký vòng 1 cho khóa 2026 **đang mở tới 25/11/2026**.

---

## A. Đăng nhập và phân quyền (2 phút) — `admin`

1. Mở trang đăng nhập: giới thiệu đăng nhập bằng tên đăng nhập hoặc email, "Quên mật khẩu".
2. Đăng nhập `admin` → vào **cổng quản trị**. Nói: mỗi vai trò vào đúng cổng của mình (cán bộ → `/admin`, giảng viên → `/teacher`, sinh viên → `/student`), menu chỉ hiện chức năng có quyền.
3. **Vận hành & Khóa sổ** (`/admin/van-hanh-he-thong`): nhập dữ liệu từ Excel (chọn loại → Tải lên), kết thúc học kỳ, sao lưu.
4. Ý để nói: 9 vai trò, ma trận quyền theo BA mục 4.2, phạm vi dữ liệu (trưởng khoa chỉ thấy khoa mình, giảng viên chỉ thấy lớp mình dạy). Truy cập trái quyền bị chặn (403) và ghi nhật ký kiểm toán.

## B. Phòng Đào tạo chuẩn bị học kỳ (5 phút) — `acad.test`

1. **Tổng quan** (`/admin`): số liệu thật — sinh viên theo trạng thái, sĩ số lớp, tiến độ nhập điểm.
2. **Năm học & Lịch học vụ** (`/admin/nam-hoc`): năm học, học kỳ, các mốc (mở / đóng đăng ký, hạn hủy, hạn rút), ngày nghỉ lễ.
3. **Học phần** (`/admin/hoc-phan`): lọc theo khoa; mở một học phần xem **tiên quyết / học trước / song hành** và **cơ cấu điểm** (tổng 100%).
4. **Sinh viên** (`/admin/sinh-vien`):
   - Lọc khoa / ngành / khóa / trạng thái, xuất Excel.
   - Bấm **Thêm sinh viên** (`/admin/sinh-vien/them`) → nhập hồ sơ một thí sinh (ngành Kỹ thuật phần mềm, khóa 2026) → lưu: hồ sơ ở trạng thái **Chờ nhập học**.
   - Mở hồ sơ vừa tạo → **Xác nhận nhập học**: hệ thống cấp **MSSV** (2 số khóa + mã ngành + số thứ tự), email trường, tài khoản, gán CTĐT, xếp lớp hành chính trong một lần.
5. **Khóa & Lớp hành chính** (`/admin/lop-hanh-chinh`): khóa K2023–K2026, lớp, sĩ số; thêm / gỡ sinh viên có xem trước.

## C. Chương trình khung → mở lớp → thông báo (5 phút) — `acad.test`

Chức năng nhóm bổ sung: lập kế hoạch mở lớp theo **khoa – khóa – học kỳ**.

1. Vào **Chương trình khung** (`/admin/chuong-trinh-khung`) → **Lập chương trình khung**: Học kỳ 2 · 2026-2027, khoa CNTT, khóa K2026 → **Lập khung**.
2. Bấm **Gợi ý từ CTĐT**: hệ thống biết HK2 năm 2026–2027 là **học kỳ thứ 2** của khóa 2026, lấy đúng **9 học phần** học kỳ 2 trong CTĐT các ngành của khoa (50 sinh viên). Có thể sửa số lớp, sĩ số, thêm / xóa học phần.
3. Bấm **Mở … lớp còn thiếu** → xác nhận: mở 9 lớp học phần mới ở trạng thái **Dự kiến** (ví dụ `TOAN103.262.02`). Bấm lại thì không mở trùng.
4. Bấm **Soạn và gửi thông báo**: nội dung soạn sẵn (danh sách học phần, mã lớp, ngày bắt đầu học kỳ, thời gian đăng ký), sửa được → **Gửi thông báo**: tới đúng 50 sinh viên CNTT khóa 2026.
5. (Cửa sổ thứ hai) đăng nhập `2601030001` → chuông thông báo có thông báo vừa gửi.

## D. Lớp học phần và thời khóa biểu (5 phút) — `acad.test`

1. **Lớp học phần** (`/admin/lop-hoc-phan`): chọn HK2 → mở lớp `TOAN103.262.02` vừa tạo:
   - **Phân công giảng viên**: chọn `GV0001`, vai trò chính (vượt định mức tiết thì hệ thống cảnh báo).
2. **Xếp thời khóa biểu** (`/admin/thoi-khoa-bieu`): chọn lớp `TOAN103.262.02` → chọn phòng (ví dụ A101), Thứ Bảy, tiết 1–3, tuần 1–15 → **Lưu lịch** → **Kiểm tra lại** (đã thử: không xung đột; nếu báo trùng phòng / giảng viên thì đổi thứ hoặc tiết) → **Sinh buổi học** (tự bỏ ngày nghỉ lễ): 15 buổi từ 30/01 đến 08/05/2027.
3. Quay lại **Lớp học phần** → chuyển trạng thái lớp sang **Mở đăng ký**. Lớp chưa có giảng viên chính thì hệ thống chặn — có thể cho xem trước bước này.
4. **Công bố thời khóa biểu HK1**: ở `/admin/thoi-khoa-bieu` chọn Học kỳ 1 → **Công bố thời khóa biểu**. Sau bước này giảng viên mới thấy lịch dạy HK1 (dữ liệu mẫu để HK1 ở trạng thái nháp).
5. (Tùy thời gian) **Nghỉ, dạy bù & lịch phòng** (`/admin/thoi-khoa-bieu/day-bu`): xếp buổi dạy bù, tra lịch phòng.

## E. Sinh viên đăng ký học phần (5 phút)

**Sinh viên `2601030001`** (cửa sổ thứ hai):
1. **Đăng ký học phần** (`/student/dang-ky-hoc-phan`): thấy đợt "HK2 2026–2027 — vòng 1", thời gian còn lại, khung 4–9 tín chỉ.
2. Chuông thông báo có thông báo **mở lớp** (phần C) và **công bố thời khóa biểu** (bước D4). Lớp `TOAN103.262.02` là lớp duy nhất ở mục **Đăng ký được**: có lịch Thứ Bảy tiết 1–3 phòng A101, giảng viên Nguyễn Văn An, **"Bắt đầu học: 30/01/2027"** → **Đăng ký lớp này**.
3. Chỉ vào lớp `INT102.262.01` (hoặc `INT102.262.02`): **không đăng ký được**, lý do "Thiếu học phần tiên quyết: Nhập môn lập trình (INT101)" — sinh viên đang học INT101 ở HK1, chưa đạt.
4. **Kết quả đăng ký** (`/student/ket-qua-dang-ky`): danh sách đã đăng ký, ngày bắt đầu học, chọn học kỳ, **Tải phiếu PDF**, **In phiếu**.
5. **Thời khóa biểu**: mở `/student/thoi-khoa-bieu?date=2027-01-25` (tuần đầu HK2) → lớp vừa đăng ký hiện ngay vào Thứ Bảy 30/01, kèm dòng "lịch dự kiến" vì TKB HK2 chưa công bố. Quay lại hủy đăng ký → lịch mất theo.

**Sinh viên `2602010001`** (nếu cần thêm ví dụ): đăng ký được ngay lớp `ENG202.262.01` (bắt đầu học 25/01/2027).

Ý để nói: các quy tắc kiểm tra khi đăng ký — đợt đang mở, đúng đối tượng, lớp còn chỗ, tiên quyết, trùng lịch, vượt tín chỉ, ngày học phải sau ngày đóng đăng ký; chỗ cuối cùng chỉ một người được (khóa dòng khi đăng ký đồng thời).

## F. Giảng viên dạy học (4 phút) — `GV0001`

1. **Lịch giảng dạy** (`/teacher/lich-giang-day`): buổi dạy hôm nay, thời khóa biểu tuần — lớp INT201 dạy Thứ Ba tiết 1–3 (cần bước D4 công bố TKB HK1 trước).
2. **Lớp học phần** (`/teacher/lop-hoc-phan`): mặc định mở Học kỳ 1 — hai lớp `INT201.261.01` Cấu trúc dữ liệu và giải thuật và `TOAN102.261.01`, mỗi lớp 50 sinh viên; in danh sách, xuất CSV. Chọn Học kỳ 2 thấy lớp `TOAN103.262.02` vừa phân công ở bước D.
3. **Điểm danh** (`/teacher/diem-danh`): lớp `INT201.261.01`, buổi gần nhất đã diễn ra được chọn sẵn (các buổi cũ ghi "quá hạn sửa") → đổi một sinh viên sang **Vắng** → **Lưu điểm danh** ("Đã lưu điểm danh (1 thay đổi)"). Quá hạn sửa thì hệ thống tạo **yêu cầu sửa** gửi Phòng Đào tạo duyệt. Có **Chuyển điểm chuyên cần vào sổ điểm**.
4. **Nhập điểm** (`/teacher/nhap-diem`): mặc định mở `INT201.261.01` (đã điền sẵn ở bước 0.5) → sửa một điểm → **Lưu nháp điểm** → **Nộp điểm**: sổ điểm chuyển sang chỉ xem, trạng thái "Đã nộp • Chờ duyệt". Còn ô trống thì hệ thống không cho nộp và liệt kê MSSV còn thiếu (có thể cho xem trước khi điền). Có **Nhập Excel**, **Xuất sổ điểm**.

## G. Sinh viên xem kết quả (3 phút) — `2501010010`

1. **Tổng quan** (`/student`): lịch hôm nay, tiến độ CTĐT (tín chỉ), kết quả mới, thông báo.
2. **Kết quả học tập & Điểm** (`/student/ket-qua-hoc-tap`): mặc định mở HK2 2025–2026 (GPA 4,00, 10 học phần); chọn HK1 2025–2026 (GPA 3,86) → điểm thành phần, điểm chữ, hệ 4, GPA tích lũy 3,94 — Xuất sắc. Muốn cho thấy cảnh báo học vụ thì đăng nhập `2501010001` (học lực Kém).
3. **Chuyên cần** (`/student/chuyen-can`): tỷ lệ vắng từng lớp, cảnh báo / cấm thi.
4. **Chương trình đào tạo**: khối kiến thức theo học kỳ.
5. **Hồ sơ cá nhân**: tự sửa liên hệ, ảnh, người thân; muốn sửa họ tên / ngày sinh / CCCD thì **tạo yêu cầu** kèm minh chứng để cán bộ duyệt.

## H. Duyệt và công bố điểm (3 phút) — `dean.test`, `acad.test`

1. `dean.test` (trưởng khoa CNTT) → **Quản lý & Duyệt điểm** (`/admin/quan-ly-diem`, mặc định Học kỳ 1): chỉ thấy các lớp của khoa CNTT; lớp `INT201.261.01` ở "Chờ duyệt" → bấm **Khoa duyệt** → "Khoa đã duyệt". **Chỉ bấm Khoa duyệt** — nút "Xác nhận" cũng hiện với trưởng khoa nhưng bấm sẽ bị chặn (403), lỗi giao diện đã biết.
2. `acad.test`: cùng trang thấy 15 lớp toàn trường → lớp INT201 bấm **Xác nhận** → **Công bố** (hộp xác nhận của trình duyệt). Trả lại bảng điểm phải có lý do.
3. Sinh viên `2501030001` → **Kết quả học tập**, chọn Học kỳ 1 2026–2027: thấy INT201 với điểm thành phần, điểm hệ 10, điểm chữ, hệ 4 và thông báo "Đã có điểm học phần Cấu trúc dữ liệu và giải thuật"; trước khi công bố thì không thấy.
4. **Tra cứu GPA** (`/admin/quan-ly-diem/gpa`) và **Duyệt điều chỉnh điểm** (`/admin/duyet-dieu-chinh-diem`).
5. Ý để nói: sinh viên chỉ thấy điểm **đã công bố**; điểm sau công bố chỉ đổi qua yêu cầu điều chỉnh có duyệt, mọi phiên bản điểm được lưu lại.

## I. Thông báo thủ công (2 phút) — `acad.test`

1. **Gửi thông báo** (`/admin/soan-thong-bao`): chọn đối tượng, ví dụ Khóa → K2025 → **Thêm** → **Xem trước số người nhận** ("150 sinh viên sẽ nhận") → nhập tiêu đề, nội dung → **Gửi thông báo** (có hẹn giờ, ghim, khẩn).
2. Sinh viên nhận ở chuông thông báo hoặc `/thong-bao`. Giảng viên gửi cho lớp mình dạy ở `/teacher/gui-thong-bao`.

## J. Nghiệm thu tự động (2 phút) — cửa sổ dòng lệnh

1. Chạy:
   ```powershell
   cd C:\laragon\www\StudentManager; php artisan uat:p1
   ```
   Lệnh chạy 23 kịch bản nghiệm thu UAT-01 → UAT-08 trên dữ liệu thật rồi **tự hoàn tác**: khóa tài khoản sau 5 lần sai, phạm vi dữ liệu, chặn vòng lặp tiên quyết, cấp MSSV, trùng phòng / giảng viên, thiếu tiên quyết, hết chỗ, trùng lịch, tính điểm khớp ví dụ BA (6,9 → C → 2,0), GPA, khóa sửa điểm sau công bố… Kết quả mới nhất (26/10): **23/23 đạt**, 1 mục bỏ qua (đăng ký đồng thời, đã đo riêng).
2. Nói thêm: kiểm thử tự động (`php artisan test`) chạy trên CSDL riêng và chạy trên GitHub Actions với mỗi Pull Request.

---

## Lưu ý để demo không vấp

- Toàn bộ kịch bản đã được chạy thử thật ngày 27/10/2026 trên dữ liệu mẫu (kể cả B4, D, E, F, H); vẫn nên **tập một lượt** sau khi dựng lại dữ liệu.
- **Phải làm bước 0.5 và D4** trước phần F, H: dữ liệu mẫu để điểm thi INT201 trống và thời khóa biểu HK1 ở dạng nháp.
- Giờ "công bố" ở trang Quản lý điểm đang hiện theo giờ UTC (chậm 7 tiếng) — lỗi giao diện đã biết, nhóm giao diện sẽ sửa.
- Cố vấn học tập: `adv.test` (gắn hồ sơ giảng viên `GV9001`, cố vấn một lớp hành chính) → **Lớp cố vấn** (`/teacher/lop-co-van`) xem GPA, cảnh báo của lớp.
- Không dựng lại dữ liệu (`migrate:fresh`) giữa buổi demo; nếu lỡ thao tác sai thì làm lại phần đó bằng sinh viên / lớp khác.
- Các màn hình học phí, lịch thi, khảo sát, học bổng, đơn từ… thuộc giai đoạn P2–P4, **chưa làm**; chỉ có bản mock của nhóm giao diện ở `/__fe-preview/...` (số liệu giả) — nếu thầy hỏi thì nói rõ là bản thiết kế.
