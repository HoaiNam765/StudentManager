# CLAUDE.md — StudentManager

Ghi nhớ cho Claude Code khi làm việc với dự án này. Cập nhật mục "Nhật ký công việc" sau mỗi Issue.

## Dự án

- Đồ án môn học: hệ thống web **Quản Lý Sinh Viên** theo học chế tín chỉ.
- Repo: `HoaiNam765/StudentManager` (GitHub, công khai). Thư mục máy: `C:\laragon\www\StudentManager`.
- Yêu cầu nghiệp vụ: `docs/BA.md` v1.0 (23 module, mã FR-/BR-/NFR-/UAT-). **Giữ nguyên mã yêu cầu**; đọc mục BA ghi trong Issue trước khi làm.
- Lộ trình: P1 (MVP) → P2 → P3 → P4 (BA mục 5.6). Thứ tự sprint P1 ở BA mục 11.1.

## Làm việc với người dùng

- Người dùng là sinh viên, viết tiếng Việt: **trả lời bằng tiếng Việt**, làm kỹ, nói rõ đã kiểm tra gì và chưa kiểm tra gì.
- "Hướng dẫn tôi…" = chuẩn bị nội dung và dạy từng bước, **không** tự thao tác trên GitHub. "Làm giúp…" = làm thật.
- **Không tự commit, push hay mở PR** trừ khi người dùng bảo. Mặc định: làm xong trong working tree, soạn nội dung PR ở `C:\laragon\www\issues-be\pr-<số issue>.md`, đưa lệnh commit/PR cho người dùng tự chạy.
- **Không thêm** `Co-Authored-By: Claude…` hay "Generated with Claude Code" vào commit hoặc PR.
- Không đụng vào dữ liệu thật: kiểm tra migration trên CSDL tạm (`sm_check<số>`) rồi xóa; với CSDL dev chỉ chạy `php artisan migrate` (không `migrate:fresh`).

## Nhóm và Issue

| Người | GitHub | Việc |
|---|---|---|
| A (chủ repo, admin) | `HoaiNam765` | BE: nền tảng, AUTH, ENR, NOT, FEE, REQ, SYS… |
| B | `nguyenphamhuynhdat-coder` | BE: FAC, ACY, ROM, TCH, CLS, TTB, EXM, ATT, RPT… |
| C | `BuiPhuNhien` | BE: cấu hình, import/export, STU, GRD, DOC, GRA, SCH, EVA… |
| FE | `mrc4789` | Toàn bộ giao diện |

- Issue FE: #2, #4–#67 (65 issue). Issue BE: #68–#218 (151 issue, chia đều theo điểm công sức). PR bắt đầu từ #219.
- Nhãn `frontend`/`backend`, `P1`–`P4`; milestone theo giai đoạn. Dữ liệu và script tạo issue: `C:\laragon\www\issues-fe`, `C:\laragon\www\issues-be` (ngoài repo).

## Quy trình Git/GitHub

- `main` được bảo vệ: bắt buộc PR, cần chủ repo duyệt (CODEOWNERS), cấm force push. Chủ repo merge PR của chính mình bằng **bypass** (admin), kiểu **Squash and merge**.
- CI: `.github/workflows/ci.yml` chạy Pint + build Vite + toàn bộ test trên MySQL 8.4 cho mỗi PR và `main`. (Chưa đặt CI làm điều kiện bắt buộc của nhánh.)
- Nhánh `feature/be-<việc>` / `feature/fe-<việc>`; commit `loại(module): mô tả` (xem `CONTRIBUTING.md`). Mỗi Issue một nhánh, một PR ghi `Closes #<số>`.

## Môi trường

- Windows 11 + Laragon: PHP 8.3, Composer, MySQL 8.4 (`C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe`, root không mật khẩu), Node 24, `gh` đã đăng nhập.
- CSDL: `student_manager` (dev), `student_manager_test` (test, `Tests\TestCase` tự tạo; test bị chặn nếu tên CSDL không kết thúc bằng `_test`).
- Lệnh kiểm tra: `php artisan test`, `composer lint` (Pint), `npm run build`.
- Đọc/ghi `.xlsx` (OpenSpout: xuất dữ liệu, Importer) cần `extension=zip`; PHP của Laragon mặc định chưa bật (máy hiện đã bật trong `php.ini`). Nếu thiếu thì bật trong `php.ini`, hoặc chạy tạm không sửa `php.ini`: đặt `PHP_INI_SCAN_DIR` trỏ tới thư mục chứa một file `.ini` có dòng `extension=zip` (cờ `-d` không truyền được cho `php artisan test` vì nó chạy phpunit ở tiến trình con).
- Tài khoản dùng thử: tên đăng nhập `admin` / `admin@studentmanager.test` (vai trò ADMIN), tạo bởi `DevUserSeeder`; mật khẩu xem trong seeder.
- Chạy thử trên trình duyệt: `php artisan serve --port=8010` chạy nền rồi mở `/login`; khung trình duyệt bị che thì không bấm được nút, đọc trang bằng JavaScript và kiểm tra `audit_logs`.

## Kiến trúc mã

- Laravel 13. Nghiệp vụ: `app/Modules/<Module>` (Models, Services, Policies, Http…). Lớp nền dùng chung: `app/Support`. Cách dùng: `app/Modules/README.md`.
- Route theo cổng: `routes/web.php` (công khai), `student.php`, `teacher.php`, `admin.php` (nạp trong `bootstrap/app.php`).
- `app/Support`: `StandardModel` (người tạo/sửa, xóa mềm, **tự ghi nhật ký kiểm toán**), `HasActiveStatus`, `HasEffectivePeriod`, `HasVietnameseSearch` (cột `search_text`), `Format`, `BaseService` + `BusinessRuleException` (lỗi nghiệp vụ → 422 kèm cách khắc phục), `DenyByDefaultPolicy`, macro migration (`standardColumns`, `activeStatus`, `effectivePeriod`, `searchText`), `Audit/` (AuditLog, AuditLogger, AuditEvent).
- Phân quyền (`app/Modules/Auth`): `AccessControl` (scoped), `ModulePolicy`, `HasDataScope`, `RoleService`, middleware `permission:MODULE.action[,ALL]`; ma trận mặc định trong `database/seeders/AuthSeeder.php`. Mọi lỗi 403 ghi nhật ký "Từ chối truy cập".
- Đăng nhập (`app/Modules/Auth`): `LoginService` (khóa tạm, thông báo lỗi giống nhau, `unlock()`), `PasswordService` (`change()`, `setTemporaryPassword()`, `mustChange()`, `rule()` = `Password::defaults()`), `PortalResolver` (vai trò → `admin.home` / `teacher.home` / `student.home`), middleware `password.changed` gắn sẵn cho 3 cổng (JSON trả 428). Route: `login`, `login.store` (throttle `login`), `logout`, `password.change`, `password.update`, `root` (`/`). View tạm của BE: `auth/login`, `auth/change-password`, `portal-home` (FE sẽ thay).
- Tiếng Việt: `lang/vi` (gói dev `laravel-lang/common`), `APP_LOCALE=vi`; lưu thời gian UTC, hiển thị UTC+7 (`config/studentmanager.php`).
- Seeder: `DatabaseSeeder::MODULE_SEEDERS` theo thứ tự phụ thuộc BA 5.5, rồi `DevUserSeeder`.
- Mỗi module mới có `app/Modules/<Module>/<Module>ServiceProvider.php` (khai báo trong `bootstrap/providers.php`): đăng ký Policy, Importer, tham chiếu (`ReferenceRegistry`). Không dồn thêm vào `AppServiceProvider`.
- `App\Support\References\ReferenceRegistry`: module có khóa ngoại tới bảng của module khác đăng ký ở đó; dùng cho quy tắc "đã dùng thì chỉ ngừng, không xóa/không đổi mã". Importer đọc Excel/CSV kế thừa `SpreadsheetImporter`.

## Quy ước kỹ thuật và bẫy đã gặp

- Tìm không dấu dùng `search_text`: cả `utf8mb4_unicode_ci` lẫn `utf8mb4_vietnamese_ci` coi `d` ≠ `đ`. Sắp xếp tiếng Việt dùng `utf8mb4_vietnamese_ci` (chỉ MySQL).
- `search_text`, người tạo/sửa và nhật ký kiểm toán chỉ chạy qua sự kiện Eloquent: **không** dùng `WithoutModelEvents` trong seeder; cập nhật hàng loạt bằng query builder không được ghi nhật ký.
- `audit_logs` có trigger MySQL chặn UPDATE/DELETE.
- Test tự tạo bảng trong `setUp` dùng `DatabaseMigrations` (RefreshDatabase hỏng trên MySQL vì DDL tự commit).
- Service cần trạng thái trong một yêu cầu (AuditLogger, AccessControl) phải đăng ký `scoped` trong `AppServiceProvider`.
- Mọi `fail()` của Service phải có cách khắc phục (GC-04); test `RoleServiceTest` kiểm tra điều này.
- PowerShell 5.1: `ConvertFrom-Json` không bung mảng cấp ngoài; tham số chứa dấu nháy kép bị tách sai (dùng file: `git commit -F`, `gh --body-file`, `gh api --input`); không pipe lệnh tạo dữ liệu qua `Select-Object -First` (giết tiến trình giữa chừng).
- Môi trường chặn `rmdir /s` và `Remove-Item Env:`: dùng thư mục mới, `$env:X = $null`.
- Khi chép khung dự án, đừng loại file theo tên trên toàn cây (từng làm mất 12 file `.gitignore` con).
- Test dùng `travelTo()` lùi đồng hồ: vai trò gán bằng `RoleService::assign` có `valid_from` = ngày thật lúc tạo, nên sau khi lùi về quá khứ quyền chưa có hiệu lực. Gọi `travelBack()` trước khi kiểm tra quyền, đừng `travelTo` sang một ngày cố định khác (chỉ xanh đúng hôm đó). `AuditLogSearchTest` từng đỏ trên `main` ngày 06/10 vì lỗi này.
- Khi PR của bạn trong nhóm đỏ CI: đọc log (`gh run view <id> --log-failed`) và chạy lại trên `main` trước khi kết luận lỗi do PR. Trình format của IDE đổi `fn (` thành `fn(` làm Pint đỏ; chạy `composer lint` trước khi push.

## Nhật ký công việc

| Issue | PR | Nội dung |
|---|---|---|
| — | #1 | Khung Laravel 13, `app/Modules`, route 3 cổng, CODEOWNERS |
| #69 | #219 | Lớp nền dùng chung (`app/Support`), tiếng Việt; sửa các `.gitignore` con bị thiếu |
| #68 | #220 | Test trên MySQL, CI GitHub Actions, `CONTRIBUTING.md`, `composer lint`, seeder theo module |
| #70 | #221 | Nhật ký kiểm toán chỉ ghi thêm, tự ghi cho `StandardModel`, tra cứu ở module System |
| #71 | #222 | RBAC: vai trò, ma trận quyền, phạm vi dữ liệu, API quản trị phân quyền; thêm `CLAUDE.md` |
| #72 | #223 | Đăng nhập bằng tên đăng nhập/email, vào cổng theo vai trò, khóa tạm, mật khẩu tạm, đổi mật khẩu, chính sách mật khẩu, giới hạn tần suất |
| #75 | #224 | Năm học – học kỳ (module `AcademicYear`, của B): tạo/xem/sửa năm học, tạo học kỳ, học kỳ hiện hành, máy trạng thái theo ngày, không xóa chỉ khóa, quyền `ACY.*`, nhật ký kiểm toán. Chưa có: sửa học kỳ, trạng thái/khóa năm học. Mình đã sửa thẳng trên nhánh của B (Pint, test, vài việc nhỏ theo review) |
| #80 | #225 | Trung tâm import (của C): lô có mã, báo lỗi theo dòng/cột, chỉ lưu dòng hợp lệ hoặc lưu toàn bộ, chạy nền, hoàn tác theo lô. Mình đã sửa thẳng trên nhánh của C theo review |
| #81 | #227 | Dịch vụ xuất Excel/CSV/PDF dùng chung (`App\Support\Services\ExportService`, của C): quyền X và phạm vi dữ liệu, cột nhạy cảm, phông tiếng Việt, audit (`Exported`, `Downloaded`), tác vụ nền theo ngưỡng (PDF ngưỡng riêng + mức trần), tệp nền giữ 7 ngày rồi dọn bằng `exports:prune`. Chưa có route/controller cho `status`/`download`. Mình đã sửa thẳng trên nhánh của C theo review (đúng lớp ngoại lệ, PDF, hạn giữ tệp) |
| #78 | (chưa mở) | Danh mục dùng chung (SYS): `LookupCategory`/`LookupValue` (giới tính, 54 dân tộc, tôn giáo, quốc tịch, đối tượng/khu vực ưu tiên, loại hợp đồng), đơn vị hành chính hai cấp 2025 + ba cấp cũ, Importer danh mục, `ReferenceRegistry`, `SpreadsheetImporter`; sửa số dòng báo lỗi của trung tâm import khi file có dòng trống — nhánh `feature/be-danh-muc-dung-chung` |
| #79 | (chưa mở) | Tham số hệ thống (`SettingService`, ghi đè config lúc khởi động) và bộ quy chế đào tạo theo khóa (`PolicySet`/`PolicyItem`, `PolicyResolver`, mặc định theo phụ lục C, ban hành không hồi tố, báo ADMIN khác qua mail log) — nhánh `feature/be-cau-hinh-quy-che` |
| #77 | (chưa mở) | Phòng học (module `Room`, của B): cơ sở, tòa nhà, loại phòng, phòng (sức chứa học/thi), tình trạng, lịch bảo trì, `RoomAvailability` cho TTB/EXM (BR-ROM-02), không xóa phòng đã dùng (BR-ROM-04), seeder 20 phòng — nhánh `feature/be-phong-hoc` |
| #73 | (chưa mở) | Khoa, bộ môn, ngành, chuyên ngành, hệ đào tạo (module `Faculty`, của B): mã ổn định, ngừng khoa liệt kê dữ liệu cần chuyển, `FacultyAccess` cho phạm vi FACULTY (rỗng tới #74), `ReferenceRegistry::activeUsages`, xuất Excel (FAC.export, ma trận chưa cấp), seeder 3 khoa 6 ngành — nhánh `feature/be-khoa-bo-mon-nganh` |
| #74 | (chưa mở) | Lãnh đạo đơn vị theo nhiệm kỳ (`LeadershipTerm`, `LeadershipService`): mỗi nhiệm kỳ sinh dòng DEAN cùng ngày hiệu lực, tối đa một trưởng mỗi đơn vị, thay trưởng từ ngày D, `FacultyAccess` lấy đơn vị từ nhiệm kỳ, lệnh `faculty:sync-leadership`, `LeaderEligibility` chờ TCH — nhánh `feature/be-lanh-dao-don-vi` |

## Việc tiếp theo của A (P1)

#85, #86 CTĐT → #87 import giảng viên → #94–#97 sinh viên, gán CTĐT → #103–#106 đăng ký học phần → #115–#117 thông báo → #118 dashboard.

Còn mở chung: chốt câu hỏi BA mục 12 (Q-01, Q-03, Q-19, Q-20, Q-22); điền "Nhóm thực hiện", "Giảng viên hướng dẫn" ở `docs/BA.md` và `README.md`; PR #3 (FE) đang Draft.
