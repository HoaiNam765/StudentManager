# StudentManager — Hệ thống quản lý sinh viên

[![CI](https://github.com/HoaiNam765/StudentManager/actions/workflows/ci.yml/badge.svg)](https://github.com/HoaiNam765/StudentManager/actions/workflows/ci.yml)

Đồ án web quản lý học vụ theo học chế tín chỉ, sử dụng Laravel 13, MySQL, Blade và Tailwind CSS 4; build giao diện bằng Vite. Hệ thống có ba cổng: quản trị/phòng đào tạo, giảng viên và sinh viên.

README hướng dẫn **cài mới và chạy trên máy cá nhân** với Windows/Laragon, Linux hoặc macOS. Migration/seeder tạo cấu trúc bảng và dữ liệu mẫu, không cần xin file SQL từ máy khác.

## Chức năng hiện có

- Đăng nhập, phân quyền theo vai trò/phạm vi, quản lý tài khoản và nhật ký thao tác.
- Quản lý khoa, bộ môn, ngành, khóa, lớp hành chính, sinh viên và giảng viên; nhập/xuất danh sách.
- Môn học, chương trình đào tạo, chương trình khung theo khoa, khóa và học kỳ.
- Mở lớp và đợt đăng ký từ chương trình khung, phân công giảng viên, xếp lịch; sinh viên xem trước và đăng ký khi đủ điều kiện và đến thời gian mở.
- Thời khóa biểu, buổi học, phòng học, ngày nghỉ; điểm danh và chuyên cần.
- Điểm thành phần, kết quả học phần, duyệt/công bố điểm, GPA và bảng điểm.
- Thông báo, thông báo hẹn giờ và dashboard theo vai trò.

Các chức năng mở rộng như học phí, thi và xét tốt nghiệp trong [tài liệu BA](docs/BA.md) là phạm vi kế hoạch; không phải mọi mục trong BA đều đã triển khai.

## Yêu cầu môi trường

| Thành phần | Yêu cầu |
|---|---|
| PHP | 8.3 trở lên, tương thích `composer.lock`; CI dùng PHP 8.3 |
| Composer | Composer 2 |
| MySQL | MySQL 8; CI dùng 8.4. Dự án và kiểm thử dùng MySQL, không dùng SQLite |
| Node.js / npm | Dùng Node.js 24 như CI; Vite trong lockfile yêu cầu Node `^20.19.0` hoặc `>=22.12.0` |
| Git | Clone và cập nhật mã nguồn |

Bật các extension PHP phục vụ dự án: `pdo_mysql`, `mbstring`, `bcmath`, `fileinfo`, `openssl`, `ctype`, `dom`, `xml`, `xmlreader`, `xmlwriter`, `zip`; các extension lõi khác được Composer kiểm tra khi cài. CI còn bật `intl`.

Trên Windows có thể dùng Laragon để quản lý PHP/MySQL. Chọn đúng PHP và bật MySQL trước khi cài. Các hệ điều hành khác cài các công cụ tương đương. Kiểm tra trong terminal:

```bash
php -v
php --ini
composer --version
node -v
npm -v
mysql --version
git --version
```

Nếu terminal không nhận lệnh, thêm thư mục công cụ vào `PATH` rồi mở lại terminal. Với Laragon, có thể mở terminal từ Laragon để dùng PHP/MySQL đã chọn.

## Cài đặt và chạy thử

Thực hiện các lệnh tại thư mục gốc dự án, nơi có file `artisan`. Cần Internet khi tải mã nguồn và thư viện.

### 1. Tải mã nguồn

```bash
git clone https://github.com/HoaiNam765/StudentManager.git
cd StudentManager
```

Trên Laragon có thể clone trong `C:\laragon\www`. Nếu tải source dạng ZIP, giải nén và mở terminal tại thư mục chứa `artisan` và `composer.json`.

### 2. Cài thư viện PHP và tạo cấu hình

```bash
composer install
```

Tạo `.env` từ file mẫu **nếu chưa có `.env`**:

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Linux/macOS/Git Bash:

```bash
cp .env.example .env
```

Tạo khóa ứng dụng cho lần cài mới:

```bash
php artisan key:generate
```

Sửa `.env` cho phù hợp máy đang cài. Ví dụ cấu hình local:

```dotenv
APP_NAME=StudentManager
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_DISPLAY_TIMEZONE=Asia/Ho_Chi_Minh

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_manager
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
DB_QUEUE_RETRY_AFTER=360
FILESYSTEM_DISK=local
MAIL_MAILER=log
```

Giữ nguyên `APP_KEY` đã sinh. `DB_USERNAME`/`DB_PASSWORD` phải khớp tài khoản MySQL trên máy; mật khẩu trống chỉ phù hợp nếu MySQL local thực sự được cấu hình như vậy. Nếu mật khẩu có khoảng trắng hoặc `#`, đặt giá trị trong dấu ngoặc kép.

Bổ sung `DB_QUEUE_RETRY_AFTER=360` để thời gian nhận lại công việc lớn hơn timeout mặc định của tác vụ nhập (120 giây) và xuất dữ liệu (300 giây). Cấu hình local này không cần Redis hoặc dịch vụ SMTP.

Sau khi sửa cấu hình:

```bash
php artisan config:clear
```

### 3. Tạo database, bảng và dữ liệu cơ bản

Bật MySQL. Mở MySQL CLI bằng tài khoản có quyền tạo database:

```bash
mysql -u root -p
```

Nhập mật khẩu khi được hỏi; nếu tài khoản không có mật khẩu, nhấn Enter. Chạy SQL:

```sql
CREATE DATABASE IF NOT EXISTS student_manager
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Có thể tạo database bằng HeidiSQL/phpMyAdmin. Nếu dùng tên khác, sửa `DB_DATABASE` tương ứng. Tài khoản ứng dụng cần quyền tạo/sửa bảng và đọc/ghi dữ liệu trong database này.

Tại terminal dự án:

```bash
php artisan migrate --seed
php artisan storage:link
```

- Migration tạo các bảng và quan hệ; seeder nạp danh mục, dữ liệu học vụ cơ bản và tài khoản dùng thử trong môi trường local.
- `storage:link` tạo liên kết `public/storage` để hiển thị ảnh/file công khai. File riêng tư vẫn nằm trong `storage/app/private`.
- Trên Linux/macOS, tài khoản chạy PHP cần quyền ghi vào `storage` và `bootstrap/cache`.

### 4. Cài và build giao diện

```bash
npm ci
npm run build
```

`npm ci` cài theo `package-lock.json` có trong source. Build xong có thể chạy giao diện mà không cần giữ Vite mở.

### 5. Nạp dữ liệu demo đầy đủ

Để thử lịch học, điểm danh, điểm và GPA với dữ liệu mẫu, chạy sau `migrate --seed`:

```bash
php artisan demo:seed
```

Lệnh tạo dữ liệu cho khoảng 300 sinh viên thuộc hai khóa nhập học 2025/2026, hai học kỳ đã có kết quả và một học kỳ đang diễn ra. Có thể mất vài phút tùy máy. Đây là dữ liệu giả, chỉ chạy trên database dùng thử; lệnh từ chối môi trường `production`.

Nếu đã có năm học `2025-2026`, lệnh bỏ qua thay vì tạo lại toàn bộ demo. Seeder dùng các mốc năm học cố định: khi thử vào thời điểm khác, kiểm tra học kỳ, ngày mở/đóng đợt đăng ký và trạng thái công bố trên giao diện phòng đào tạo.

### 6. Khởi động web và các tiến trình nền

Mở **ba terminal**, tất cả ở thư mục dự án, và giữ chúng chạy:

**Terminal 1 — web:**

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

**Terminal 2 — hàng đợi:**

```bash
php artisan queue:work --sleep=1 --tries=1 --timeout=300
```

**Terminal 3 — tác vụ hẹn giờ:**

```bash
php artisan schedule:work
```

Hàng đợi xử lý các công việc như nhập/xuất dữ liệu. Scheduler gửi thông báo hẹn giờ, công bố điểm theo lịch và chạy các tác vụ học vụ định kỳ. Chỉ chạy web thì những công việc nền này không tự hoàn thành. Nhấn `Ctrl+C` trong từng terminal để dừng.

Mở [trang đăng nhập](http://127.0.0.1:8000/login). Dùng nhất quán địa chỉ đã đặt ở `APP_URL` khi đăng nhập và tải file.

Nếu sửa giao diện và muốn tự cập nhật khi lưu file, mở thêm terminal chạy `npm run dev` và giữ terminal đó chạy. Khi chuyển về bản build, dừng Vite và chạy lại `npm run build`.

Nếu dùng virtual host của Laragon thay cho `artisan serve`, cấu hình document root trỏ vào **`StudentManager/public`**, đặt `APP_URL` đúng tên miền local rồi chạy `php artisan config:clear`. Vẫn cần chạy queue và scheduler riêng.

## Tài khoản dùng thử

Các tài khoản dưới đây dùng mật khẩu **`password`** trên database demo mới tạo. Không dùng tài khoản/mật khẩu demo cho hệ thống thật.

| Vai trò | Tên đăng nhập | Có sau bước | Mục đích |
|---|---|---|---|
| Quản trị hệ thống | `admin` hoặc `admin@studentmanager.test` | `migrate --seed` | Tài khoản, phân quyền, cấu hình, nhật ký |
| Phòng Đào tạo | `acad.test` | `migrate --seed` | Sinh viên, lớp, chương trình khung, đợt đăng ký, thời khóa biểu, duyệt/công bố điểm |
| Trưởng khoa CNTT | `dean.test` | `migrate --seed` | Duyệt và xem dữ liệu trong phạm vi khoa |
| Cố vấn học tập | `adv.test` | `migrate --seed` | Theo dõi lớp hành chính được phân công |
| Giảng viên | `GV0001` đến `GV0020` | `demo:seed` | Lịch dạy, danh sách lớp, điểm danh, nhập điểm |
| Sinh viên | MSSV, ví dụ `2501010001` | `demo:seed` | Đăng ký học phần, lịch học, điểm và chuyên cần |

- Dùng `acad.test` để thử nghiệp vụ phòng đào tạo; `admin` không có toàn bộ quyền sửa dữ liệu học vụ.
- Seeder còn tạo `stu.test`, `lec.test`, nhưng chúng không gắn sẵn hồ sơ sinh viên/giảng viên để demo đầy đủ. Hãy dùng MSSV hoặc mã `GV...`.
- Nếu MSSV ví dụ không có trên database đang dùng, đăng nhập `acad.test`, mở danh sách sinh viên và lấy MSSV của sinh viên đã nhập học.
- Khi chỉ seed cơ bản, giảng viên được cấp mật khẩu tạm qua email. `demo:seed` đặt mật khẩu demo cho tài khoản còn mật khẩu tạm; không ghi đè mật khẩu người dùng đã tự đổi.
- `MAIL_MAILER=log` ghi email vào `storage/logs/laravel.log`, không gửi thư thật. Muốn nhận thư thật, cấu hình mailer/SMTP trong `.env`, xóa cache cấu hình và khởi động lại worker.

## Kiểm tra sau cài đặt

1. Mở `/login`, đăng nhập `acad.test`, kiểm tra danh sách sinh viên, môn học và lớp học phần có dữ liệu.
2. Đăng nhập bằng MSSV, kiểm tra lịch học/kết quả học tập sau khi nạp demo.
3. Đăng nhập `GV0001`, kiểm tra lịch dạy và lớp được phân công.
4. Thử xuất danh sách, kiểm tra hàng đợi hoàn thành và tải được file.
5. Khi thử đăng ký học phần, kiểm tra đúng khóa/ngành, đợt đã công bố và đang trong thời gian cho phép. Có lớp hiển thị nhưng chưa đăng ký được có thể do điều kiện học vụ hoặc lịch mở đợt.

Các lệnh hỗ trợ kiểm tra:

```bash
composer check-platform-reqs
php artisan migrate:status
php artisan schedule:list
php artisan queue:failed
```

Kịch bản chi tiết: [demo](docs/kich-ban-demo.md), [nghiệm thu P1](docs/nghiem-thu-p1.md), [đăng ký theo chương trình khung](docs/dang-ky-hoc-phan-theo-khung.md), [ví dụ học kỳ mới](docs/vi-du-hoc-ky-moi.md).

## Chạy lại và cập nhật source

Những lần mở máy sau, bật MySQL rồi chạy lại ba tiến trình ở bước 6. Không cần tạo lại `.env`, `APP_KEY` hay chạy lại demo.

Khi lấy phiên bản mới, lưu/commit thay đổi cá nhân trước và sao lưu database nếu có dữ liệu cần giữ. Dừng web/queue/scheduler local, sau đó chạy:

```bash
git pull --ff-only
composer install
npm ci
php artisan optimize:clear
php artisan migrate
npm run build
```

Đối chiếu `.env.example` để bổ sung biến mới vào `.env` hiện có, rồi khởi động lại ba tiến trình. Không chép đè `.env`, không sinh lại `APP_KEY`, không dùng `migrate:fresh` để cập nhật vì lệnh đó xóa toàn bộ bảng. Nếu bản cập nhật yêu cầu thêm dữ liệu danh mục, chạy seeder được chỉ rõ trong ghi chú thay đổi.

## Kiểm thử và chuẩn mã

```bash
composer lint
php artisan config:clear
php artisan test
```

- Kiểm thử dùng MySQL riêng **`student_manager_test`** theo `phpunit.xml`; MySQL phải chạy. Host/cổng/tài khoản dùng theo cấu hình kiểm thử; nếu không cấu hình riêng, chúng lấy từ `.env`.
- Test tự tạo database nếu chưa có; tài khoản MySQL phải có quyền tạo database hoặc cần tạo sẵn `student_manager_test` và cấp quyền trước.
- Test dựng lại bảng và xóa dữ liệu trong database kiểm thử. Chỉ dùng database riêng có hậu tố `_test`; không trỏ test vào database đang sử dụng.
- `composer lint` kiểm tra chuẩn mã bằng Laravel Pint. CI chạy lint, build và test trên mỗi PR và mỗi lần cập nhật `main`.

## Lỗi thường gặp

| Hiện tượng | Cách xử lý |
|---|---|
| Không nhận lệnh `php`, `composer`, `npm`, `mysql` | Kiểm tra cài đặt và `PATH`, mở lại terminal; trên Laragon thử terminal tích hợp |
| Composer báo thiếu extension / `could not find driver` | Xem `php --ini`, bật extension trong đúng `php.ini` của PHP CLI; `pdo_mysql` cần cho MySQL. Chạy lại `composer install` |
| `SQLSTATE[HY000] [2002]` / connection refused | Bật MySQL; kiểm tra `DB_HOST`, `DB_PORT` rồi `php artisan config:clear` |
| `Access denied for user` | Sửa tài khoản/mật khẩu và quyền database trong MySQL; đối chiếu `.env` |
| `Unknown database` / thiếu bảng | Tạo đúng database ở bước 3; chạy `php artisan migrate --seed` khi cài mới |
| `No application encryption key` | Kiểm tra `.env`; với lần cài mới chạy `php artisan key:generate` |
| `Vite manifest not found`, thiếu CSS/JS | Chạy `npm ci` và `npm run build`; nếu đang phát triển, giữ `npm run dev` chạy |
| Trình duyệt vẫn gọi Vite đã tắt | Dừng Vite; nếu còn file `public/hot` cũ, xóa riêng file đó rồi build lại |
| Ảnh tải lên không hiển thị | Chạy `php artisan storage:link`, kiểm tra `APP_URL`. Trên Windows nếu bị từ chối tạo liên kết, bật Developer Mode hoặc mở terminal có quyền tạo liên kết |
| Import/export đứng ở trạng thái chờ | Kiểm tra `queue:work`; xem `php artisan queue:failed` và `storage/logs/laravel.log` |
| Thông báo/điểm hẹn giờ chưa công bố | Giữ `schedule:work` chạy; xem `php artisan schedule:list` và thời gian đã cấu hình |
| Không nhận được email | Mặc định mail ghi vào `storage/logs/laravel.log`; cấu hình SMTP nếu cần gửi thật |
| HTTP 419 / phiên đăng nhập không ổn định | Dùng nhất quán `127.0.0.1` hoặc `localhost`, kiểm tra `APP_URL`, xóa cookie của site và đăng nhập lại |
| HTTP 403 / không được sửa dữ liệu | Kiểm tra vai trò/phạm vi; dùng `acad.test` cho nghiệp vụ đào tạo, MSSV/`GV...` có hồ sơ cho cổng tương ứng |
| Cổng 8000 đã được sử dụng | Dừng tiến trình giữ cổng hoặc chạy `php artisan serve --port=8001`, đổi `APP_URL` tương ứng và xóa cache cấu hình |
| Không ghi được log/cache | Cấp quyền ghi phù hợp cho `storage` và `bootstrap/cache` cho tài khoản chạy PHP |

## Cấu trúc source và tài liệu

```text
StudentManager/
├── app/Modules/       # Nghiệp vụ theo module
├── app/Support/       # Model, service và tiện ích dùng chung
├── app/Http/          # Controller/middleware dùng chung
├── config/           # Cấu hình ứng dụng
├── database/         # Migrations, seeders, factories
├── docs/             # BA, kịch bản demo và nghiệm thu
├── resources/        # Blade, CSS, JavaScript
├── routes/           # Web, admin, teacher, student và lịch tác vụ
├── public/           # Document root của web server
├── storage/          # Log, file tải lên, file xuất và cache
├── tests/            # Kiểm thử Unit/Feature
└── .github/          # CI và mẫu Issue/PR
```

- [Tổ chức module](app/Modules/README.md)
- [Phân tích nghiệp vụ](docs/BA.md)
- [Quy ước đóng góp](CONTRIBUTING.md): tạo nhánh riêng và PR vào `main`, kiểm tra CI trước khi merge.
- Không commit `.env`, thông tin truy cập, dữ liệu thật, `vendor/` hay `node_modules/`.

## Tác giả

- GitHub: [@HoaiNam765](https://github.com/HoaiNam765)
- Nhóm thực hiện: *[Họ tên — MSSV — Lớp]*
- Giảng viên hướng dẫn: *[Họ tên]*
