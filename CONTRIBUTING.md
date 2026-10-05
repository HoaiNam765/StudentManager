# Quy ước làm việc

Áp dụng cho mọi thành viên nhóm (FE và BE). Cách cài đặt môi trường xem [README](README.md#cài-đặt-và-chạy-thử).

## 1. Quy trình: Issue → nhánh → Pull Request

1. Chỉ làm việc đã có Issue và được gán cho mình. Bắt đầu thì bình luận vào Issue để mọi người biết.
2. Tạo nhánh từ `main` mới nhất:

   ```bash
   git switch main
   git pull
   git switch -c feature/be-ten-viec
   ```

3. Commit nhỏ, mỗi commit một ý (xem mục 2).
4. Trước khi đẩy lên, chạy kiểm tra ở máy mình (mục 4).
5. Đẩy nhánh và mở Pull Request: điền đủ mẫu PR, ghi `Closes #<số Issue>`. Mỗi Issue một PR; không gộp nhiều Issue vào một PR.
6. Chờ duyệt. `main` được bảo vệ: **không ai push trực tiếp**, PR cần chủ repo duyệt và CI phải đạt (dấu tích xanh) mới được merge.
7. Nếu `main` có thay đổi mới trong lúc đang làm: `git fetch` rồi `git merge origin/main` vào nhánh của mình.

## 2. Đặt tên

**Nhánh:** `loại/khu-vuc-mo-ta`, tiếng Việt không dấu, nối bằng gạch ngang.

| Loại | Khi nào | Ví dụ |
|---|---|---|
| `feature/` | Chức năng mới | `feature/be-dang-ky-hoc-phan`, `feature/fe-trang-dang-nhap` |
| `fix/` | Sửa lỗi | `fix/loi-tinh-gpa` |
| `docs/` | Tài liệu | `docs/cap-nhat-ba` |
| `chore/` | Cấu hình, công cụ | `chore/cap-nhat-ci` |

**Thông điệp commit:** `loại(module): mô tả ngắn bằng tiếng Việt`

- `loại`: `feat` (chức năng), `fix` (sửa lỗi), `docs`, `test`, `refactor`, `style` (chỉ định dạng), `chore` (cấu hình, công cụ).
- `module`: mã module trong BA viết thường (`auth`, `enr`, `grd`…), `ui` cho giao diện, `sys` cho phần dùng chung.
- Ví dụ: `feat(enr): kiểm tra trùng lịch khi đăng ký`, `fix(grd): làm tròn điểm tổng kết sai`, `feat(ui): trang đăng nhập`.

## 3. Viết mã

- Chuẩn mã: Laravel Pint (preset `laravel`, file `pint.json`). Chạy `vendor/bin/pint` để tự sửa định dạng.
- Vị trí: nghiệp vụ trong `app/Modules/<TênModule>`, lớp nền dùng chung trong `app/Support`, migration ở `database/migrations`, view ở `resources/views/<cổng>/<module>`. Xem [app/Modules/README.md](app/Modules/README.md).
- Dùng lớp nền có sẵn (`StandardModel`, `HasActiveStatus`, `HasVietnameseSearch`, `BaseService`, `DenyByDefaultPolicy`, `Format`…) thay vì tự viết lại.
- Quy tắc nghiệp vụ đặt trong `Services`, không đặt trong Controller. Từ chối thì báo lý do và cách khắc phục bằng tiếng Việt.
- Ngưỡng và thông số lấy từ cấu hình, không viết cứng trong mã.

## 4. Kiểm tra trước khi mở PR

```bash
composer lint
php artisan test
npm run build
```

- `composer lint` kiểm tra chuẩn mã (giống CI). Sửa nhanh bằng `vendor/bin/pint`.
- `php artisan test` chạy trên CSDL MySQL riêng `student_manager_test` (tự tạo lần đầu). Test xóa sạch CSDL đang kết nối, nên hệ thống chặn nếu tên CSDL không kết thúc bằng `_test`.
- `npm run build` bắt buộc nếu có sửa giao diện.
- Quy tắc lõi (điều kiện đăng ký, tính điểm, GPA, học phí…) phải có kiểm thử đơn vị; luồng chính có kiểm thử Feature.
- Test tự tạo bảng trong `setUp` dùng `DatabaseMigrations` thay cho `RefreshDatabase` (MySQL tự commit khi tạo bảng).

## 5. CSDL, migration và dữ liệu mẫu

- Ràng buộc đặt ở CSDL: khóa ngoại, unique.
- **Không sửa migration đã được merge vào `main`**; cần thay đổi thì tạo migration mới.
- Mỗi module có seeder riêng `database/seeders/<TênModule>Seeder.php`, thêm vào `DatabaseSeeder::MODULE_SEEDERS` đúng thứ tự phụ thuộc. Seeder chạy lặp lại được không lỗi trùng.
- Chỉ dùng dữ liệu giả (Faker `vi_VN`). Không đưa dữ liệu thật của sinh viên vào repo.
- Không commit `.env`, mật khẩu, khóa API, `vendor/`, `node_modules/` hay file tự sinh.

## 6. Khi nào một Issue được coi là xong

Theo định nghĩa hoàn thành ở `docs/BA.md` mục 11.3: đúng mô tả và quy tắc nghiệp vụ (kể cả trường hợp từ chối), có kiểm thử, đã kiểm tra quyền theo vai trò, có nhật ký cho thao tác ghi quan trọng, CI đạt và PR đã được merge.
