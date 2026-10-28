# Kịch bản demo — Hệ thống Quản lý Sinh viên

Thời lượng khoảng 30 phút. Đi theo đúng vòng đời một học kỳ: Phòng Đào tạo chuẩn bị → lập chương trình khung → mở lớp, phân công giảng viên, xếp lịch trong đợt đăng ký → thông báo xem trước → công bố → sinh viên đăng ký → giảng viên dạy, điểm danh, nhập điểm → sinh viên xem kết quả → nghiệm thu tự động.

Số liệu trong kịch bản lấy từ bộ dữ liệu mẫu (`demo:seed`), đã chạy thử thật ngày 27/10/2026 (sau khi có CTĐT đầy đủ 8 học kỳ và luồng đăng ký theo chương trình khung). Dữ liệu mẫu cố định (họ tên, điểm, phân công giảng viên giống nhau mỗi lần dựng lại); riêng thời gian đợt đăng ký tính theo ngày dựng.

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
| Sinh viên khóa 16DH (2026), ngành Kỹ thuật phần mềm (khoa CNTT) | `2601030001` (Hoàng Văn Mai) | E |
| Sinh viên khóa 16DH (2026), ngành Ngôn ngữ Anh | `2602010001` (Nguyễn Văn An) | E |
| Sinh viên khóa 15DH (2025), học lực Xuất sắc (GPA 3,82) | `2501010010` (Dương Như Tiên) | G |
| Sinh viên khóa 15DH (2025), học lực Kém (bị cảnh báo học vụ) | `2501010001` (Mang Quang Hồng) | G (tùy chọn) |
| Sinh viên lớp INT201.261.01 của GV0001 | `2501030001` (Nhâm Bích Phong) | F, H |
| Cố vấn học tập | `adv.test` (hồ sơ GV9001) | F (tùy chọn) |

### Dữ liệu mẫu có sẵn
- Khóa 14DH (2024, chưa có sinh viên), 15DH (2025), 16DH (2026); lớp hành chính mã khóa + khoa + số thứ tự (16DHCNTT01 ngành CNTT, 16DHCNTT02 ngành KTPM, 16DHKT01, 16DHNN01…).
- 300 sinh viên (khóa 15DH, 16DH), 20 giảng viên, 184 học phần, 6 CTĐT đầy đủ (8 học kỳ, 126–129 tín chỉ), 127 lớp học phần, 5.224 lượt đăng ký, khoảng 18.800 lượt điểm danh, 50 phòng học (tòa A–E, mỗi tòa A101–A110 …).
- Năm học 2025–2026: hai học kỳ **đã khóa**, có điểm, GPA.
- HK1 2026–2027 (01/09/2026 – 10/01/2027): **đang học**, thời khóa biểu **đã công bố**, có phòng, sinh viên không trùng lịch.
- HK2 2026–2027 (bắt đầu 25/01/2027): đợt đăng ký vòng 1 cho khóa 16DH (luồng cũ, 18 lớp có sẵn) **đang mở khoảng một tháng tính từ ngày dựng dữ liệu**. Chưa có chương trình khung nào — phần C lập từ đầu.

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
   - Bấm **Thêm sinh viên** (`/admin/sinh-vien/them`) → nhập hồ sơ một thí sinh (ngành Kỹ thuật phần mềm, khóa 16DH) → lưu: hồ sơ ở trạng thái **Chờ nhập học**.
   - Mở hồ sơ vừa tạo → **Xác nhận nhập học**: hệ thống cấp **MSSV** (2 số khóa + mã ngành + số thứ tự), email trường, tài khoản, gán CTĐT, xếp lớp hành chính trong một lần.
5. **Khóa & Lớp hành chính** (`/admin/lop-hanh-chinh`): khóa 14DH–16DH, lớp (ví dụ 16DHCNTT01), sĩ số; thêm / gỡ sinh viên có xem trước.

## C. Chương trình khung → tạo đợt đăng ký (4 phút) — `acad.test`

Chương trình khung = **danh sách môn** mỗi khoa – khóa học trong một học kỳ. Từ khung, Phòng Đào tạo tạo luôn đợt đăng ký: số lớp mỗi môn và giảng viên của khoa.

1. **Chương trình khung** (`/admin/chuong-trinh-khung`) → ở đầu trang chọn **Học kỳ 2 · 2026-2027**, Khoa để **Tất cả khoa**, Khóa **16DH** → bên phải hiện **từng khoa** với các môn của **học kỳ thứ 2** của khóa theo CTĐT (xem trước): CNTT 9 môn, Kinh tế 10 môn, Ngoại ngữ 12 môn.
2. Bấm **Lập khung cho 3 khoa còn thiếu** → mỗi khoa có khung, môn lấy theo CTĐT (thẻ khoa ghi "9 môn", "10 môn", "12 môn"). Có thể thêm / sửa / xóa môn từng khung.
   - Cách khác: chọn Học kỳ + **Khoa CNTT** (để trống Khóa) → hiện các khóa của khoa (16DH học kỳ thứ 2, 15DH học kỳ thứ 4) → bấm khóa là lập / mở khung.
3. Bấm **Khoa Công nghệ thông tin** → mở khung 9 môn → **Tạo đợt đăng ký**. Hộp thoại có tên đợt (soạn sẵn), vòng, giờ mở / đóng (mặc định ngày mai 08:00 → 10 ngày trước ngày học), và bảng học phần: **Số lớp**, **Sĩ số tối đa**, **giảng viên từng lớp** (chỉ giảng viên khoa CNTT, kèm số tiết đã nhận / định mức):
   - Vòng: **Vòng 2**.
   - Để demo gọn: **Số lớp = 0** cho mọi môn trừ **TOAN103** (1 lớp, giảng viên `GV0001` Nguyễn Văn An) và **TOAN104** (1 lớp, `GV0008` Bùi Văn Khánh). Gõ số lớp 2 thì hiện thêm ô giảng viên lớp 2.
   - **Tạo đợt và mở lớp** → hệ thống tạo đợt nháp gắn khung, mở `TOAN103.262.02`, `TOAN104.262.02` (lớp + giảng viên trong một giao dịch), rồi **tự chuyển sang trang Đăng ký học phần** đã mở sẵn đợt đó.
4. Ý để nói: CTĐT theo **ngành – khóa** (8 học kỳ) là chương trình gốc; khung là phần của một học kỳ, Phòng Đào tạo điều chỉnh theo khoa – khóa mà không sửa CTĐT. Giảng viên ngoài khoa bị từ chối, lỗi ở một lớp thì không tạo gì cả.

## D. Xếp lịch → xem trước → công bố (5 phút) — `acad.test`

1. Trang **Đăng ký học phần** (đến từ bước C4): hai lớp ghi **"Chưa xếp lịch"**. Lớp `TOAN103.262.02` → **Sửa giảng viên / lịch**, tuần 1–15:
   - Chọn **Thứ Hai tiết 1–4**, phòng **A101** → ô giảng viên: `GV0001` hiện "— TRÙNG LỊCH", không chọn được (đang dạy `INT102.262.01` Thứ Hai tiết 1–4).
   - Chọn **Thứ Hai tiết 1–3**, phòng **B101**, giảng viên `GV0001` → **Lưu** → bị chặn "Phòng B101 đã có lớp ACC201.262.01 (Thứ Hai tiết 1–3…)".
   - Chọn **Thứ Bảy tiết 1–4**, phòng **A101** → giảng viên giữ nguyên `GV0001` → **Lưu lớp học phần**.
   - Lớp `TOAN104.262.02`: **Thứ Bảy tiết 6–9**, phòng **A101**, `GV0008` → lưu.
2. Bấm **Công bố** ngay → bị chặn "Cần thông báo danh sách học phần để sinh viên xem trước". (Còn lớp chưa xếp lịch thì **Thông báo xem trước** cũng bị chặn và nêu mã lớp.)
3. Bấm **Thông báo xem trước** → đợt ghi "Đã thông báo – chỉ xem trước"; 50 sinh viên CNTT khóa 16DH nhận thông báo.
4. (Cửa sổ thứ hai) `2601030001` → chuông thông báo **"Xem trước học phần: …"** → **Xem chi tiết** mở đúng đợt: thấy lớp, giảng viên, lịch, phòng, **"Bắt đầu học: 30/01/2027"**, nhưng chưa đăng ký được.
5. Quay lại `acad.test` → **Công bố** → sinh viên bấm **Làm mới**: "Chờ đến giờ mở đăng ký", khung đếm ngược **"Mở đăng ký sau"**. Bấm **Mở ngay (sớm hơn lịch)** → đợt mở luôn, giữ nguyên hạn đóng.

Ý để nói: lớp mở trong đợt **không sửa chương trình khung**; lớp bỏ khỏi đợt chuyển sang "Đã hủy" để giữ lịch sử. Sinh viên khác khoa hoặc khác khóa không thấy đợt này.

## E. Sinh viên đăng ký học phần (4 phút) — `2601030001`

1. **Đăng ký học phần**: trang mặc định mở đợt vòng 1 (luồng cũ, mở sớm hơn) → ở ô chọn đợt dưới tên đợt chọn "Đăng ký Học kỳ 2 2026-2027 — CNTT 16DH" (hoặc bấm từ thông báo): **Đang mở đăng ký**, khung **11–24 tín chỉ**, "Hạn đóng đợt" đếm ngược.
2. Lớp `TOAN103.262.02` → **Đăng ký lớp này** → "Đã đăng ký lớp TOAN103.262.02 – Xác suất thống kê."
3. Chọn đợt **vòng 1** → lớp `INT102.262.01` **không đăng ký được**: "Thiếu học phần tiên quyết: Nhập môn lập trình (INT101)" — sinh viên đang học INT101 ở HK1, chưa có điểm.
4. **Kết quả đăng ký** (`/student/ket-qua-dang-ky`): danh sách, ngày bắt đầu học, **Tải phiếu PDF**, **In phiếu**.
5. **Thời khóa biểu** → ô **Học kỳ** chọn "Học kỳ 2 · 2026-2027" → mở tuần đầu 25/01–31/01/2027: Thứ Bảy có `TOAN103.262.02` 07:00–10:30 phòng A101, ghi "lịch dự kiến" vì TKB HK2 chưa công bố. Hủy đăng ký → lịch mất theo. Chọn lại Học kỳ 1 thấy lịch đang học, mỗi buổi đều có phòng.
6. **Chương trình đào tạo** → nút **Theo học kỳ**: 8 học kỳ, tổng tín chỉ từng học kỳ, học phần bắt buộc / tự chọn theo nhóm, số tiết LT / TH, điều kiện tiên quyết, cột Đạt.

Ý để nói: các quy tắc kiểm tra khi đăng ký — đợt đang mở, đúng khoa – khóa, lớp còn chỗ, tiên quyết / học trước, trùng lịch, vượt tín chỉ, môn đã đạt; chỗ cuối cùng chỉ một người được (khóa dòng khi đăng ký đồng thời).

## F. Giảng viên dạy học (4 phút) — `GV0001`

1. **Lịch giảng dạy** (`/teacher/lich-giang-day`): buổi dạy hôm nay, thời khóa biểu tuần — lớp INT201 của HK1 (thời khóa biểu HK1 đã công bố sẵn). Trang **Tổng quan** (`/teacher`) cũng hiện buổi dạy hôm nay (hoặc ngày dạy gần nhất) với phòng, sĩ số, số đã điểm danh; nút **Điểm danh** mở đúng lớp và buổi.
2. **Lớp học phần** (`/teacher/lop-hoc-phan`): mặc định mở Học kỳ 1 — hai lớp `INT201.261.01` Cấu trúc dữ liệu và giải thuật và `TOAN102.261.01`, mỗi lớp 50 sinh viên; in danh sách, xuất CSV. Chọn Học kỳ 2 thấy lớp `TOAN103.262.02` vừa mở ở bước D.
3. **Điểm danh** (`/teacher/diem-danh`): lớp `INT201.261.01`, buổi gần nhất đã diễn ra được chọn sẵn (các buổi cũ ghi "quá hạn sửa") → đổi một sinh viên sang **Vắng** → **Lưu điểm danh** ("Đã lưu điểm danh (1 thay đổi)"). Quá hạn sửa thì hệ thống tạo **yêu cầu sửa** gửi Phòng Đào tạo duyệt. Có **Chuyển điểm chuyên cần vào sổ điểm**.
4. **Nhập điểm** (`/teacher/nhap-diem`): mặc định mở `INT201.261.01` (đã điền sẵn ở bước 0.5) → sửa một điểm → **Lưu nháp điểm** → **Nộp điểm**: sổ điểm chuyển sang chỉ xem, trạng thái "Đã nộp • Chờ duyệt". Còn ô trống thì hệ thống không cho nộp và liệt kê MSSV còn thiếu (có thể cho xem trước khi điền). Có **Nhập Excel**, **Xuất sổ điểm**.

## G. Sinh viên xem kết quả (3 phút) — `2501010010`

1. **Tổng quan** (`/student`): lịch hôm nay, tiến độ CTĐT (tín chỉ), kết quả mới, thông báo.
2. **Kết quả học tập & Điểm** (`/student/ket-qua-hoc-tap`): mặc định mở HK2 2025–2026 (GPA 3,82); chọn HK1 2025–2026 (GPA 3,82) → điểm thành phần, điểm chữ, hệ 4, GPA tích lũy 3,82 — Xuất sắc. Muốn cho thấy cảnh báo học vụ thì đăng nhập `2501010001` (học lực Kém).
3. **Chuyên cần** (`/student/chuyen-can`): tỷ lệ vắng từng lớp, cảnh báo / cấm thi.
4. **Chương trình đào tạo**: xem theo khối kiến thức hoặc **Theo học kỳ** — học kỳ 1–2 có dấu ✓ ở môn đã đạt, học kỳ 3 ghi "Đang học".
5. **Hồ sơ cá nhân**: tự sửa liên hệ, ảnh, người thân; muốn sửa họ tên / ngày sinh / CCCD thì **tạo yêu cầu** kèm minh chứng để cán bộ duyệt.

## H. Duyệt và công bố điểm (3 phút) — `dean.test`, `acad.test`

1. `dean.test` (trưởng khoa CNTT) → **Quản lý & Duyệt điểm** (`/admin/quan-ly-diem`, mặc định Học kỳ 1): chỉ thấy các lớp của khoa CNTT; lớp `INT201.261.01` ở "Chờ duyệt" → bấm **Khoa duyệt** → "Khoa đã duyệt". **Chỉ bấm Khoa duyệt** — nút "Xác nhận" cũng hiện với trưởng khoa nhưng bấm sẽ bị chặn (403), lỗi giao diện đã biết.
2. `acad.test`: cùng trang thấy các lớp toàn trường → lớp INT201 bấm **Xác nhận** → **Công bố** (hộp xác nhận của trình duyệt). Trả lại bảng điểm phải có lý do.
3. Sinh viên `2501030001` → **Kết quả học tập**, chọn Học kỳ 1 2026–2027: thấy INT201 với điểm thành phần, điểm hệ 10, điểm chữ, hệ 4 và thông báo "Đã có điểm học phần Cấu trúc dữ liệu và giải thuật"; trước khi công bố thì không thấy.
4. **Tra cứu GPA** (`/admin/quan-ly-diem/gpa`) và **Duyệt điều chỉnh điểm** (`/admin/duyet-dieu-chinh-diem`).
5. Ý để nói: sinh viên chỉ thấy điểm **đã công bố**; điểm sau công bố chỉ đổi qua yêu cầu điều chỉnh có duyệt, mọi phiên bản điểm được lưu lại.

## I. Thông báo thủ công (2 phút) — `acad.test`

1. **Gửi thông báo** (`/admin/soan-thong-bao`): chọn đối tượng, ví dụ Khóa → 15DH → **Thêm** → **Xem trước số người nhận** ("150 sinh viên sẽ nhận") → nhập tiêu đề, nội dung → **Gửi thông báo** (có hẹn giờ, ghim, khẩn).
2. Sinh viên nhận ở chuông thông báo hoặc `/thong-bao`. Giảng viên gửi cho lớp mình dạy ở `/teacher/gui-thong-bao`.

## J. Nghiệm thu tự động (2 phút) — cửa sổ dòng lệnh

1. Chạy:
   ```powershell
   cd C:\laragon\www\StudentManager; php artisan uat:p1
   ```
   Lệnh chạy 23 kịch bản nghiệm thu UAT-01 → UAT-08 trên dữ liệu thật rồi **tự hoàn tác**: khóa tài khoản sau 5 lần sai, phạm vi dữ liệu, chặn vòng lặp tiên quyết, cấp MSSV, trùng phòng / giảng viên, thiếu tiên quyết, hết chỗ, trùng lịch, tính điểm khớp ví dụ BA (6,9 → C → 2,0), GPA, khóa sửa điểm sau công bố… Kết quả mới nhất (27/10, sau khi có CTĐT đầy đủ): **tất cả đạt**, 1 mục bỏ qua (đăng ký đồng thời, đã đo riêng).
2. Nói thêm: kiểm thử tự động (`php artisan test`) chạy trên CSDL riêng và chạy trên GitHub Actions với mỗi Pull Request.

---

## Lưu ý để demo không vấp

- Toàn bộ kịch bản đã được chạy thử thật ngày 27/10/2026 trên dữ liệu mẫu (phần C–E chạy lại theo luồng đăng ký theo chương trình khung); vẫn nên **tập một lượt** sau khi dựng lại dữ liệu.
- **Phải làm bước 0.5** trước phần H: dữ liệu mẫu để điểm thi INT201 trống.
- Giờ "công bố" ở trang Quản lý điểm đang hiện theo giờ UTC (chậm 7 tiếng) — lỗi giao diện đã biết, nhóm giao diện sẽ sửa.
- Cố vấn học tập: `adv.test` (gắn hồ sơ giảng viên `GV9001`, cố vấn một lớp hành chính) → **Lớp cố vấn** (`/teacher/lop-co-van`) xem GPA, cảnh báo của lớp.
- Đợt theo chương trình khung **không xóa được** khi đã có lớp (kể cả lớp đã bỏ), và đã "Thông báo xem trước" thì sinh viên thấy đợt tới ngày đóng; tập trên dữ liệu rồi dựng lại trước buổi demo.
- Khóa 2026 chưa có điểm HK1 nên các môn HK2 có tiên quyết (INT102, ENG102) hoặc học trước (LLCT102, GDTC102) đều bị chặn — demo bằng TOAN103 / TOAN104 / GDQP (không có điều kiện).
- Không dựng lại dữ liệu (`migrate:fresh`) giữa buổi demo; nếu lỡ thao tác sai thì làm lại phần đó bằng sinh viên / lớp khác.
- Các màn hình học phí, lịch thi, khảo sát, học bổng, đơn từ… thuộc giai đoạn P2–P4, **chưa làm**; chỉ có bản mock của nhóm giao diện ở `/__fe-preview/...` (số liệu giả) — nếu thầy hỏi thì nói rõ là bản thiết kế.
