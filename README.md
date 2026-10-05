# StudentManager — Hệ thống Quản Lý Sinh Viên

Hệ thống web quản lý trọn vòng đời học vụ của sinh viên đại học chính quy theo **học chế tín chỉ**: nhập học, lớp, đăng ký học phần, thời khóa biểu, điểm danh, thi, điểm, học phí, giấy tờ, thông báo và xét tốt nghiệp.

Đây là đồ án môn học. Hiện dự án đang ở **giai đoạn phân tích nghiệp vụ (BA)**; mã nguồn chưa bắt đầu.

## Trạng thái

| Hạng mục | Trạng thái |
|---|---|
| Phân tích nghiệp vụ (BA v1.0) | Hoàn thành, chờ duyệt — xem [docs/BA.md](docs/BA.md) |
| SRS / use case | Chưa bắt đầu |
| Thiết kế CSDL | Chưa bắt đầu |
| Lập trình | Chưa bắt đầu |
| Kiểm thử, nghiệm thu | Chưa bắt đầu |

## Phạm vi

23 module, chia thành 4 giai đoạn phát hành:

| Giai đoạn | Tên | Module chính |
|---|---|---|
| **P1** | MVP — Học vụ cốt lõi | AUTH, SYS, FAC, ACY, ROM, SUB, CUR, TCH, CLS, STU, ENR, TTB, GRD, NOT, RPT |
| **P2** | Vận hành | ATT, EXM, FEE, DOC, NOT (email), TTB nâng cao |
| **P3** | Hoàn thiện | GRA, REQ, RPT đầy đủ, SYS đầy đủ |
| **P4** | Mở rộng (tùy chọn) | SCH, EVA, cổng thanh toán, SSO, xếp lịch tự động |

Chi tiết từng module, quy tắc nghiệp vụ (BR-), yêu cầu chức năng (FR-) và phi chức năng (NFR-) nằm trong [docs/BA.md](docs/BA.md).

## Công nghệ

- Backend: Laravel 13, PHP 8.3
- Cơ sở dữ liệu: MySQL 8 (không dùng SQLite vì đăng ký học phần ghi đồng thời)
- Giao diện: Blade + Tailwind CSS 4, build bằng Vite (mặc định của Laravel; nhóm giao diện có thể đề xuất đổi qua Issue)
- Môi trường phát triển: Laragon trên Windows

## Cấu trúc thư mục

```
StudentManager/
├── app/
│   ├── Modules/            # Mã nghiệp vụ theo module (xem app/Modules/README.md)
│   ├── Http/               # Controller, middleware dùng chung
│   └── Models/             # Model dùng chung
├── bootstrap/app.php       # Nạp route của 3 cổng giao diện
├── config/
├── database/               # migrations, factories, seeders
├── docs/
│   └── BA.md               # Tài liệu phân tích nghiệp vụ
├── public/
├── resources/
│   ├── css/  js/           # Tailwind, JavaScript (Vite)
│   └── views/
│       ├── layouts/        # Khung trang
│       ├── components/     # Thành phần dùng lại (nút, bảng, biểu mẫu...)
│       ├── partials/       # Đoạn giao diện dùng chung (menu, breadcrumb...)
│       ├── auth/           # Đăng nhập, quên mật khẩu (trang công khai)
│       ├── student/        # Cổng Sinh viên
│       ├── teacher/        # Cổng Giảng viên
│       └── admin/          # Cổng Quản trị / Văn phòng
├── routes/
│   ├── web.php             # Trang công khai
│   ├── student.php         # /student
│   ├── teacher.php         # /teacher
│   └── admin.php           # /admin
├── tests/
├── .github/                # Mẫu Issue, mẫu PR, CODEOWNERS
├── .env.example
└── README.md
```

## Cài đặt và chạy thử

Yêu cầu: PHP 8.3, Composer, Node.js, MySQL (Laragon có sẵn các thứ này, trừ Node.js).

1. Tải mã nguồn và vào thư mục dự án:

   ```bash
   git clone https://github.com/HoaiNam765/StudentManager.git
   cd StudentManager
   ```

2. Cài thư viện và tạo file cấu hình:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Trên PowerShell dùng `copy .env.example .env` thay cho `cp`.

3. Tạo cơ sở dữ liệu `student_manager` trong MySQL (HeidiSQL hoặc phpMyAdmin của Laragon):

   ```sql
   CREATE DATABASE student_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   Nếu MySQL của bạn có mật khẩu root, sửa `DB_PASSWORD` trong `.env`. Không commit file `.env`.

4. Tạo bảng:

   ```bash
   php artisan migrate
   ```

5. Cài và chạy phần giao diện (giữ cửa sổ này mở), rồi mở cửa sổ khác chạy server:

   ```bash
   npm install
   npm run dev
   ```

   ```bash
   php artisan serve
   ```

   Mở http://localhost:8000. Nếu dùng Laragon, thư mục nằm trong `www` thì có thể truy cập qua http://studentmanager.test.

Chạy kiểm thử: `php artisan test`.

## Quy trình làm việc

- Nhánh `main` được bảo vệ: **không ai push trực tiếp**. Mọi thay đổi đi qua Pull Request và cần chủ repo duyệt mới được merge.
- Mỗi việc làm trên một nhánh riêng, đặt tên `loai/mo-ta-ngan`, ví dụ `feature/enr-dang-ky-hoc-phan`, `fix/loi-dang-nhap`, `docs/cap-nhat-ba`.
- Mỗi việc gắn với một Issue; Pull Request ghi `Closes #<số issue>` để Issue tự đóng khi gộp.
- Thông điệp commit theo dạng `loại: mô tả ngắn` (`feat`, `fix`, `docs`, `refactor`, `test`, `chore`).

## Tác giả

- GitHub: [@HoaiNam765](https://github.com/HoaiNam765)
- Nhóm thực hiện: *[Họ tên — MSSV — Lớp]*
- Giảng viên hướng dẫn: *[Họ tên]*
