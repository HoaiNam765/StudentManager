# Ví dụ tự làm: chuẩn bị một học kỳ mới từ đầu

Làm theo trên giao diện, đúng thứ tự nghiệp vụ của hệ thống. Ví dụ dùng **Học kỳ 2 năm 2026–2027** (bắt đầu 25/01/2027, đang ở trạng thái *Dự kiến*) và **khóa K2025, khoa Công nghệ thông tin**.

Đã chạy thử trên bản sao dữ liệu mẫu ngày 27/10/2026 (CTĐT đầy đủ 8 học kỳ, luồng đăng ký theo chương trình khung); số liệu bên dưới là kết quả thật.

> **Lưu ý:** làm ví dụ này sẽ thêm dữ liệu vào `student_manager`, và đợt đăng ký đã có lớp thì không xóa được. Nếu sau đó demo theo `docs/kich-ban-demo.md`, hãy dựng lại dữ liệu sạch trước buổi demo (`php artisan migrate:fresh --seed; php artisan demo:seed`). Ví dụ dùng khóa K2025 nên không trùng với kịch bản demo (khóa K2026).

Mật khẩu mọi tài khoản: `password`. Chạy `php artisan serve`, mở http://127.0.0.1:8000/login.

---

## Tổng quan thứ tự

```
Lịch học vụ → Chương trình khung (môn theo khoa – khóa – học kỳ) → Tạo đợt đăng ký từ khung (số lớp + giảng viên của khoa)
→ Xếp lịch từng lớp (trang Đăng ký học phần) → Thông báo xem trước → Công bố → (đúng giờ / Mở ngay)
→ Sinh viên đăng ký → (hết đợt) Xử lý lớp thiếu sĩ số → Chốt đăng ký → Sinh buổi học, công bố TKB
→ (học kỳ bắt đầu) Đang học → Điểm danh → Nhập điểm → Nộp → Duyệt → Công bố → GPA
```

Bước 1–6 làm được ngay hôm nay. Bước 7 chỉ làm được **sau khi đợt đăng ký đóng**; bước 8 **sau khi học kỳ bắt đầu** (25/01/2027) — đây là quy tắc của hệ thống, không phải lỗi.

---

## Bước 1. Lịch học vụ của học kỳ — `acad.test`

**Năm học & Lịch học vụ** (`/admin/nam-hoc`) → mục **Các mốc lịch học vụ** → chọn **Học kỳ 2 · 2026-2027** → **Sao chép lịch** từ **Học kỳ 1 · 2026-2027**. Các mốc tự dời theo ngày bắt đầu học kỳ (mở / đóng đăng ký, bắt đầu học, hạn hủy, hạn rút…). Sửa ngày nào cần rồi **Lưu lịch học vụ**.

> Vì sao: hạn **hủy** / **rút** học phần của sinh viên lấy từ các mốc này. Ngày nghỉ lễ (mục Ngày nghỉ) được dùng khi sinh buổi học.

## Bước 2. Chương trình khung — `acad.test`

**Chương trình khung** (`/admin/chuong-trinh-khung`) → **Lập chương trình khung**: Học kỳ 2 · 2026-2027, khoa **CNTT**, khóa **K2025** → **Lập khung**.

Bấm **Gợi ý từ CTĐT**. Hệ thống tính HK2 năm 2026–2027 là **học kỳ thứ 4** của khóa 2025 → thêm **11 học phần** có "học kỳ dự kiến = 4" trong CTĐT các ngành của khoa (50 sinh viên):

| Mã | Học phần | TC |
|---|---|---|
| INT204 | Hệ điều hành | 3 |
| INT205 / INT207 | Mạng máy tính / Thực hành mạng máy tính | 3 / 1 |
| INT208 / INT209 | Lập trình Java / Thực hành | 2 / 1 |
| INT210 / INT211 | Hệ quản trị cơ sở dữ liệu / Thực hành | 2 / 1 |
| INT212 / INT213 | Lập trình Python / Thực hành | 2 / 1 |
| LLCT104 | Lịch sử Đảng Cộng sản Việt Nam | 2 |
| LLCT105 | Tư tưởng Hồ Chí Minh | 2 |

> Lần sau chọn ba ô **Học kỳ – Khoa – Khóa** ở đầu trang là khung tự mở.

## Bước 3. Tạo đợt đăng ký từ chương trình khung — `acad.test`

Cùng trang, bấm **Tạo đợt đăng ký**:
- Tên (soạn sẵn): `Đăng ký Học kỳ 2 2026-2027 — CNTT K2025`; Vòng: **Vòng 2**
- Mở lúc: ngày mai 08:00; Đóng lúc: **15/01/2027 23:00**
- Bảng học phần — mỗi môn **Số lớp**, **Sĩ số tối đa**, **giảng viên từng lớp** (chỉ giảng viên khoa CNTT, kèm "đã nhận / định mức tiết"). Ví dụ chỉ mở 3 môn, các môn khác để **Số lớp = 0**:

| Học phần | Số lớp | Giảng viên | Lớp tạo ra |
|---|---|---|---|
| INT205 Mạng máy tính | 1 | `GV0007` Đặng Thu Hà | `INT205.262.01` |
| LLCT104 Lịch sử Đảng | 1 | `GV0005` Hoàng Thị Giang | `LLCT104.262.01` |
| INT204 Hệ điều hành | 1 | `GV0004` Phạm Minh Đức | `INT204.262.01` |

→ **Tạo đợt và mở lớp**: đợt nháp gắn khung + 3 lớp + giảng viên được tạo trong một lần (giảng viên ngoài khoa hay lỗi ở một lớp thì không tạo gì), "Số lớp dự kiến" của khung cập nhật theo, rồi trang **tự chuyển sang Đăng ký học phần** đã mở sẵn đợt này.

Thử trước: Đóng = 26/01/2027 → **bị từ chối**: "Đợt đăng ký đóng ngày 26/01/2027, không trước ngày bắt đầu học của Học kỳ 2 (25/01/2027)…".

> Đợt gắn khung chỉ dành cho sinh viên **đúng khoa và khóa** của khung.

## Bước 4. Xếp lịch từng lớp — `acad.test`

Ở trang **Đăng ký học phần**, mỗi lớp ghi "Chưa xếp lịch" → **Sửa giảng viên / lịch** (giảng viên đã chọn ở bước 3 được giữ), tuần 1–15:

| Lớp | Lịch | Bắt đầu học |
|---|---|---|
| `INT205.262.01` | Thứ Hai tiết 7–10, phòng A103 | 25/01/2027 |
| `LLCT104.262.01` | Thứ Sáu tiết 6–7, phòng A103 | 29/01/2027 |
| `INT204.262.01` | Thứ Năm tiết 1–4, phòng A101 | 28/01/2027 |

Những gì hệ thống kiểm tra khi lưu:
- Giảng viên **trùng lịch** thì ghi "— TRÙNG LỊCH" và không chọn được (ví dụ Thứ Hai tiết 1–4 thì `GV0001` bị khóa vì đang dạy `INT102.262.01`).
- **Trùng phòng** thì bị chặn (ví dụ Thứ Hai tiết 1–3 phòng B101: "Phòng B101 đã có lớp ACC201.262.01…").
- Lớp, giảng viên, lịch lưu **trong một lần**. Muốn bỏ một lớp: **Xóa khỏi đợt** (lớp chuyển "Đã hủy", giữ lịch sử).

> Vì sao 4 tiết: học phần 3 tín chỉ (30 tiết LT + 30 tiết TH) cần 60 tiết = 4 tiết × 15 tuần. Còn lớp chưa xếp lịch thì chưa thông báo xem trước được.

## Bước 5. Thông báo xem trước → Công bố → Mở — `acad.test`

1. Bấm **Công bố** ngay → **bị chặn**: "Cần thông báo danh sách học phần để sinh viên xem trước."
2. Bấm **Thông báo xem trước** → đợt ghi "Đã thông báo – chỉ xem trước"; 50 sinh viên CNTT khóa 2025 nhận thông báo, mở ra xem được lớp / giảng viên / lịch nhưng **chưa đăng ký được**.
3. Bấm **Công bố** → đợt chờ đến giờ mở (đúng giờ máy chủ tự cho đăng ký). Muốn thử ngay thì bấm **Mở ngay (sớm hơn lịch)**: đợt mở luôn, giữ nguyên hạn đóng, sinh viên nhận thêm thông báo.

> Sau khi công bố, danh sách lớp của đợt không thêm / sửa được nữa — kiểm tra kỹ ở bước 3–4 trước khi công bố.

## Bước 6. Sinh viên đăng ký — `2501030001` (Nhâm Bích Phong, K2025, Kỹ thuật phần mềm)

1. Chuông thông báo: "Xem trước học phần: …" và "Công bố đăng ký: …" → **Xem chi tiết** mở đúng đợt.
2. **Đăng ký học phần**: khung **11–27 tín chỉ** (học lực khá trở lên được đăng ký thêm).
   - `INT205.262.01` — **Bắt đầu học: 25/01/2027** → **Đăng ký lớp này** → thành công. `LLCT104.262.01` cũng đăng ký được.
   - `INT204.262.01` — **không đăng ký được**: "Chưa học trước: Kiến trúc máy tính (INT206). Phải học (không cần đạt) các học phần này trước (BR-ENR-03)" — sinh viên đang học INT206 ở HK1, chưa có điểm.
3. **Kết quả đăng ký**: các lớp đã đăng ký, ngày bắt đầu học, **Tải phiếu PDF**.
4. **Thời khóa biểu** → ô **Học kỳ** chọn "Học kỳ 2 · 2026-2027" → tuần 25/01–31/01: Thứ Hai có INT205 phòng A103, ghi "lịch dự kiến" (TKB HK2 chưa công bố).
5. **Chương trình đào tạo** → **Theo học kỳ**: học kỳ 4 có đúng các môn ở bước 2; học kỳ 1–2 có dấu ✓ ở môn đã đạt, học kỳ 3 ghi "Đang học".

> Trường hợp đặc biệt (sinh viên bị chặn nhưng khoa đồng ý ngoại lệ): Phòng Đào tạo **đăng ký thay**, bỏ qua từng điều kiện, **bắt buộc ghi lý do**. Chức năng đã có ở máy chủ (`POST /admin/students/{id}/enrollments`), **chưa có màn hình**.

## Bước 7. Hết đợt (sau 15/01/2027) — `acad.test`

1. **Đăng ký học phần** → **Lớp thiếu sĩ số**: lớp dưới sĩ số tối thiểu được đề xuất **Hủy** hoặc **Gộp** vào lớp cùng học phần còn chỗ. Hủy lớp thì đăng ký của sinh viên tự hủy và sinh viên được báo.
2. **Chốt đăng ký học kỳ**: xem trước → chốt. Hệ thống **chặn** nếu còn đợt đang mở hoặc còn lớp thiếu sĩ số chưa xử lý. Sau chốt sinh viên không tự hủy được nữa.
3. **Xếp thời khóa biểu** → Học kỳ 2 → từng lớp **Sinh buổi học** (buổi trùng ngày nghỉ tự thành "Nghỉ — cần xếp dạy bù") → **Công bố thời khóa biểu**: giảng viên thấy lịch dạy; từ đây đổi lịch phải có lý do và báo trước.

## Bước 8. Khi học kỳ bắt đầu (từ 25/01/2027)

Lớp chuyển **Đang học** → giảng viên điểm danh từng buổi đã diễn ra, nhập điểm thành phần, nộp → trưởng khoa duyệt → Phòng Đào tạo xác nhận, công bố → sinh viên thấy điểm, GPA tự tính lại.

Muốn tập ngay phần này thì dùng **Học kỳ 1 năm 2026–2027 (đang học)** theo `docs/kich-ban-demo.md`, phần F và H (lớp `INT201.261.01` của `GV0001`).
