# Ví dụ tự làm: mở đăng ký học phần cho một học kỳ mới

Làm theo trên giao diện, đúng thứ tự nghiệp vụ của hệ thống. Ví dụ là **Học kỳ 1 năm học 2027–2028** (chưa có trong dữ liệu mẫu, tạo ở bước 0) cho mọi khoa – khóa đang học (15DH, 16DH).

Đã chạy thử trên bản sao dữ liệu mẫu ngày 28/10/2026; số liệu bên dưới là kết quả thật.

> **Lưu ý:** làm ví dụ này sẽ thêm dữ liệu vào `student_manager`, và đợt đăng ký đã có lớp thì không xóa được. Nếu sau đó demo theo `docs/kich-ban-demo.md`, hãy dựng lại dữ liệu sạch trước buổi demo (`php artisan migrate:fresh --seed; php artisan demo:seed`).

Mật khẩu mọi tài khoản: `password`. Chạy `php artisan serve`, mở http://127.0.0.1:8000/login, đăng nhập `acad.test` (Phòng Đào tạo).

---

## Tổng quan thứ tự

```
Năm học + học kỳ → Lịch học vụ → Đợt đăng ký "Theo chương trình khung" (cả học kỳ)
→ Bảng Khoa × Khóa: chọn kỳ CTĐT cho mọi ô (môn tự lấy theo CTĐT) → Lớp & giảng viên từng khung → Xếp lịch từng lớp
→ Công bố (sinh viên thấy lớp, chưa đăng ký) → đúng giờ / Mở đăng ký ngay (hiện nút đăng ký) → Sinh viên đăng ký
→ (hết đợt) Lớp thiếu sĩ số → Chốt đăng ký → Công bố TKB → (học kỳ bắt đầu) điểm danh, điểm
```

---

## Bước 0. Năm học và học kỳ — `/admin/nam-hoc`

1. **Tạo / sửa năm học**: tên `2027-2028`, bắt đầu 16/08/2027, kết thúc 13/08/2028 → **Lưu năm học** (không chồng lên năm 2026–2027 kết thúc 14/08/2027).
2. **Tạo học kỳ**: Năm học `2027-2028`, tên `Học kỳ 1`, loại **Học kỳ chính**, 18 tuần, 06/09/2027 → 09/01/2028 → **Tạo học kỳ** (trạng thái *Dự kiến*).

## Bước 1. Lịch học vụ — `/admin/nam-hoc`, mục Lịch học vụ

Chọn **Học kỳ 1 · 2027-2028** → **Sao chép lịch** từ học kỳ trước → sửa ngày nào cần → **Lưu lịch học vụ**. Hạn hủy / rút học phần của sinh viên lấy từ các mốc này.

## Bước 2. Tạo đợt đăng ký cả học kỳ — `/admin/dang-ky-hoc-phan`

Chọn **Học kỳ 1 · 2027-2028** → **Tạo đợt đăng ký**:
- Loại đợt: **Theo chương trình khung — cả học kỳ, mọi khoa – khóa** (mục "Đối tượng áp dụng" tự ẩn)
- Tên: `Đăng ký học phần HK1 2027-2028`; Vòng: **Vòng 1**
- Mở lúc: **01/07/2027 08:00**; Đóng lúc: **20/08/2027 23:00** (phải trước ngày học 06/09/2027)
- **Lưu đợt đăng ký** → thẻ đợt ghi "Nháp · Theo chương trình khung · 0 khoa – khóa đã chọn kỳ".

## Bước 3. Bảng khung Khoa × Khóa

Bấm **Khung & lớp** trên thẻ đợt. Bảng có **3 khoa × 2 khóa** (khoa – khóa đang có sinh viên): mỗi ô có ô chọn **Kỳ 1–8** và gợi ý — khóa **16DH: gợi ý kỳ 3**, khóa **15DH: gợi ý kỳ 5**, mỗi ô 50 sinh viên.

1. Thử bấm **Công bố** ngay → **bị chặn**: "Chưa chọn kỳ cho: Khoa Công nghệ thông tin – 16DH, Khoa Công nghệ thông tin – 15DH, Khoa Kinh tế – 16DH, …".
2. Chọn kỳ từng ô (tùy ý), hoặc bấm **Điền theo gợi ý (6 ô)**. Chọn xong, môn của kỳ đó lấy theo CTĐT các ngành của khoa:

| Khoa | 16DH (kỳ 3) | 15DH (kỳ 5) |
|---|---|---|
| Công nghệ thông tin | 7 môn | 7 môn |
| Kinh tế | 8 môn | 10 môn |
| Ngoại ngữ | 11 môn | 11 môn |

> Đổi kỳ của một ô: chọn lại kỳ (được khi khung đó chưa có lớp). Môn của khung xem / sửa thêm ở trang **Chương trình khung**.

## Bước 4. Lớp & giảng viên từng khung

Dưới bảng, mỗi khung một khối. Bấm **Lớp & giảng viên**: mỗi môn nhập **Số lớp**, **Sĩ số tối đa** và chọn **giảng viên của khoa** cho từng lớp (kèm "đã nhận / định mức tiết", gợi ý chia ít tiết trước) → **Mở lớp**. Giảng viên ngoài khoa bị từ chối; lỗi ở một lớp thì không mở lớp nào.

Ví dụ đã chạy (mỗi khung 1 lớp):

| Khung | Lớp | Giảng viên |
|---|---|---|
| CNTT – 16DH | `ENG103.271.01` Tiếng Anh 3 | `GV0008` |
| CNTT – 15DH | `INT301.271.01` Công nghệ phần mềm | `GV0008` |
| Kinh tế – 16DH | `ACC203.271.01` | `GV0012` |
| Kinh tế – 15DH | `ACC302.271.01` Kế toán tài chính 2 | `GV0012` |
| Ngoại ngữ – 16DH | `CHN103.271.01` Tiếng Trung tổng hợp 3 | `GV0016` |
| Ngoại ngữ – 15DH | `CHN105.271.01` Tiếng Trung tổng hợp 5 | `GV0016` |

Môn không có trong khung: **Thêm học phần khác** (môn được thêm vào khung của khoa – khóa đó).

## Bước 5. Xếp lịch từng lớp

Mỗi lớp mới ghi **"Chưa xếp lịch"** → **Sửa giảng viên / lịch**: chọn thứ, tiết, phòng, tuần 1–15. Giảng viên trùng lịch bị khóa trong danh sách; trùng phòng bị chặn khi lưu. Ví dụ: 6 lớp ở 6 ngày Thứ Hai → Thứ Bảy, tiết 1–4, phòng A101 → A106.

Ô của bảng cập nhật "1 lớp" (lớp chưa có lịch ghi thêm "(1 chưa xếp lịch)" màu cam).

## Bước 6. Công bố → Mở đăng ký

1. **Công bố** → hệ thống kiểm tra: mọi ô đã chọn kỳ, mỗi khung có lớp, mọi lớp có giảng viên chính của khoa và lịch, ngày học sau ngày đóng. Thiếu gì báo đúng chỗ đó (ví dụ "Khung chưa có lớp nào: …", "Lớp … thiếu giảng viên hoặc lịch học").
2. Công bố xong: thẻ đợt ghi **"Đã công bố – chờ giờ mở"**; sinh viên mọi khoa – khóa của đợt nhận thông báo, **thấy lớp nhưng chưa có nút đăng ký**.
3. Đúng 01/07/2027 08:00 nút đăng ký tự hiện; muốn sớm hơn bấm **Mở đăng ký ngay** (giữ nguyên giờ đóng).

## Bước 7. Sinh viên — `2601030001` (16DH, Kỹ thuật phần mềm)

**Đăng ký học phần** → chọn đợt "Đăng ký học phần HK1 2027-2028" (hoặc bấm từ thông báo):
- Chỉ thấy lớp của **khung CNTT – 16DH** (`ENG103.271.01`); **không** thấy lớp của 15DH hay khoa khác.
- Lớp môn **kỳ trước chưa học / chưa đạt** của CTĐT mình mà đợt có mở (ở khung bất kỳ) cũng hiện, gắn nhãn **"Học bù"**. Môn đã đạt không hiện.
- Trước giờ mở: "Chờ đến giờ mở đăng ký"; sau khi mở: đăng ký được nếu đủ điều kiện. Ví dụ `ENG103.271.01` báo "Thiếu học phần tiên quyết: Tiếng Anh 2 (ENG102)" vì dữ liệu mẫu mới tới học kỳ 1 năm nhất của 16DH.

## Bước 8. Hết đợt và học kỳ bắt đầu

- **Lớp thiếu sĩ số**: hủy hoặc gộp; **Chốt đăng ký học kỳ**; **Xếp thời khóa biểu** → **Công bố thời khóa biểu**.
- Khi học kỳ bắt đầu: điểm danh, nhập điểm, duyệt, công bố — tập ngay trên **Học kỳ 1 năm 2026–2027 (đang học)** theo `docs/kich-ban-demo.md`, phần F và H.
