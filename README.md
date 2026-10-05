# StudentManager — Hệ thống Quản Lý Sinh Viên

[![CI](https://github.com/HoaiNam765/StudentManager/actions/workflows/ci.yml/badge.svg)](https://github.com/HoaiNam765/StudentManager/actions/workflows/ci.yml)

Hệ thống web quản lý trọn vòng đời học vụ của sinh viên đại học chính quy theo **học chế tín chỉ**: nhập học, lớp, đăng ký học phần, thời khóa biểu, điểm danh, thi, điểm, học phí, giấy tờ, thông báo và xét tốt nghiệp.

Đây là đồ án môn học. Dự án đang ở **giai đoạn P1 (MVP — học vụ cốt lõi)**: đã có khung Laravel, lớp nền dùng chung và môi trường kiểm thử tự động; các module được xây dựng theo [Issue](https://github.com/HoaiNam765/StudentManager/issues) và [milestone](https://github.com/HoaiNam765/StudentManager/milestones).

## Trạng thái

| Hạng mục | Trạng thái |
|---|---|
| Phân tích nghiệp vụ (BA v1.0) | Hoàn thành, chờ duyệt — xem [docs/BA.md](docs/BA.md) |
| SRS / use case | Chưa bắt đầu |
| Thiết kế CSDL | Làm dần theo từng module |
| Thiết kế giao diện | Đang làm (nhóm FE) |
| Lập trình | Đang làm giai đoạn P1 |
| Kiểm thử, nghiệm thu | Kiểm thử tự động chạy trên mỗi Pull Request |

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
│   ├── Support/            # Lớp nền dùng chung: model, trait, Service, Policy, định dạng
│   ├── Http/               # Controller, middleware dùng chung
│   └── Models/             # Model dùng chung
├── bootstrap/app.php       # Nạp route của 3 cổng giao diện
├── config/
├── database/               # migrations, factories, seeders
├── docs/
│   └── BA.md               # Tài liệu phân tích nghiệp vụ
├── lang/vi/                # Bản dịch tiếng Việt (thông báo lỗi, xác thực...)
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
├── .github/                # Mẫu Issue, mẫu PR, CODEOWNERS, workflow CI
├── .env.example
├── CONTRIBUTING.md         # Quy ước làm việc: nhánh, commit, PR, kiểm thử
└── README.md
```

## Cài đặt và chạy thử

Yêu cầu: PHP 8.3, Composer, MySQL 8 (Laragon có sẵn) và Node.js từ 20.19 trở lên (khuyên dùng 24). Với Laragon, bấm **Start All** để bật MySQL trước khi làm các bước dưới.

1. Tải mã nguồn và vào thư mục dự án:

   ```bash
   git clone https://github.com/HoaiNam765/StudentManager.git
   cd StudentManager
   ```

2. Cài thư viện PHP và tạo file cấu hình:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Trên PowerShell dùng `copy .env.example .env` thay cho `cp`. Nếu MySQL của bạn có mật khẩu root, sửa `DB_PASSWORD` trong `.env`. **Không commit file `.env`.**

3. Tạo CSDL, tạo bảng và dữ liệu dùng thử:

   ```bash
   php artisan migrate --seed
   ```

   Nếu CSDL `student_manager` chưa có, Laravel hỏi *Would you like to create it?*: chọn **Yes**.

4. Cài và build giao diện:

   ```bash
   npm install
   npm run build
   ```

5. Chạy thử: mở hai cửa sổ dòng lệnh, một cửa sổ chạy `npm run dev` (tự cập nhật khi sửa giao diện), cửa sổ kia chạy `php artisan serve`. Mở http://localhost:8000; nếu thư mục nằm trong `www` của Laragon thì vào được cả http://studentmanager.test.

Tài khoản dùng thử (chỉ có ở môi trường phát triển, tạo bởi `DevUserSeeder`): `admin@studentmanager.test`, mật khẩu `password`.

**Sau mỗi lần `git pull`:** chạy `composer install` (nếu `composer.lock` đổi), `npm install` (nếu `package-lock.json` đổi) và `php artisan migrate` (nếu có migration mới). So `.env` của bạn với `.env.example` xem có biến mới không.

### Kiểm thử và chuẩn mã

```bash
php artisan test
composer lint
```

- `php artisan test` chạy trên **CSDL MySQL riêng `student_manager_test`**, tự tạo ở lần chạy đầu; MySQL phải đang chạy. Test xóa sạch CSDL mỗi lần chạy, nên hệ thống **từ chối chạy** nếu tên CSDL không kết thúc bằng `_test`.
- `composer lint` kiểm tra chuẩn mã bằng Laravel Pint; sửa tự động bằng `vendor/bin/pint`.
- Cả hai lệnh tự chạy trên GitHub Actions (CI) với mỗi Pull Request và mỗi lần cập nhật `main`.

### Lỗi thường gặp

| Thông báo | Cách xử lý |
|---|---|
| `SQLSTATE[HY000] [2002]` hoặc *No connection could be made* | MySQL chưa chạy: bật Laragon (**Start All**) |
| `Unknown database 'student_manager'` | Chạy `php artisan migrate` và chọn **Yes** để tạo CSDL |
| `Vite manifest not found` | Chạy `npm run build` (hoặc đang chạy `npm run dev`) |
| `Class ... not found` sau khi pull | Chạy `composer install` |
| *Từ chối chạy test trên CSDL …* | `DB_DATABASE` khi chạy test phải kết thúc bằng `_test`; xem `phpunit.xml` |

## Quy trình làm việc

Tóm tắt (chi tiết trong [CONTRIBUTING.md](CONTRIBUTING.md)):

- Nhánh `main` được bảo vệ: **không ai push trực tiếp**. Mọi thay đổi đi qua Pull Request, cần chủ repo duyệt và CI đạt mới được merge.
- Mỗi Issue làm trên một nhánh riêng `loại/khu-vuc-mo-ta`, ví dụ `feature/be-dang-ky-hoc-phan`, `fix/loi-tinh-gpa`; Pull Request ghi `Closes #<số issue>`.
- Thông điệp commit dạng `loại(module): mô tả`, ví dụ `feat(enr): kiểm tra trùng lịch khi đăng ký`.

## Tác giả

- GitHub: [@HoaiNam765](https://github.com/HoaiNam765)
- Nhóm thực hiện: *[Họ tên — MSSV — Lớp]*
- Giảng viên hướng dẫn: *[Họ tên]*
