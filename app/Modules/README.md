# app/Modules

Mỗi module nghiệp vụ trong `docs/BA.md` có một thư mục riêng, namespace `App\Modules\<TênModule>`. Thư mục dưới đây là các module của giai đoạn P1 (xem BA mục 5.6).

| Thư mục | Mã BA | Tên module |
|---|---|---|
| `Auth` | AUTH | Xác thực và phân quyền |
| `System` | SYS | Quản trị hệ thống |
| `Faculty` | FAC | Khoa, bộ môn, ngành |
| `AcademicYear` | ACY | Năm học, học kỳ |
| `Room` | ROM | Phòng học |
| `Subject` | SUB | Học phần |
| `Curriculum` | CUR | Chương trình đào tạo |
| `Teacher` | TCH | Giảng viên |
| `SchoolClass` | CLS | Khóa, lớp hành chính, lớp học phần |
| `Student` | STU | Sinh viên |
| `Enrollment` | ENR | Đăng ký học phần |
| `Timetable` | TTB | Thời khóa biểu |
| `Grade` | GRD | Điểm |
| `Notification` | NOT | Thông báo |
| `Report` | RPT | Dashboard và báo cáo |

Module của P2 trở đi (`ATT`, `EXM`, `FEE`, `DOC`, `GRA`, `REQ`, `SCH`, `EVA`) được thêm khi bắt đầu giai đoạn đó.

## Quy ước

- Bên trong một module: `Models/`, `Services/`, `Policies/`, `Http/Controllers/`, `Http/Requests/` (tạo khi cần).
- Quy tắc nghiệp vụ lõi (kiểm tra điều kiện đăng ký, tính điểm, GPA, học phí…) viết thành lớp trong `Services/`, không đặt trong controller, để kiểm thử đơn vị được.
- Module chỉ phụ thuộc module khác theo chiều của ma trận phụ thuộc (BA mục 5.5); không tham chiếu vòng.
- Migration vẫn đặt ở `database/migrations/`; view đặt ở `resources/views/<cổng>/<module>/`.

### Thành viên và cố vấn lớp hành chính (CLS)

`ClassRosterService` xử lý xem trước, gán/chuyển/gỡ thành viên; khi nhập học, `AdmissionService` dùng cùng dịch vụ để tự
chọn lớp phù hợp theo khóa, ngành, hệ và sĩ số. Lịch sử giữ ngày hiệu lực và lý do kết thúc; một sinh viên chỉ có một
phân công đang mở. `AdvisorAssignmentService` quản lý lịch sử cố vấn, chỉ nhận giảng viên đang làm việc, và lớp thiếu
cố vấn được truy vấn qua `admin-classes/missing-advisors`. Các API quản trị nằm dưới `/admin/admin-classes`; vai trò
`ADV` và dữ liệu sinh viên trong phạm vi cố vấn được lọc bằng `ADVISEE`.

## Lớp nền dùng chung (`app/Support`)

Dùng lại thay vì tự viết trong từng module. Ví dụ đầy đủ có test nằm ở `tests/Support` và `tests/Feature/Support`.

| Cần gì | Dùng gì |
|---|---|
| Model nghiệp vụ có người tạo/sửa (GC-11) và xóa mềm (GC-02) | `extends App\Support\Models\StandardModel` + `$table->standardColumns()` |
| Danh mục Hoạt động / Ngừng hoạt động (GC-05) | `use HasActiveStatus` + `$table->activeStatus()` |
| Dữ liệu có hiệu lực theo thời gian (GC-06) | `use HasEffectivePeriod` + `$table->effectivePeriod()`; đổi dữ liệu bằng `supersedeWith()` |
| Tìm không dấu, sắp xếp theo chữ cái tiếng Việt (GC-01) | `use HasVietnameseSearch` + `$table->searchText()` + `protected array $searchable = [...]`; truy vấn `->search($tuKhoa)->orderByVietnamese('name')` |
| Quy tắc nghiệp vụ | `extends App\Support\Services\BaseService`; từ chối bằng `$this->fail('Lý do.', 'Cách khắc phục.')` |
| Phân quyền theo ma trận vai trò (xem mục "Phân quyền" bên dưới) | Policy `extends App\Modules\Auth\Policies\ModulePolicy` + model `implements HasDataScope` |
| Hiển thị ngày, giờ, tiền (GC-09) | `App\Support\Format::date()`, `dateTime()`, `money()` |
| Nhật ký kiểm toán khi tạo/sửa/xóa/khôi phục (GC-03) | Tự động với mọi model kế thừa `StandardModel`; che cột nhạy cảm bằng `protected array $auditMasked = [...]`, bỏ qua cột bằng `$auditExclude` |
| Ghi kèm lý do thay đổi | `app(AuditLogger::class)->withReason('Lý do…', fn () => $model->update([...]))` |
| Ghi hành động không phải tạo/sửa/xóa (đăng nhập, từ chối truy cập, xem dữ liệu nhạy cảm, duyệt, khóa…) | `app(AuditLogger::class)->record(AuditEvent::ViewSensitive, $model, reason: '…')`; thiếu loại thì thêm case vào `App\Support\Audit\AuditEvent` |
| Tra cứu nhật ký (màn hình quản trị) | `App\Modules\System\Services\AuditLogSearch`; quyền ở `AuditLogPolicy` |
| Xuất danh sách Excel / PDF | `App\Support\Services\ExportService`; truyền truy vấn Eloquent chưa phân trang, mã module, người dùng và `ExportColumn[]`; dịch vụ tự lọc theo quyền X/phạm vi, audit và chuyển tác vụ trên ngưỡng sang nền |

Ví dụ xuất một danh sách:

```php
$result = app(ExportService::class)->export(
    Student::query()->where('status', 'active'),
    [
        new ExportColumn('student_code', 'MSSV'),
        new ExportColumn('full_name', 'Họ và tên'),
    ],
    'STU',
    $request->user(),
    format: 'xlsx',
    filename: 'sinh-vien-dang-hoc',
);

if ($result->isQueued()) {
    return response()->json([
        'id' => $result->requestId,
        'status' => app(ExportService::class)->status($result->requestId, $request->user()),
    ], 202);
}

return $result->download;
```

Với tác vụ nền, kiểm tra bằng `ExportService::status($id, $user)` và tải bằng
`ExportService::download($id, $user)`. Chỉ người tạo còn quyền mới xem/tải được yêu cầu.
Đánh dấu `new ExportColumn('national_id', 'Số định danh', sensitive: true)` cho dữ liệu nhạy cảm;
các cột này chỉ được xuất khi người dùng có quyền xem toàn trường (`View/ALL`). PDF dùng DejaVu Sans
được nhúng để giữ dấu tiếng Việt. Cấu hình ngưỡng (`EXPORT_SYNC_THRESHOLD`), disk và thời gian job
ở `config/studentmanager.php` (`studentmanager.export`).

Lưu ý khi dùng `ExportService`:

- **PDF nặng hơn Excel/CSV nhiều** (dompdf dựng cả bảng trong bộ nhớ; đo 3 cột: 1.000 dòng ≈ 3,6 giây/190 MB, 2.000 dòng ≈ 12 giây/480 MB; Excel 50.000 dòng ≈ 2 giây/22 MB). PDF từ `pdf_sync_threshold` (mặc định 300 dòng) chạy nền, quá `pdf_max_rows` (mặc định 2.000) bị từ chối kèm gợi ý xuất Excel/CSV. Worker xử lý PDF cần `memory_limit` từ 512 MB.
- **Tệp xuất nền chỉ được giữ `retention_days` ngày** (mặc định 7) vì có thể chứa dữ liệu nhạy cảm. Lệnh `exports:prune` chạy hằng ngày lúc 02:00 (`routes/console.php`) xóa tệp và đặt yêu cầu sang `expired`; máy chủ cần chạy `php artisan schedule:run` mỗi phút. `status()` trả `expired` và `download()` báo rõ "đã hết hạn" ngay khi quá hạn, kể cả khi lệnh dọn chưa chạy tới.
- **Tải tệp ghi sự kiện `Downloaded`**, tách khỏi `Exported` (ghi lúc xuất) để báo cáo không đếm đôi.
- **Dữ liệu nhạy cảm trong hàng đợi:** tác vụ nền lưu câu SQL và các giá trị ràng buộc (`bindings`) của truy vấn vào bảng `jobs`. Không đưa dữ liệu cá nhân (CCCD, số điện thoại…) vào điều kiện lọc của truy vấn xuất nền; lọc theo mã, trạng thái, khoảng ngày thì an toàn.
- **Đếm số dòng** bằng `fromSub` nên truy vấn có `join` phải `select` các cột có tên riêng (alias), nếu không MySQL báo trùng tên cột.
- **Cần `ext-zip`** của PHP để ghi `.xlsx` (OpenSpout ghi theo luồng, bộ nhớ gần như không đổi theo số dòng; không dùng PhpSpreadsheet).

Ví dụ migration và model của một danh mục:

```php
Schema::create('faculties', function (Blueprint $table) {
    $table->id();
    $table->string('code', 20)->unique();
    $table->string('name');
    $table->activeStatus();
    $table->searchText();
    $table->standardColumns();
});
```

```php
class Faculty extends StandardModel
{
    use HasActiveStatus, HasVietnameseSearch;

    protected array $searchable = ['code', 'name'];
}
```

Lưu ý:

- Nhật ký kiểm toán chỉ được ghi thêm: bảng `audit_logs` có trigger MySQL chặn mọi lệnh sửa, xóa. Thay đổi hàng loạt bằng query builder (`Model::query()->update()`) **không** qua sự kiện model nên không được ghi nhật ký: thao tác nghiệp vụ quan trọng phải đi qua Eloquent hoặc ghi thủ công bằng `AuditLogger::record()`.
- Nhật ký nằm cùng giao dịch với thay đổi: giao dịch bị hoàn tác thì nhật ký cũng mất, không có nhật ký "ma".
- `search_text` chỉ tự cập nhật khi lưu bằng Eloquent. Import hoặc cập nhật hàng loạt bằng query builder phải tự điền cột này (dùng `App\Support\Text\Vietnamese::fold()`).
- Không dựa vào collation để tìm không dấu: cả `utf8mb4_unicode_ci` lẫn `utf8mb4_vietnamese_ci` đều coi `d` khác `đ`.
- `HasActiveStatus` chỉ dành cho danh mục. Sinh viên, lớp học phần… có máy trạng thái riêng.
- Bảng có hiệu lực theo thời gian: ràng buộc unique phải gồm cả `effective_from`.
- Test tự tạo bảng (DDL) dùng `DatabaseMigrations` thay cho `RefreshDatabase`, vì MySQL tự commit khi tạo bảng.

## Phân quyền (module AUTH)

Mọi quyết định truy cập đi qua ma trận vai trò – hành động – phạm vi dữ liệu (docs/BA.md mục 4), kiểm tra ở máy chủ và **mặc định từ chối**. Ẩn nút trên giao diện không được coi là đủ.

**1. Policy của module** chỉ cần mã module; `view/update/delete/approve` tự kiểm tra phạm vi trên đúng bản ghi:

```php
class StudentPolicy extends ModulePolicy
{
    protected string $module = 'STU';
}
// AppServiceProvider::boot(): Gate::policy(Student::class, StudentPolicy::class);
```

**2. Model khai báo cách lọc theo phạm vi** (ALL không cần xử lý). Phạm vi nào không thêm điều kiện thì bị coi là từ chối:

```php
class Student extends StandardModel implements HasDataScope
{
    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Own => $query->where('user_id', $user->id),
            DataScope::Advisee => $query->whereIn('admin_class_id', /* lớp user đang cố vấn */),
            DataScope::Faculty => $query->whereIn('faculty_id', /* khoa user quản lý */),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
```

**3. Trong Controller:**

```php
Gate::authorize('view', $student);                                  // một bản ghi: chống truy cập trái quyền (IDOR)
$list = app(AccessControl::class)
    ->constrain(Student::query(), $request->user(), 'STU', PermissionAction::View)
    ->paginate();                                                   // danh sách: chỉ dữ liệu trong phạm vi
```

**4. Chặn cả route:** `->middleware('permission:STU.view')`, hoặc `'permission:AUTH.update,ALL'` khi cần quyền toàn trường.

Ghi nhớ:

- Vai trò mặc định và ma trận nằm ở `database/seeders/AuthSeeder.php` (chép từ BA mục 4.2). Thêm module mới không cần sửa gì: danh mục quyền đã có đủ 23 module.
- Người có nhiều vai trò được hợp các quyền. Vai trò có thời hạn (`valid_from`, `valid_to`); gỡ vai trò chỉ đặt ngày kết thúc, không xóa lịch sử.
- Với `create` ở phạm vi hẹp (ví dụ sinh viên tự đăng ký học phần, phạm vi OWN), Service phải tự bảo đảm bản ghi tạo ra thuộc đúng người đó.
- Mọi lỗi 403 tự được ghi nhật ký kiểm toán (hành động "Từ chối truy cập").
- Khóa hoặc ngừng tài khoản phải gọi `RoleService::ensureNotLastAdmin()` trước (BR-AUTH-05).
- Trong test: dùng trait `Tests\Support\InteractsWithRoles` (`seedRoles()`, `userWithRoles('LEC', 'ADV')`).

## Đăng nhập và mật khẩu (module AUTH)

- Đăng nhập bằng tên đăng nhập (`users.username`: MSSV với sinh viên, mã cán bộ với nhân sự) hoặc email. Module tạo tài khoản (STU, TCH, quản lý người dùng) phải điền `username`.
- **Cấp mật khẩu tạm** (tài khoản mới, quản trị viên đặt lại): `app(PasswordService::class)->setTemporaryPassword($user, $matKhauTam)`. Người dùng bị buộc đổi ở lần đăng nhập đầu, mật khẩu tạm hết hạn sau 7 ngày (BR-AUTH-10), mọi phiên đang mở bị đăng xuất.
- **Quên mật khẩu** (FR-AUTH-003): `/forgot-password` → email chứa liên kết (`ResetPasswordLink`) → `/reset-password/{token}`. Mã do password broker của Laravel tạo, lưu dạng băm, dùng một lần, hết hạn sau `AUTH_PASSWORD_RESET_MINUTES` phút (mặc định 30, BR-AUTH-03). Phản hồi luôn giống nhau dù tài khoản có tồn tại hay không; tài khoản đã khóa, đã ngừng thì không gửi liên kết. Đặt lại xong (`PasswordService::reset()`): mật khẩu mới theo chính sách, không trùng mật khẩu gần đây, mở khóa tạm, đăng xuất mọi phiên. Giới hạn tần suất `throttle:password-reset` (5 lần/phút/IP).
- **Phiên đăng nhập** (FR-AUTH-007): phiên lưu ở bảng `sessions` (driver `database`), hết hạn sau `session.lifetime` phút không hoạt động (SESSION_LIFETIME, tham số hệ thống `session.lifetime`). "Ghi nhớ đăng nhập" tối đa `studentmanager.auth.remember_days` ngày (0 = tắt). Người dùng xem và thu hồi phiên ở `/tai-khoan/phien-dang-nhap` (`SessionController`; JSON nếu gửi `Accept: application/json`). Muốn hủy phiên của ai đó từ module khác: `app(UserSessions::class)->terminate($user)`. Mã phiên thật không trả ra ngoài, mỗi phiên có `handle` (HMAC theo APP_KEY). Thu hồi ghi nhật ký `session_revoked`.
- **Lịch sử đăng nhập** (FR-AUTH-016): bảng `login_histories` (thành công/thất bại, lý do, IP, thiết bị, `unusual`), ghi trong `LoginService` qua `LoginHistoryService`. Người dùng xem 20 lần gần nhất của mình ở trang phiên đăng nhập; ADMIN tra cứu toàn trường ở `GET /admin/login-histories` (quyền `AUTH.view` toàn trường; lọc `user_id`, `login`, `successful`, `unusual`, `ip`, `from`, `to`). Đăng nhập bất thường (IP và thiết bị đều chưa gặp trong `AUTH_UNUSUAL_LOGIN_LOOKBACK_DAYS` ngày, mặc định 90) thì đánh dấu `unusual` và gửi email `UnusualLoginAlert` cho chủ tài khoản (EV-AUTH-02; mail log ở P1, NOT sẽ báo thêm ADMIN).
- **Mở khóa trước hạn** (FR-AUTH-006): `app(LoginService::class)->unlock($user)`.
- **Khóa hẳn / ngừng tài khoản**: dùng `UserService` (mục dưới). `LoginService::attempt()` chặn bằng `User::canSignIn()` sau khi đúng mật khẩu, nên chỉ người biết mật khẩu mới thấy lý do cụ thể.
- Mật khẩu mới luôn kiểm tra bằng `Password::defaults()` (chính sách trong `config/studentmanager.php`, mục `auth.password`).
- Mọi route của 3 cổng đã có middleware `password.changed`: chưa đổi mật khẩu tạm thì bị đưa về trang đổi mật khẩu (JSON trả mã 428).

## Quản lý người dùng (AUTH, FR-AUTH-008, 009)

- `UserService::create(['username', 'name', 'email', 'profile_type', 'profile_id'?, 'roles'?])`: tên đăng nhập duy nhất, không đổi, không tái sử dụng; gán vai trò mặc định theo loại hồ sơ (`ProfileType`: sinh viên → STU, giảng viên → LEC, nhân viên phải chọn vai trò); cấp mật khẩu tạm gửi qua email (`TemporaryPasswordIssued`), buộc đổi ở lần đầu.
- STU, TCH tạo hồ sơ xong thì gọi `UserService::create()` (hoặc `linkProfile($user, ProfileType::Student, $student->id)` nếu tài khoản đã có). Mỗi tài khoản đúng một hồ sơ chính, mỗi hồ sơ một tài khoản (BR-AUTH-01).
- Trạng thái (`AccountStatus`): hoạt động, đã khóa (`lock`/`unlock`, quyền AUTH.approve), ngừng (`deactivate`/`activate`), chỉ đọc. Khóa và ngừng hủy mọi phiên (`UserSessions::terminate`), không làm mất ADMIN cuối cùng còn hoạt động (BR-AUTH-05), không tự khóa mình.
- **BR-AUTH-07:** khi đổi tình trạng học tập, STU gọi `UserService::applyStudentStatus($user, StudentAccountEvent::…)`. Thôi học, buộc thôi học, chuyển trường: hẹn ngừng sau `auth.student_deactivate_after_days` ngày (mặc định 30); lệnh `users:apply-deactivations` chạy 00:15 hằng ngày. Tốt nghiệp: chỉ đọc (middleware `EnsureAccountWritable` chặn mọi yêu cầu ghi, trừ đăng xuất và đổi mật khẩu). Học lại: hoạt động.
- Tạo hàng loạt qua trung tâm import, Importer `users` (cột `username`, `name`, `email`, `profile_type`, `roles`).
- API `/admin/users` (danh sách lọc theo trạng thái, loại hồ sơ, vai trò, từ khóa; chi tiết, tạo, sửa, khóa, mở khóa, ngừng, kích hoạt, cấp lại mật khẩu tạm); theo ma trận chỉ ADMIN có quyền AUTH toàn trường.
- Môi trường phát triển: `DevRoleUsersSeeder` tạo một tài khoản mẫu cho mỗi vai trò (`acad.test`, `stu.test`…; DEAN được giao nhiệm kỳ trưởng khoa CNTT).

## Trung tâm import (SYS, FR-SYS-007)

Module nào cần nhập dữ liệu từ file (sinh viên, giảng viên, học phần…) chỉ cài đặt `App\Modules\System\Contracts\ImporterContract` rồi đăng ký, không tự viết quy trình tải file, báo lỗi, lưu và hoàn tác:

```php
// trong ServiceProvider của module
$this->app->make(ImportRegistry::class)->register(new StudentImporter());
```

- Importer lo: đọc file (`parseRows`, nên trả `Generator`), kiểm tra một dòng (`validateRow`), lưu một dòng (`saveRow`), và hoàn tác (`canRollbackRow` chỉ kiểm tra, `rollbackRow` mới xóa).
- Trung tâm import lo: lô và mã lô, báo lỗi theo dòng/cột, "Chỉ lưu dòng hợp lệ" hoặc "Lưu toàn bộ", chạy nền cho file lớn, hoàn tác theo lô, nhật ký kiểm toán.
- API: `/admin/imports` (route `admin.imports.*`). Quyền theo ma trận module `SYS`: xem = `SYS.view`, tải lên/kiểm tra/lưu = `SYS.create`, hoàn tác/xóa = `SYS.delete`.
- Ngưỡng chạy nền, cỡ lô ghi CSDL, dung lượng file, timeout job: `config/studentmanager.php`, mục `import`.
- Cách viết Importer mẫu và test: `tests/Support/FakeImporter.php`, `tests/Unit/Modules/System/ImportServiceTest.php`.
- Đọc file Excel/CSV: kế thừa `App\Modules\System\Importers\SpreadsheetImporter`, khai báo `columns()` (cột bắt buộc, tùy chọn theo tiêu đề dòng 1) rồi chỉ viết `validateRow`/`saveRow`/`canRollbackRow`/`rollbackRow`. Lớp nền đọc `.xlsx`/`.csv` theo luồng, báo thiếu cột, bỏ dòng trống mà vẫn báo lỗi đúng số dòng trong file, từ chối `.xls` cũ kèm hướng dẫn lưu lại. Ví dụ: `LookupValueImporter`, `AdministrativeUnitImporter`.

## Danh mục dùng chung (SYS, FR-SYS-002)

- Ô chọn trong biểu mẫu: `app(LookupService::class)->options(LookupCategory::GENDER)` (chỉ giá trị đang hoạt động, đúng thứ tự). Giao diện gọi `GET /danh-muc/{MÃ}` (mọi người dùng đã đăng nhập). Danh mục có sẵn: `GENDER`, `ETHNICITY` (54 dân tộc), `RELIGION`, `NATIONALITY`, `PRIORITY_GROUP`, `PRIORITY_AREA`, `CONTRACT_TYPE`.
- Địa chỉ (BR-STU-09): bảng `administrative_units`, mặc định mô hình hai cấp từ 01/07/2025 (`GET /danh-muc/don-vi-hanh-chinh?parent_id=`); dữ liệu ba cấp cũ lấy bằng `?scheme=three_level_legacy`, đơn vị cũ trỏ tới đơn vị mới qua `successor_id`. Lưu địa chỉ: lưu `id` của xã/phường (mô hình hai cấp) và dòng chi tiết (số nhà, đường).
- Seeder có 34 tỉnh/thành; **danh sách xã/phường nhập bằng trung tâm import** (Importer `administrative_units`, nhập tỉnh trước, xã ở lô sau). Mã tỉnh và mã dân tộc cần đối chiếu danh mục chính thức trước khi dùng thật.
- Mã là khóa ổn định: không đổi, không cấp lại (kể cả đã xóa). Quản trị: `/admin/lookups`, `/admin/administrative-units` (quyền `SYS.*`).

## Tham số hệ thống và bộ quy chế (SYS, FR-SYS-001, FR-SYS-003)

**Mọi ngưỡng quy chế đọc qua `PolicyResolver`, không viết cứng (GC-12):**

```php
$resolver = app(PolicyResolver::class);
$resolver->value('attendance.ban_percent', cohort: $student->cohort_year, date: $session->date);   // 20
$resolver->value('grading.scale', $khoa);         // [['min' => 8.5, 'letter' => 'A', 'gpa' => 4.0, 'passed' => true], ...]
$resolver->setFor($khoa, $ngay)->code;            // bộ quy chế đang áp dụng
```

- Danh sách tham số, nhãn và giá trị mặc định (docs/BA.md phụ lục C): `App\Modules\System\Settings\PolicyDefinitions`. Cần ngưỡng mới thì khai báo ở đó; bộ đã ban hành trước đó dùng giá trị mặc định.
- Chọn bộ: trong các bộ **đã ban hành** có phạm vi khóa chứa khóa của sinh viên và ngày hiệu lực không sau ngày cần tính, lấy bộ hiệu lực gần nhất. Dữ liệu đã chốt thì truyền đúng ngày của dữ liệu (ví dụ ngày công bố điểm) để không bị hồi tố.
- Bộ đã ban hành không sửa, không xóa; muốn đổi thì tạo phiên bản mới (`based_on_id`, cùng mã) với ngày hiệu lực từ hôm nay trở đi (BR-SYS-02). Ban hành ghi nhật ký toàn bộ giá trị và báo các ADMIN khác (BR-SYS-08).
- Seeder có bộ `QC-MAC-DINH` áp dụng mọi khóa. API: `/admin/policy-sets` (xem `SYS.view`; soạn, ban hành `SYS.update`), xem trước `GET /admin/policy-sets/resolve?cohort=2026&date=…`.

**Tham số hệ thống** (tên trường, logo, liên hệ, múi giờ, ngôn ngữ, định dạng ngày, chính sách phiên): `app(SettingService::class)->get('school.name')`; danh sách ở `SettingDefinitions`. Tham số đã lưu ghi đè config lúc khởi động (`studentmanager.school.name`, `short_name`, `abbr` mà giao diện đang hiển thị, `studentmanager.display_timezone`, `app.locale`, `studentmanager.formats.date`, `session.lifetime`, `studentmanager.auth.remember_days`). Học kỳ hiện hành thuộc module ACY. API: `GET/PUT /admin/settings`, `POST /admin/settings/logo`.

## Lịch học vụ và ngày nghỉ (ACY, FR-ACY-004, 005, 007)

- **Đọc mốc của học kỳ:** `app(MilestoneService::class)->date($term, MilestoneType::WithdrawDeadline)` (ENR: mở/đóng đăng ký, hạn hủy, hạn rút; EXM: thi; GRD: nhập, công bố điểm; FEE: hạn nộp học phí). Mốc chưa đặt thì trả `null`.
- Thứ tự hợp lý kiểm tra theo `MilestoneType::orderRules()` (BR-ACY-02). Học kỳ đã bắt đầu thì `save()` tạo `MilestoneChangeRequest` chờ một cán bộ khác có quyền ACY.approve duyệt (BR-ACY-05); duyệt xong mới áp dụng, nhật ký ghi lý do và người duyệt, người đề nghị nhận thông báo.
- **Ngày nghỉ cho TTB:** `app(HolidayService::class)->daysOff($tu, $den, $term)` / `isDayOff($ngay, $term)` (nghỉ toàn trường + nghỉ riêng của học kỳ). Ngày nghỉ đã bắt đầu thì không sửa, không xóa.
- Sao chép lịch từ học kỳ trước, dời theo chênh lệch ngày bắt đầu: `copyFrom($nguon, $dich)` (FR-ACY-007).
- API: `/admin/terms/{id}/milestones` (+ `/copy`), `/admin/milestone-change-requests` (+ `/{id}/approve`, `/reject`), `/admin/holidays` (+ `/days-off`). Lưu ý ma trận ACY: ADMIN chỉ có Xem, ACAD quản lý.

## Phòng học (ROM, FR-ROM-001, 002)

- Bảng `campuses` → `buildings` → `rooms` (+ `room_types`, `room_maintenances`). Mã phòng duy nhất toàn trường, sức chứa học ≥ 1, sức chứa thi từ 0 (không dùng làm phòng thi) đến sức chứa học (BR-ROM-01).
- **TTB, EXM phải hỏi trước khi xếp lịch (BR-ROM-02):** `app(RoomAvailability::class)->assertSchedulable($room, $tuNgay, $denNgay)` (hoặc `problems()` để lấy danh sách lý do): phòng phải "Sử dụng được", tòa nhà và cơ sở còn hoạt động, không trùng lịch bảo trì. Kiểm tra trùng lịch học, lịch thi là việc của TTB/EXM.
- Loại phòng có mã cố định `RoomType::LECTURE` (LT), `COMPUTER_LAB` (MT), `LABORATORY` (TN), `HALL` (HT), `EXAM` (PT) để TTB kiểm tra BR-ROM-03.
- **Module lưu `room_id` (buổi học, ca thi…) phải đăng ký với `ReferenceRegistry`** trong ServiceProvider của mình, khi đó phòng đã có lịch sử sử dụng chỉ ngừng được, không xóa, không đổi mã (BR-ROM-04).
- API: `/admin/campuses`, `/admin/buildings`, `/admin/room-types`, `/admin/rooms`, `/admin/rooms/{id}/availability?from=&to=`, `/admin/rooms/{id}/maintenances` (quyền `ROM.*`; mọi vai trò được xem theo ma trận). Seeder: 1 cơ sở, 2 tòa nhà, 20 phòng mẫu.

## Khóa và lớp hành chính (CLS, FR-CLS-001, 002)

- Bảng `cohorts` (mã khóa ví dụ K2026, **năm bắt đầu duy nhất** = "khóa" mà STU, CUR, SYS dùng dạng số, năm kết thúc dự kiến, bộ quy chế `policy_set_id`), `admin_classes` (mã lớp duy nhất kể cả lớp đã xóa, tên, khóa, ngành, chuyên ngành tùy chọn, hệ đào tạo, sĩ số tối đa, trạng thái hoạt động / đã đóng).
- `AdminClassService`: tạo khóa (mã mặc định `K{năm}`, năm kết thúc mặc định + `studentmanager.school_class.default_cohort_years`); bộ quy chế gắn cho khóa phải **đã ban hành và áp dụng cho năm bắt đầu**, để trống thì `Cohort::effectivePolicySet()` lấy theo `PolicyResolver`. Khóa đã có lớp thì không đổi năm bắt đầu, không xóa; còn lớp đang hoạt động thì không ngừng.
- Lớp: đúng một khóa và một ngành đang hoạt động, chuyên ngành phải thuộc ngành (BR-CLS-01); sĩ số 1–`admin_class_max_size` (mặc định 120, sĩ số gợi ý 60). **Module có khóa ngoại tới `admin_classes` (thành viên lớp, cố vấn — #99) đăng ký `ReferenceRegistry` kèm `active:`** để: lớp đã có dữ liệu không đổi mã / khóa / ngành và không xóa; lớp còn sinh viên đang học không đóng được.
- Phạm vi: ACAD quản lý toàn trường (khóa chỉ người có quyền CLS toàn trường thêm / sửa / xóa); DEAN (FACULTY) xem lớp thuộc ngành của khoa mình; ADV, STU chưa có dữ liệu cho tới #99. Danh sách khóa là danh mục chung, ai có CLS.view cũng xem được.
- API: `/admin/cohorts` (+ `/{id}`), `/admin/admin-classes` (lọc `q`, `cohort_id`, `major_id`, `faculty_id`, `status`; `+ /{id}`). Ngành, chuyên ngành, hệ đào tạo còn lớp đang hoạt động thì không ngừng được. Seeder: khóa K2023–K2026, mỗi ngành một lớp K2026 (`26DH<chữ đầu tên ngành>01`).

## Lớp học phần (CLS, FR-CLS-009..012)

- Bảng `course_sections` (mã tự sinh `<mã học phần>.<2 số năm học><thứ tự học kỳ>.<số thứ tự>`, ví dụ `INT101.261.01`, duy nhất; học phần, học kỳ, loại `theory|practice|mixed`, hình thức `in_person|online|hybrid`, ngôn ngữ, sĩ số tối thiểu – tối đa, phòng do TTB xếp, trạng thái `SectionStatus`), `section_instructors` (giảng viên `main` / `assistant`). Lớp học phần **không gắn lớp hành chính** (lớp ghép).
- `CourseSectionService::open()`: học phần đang hoạt động, thuộc CTĐT đang áp dụng (`CurriculumCatalog::isInForce`, BR-CLS-03), có đề cương áp dụng được (`SyllabusService::assertUsableForNewSection`, BR-SUB-08, tắt bằng `SYLLABUS_REQUIRES_APPROVAL=false`), học kỳ chưa kết thúc / khóa; sĩ số ≤ `studentmanager.course_section.max_size` và ≤ sức chứa phòng (BR-CLS-02). Số thứ tự lấy trong giao dịch có khóa dòng.
- **Phân công (FR-CLS-011):** `assign($lop, $gv, 'main'|'assistant', $xacNhan)`: giảng viên đang làm việc; tổng tiết (LT + TH của học phần) các lớp còn hoạt động trong học kỳ vượt `max_periods_per_term` (mặc định 400) thì từ chối kèm số liệu, gửi lại `confirm_overload=true` mới phân công và trả `warnings`. ACAD (CLS.update) hoặc DEAN trong đơn vị (CLS.approve) phân công được.
- **Vòng đời (FR-CLS-012):** `advance($lop, $buocKeTiep)` chỉ đi một bước; sang Mở đăng ký phải có giảng viên chính (BR-CLS-04); lớp đã mở đăng ký không gỡ được giảng viên chính cuối cùng. `cancel($lop, $lyDo)` ở ba bước đầu (ENR/FEE/NOT xử lý hệ quả ở #108). Xóa hẳn chỉ lớp Dự kiến chưa có dữ liệu liên quan (BR-CLS-06).
- **Cài cho module khác:** `TeachingAssignments` → `SectionTeachingAssignments` (giảng viên còn lớp chưa kết thúc thì chưa ghi nhận nghỉ việc được, BR-TCH-02); `SubjectAssignments` → `SectionSubjectAssignments` (phạm vi SECTION: học phần của lớp được phân công cộng học phần của bộ môn). **ENR đăng ký `ReferenceRegistry` với `CourseSection`** để lớp có sinh viên không xóa được.
- Phạm vi: DEAN (FACULTY) lớp của học phần thuộc bộ môn / khoa mình; LEC (SECTION) lớp được phân công; STU chờ ENR. API `/admin/course-sections` (lọc `q`, `term_id`, `subject_id`, `status`, `lecturer_id`, `without_main_instructor=1`; `+ /{id}`, `/instructors`, `/status/{trạng thái}`), `/admin/section-instructors/{id}`. Seeder: học kỳ 2 năm 2026–2027, mỗi học phần học kỳ 2 của CTĐT đang áp dụng một lớp Dự kiến, phân công giảng viên đầu tiên của bộ môn (nếu có).
- Sau đăng ký (BR-CLS-05, 06, 09; FR-CLS-014, 018): `CourseSectionService::underMinSections($hk)` liệt kê lớp Mở / Đóng đăng ký dưới sĩ số tối thiểu kèm đề xuất (hủy / gộp vào lớp cùng học phần còn đủ chỗ) — **#113 chặn chốt khi còn lớp chưa xử lý**. Hủy lớp chạy trong một giao dịch và phát `CourseSectionCancelled`: ENR hủy mọi đăng ký còn hiệu lực + phát `EnrollmentsCancelledByClass` (EV-ENR-03, FEE / NOT nghe khi có), TTB hủy buổi học chưa diễn ra. `SectionMergeService::merge($nguon, $dich, $lyDo)` (ENR) chuyển đăng ký sang lớp đích rồi hủy lớp nguồn, liệt kê sinh viên bị trùng lịch. Không xóa lớp đã có đăng ký (tham chiếu `enrollments`). `unlock($lop, $lyDo)` mở khóa ngoại lệ (CLS.approve, audit `unlocked`). API: `GET /admin/terms/{id}/under-min-sections`, `POST /admin/course-sections/{id}/merge`, `GET /admin/course-sections/{id}/enrollments/export?format=xlsx|csv|pdf` (ENR.export), `POST /admin/course-sections/{id}/unlock`.

## Giảng viên (TCH, FR-TCH-001, 003)

- Bảng `lecturers`: mã giảng viên (duy nhất, không cấp lại, cũng là tên đăng nhập), **đúng một đơn vị chính là bộ môn** (BR-FAC-03), chức danh (`AcademicTitle`), học vị (`AcademicDegree`), chuyên môn, liên hệ, ảnh, CCCD (nhạy cảm, che trong nhật ký), tình trạng (`LecturerStatus`: đang làm việc / nghỉ việc / nghỉ hưu). `lecturer_units`: kiêm nhiệm ở bộ môn hoặc khoa khác, có ngày hiệu lực. Chức vụ quản lý vẫn là nhiệm kỳ lãnh đạo của FAC.
- **Tạo hồ sơ (BR-TCH-01):** `LecturerService::create($data)` với `account` = `new` (mặc định: tạo tài khoản qua `UserService`, tên đăng nhập = mã, vai trò LEC, mật khẩu tạm gửi email), `link` (gắn `user_id` có sẵn, bổ sung LEC) hoặc `none`. Mỗi hồ sơ tối đa một tài khoản; cấp sau bằng `createAccount()`. Đổi họ tên trên hồ sơ thì tên tài khoản đổi theo.
- **Phân công (CLS, TTB, ATT, EXM, GRD) phải hỏi `$lecturer->isAssignable()`** (chỉ giảng viên đang làm việc, BR-TCH-02). Lấy hồ sơ của người đăng nhập: `Lecturer::query()->where('user_id', $user->id)`.
- **Nghỉ việc / nghỉ hưu (BR-TCH-02):** `changeStatus($lecturer, LecturerStatus::Resigned, $ngay, $lyDo)` bị từ chối khi còn lớp đang dạy hoặc chức vụ quản lý sau ngày nghỉ, kèm danh sách cần chuyển (`workToHandOver()`, API `GET /admin/lecturers/{id}/handover`). **CLS (#107) bind `App\Modules\Teacher\Contracts\TeachingAssignments`** bằng lớp trả về các lớp đang dạy; hiện bản mặc định trả rỗng. Kiêm nhiệm tự kết thúc vào ngày nghỉ, lịch sử giữ nguyên.
- **Giao chức vụ lãnh đạo đơn vị:** TCH bind `LeaderEligibility` bằng `TeacherLeaderEligibility`: giảng viên phải đang làm việc và thuộc đơn vị (đơn vị chính hoặc kiêm nhiệm; khoa tính cả bộ môn của khoa) trừ khi bật `studentmanager.faculty.allow_leader_outside_unit` (BR-FAC-04); mỗi người chỉ giữ chức vụ trưởng ở một đơn vị tại một thời điểm (BR-TCH-06). Tài khoản cán bộ chưa có hồ sơ nhân sự nên chưa kiểm tra đơn vị; tài khoản sinh viên bị từ chối.
- **Lịch bận, nguyện vọng, nghỉ phép (FR-TCH-008, 012; #89):** `LecturerScheduleService`. Lịch bận (`lecturer_availabilities`): thứ, tiết (1..`TEACHER_PERIODS_PER_DAY`), khoảng ngày, không chồng nhau; **TTB gọi `busySlot($gv, $ngay, $tietTu, $tietDen)`** để cảnh báo xếp vào giờ bận (FR-TTB-010). Nguyện vọng (`teaching_preferences`): học phần cho học kỳ chưa bắt đầu, có mức ưu tiên; CLS xem `GET /admin/teaching-preferences?term_id=&subject_id=`. Nghỉ phép / ốm / công tác (`lecturer_leaves`): không trùng đơn còn hiệu lực, DEAN trong đơn vị hoặc ACAD duyệt (không tự duyệt), duyệt xong phát sự kiện `LecturerLeaveApproved` cho TTB/NOT; **TTB, CLS gọi `isOnLeave($gv, $ngay)`**. Cổng giảng viên: `/teacher/availability`, `/teacher/preferences`, `/teacher/leaves`; quản trị: `/admin/lecturers/{id}/availability`, `/admin/lecturer-leaves` (+ `/approve`, `/reject`).
- **Giảng viên tự sửa hồ sơ (BR-TCH-07):** `PUT /teacher/profile` sửa ngay điện thoại, email; `POST /teacher/profile/photo` đổi ảnh (disk `public`, tối đa `TEACHER_PHOTO_MAX_KB`); họ tên, chức danh, học vị thành **đề nghị** (`LecturerChangeRequest`, tối đa một đề nghị đang chờ), do cán bộ khác có quyền xác nhận: DEAN trong đơn vị (TCH.approve) hoặc ACAD (TCH.update toàn trường), `POST /admin/lecturer-change-requests/{id}/approve|reject`. Trường khác bị từ chối kèm tên trường.
- Phạm vi: DEAN (FACULTY) quản lý giảng viên có đơn vị chính hoặc kiêm nhiệm đang hiệu lực thuộc đơn vị mình; LEC, ADV (OWN) chỉ hồ sơ của mình; các vai trò khác xem thông tin cơ bản. CCCD, ngày sinh, điện thoại… chỉ trả cho người sửa được hồ sơ; người khác xem CCCD được ghi nhật ký `view_sensitive`.
- API: `/admin/lecturers` (lọc `q` không dấu, `department_id`, `faculty_id`, `status`, `academic_title`, `academic_degree`; `+ /{id}`, `/status`, `/account`, `/units`, `/handover`), `/admin/lecturer-units/{id}/end`, `/admin/lecturer-change-requests`. Bộ môn, khoa còn giảng viên đang làm việc / kiêm nhiệm thì không ngừng được (đăng ký `ReferenceRegistry`). Seeder: 20 giảng viên mẫu có tài khoản.
- **Nhập / xuất danh sách (FR-TCH-013, #87):** Importer `lecturers` của trung tâm import (cột bắt buộc `code`, `full_name`, `department_code`; tùy chọn `email` (có thì cấp tài khoản LEC), `phone`, `academic_title`, `academic_degree` (nhận mã hoặc tên tiếng Việt), `expertise`, `hired_on`, `date_of_birth` (dd/mm/yyyy, yyyy-mm-dd hoặc ô ngày của Excel), `national_id`). Hoàn tác xóa hẳn hồ sơ và tài khoản của lô nếu chưa phát sinh dữ liệu (kiêm nhiệm, đề nghị, chức vụ, đã đăng nhập). Xuất `GET /admin/lecturers/export` theo bộ lọc khoa, bộ môn, chức danh, học vị, tình trạng (quyền TCH.export: ACAD, DEAN đơn vị mình; điện thoại, ngày sinh là cột nhạy cảm).
## Sinh viên (STU, FR-STU-001, 004, 005, 013)

- Bảng `students` (họ tên, giới tính, ngày sinh, dân tộc, tôn giáo, quốc tịch theo danh mục dùng chung; giấy tờ CCCD hoặc hộ chiếu; email trường, email cá nhân, điện thoại; địa chỉ thường trú / tạm trú kèm xã / phường; ngành, chuyên ngành, hệ đào tạo, khóa (`cohort` = năm bắt đầu), CTĐT (#97), ngày nhập học, đối tượng / khu vực ưu tiên; trạng thái `StudentStatus`), `student_contacts` (người thân, liên hệ khẩn cấp), `student_change_logs` (lịch sử từng trường).
- **Số giấy tờ mã hóa** (cast `encrypted`, NFR-SEC-06); kiểm tra trùng bằng `national_id_hash` (HMAC theo `APP_KEY`, unique) qua `IdentityNumber::hash()`. **Đổi `APP_KEY` thì phải tính lại mã băm và giải mã lại**. Tìm theo CCCD: `?q=` là chuỗi số đầy đủ (so mã băm, không tìm một phần).
- `StudentService`: tạo ở trạng thái **Chờ nhập học**; số giấy tờ và email trường duy nhất tính cả hồ sơ đã xóa (BR-STU-02), thông báo trùng chỉ nêu tên / mã hồ sơ khi người thao tác được xem hồ sơ đó; tuổi khi nhập học trong `studentmanager.student.min_age`–`max_age` (mặc định 16–60), định dạng điện thoại / email ở `StudentRequest` (BR-STU-03); chuyên ngành phải thuộc ngành.
- **Sửa hồ sơ** (`update($sv, $data, $lyDo)`): mỗi trường đổi ghi một dòng lịch sử (trước – sau, người sửa, thời điểm, lý do); trường nhạy cảm (`Student::SENSITIVE_FIELDS`: họ tên, ngày sinh, giấy tờ, ngành, chuyên ngành) bắt buộc lý do (BR-STU-06). Không sửa MSSV, trạng thái, tài khoản, CTĐT ở đây. Lịch sử lưu số giấy tờ và địa chỉ đã che.
- **Che dữ liệu (BR-STU-11):** API luôn trả số giấy tờ, địa chỉ đã che (`IdentityNumber::mask`, `maskAddress`); `GET /admin/students/{id}/sensitive` (cần quyền sửa hồ sơ) trả đầy đủ và ghi nhật ký `view_sensitive` mỗi lần xem. Nhật ký kiểm toán tự động cũng che các cột này.
- **Xóa (BR-STU-07):** chỉ hồ sơ Chờ nhập học, chưa có MSSV, chưa được module nào tham chiếu (`ReferenceRegistry::usages($sv)`) → xóa mềm. Hồ sơ đã phát sinh dữ liệu chỉ chuyển trạng thái theo quyết định (#114). **Ma trận BA 4.2 chưa cấp D cho STU** nên mặc định không ai xóa được; cấp `STU.delete` qua ma trận nếu cần.
- **Module có khóa ngoại tới `students` đăng ký với `ReferenceRegistry`** (ví dụ ENR: `register(Student::class, 'enrollments', 'student_id', 'đăng ký học phần')`) để chặn xóa hồ sơ đã có dữ liệu.
- Phạm vi: CTSV, ACAD toàn trường; DEAN (FACULTY) sinh viên thuộc ngành của khoa mình; STU (OWN) hồ sơ của mình qua `user_id` (cấp ở #102); LEC (SECTION), ADV (ADVISEE) chưa có dữ liệu cho tới ENR và #99. Ngành, chuyên ngành, hệ đào tạo, CTĐT còn sinh viên đang học thì không ngừng được.
- API: `/admin/students` (lọc `q` không dấu hoặc số giấy tờ, `status`, `major_id`, `faculty_id`, `cohort`, `training_type_id`; danh sách đầy đủ / xuất ở #94), `/admin/students/{id}` (+ `/change-logs`, `/sensitive`, `/contacts`), `/admin/student-contacts/{id}`. Seeder: 36 thí sinh khóa 2026 (mỗi ngành 6) ở trạng thái Chờ nhập học, dữ liệu giả.

## Gán CTĐT cho sinh viên (CUR, FR-CUR-006; BR-CUR-01, BR-STU-04)

- Bảng `student_curricula` (sinh viên, CTĐT, `effective_from`, `effective_to`, lý do): lịch sử gán; `students.curriculum_id` luôn là CTĐT hiện hành.
- `StudentCurriculumService::assignDefault($sv)`: CTĐT đang áp dụng cho ngành – khóa – hệ (ưu tiên chuyên ngành, `CurriculumCatalog::forStudent`); không có thì từ chối kèm hướng dẫn. **Xác nhận nhập học (#102) gọi hàm này.** Gán lại khi CTĐT không đổi thì giữ nguyên.
- `change($sv, $ctdt, $tuNgay, $lyDo)`: chỗ cắm cho chuyển ngành / đổi phiên bản (STU ở P3). CTĐT phải đã ban hành, đúng ngành – hệ hiện tại của sinh viên (đổi ngành trước rồi mới gán), ngày hiệu lực sau lần gán trước, có lý do; lần gán cũ kết thúc trước một ngày.
- **ENR, GRA đọc CTĐT hiện hành bằng `StudentCurriculumService::current($sv, $ngay)`** (BR-CUR-01).
- STU (OWN) xem được CTĐT đang / đã gán cho mình (`Curriculum::applyDataScope`). API `GET|POST /admin/students/{id}/curricula` (POST không gửi `curriculum_id` thì gán theo ngành – khóa).

## Xác nhận nhập học (STU, FR-STU-002; BR-STU-01, 02, 04; EV-STU-01; UAT-04)

- `AdmissionService::admit($sv, $lop?, $ngay?)` trong **một giao dịch**: cấp MSSV, email trường `{MSSV}@{STUDENT_EMAIL_DOMAIN}`, tài khoản (`UserService::create`, tên đăng nhập = MSSV, vai trò STU, mật khẩu tạm gửi email), gán CTĐT (`StudentCurriculumService::assignDefault`), xếp lớp hành chính, chuyển sang Đang học. Bước nào lỗi thì hoàn tác hết; xong mới phát `StudentAdmitted` (EV-STU-01).
- MSSV = 2 số cuối của khóa + `code_major_digits` số cuối mã ngành + số thứ tự `code_sequence_digits` chữ số trong khóa – ngành (cấu hình `studentmanager.student`). Số thứ tự tính cả hồ sơ đã xóa mềm nên không tái sử dụng (BR-STU-01).
- Lớp hành chính: gửi `admin_class_id` hoặc để hệ thống chọn lớp đang hoạt động cùng khóa – ngành – hệ còn chỗ (theo mã lớp). Bảng `admin_class_members` có ngày hiệu lực (BR-CLS-08); dòng hiện hành có `effective_to` trống. Gán / chuyển lớp hàng loạt, cố vấn học tập ở #99.
- Hủy nhập học: hồ sơ Chờ nhập học xóa mềm kèm lý do (đi qua `StudentService::delete`, BR-STU-07).
- API (quyền `STU.approve`: ACAD, CTSV): `POST /admin/students/{id}/admit`, `POST /admin/students/{id}/cancel-admission` (bắt buộc `reason`).

## Danh sách, tìm kiếm, xuất sinh viên (STU, FR-STU-003, 008; BR-STU-11)

- `GET /admin/students`: tìm `q` theo MSSV, họ tên không dấu, hoặc số CCCD đầy đủ (so bằng `national_id_hash`); lọc `faculty_id`, `major_id`, `cohort`, `training_type_id`, `admin_class_id` (lớp hiện tại), `status`; `sort` = student_code | full_name (collation tiếng Việt) | cohort | status, `dir`, `per_page` ≤ 100. Mỗi dòng có `national_id_masked` (che, BR-STU-11); xem đầy đủ qua `/sensitive` (ghi `view_sensitive`, #101).
- Phạm vi xem: DEAN khoa mình (FACULTY), cố vấn lớp mình (ADVISEE, #99), giảng viên sinh viên đang đăng ký lớp mình dạy (SECTION — đọc `enrollments` + `section_instructors` bằng truy vấn), sinh viên chính mình (OWN).
- `GET /admin/students/export?format=xlsx|csv|pdf&…bộ lọc`: qua ExportService (STU.export, lọc theo phạm vi xuất, audit `exported`); mặc định CCCD đã che; `include_sensitive=1` thêm CCCD và địa chỉ đầy đủ — chỉ người xem STU toàn trường (ACAD, CTSV…), DEAN bị 403.

## Import hồ sơ sinh viên (STU, FR-STU-007; NFR-PERF-04)

- `StudentImporter` (khóa `students`) đăng ký ở trung tâm import (`/admin/imports`, quyền SYS — hiện chỉ ADMIN): cột bắt buộc `full_name`, `date_of_birth`, `major_code`, `training_type_code`, `cohort`; tùy chọn giới tính / dân tộc (tên trong danh mục), CCCD, ngày / nơi cấp, email cá nhân, điện thoại, địa chỉ, chuyên ngành, ngày nhập học, ghi chú. Hồ sơ tạo ở **Chờ nhập học** (chưa MSSV, chưa tài khoản).
- Kiểm tra theo dòng / cột: định dạng, mã ngành / hệ / chuyên ngành, danh mục, trùng CCCD / email trong cùng file, và mọi quy tắc tạo hồ sơ qua `StudentService::assertCreatable()` (tuổi BR-STU-03, trùng CCCD và email trường BR-STU-02, chuyên ngành đúng ngành). Hoàn tác theo lô chỉ với hồ sơ còn Chờ nhập học, chưa MSSV, chưa phát sinh dữ liệu.
- Đã đo trên máy dev: 100 dòng / 5 dòng lỗi → lưu đúng 95; 5.000 dòng → kiểm tra 21 giây + lưu 38 giây (job hàng đợi `database`).

## Sinh viên tự cập nhật hồ sơ (STU, FR-STU-006; BR-STU-06; EV-STU-03)

- `StudentSelfService`: sinh viên sửa ngay điện thoại, email cá nhân, địa chỉ thường trú / tạm trú, ảnh, người liên hệ (qua `StudentService::update`, có lịch sử trước – sau, lý do "Sinh viên tự cập nhật"). Họ tên, ngày sinh, số CCCD, ngành chỉ qua `student_change_requests` (giá trị đề nghị mã hóa, minh chứng PDF / ảnh trên disk riêng tư, tối đa một yêu cầu chờ); người gửi hủy được.
- Cán bộ khác (STU.approve trên hồ sơ — CTSV, ACAD; không tự duyệt) duyệt → áp dụng bằng `StudentService::update` (kiểm tra trùng CCCD, tuổi, ngành; lịch sử ghi "Duyệt yêu cầu #…"), hoặc từ chối có lý do; cả hai phát `StudentChangeRequestDecided` (EV-STU-03). Nhật ký kiểm toán ẩn giá trị đề nghị; API trả CCCD đã che.
- API: cổng sinh viên `GET|PUT /student/profile`, `POST /student/profile/photo`, `POST|PUT|DELETE /student/profile/contacts[/{id}]`, `POST /student/profile/change-requests[/{id}/cancel]`; cán bộ `GET /admin/student-change-requests[?status=]`, `GET …/{id}/evidence`, `POST …/{id}/approve|reject`.

## Trạng thái sinh viên (STU, FR-STU-009, 010; BR-STU-05, 10; BR-AUTH-07; EV-STU-02)

- `StudentStatusService::change($sv, $trangThai, $quyetDinh, $tep)`: chỉ theo sơ đồ BA 6.2 (Đang học → Bảo lưu / Thôi học / Buộc thôi học / Chuyển trường / Đã tốt nghiệp; Bảo lưu → Đang học / Thôi học / Buộc thôi học). Trạng thái cuối chỉ về Đang học bằng `special` (STU.approve toàn trường — ACAD), bắt buộc lý do. Mỗi lần có số quyết định, ngày hiệu lực, lý do, tệp (PDF / ảnh, disk `studentmanager.student.decision_disk`), ghi `student_status_histories` và nhật ký kiểm toán.
- Hệ quả trong cùng giao dịch: phát `StudentStatusChanged` (EV-STU-02) → ENR hủy đăng ký **chưa chốt** của học kỳ bắt đầu sau ngày hiệu lực (BR-STU-10, nhật ký đăng ký `status_cancelled`); đăng ký mới bị chặn vì ENR chỉ nhận sinh viên Đang học. AUTH (`UserService::applyStudentStatus`): thôi học / buộc thôi học / chuyển trường → hẹn ngừng tài khoản, tốt nghiệp → chỉ đọc, khôi phục đặc biệt → hoạt động (BR-AUTH-07).
- API: `GET|POST /admin/students/{id}/status` (lịch sử + trạng thái được chuyển tới; POST multipart có `attachment`), `GET /admin/student-status-histories/{id}/attachment`.

## Thời khóa biểu cơ bản (TTB, FR-TTB-001, 002; BR-TTB-02, 03, 04, 07)

- Khung tiết `periods`: số tiết, giờ bắt đầu – kết thúc, ca (sáng / chiều / tối; giữa hai ca là giờ nghỉ). Khung chung (`campus_id` trống) hoặc riêng một cơ sở; cơ sở có khung riêng thì dùng khung riêng (`PeriodService::periodsFor($coSo)`). Số tiết không trùng, giờ không chồng và tăng dần; tiết đã có lịch không đổi số / ca, không xóa; cơ sở đã có lịch theo khung chung thì không tạo khung riêng. Seeder: 15 tiết chung.
- Mẫu lịch `schedule_rules`: lớp học phần, thứ (ISO 1 = Thứ Hai … 7 = Chủ nhật, như `lecturer_availabilities`), tiết từ – đến, phòng, tuần từ – đến, mọi tuần / tuần lẻ / tuần chẵn. Tuần 1 là tuần chứa ngày bắt đầu học kỳ (`ScheduleRuleService::dateOf`).
- `ScheduleRuleService` từ chối: lớp đã Đang học / Kết thúc / Hủy; tiết không có trong khung; buổi vắt qua hai ca hoặc kết thúc sau `studentmanager.timetable.latest_end` (mặc định 21:00, BR-TTB-03); tuần vượt số tuần hoặc buổi ngoài ngày học kỳ (BR-TTB-07); lớp trực tiếp không có phòng; phòng thiếu chỗ so với sĩ số tối đa (BR-TTB-02) hoặc không xếp được (`RoomAvailability`); chồng với dòng lịch khác của cùng lớp. Tổng tiết lệch số tiết học phần (LT + TH) chỉ **cảnh báo** (`warnings`, BR-TTB-04).
- Xung đột (FR-TTB-003, `ScheduleConflictService`): trùng phòng, trùng giảng viên (BR-TTB-01), trùng lịch sinh viên = hai học phần bắt buộc cùng học kỳ dự kiến của một CTĐT đang áp dụng, lớp thực hành không ở phòng `practice_room_types` (BR-ROM-03). Mỗi loại chặn hay cảnh báo theo `studentmanager.timetable.conflicts`; thông báo nêu lớp gây xung đột. **ENR gọi `sectionsClash($lopA, $lopB)`** khi đăng ký. Đổi lịch thì liệt kê sinh viên bị trùng qua `affectedStudents()` (BR-TTB-08) — dùng hợp đồng `Timetable\Contracts\SectionStudents`, ENR bind cài đặt thật (tạm `NoSectionStudents`). `GET /admin/course-sections/{id}/schedule-conflicts`.
- Buổi học cụ thể `class_sessions` (FR-TTB-005, 009; BR-TTB-06): `SessionService::generate($lop)` sinh một buổi mỗi tuần học từ mẫu lịch; ngày trùng `holidays` (học kỳ hoặc toàn trường) thành **Nghỉ** kèm tên ngày nghỉ, cần dạy bù. Tự chạy sau mỗi lần thêm / sửa / xóa dòng lịch: buổi Dự kiến / Nghỉ từ hôm nay trở đi bị xóa mềm (giữ lịch sử) rồi sinh lại; buổi đã qua, Đã dạy, Dạy bù, Hủy và buổi Nghỉ đã có dạy bù giữ nguyên. `applyHolidays($hk)` + lệnh `timetable:apply-holidays` (chạy 00:30 hằng ngày) cho ngày nghỉ khai báo sau. `makeup()` xếp dạy bù cho buổi Nghỉ / Hủy (trong học kỳ, phòng xếp được, không trùng buổi của lớp / phòng); `cancel()` hủy buổi có lý do. Buổi Nghỉ / Hủy không tính vắng (`SessionStatus::countsForAttendance`, ATT dùng). API `GET /admin/course-sections/{id}/sessions`, `POST .../sessions/generate`, `POST /admin/class-sessions/{id}/makeup|cancel`.
- Nháp → công bố → khóa theo học kỳ (`term_timetables`, `TimetablePublication`; FR-TTB-006): công bố phát `TimetablePublished` (EV-TTB-01), từ đó sinh viên / giảng viên mới xem được lịch. Sau công bố, thêm / sửa / xóa dòng lịch phải có `reason`; buổi bị ảnh hưởng sớm nhất phải còn ≥ `change_notice_hours` giờ (mặc định 24), ít hơn thì gửi `urgent: true` (nhật ký ghi "[Khẩn cấp] lý do"); phát `ScheduleChanged` (EV-TTB-02). Đã khóa thì không đổi lịch, không dạy bù / hủy buổi (BR-TTB-05).
- Xem thời khóa biểu (`TimetableView`; FR-TTB-004): theo sinh viên, giảng viên, lớp học phần, lớp hành chính, phòng, khoa; `view=week|month|term`, `date`, `term_id`. `GET /admin/timetable?target=&id=` (TTB toàn trường xem mọi thứ kể cả nháp; DEAN xem khoa mình và lớp trong phạm vi), `GET /student/timetable`, `GET /teacher/timetable` (của chính mình, chỉ khi đã công bố). Trạng thái / công bố / khóa: `GET|POST /admin/terms/{id}/timetable[/publish|/lock]` (ACAD). "Sinh viên học lớp nào" lấy qua `SectionStudents::sectionIdsOfStudents` (ENR cài).
- Chưa làm (issue sau): lịch bận giảng viên khi xếp lịch (FR-TTB-010).
- API: `GET /admin/periods[?campus_id=]` (TTB.view), `POST|PUT|DELETE /admin/periods` (ACAD); `GET /admin/course-sections/{id}/schedule` (TTB.view theo phạm vi lớp: DEAN khoa mình, LEC lớp mình dạy), `POST /admin/course-sections/{id}/schedule`, `PUT|DELETE /admin/schedule-rules/{id}` (TTB toàn trường — ACAD; chữ C của DEAN, LEC ở ma trận TTB là đề xuất, không tự xếp lịch).

## Điểm — cơ cấu lớp và điểm thành phần (GRD, FR-GRD-001, 002; BR-GRD-05, 10)

- `section_grade_components` (kế thừa `grade_component_templates` của học phần, có `version`; sửa cơ cấu = phiên bản mới, bản cũ xóa mềm; khóa khi lớp đã có điểm), `grade_entries` (đăng ký – thành phần – điểm, `draft` / `missing`, CHECK 0–10).
- `GradebookService`: `initialize`, `replaceStructure` (GRD.update trên lớp), `saveDraft` (chỉ giảng viên được phân công, lớp Đang học / Kết thúc, đăng ký còn hiệu lực, khoảng và bước điểm theo `config/grades.php`), `gradebook` (lưới điểm, "chưa đủ dữ liệu" khi thiếu thành phần bắt buộc).
- `CourseGradeService::calculate($dangKy)` (#120; BR-GRD-01..03, 11): điểm thang 10 làm tròn `grades.round_digits`, chữ / hệ 4 theo `grading.scale` và đạt theo `grading.pass_score` của **bộ quy chế áp dụng cho khóa** (PolicyResolver), trượt điểm sàn thành phần bắt buộc → F, thiếu thành phần bắt buộc → `incomplete`, học phần Đạt / Không đạt không có điểm số; `save()` ghi `course_results`. Ví dụ UAT-07: 9,0 / 7,0 / 6,5 × 10 / 30 / 60 → 6,9, C, 2,0, Đạt. `GET /teacher/enrollments/{id}/grade-result` (GRD.view trên lớp).
- Duyệt và công bố điểm (#124; FR-GRD-004, 005; BR-GRD-06, 08): `section_grade_workflows` + `GradeWorkflowService` — Nháp → Đã nộp (giảng viên được phân công; mọi sinh viên phải đủ điểm bắt buộc, kết quả lưu `course_results`) → Khoa duyệt (DEAN, bật / tắt `GRD_FACULTY_APPROVAL`) → Đã xác nhận → Đã công bố (ngay hoặc hẹn giờ, lệnh `grades:publish-scheduled` mỗi 5 phút) → Đã khóa (ACAD / EXAM); trả lại bắt buộc lý do. Từ khi nộp, `saveDraft` bị chặn (bị trả lại thì sửa được). Công bố phát `GradesPublished` (EV-GRD-01, chỉ mã lớp). Sinh viên chỉ thấy lớp đã công bố, kể cả điểm thành phần (`GET /student/grades`). API: `/teacher/course-sections/{id}/grade-workflow` (xem, nộp), `/admin/grade-workflows`, `/admin/course-sections/{id}/grade-workflow` (xem, chuyển bước).
- GPA và xếp loại (#125; FR-GRD-008, 009; BR-GRD-04): `GpaService::recalculate($sv)` lưu `term_gpas`, `cumulative_gpas` — chỉ lớp đã công bố / khóa, bỏ học phần rút / hủy, học phần Đạt / Không đạt (`counts_toward_gpa` = false) và trạng thái loại trừ; GPA học kỳ tính cả F; GPA tích lũy lấy kết quả cao nhất mỗi học phần (`GRD_RETAKE_POLICY=highest`, hoặc `latest`); tín chỉ tích lũy = học phần đạt hoặc miễn / công nhận, mỗi học phần một lần; xếp loại theo `classification.bands` của bộ quy chế khóa. Tự tính lại khi `GradesPublished` / `GradeAdjusted` (listener `RecalculateGpa`), hoặc lệnh `grades:recalculate-gpa [MSSV]`. Ví dụ: B (3 TC) + C (3 TC) → 2,50 Khá. `GradeAcademicRecord` bind `Enrollment\Contracts\AcademicRecord` (đã đạt gồm miễn / công nhận, đã học, cảnh báo tạm theo `academic_warning.term_gpa_*`, học lực khá theo GPA học kỳ gần nhất). API: `GET /student/gpa`, `GET /admin/students/{id}/gpa` (GRD.view + sinh viên trong phạm vi STU.view).
- Điều chỉnh điểm sau công bố và trạng thái đặc biệt (#126; FR-GRD-006, 012; BR-GRD-06, 07): `GradeChangeService` — giảng viên được phân công gửi yêu cầu (lý do, minh chứng PDF / ảnh trên disk riêng tư, điểm mới từng thành phần; không trùng yêu cầu đang chờ, không trùng điểm cũ) → khoa duyệt (khi bật `GRD_FACULTY_APPROVAL`) → ACAD / EXAM xác nhận thì ghi điểm, `CourseGradeService::save`, lưu `grade_versions` (trước – sau, bất biến), phát `GradeAdjusted` (EV-GRD-02); không tự duyệt; từ chối bắt buộc lý do. `SpecialStatusService::set($dangKy, $gv, $trangThai, $lyDo)` khi bảng điểm còn Nháp / Bị trả lại: danh sách ở `config/grade_special_statuses.php` (vắng thi không lý do / cấm thi ghi 0 thi cuối kỳ; hoãn thi, miễn, công nhận… không có điểm số, `status` = `special`, không tính GPA, không cần đủ điểm khi nộp). API giảng viên: `POST /teacher/enrollments/{id}/special-status`, `GET|POST /teacher/…grade-change-requests`, `GET /teacher/enrollments/{id}/grade-history`; quản trị (GRD.approve): `/admin/grade-change-requests` (danh sách người xem duyệt được, `can_act`), `/{id}/approve|reject|evidence`; trang `/admin/duyet-dieu-chinh-diem`.
- Bảng điểm và sổ điểm (#127; FR-GRD-010, 011): `TranscriptService::personal()` (theo học kỳ, GPA từng kỳ và tích lũy, ký hiệu trạng thái đặc biệt; PDF `pdf.grade-transcript` tiếng Việt / Anh, mã QR chờ DOC P2), `section()` (điểm thành phần, kết quả, thống kê điểm trung bình / tỷ lệ đạt / phân bố; PDF `pdf.grade-section` có chỗ ký, Excel). API: `GET /student/transcript?format=json|pdf&lang=vi|en`, `GET /admin/students/{id}/transcript`, `GET /teacher|admin/course-sections/{id}/grade-report?format=json|pdf|xlsx` (GRD.view trên lớp). Trang sinh viên `/student/ket-qua-hoc-tap`.
- API cổng giảng viên: `/teacher/course-sections/{id}/gradebook`, `grade-components/initialize`, `PUT grade-components`, `POST grades`, `GET grades/template` (mẫu Excel), `POST grades/import`.

## Dashboard sinh viên, giảng viên (RPT, FR-RPT-009, 010; FR-TCH-009)

- `DashboardService` (chỉ dữ liệu của chính mình): lịch hôm nay từ buổi học cụ thể khi thời khóa biểu đã công bố; hạn chót = mốc lịch học vụ sắp tới trong `studentmanager.dashboard.deadline_days` ngày (sinh viên: đăng ký, hủy, rút, học phí, thi, công bố điểm + hạn đóng đợt đăng ký đang mở; giảng viên: bắt đầu / kết thúc giảng dạy, thi, hạn nhập điểm, công bố điểm); giảng viên có danh sách lớp đang trong giai đoạn nhập điểm. GPA / tín chỉ tích lũy / tín chỉ còn thiếu (GRD #125) và thông báo chưa đọc (NOT #115) — nối ở #118.
- API `GET /student/dashboard/data`, `GET /teacher/dashboard/data`.

## Thông báo trong ứng dụng (NOT, FR-NOT-001; BR-NOT-01, 06, 09)

- Bảng `notifications` (nội dung một lần gửi: nguồn sự kiện / thủ công / hệ thống, mã `EV-…`, `event_key` duy nhất, nhóm, liên kết tương đối, khẩn, bắt buộc, ghim, hạn hiển thị, thu hồi) và `notification_recipients` (mỗi người nhận một dòng, `read_at`).
- **Module khác gửi thông báo qua một cổng duy nhất:** `app(NotificationDispatcher::class)->send(['title' => …, 'body' => …, 'link' => '/student/…', 'category' => 'grade', 'event_code' => 'EV-GRD-01', 'event_key' => 'EV-GRD-01:section:12:…'], $userIds)`. Cùng `event_key` gửi lại thì trả `null`, không tạo trùng (BR-NOT-09). Ghi người nhận theo lô `notifications.chunk_size`; bỏ tài khoản đã ngừng; liên kết phải là đường dẫn trong hệ thống. **Không đưa điểm, số nợ, CCCD vào nội dung** (BR-NOT-03).
- `NotificationCenter`: số chưa đọc, danh sách (lọc chưa đọc / nhóm / từ khóa; hết hạn thì ẩn, xem ở kho `archived=1` — BR-NOT-06), đánh dấu đã đọc / đọc tất cả. Mọi truy vấn đi từ dòng người nhận của chính người dùng; thông báo người khác trả 404.
- API (mọi cổng, `NOT.view`): `GET /notifications`, `GET /notifications/unread-count`, `POST /notifications/{id}/read`, `POST /notifications/read-all`; trang `/thong-bao`; chuông trên thanh trên cùng của `layouts/portal` hỏi số chưa đọc mỗi `notifications.poll_seconds` giây. Lệnh `notifications:prune` (hằng ngày) xóa thông báo hết hạn quá `notifications.retention_days` ngày.

## Thông báo tự động theo sự kiện (NOT, FR-NOT-004, 007; BR-NOT-02, 03, 09; phụ lục A)

- `event_subscriptions`: 38 sự kiện của phụ lục A (`EventCatalog`, nạp bằng `NotificationEventSeeder` — chạy lại không ghi đè mẫu / bật tắt ADMIN đã sửa): người nhận, kênh, mẫu `{{bien}}`, bắt buộc, bật / tắt, `wired` (module nguồn đã phát). P1 chỉ gửi kênh trong ứng dụng; email ở P2.
- `EventNotifier::notify($ma, $khoa, $userIds, $bien, $lienKet)`: sự kiện tắt / không có kênh trong ứng dụng thì bỏ qua; bỏ người đã tắt loại không bắt buộc (`notification_preferences`); khóa `{$ma}:{$khoa}` chống trùng. **Biến chỉ là tên học phần, mã lớp, MSSV, lý do — không truyền điểm, số nợ, CCCD.**
- `EventNotificationSubscriber` nối sự kiện P1: EV-AUTH-02 (`AccountSecurityAlert`: khóa tạm, đăng nhập bất thường → chủ tài khoản + ADMIN), EV-AUTH-03 (`UserRolesChanged`), EV-STU-01 (`StudentAdmitted`), EV-STU-02 (sinh viên + cố vấn hiện hành), EV-STU-03, EV-ENR-02 (`EnrollmentChanged`, phát trong `EnrollmentService` sau mỗi đăng ký / hủy / rút / đổi lớp thành công), EV-ENR-03 (sinh viên + giảng viên của lớp bị hủy), EV-TTB-01, EV-TTB-02, EV-GRD-01 (mỗi sinh viên của lớp một thông báo, không kèm điểm), EV-GRD-02 (sinh viên + giảng viên). `RecipientResolver` tìm tài khoản người nhận. Module mới phát sự kiện thì thêm một hàm vào subscriber và đặt `wired` trong `EventCatalog`.
- API: ADMIN `GET /admin/notification-events`, `PUT /admin/notification-events/{EV-…}` (NOT.update toàn trường; sự kiện bắt buộc không tắt được); người dùng `GET|PUT /notifications/preferences` (loại bắt buộc không tắt được), trang `/tai-khoan/thong-bao`.

## Thông báo thủ công, hẹn giờ (NOT, FR-NOT-006, 009, 015; BR-NOT-05, 08)

- `AudienceResolver`: bộ chọn `{"targets": [{"type": "all|faculty|major|cohort|admin_class|course_section", "id": …}], "student_codes": […], "group_id": …}` → tài khoản sinh viên đang học / bảo lưu. **Phạm vi (BR-NOT-05):** toàn trường / khóa cần NOT.create toàn trường; khoa / ngành thì toàn trường hoặc lãnh đạo khoa đó; lớp hành chính, lớp học phần, MSSV phải nằm trong phạm vi NOT.create (khoa / lớp mình dạy / lớp mình cố vấn). Một mục ngoài phạm vi là từ chối cả lần gửi (422 kèm cách khắc phục); cuối cùng vẫn lọc lại theo phạm vi. `options()` chỉ liệt kê đối tượng người gửi được chọn.
- `AnnouncementService`: `preview` (số người nhận), `create` (gửi ngay hoặc hẹn giờ — `status` = scheduled, lệnh `notifications:send-scheduled` chạy mỗi phút, tính lại người nhận theo phạm vi lúc gửi; ghim, khẩn, hạn hiển thị, tệp đính kèm disk riêng tư; lưu người gửi và đối tượng), `revoke` (chỉ ADMIN, chỉ thông báo thủ công; người nhận không còn thấy), `history` (tỷ lệ đã đọc; ADMIN thấy tất cả), nhóm người nhận `recipient_groups` (lưu bộ chọn của chính người gửi, kiểm tra phạm vi khi lưu và khi gửi).
- API (NOT.create): `/admin|teacher/announcements` (`options`, `preview`, danh sách, gửi — form có `audience` dạng JSON), `/admin/announcements/{id}/revoke` (NOT.delete toàn trường + vai trò ADMIN), `/admin|teacher/recipient-groups`; tệp đính kèm `GET /notifications/{id}/attachment` (người nhận, người gửi, ADMIN). Trang `/admin/soan-thong-bao`, `/teacher/gui-thong-bao`.

## Dashboard quản trị (RPT, FR-RPT-001, 002, 008; BR-RPT-03, 07)

- `AdminDashboardService::data($user)`: bộ widget theo vai trò ở `studentmanager.dashboard.widgets` (người nhiều vai trò thấy hợp các bộ): sinh viên theo trạng thái, nhập học mới (theo ngành, từ đầu năm học hiện hành), sĩ số lớp học phần của học kỳ hiện hành (dưới tối thiểu / đầy), tiến độ nhập điểm (theo trạng thái bảng điểm), sĩ số lớp hành chính, yêu cầu điều chỉnh điểm chờ duyệt.
- Mọi chỉ số đọc thẳng từ bảng nghiệp vụ và lọc theo phạm vi **RPT.view** (`AccessControl::constrain`): lãnh đạo khoa chỉ thấy dữ liệu khoa mình. Mỗi widget có `definition` công bố (BR-RPT-07); trang tự làm mới sau `studentmanager.dashboard.refresh_seconds` (mặc định 300). Học kỳ hiện hành: học kỳ ACAD đặt, không có thì theo ngày.
- API `GET /admin/dashboard/data` (RPT.view), trang `admin.home` (`admin/dashboard.blade.php`; mẫu FE cũ giữ ở `admin/admin-dashboard.blade.php`). Dashboard sinh viên / giảng viên (#121) nay có GPA từng kỳ, GPA / tín chỉ tích lũy, tín chỉ còn thiếu theo CTĐT và thông báo chưa đọc.

## Dữ liệu demo (SYS, FR-SYS-016; BR-SYS-10; NFR-DAT-02)

- `php artisan migrate:fresh --seed` (dữ liệu mẫu của các module) rồi `php artisan demo:seed` (`DemoDataSeeder`, khoảng 4 phút). Cả hai **không chạy ở production**: `demo:seed` từ chối; `DatabaseSeeder` bỏ qua tài khoản dùng thử (`DevUserSeeder`, `DevRoleUsersSeeder`). Chạy `demo:seed` lần hai thì bỏ qua (đã có năm học 2025-2026); cả lần chạy trong một giao dịch nên lỗi giữa chừng không để lại dữ liệu dở dang.
- Kết quả: 300 sinh viên (khóa 2025 và 2026, mỗi ngành 25 / khóa; `Student::factory()` dùng Faker vi_VN, CCCD giả `0993…`, email `example.test`), nhập học qua `AdmissionService` (MSSV, tài khoản, CTĐT, lớp hành chính). Năm học 2025–2026 hai học kỳ **đã khóa** (lớp theo CTĐT, chia lớp khi quá 80 sinh viên, điểm công bố + khóa, GPA đã tính); học kỳ 1 2026–2027 **đang diễn ra** (đăng ký đã chốt, đang nhập điểm, hai lớp đã nộp). Tình huống đặc biệt: học lại, học lực yếu (bị cảnh báo), vắng thi không lý do, bảo lưu. Nợ học phí, vắng quá ngưỡng chờ FEE, ATT.
- Chỉ trong dữ liệu demo: CTĐT mẫu đổi `cohort_from` từ 2026 về 2025 để khóa 2025 nhập học được. Mật khẩu sinh viên demo = `DevRoleUsersSeeder::PASSWORD`, đăng nhập bằng MSSV.

## Đăng ký học phần (ENR, FR-ENR-001, 002; BR-ENR-01, 11)

- Đợt đăng ký `registration_periods`: học kỳ, vòng (vòng 1 / vòng 2 / bổ sung), mở – đóng (nhập giờ Việt Nam, lưu UTC; không nhập thì lấy mốc "Mở đăng ký" 00:00 và "Đóng đăng ký" 23:59 trong lịch học vụ ACY), khung tín chỉ riêng (trống = theo quy chế, #104), trạng thái nháp → công bố → đã chốt (#113). Đối tượng `registration_period_targets`: mỗi dòng một tổ hợp khóa / ngành / lớp hành chính, không có dòng nào = mọi sinh viên.
- `RegistrationAccess`: `now()` là giờ máy chủ (BR-ENR-11); `openPeriodsFor($sv)`; `problems($dot, $sv)` / `assertCanRegister()` — Đang học, thuộc đối tượng, đợt đang mở (BR-ENR-01). **#105 gọi trước mỗi lần đăng ký.**
- `AvailableSections::listFor($sv, $dot)`: lớp của học kỳ thuộc học phần trong CTĐT hiện hành, kèm sĩ số còn lại (qua `SectionStudents` của TTB), lịch, giảng viên, phòng; lớp không chọn được có `reasons` (chưa mở đăng ký, hết chỗ). **#104 bổ sung lý do trong `reasonsFor()`.**
- `EnrollmentRules::check($sv, $lop, $dot, $loai, $doiTuLop, $boQua)` (FR-ENR-004): trả danh sách lý do từ chối, mỗi lý do nêu cách khắc phục; rỗng là được. Gồm: đợt / sinh viên (BR-ENR-01), lớp mở đăng ký – đúng học kỳ – còn chỗ (BR-ENR-05), học phần đã đạt chỉ học cải thiện (BR-ENR-07), cùng học phần hai lớp (cấu hình `allow_same_subject_twice`) và trùng lịch qua `ScheduleConflictService::sectionsClash` (BR-ENR-04), tiên quyết phải đạt / học trước chỉ cần đã học / song hành đăng ký cùng hoặc trước (BR-ENR-03), tín chỉ tối đa (BR-ENR-02), nợ quá hạn khi bật `block_on_debt` (BR-ENR-09). `$boQua` cho cán bộ đăng ký thay (#113). **#105 gọi trong giao dịch trước khi ghi nhận.**
- `CreditLimits::for($sv, $dot)`: khung tín chỉ = trung bình một học kỳ CTĐT × `registration.load_min_ratio` / `load_max_ratio` (quy chế khóa); khung riêng khi cảnh báo học vụ, học kỳ cuối, học lực khá (`studentmanager.enrollment`); khung của đợt ghi đè.
- Hợp đồng chờ module khác: `Enrollment\Contracts\AcademicRecord` (đã đạt / đã học / cảnh báo / học lực — GRD bind `GradeAcademicRecord`, #125), `TuitionStatus` (nợ quá hạn — FEE bind, tạm `NoTuitionDebt`); "đã đăng ký" đọc qua `Timetable\Contracts\SectionStudents` (#105 bind).
- Đăng ký `enrollments` (loại thường / học lại / cải thiện; Đã đăng ký → Đã chốt → Hoàn thành, Đã hủy, Đã rút; nguồn sinh viên / cán bộ; lý do ngoại lệ). Cột sinh `active_section_id` + unique (sinh viên, lớp còn hiệu lực) chặn đăng ký trùng ở CSDL. `EnrollmentSectionStudents` thay `NoSectionStudents` của TTB (sĩ số, trùng lịch, BR-TTB-08 dùng dữ liệu thật).
- `EnrollmentService` (FR-ENR-003, 005..008): `register` khóa dòng sinh viên rồi lớp (`lockForUpdate`), gọi `EnrollmentRules` trên dữ liệu vừa khóa rồi mới ghi — chỗ cuối chỉ một người được (đã thử 12 tiến trình song song: 1 thành công, 11 "đã đủ"); `cancel` khi đợt của sinh viên còn mở hoặc chưa qua mốc "Hạn hủy học phần"; `withdraw` sau hạn hủy tới hết ngày "Hạn rút học phần" (BR-ENR-06); `change` sang lớp khác cùng học phần trong một giao dịch, lỗi thì vẫn ở lớp cũ. Mỗi lần thử ghi `enrollment_logs` (IP, lý do; FR-ENR-017). Lỗi điều kiện trả JSON `reasons` (danh sách) kèm `message`.
- `EnrollmentReport` (FR-ENR-010, 013): `result($sv, $hk)` — học phần đã đăng ký / đã rút (không tính GPA), tổng tín chỉ, khung tín chỉ, thời khóa biểu tuần có giờ theo khung tiết; `slipPdf()` — phiếu đăng ký PDF (view `pdf.enrollment-slip`, phông `studentmanager.export.pdf_font`, tải phiếu ghi audit `downloaded`); `stats($hk)` — từng lớp (đã đăng ký / tối thiểu / tối đa, đầy hoặc thiếu), sinh viên Đang học chưa đăng ký hoặc dưới mức tín chỉ tối thiểu. Không gửi `term_id` thì lấy học kỳ của đợt mới nhất.
- Đăng ký / hủy thay và chốt (FR-ENR-009, 012; BR-ENR-08): cán bộ ENR toàn trường đăng ký thay `POST /admin/students/{id}/enrollments` (`skip`: period, capacity, prerequisite, schedule, credits, debt — có `skip` thì bắt buộc `reason`; người thao tác ghi vào `approved_by`, lý do vào `override_reason`; bỏ qua `period` thì nhận cả lớp Đóng đăng ký / Đang học), hủy thay `POST /admin/enrollments/{id}/cancel` (bắt buộc lý do, hủy được cả đăng ký Đã chốt, nhật ký kiểm toán ghi lý do). `RegistrationFinalizer`: `GET|POST /admin/terms/{id}/registration/finalize` — xem trước rồi chốt; chặn khi còn đợt mở, còn lớp dưới sĩ số (#108), sinh viên dưới tín chỉ tối thiểu thì phải `acknowledge_under_min`; chốt = đăng ký → Đã chốt, đợt → Đã chốt, lớp Mở đăng ký → Đóng đăng ký, phát `RegistrationFinalized` (FEE phát sinh công nợ). Sau chốt sinh viên không tự hủy; đăng ký thay sau chốt có trạng thái Đã chốt; mọi thay đổi sau chốt phát `EnrollmentAdjusted` (FEE điều chỉnh).
- API: `/admin/registration-periods` (xem = ENR.view, đợt nháp chỉ ENR toàn trường thấy; thêm / sửa / công bố / xóa nháp = ACAD), `GET /admin/students/{id}/registration-options[?period_id=]`, cổng sinh viên `GET /student/registration/options`, `GET|POST /student/registration/enrollments`, `POST /student/registration/enrollments/{id}/cancel|withdraw|change`; danh sách sinh viên của lớp `GET /admin/course-sections/{id}/enrollments` (ENR.view theo phạm vi lớp); kết quả `GET /student/registration/result[/slip]`, `GET /admin/students/{id}/registration-result[/slip]`; thống kê `GET /admin/enrollment-stats[?term_id=]` (DEAN, LEC chỉ lớp trong phạm vi, không có danh sách / số sinh viên toàn trường).
- Seeder `EnrollmentSeeder` (dùng thử): xác nhận nhập học 12 thí sinh, mở đăng ký các lớp HK2 có giảng viên chính và lịch, đợt vòng 1 khóa 2026 mở từ hôm qua tới 30 ngày sau.

## Khoa, bộ môn, ngành (FAC, FR-FAC-001..005, 010)

- Bảng `faculties` → `departments`; `faculties` → `majors` → `specializations`; `training_types` (đúng một hệ mặc định: chính quy). Lấy hệ mặc định: `TrainingType::default()`.
- `Major::standard_terms` và `averageCreditsPerTerm()` là căn cứ thời gian học tối đa (STU) và khối lượng đăng ký (ENR) (BR-FAC-07).
- **Phạm vi FACULTY dùng chung:** `app(FacultyAccess::class)->facultyIds($user)` / `departmentIds($user)` cho các module có `faculty_id`, `department_id` (ví dụ `DataScope::Faculty => $q->whereIn('faculty_id', …)`). Nguồn là nhiệm kỳ lãnh đạo đang hiệu lực: trưởng/phó khoa quản lý khoa và mọi bộ môn của khoa, trưởng/phó bộ môn chỉ quản lý bộ môn đó.
- **Lãnh đạo đơn vị (FR-FAC-006):** `LeadershipService::assign()` giao chức vụ theo nhiệm kỳ, tự sinh dòng vai trò DEAN có cùng ngày hiệu lực nên quyền đổi đúng ngày mà không cần sửa tay vai trò (BR-FAC-06); tối đa một trưởng mỗi đơn vị (BR-FAC-04); `replace_current` để thay trưởng từ ngày D. Đừng gán hoặc gỡ DEAN bằng tay cho lãnh đạo đơn vị: dùng nhiệm kỳ. Lệnh `faculty:sync-leadership` chạy hằng ngày sửa dòng vai trò lệch. Điều kiện "người được giao thuộc đơn vị" và "chỉ trưởng ở một đơn vị" đặt sau `LeaderEligibility` (TCH cài `TeacherLeaderEligibility`, xem mục Giảng viên). API: `/admin/leadership-terms`.
- **Module có khóa ngoại tới khoa, bộ môn, ngành… đăng ký với `ReferenceRegistry`**, kèm `active:` (điều kiện "còn hoạt động", ví dụ giảng viên đang làm việc) để đơn vị không ngừng được khi chưa chuyển hết dữ liệu (BR-FAC-05). Đơn vị đã có dữ liệu liên quan thì không đổi mã (BR-FAC-01) và không xóa.
- API: `/admin/{faculties|departments|majors|specializations}` (+ `/{id}`, `/export`), `/admin/training-types`. DEAN chỉ thấy và chỉ sửa thông tin mô tả, liên hệ của đơn vị mình. Xuất Excel cần quyền `FAC.export`: **ma trận BA 4.2 chưa cấp X cho FAC** nên mặc định không ai xuất được (FR-FAC-010 ghi ACAD xuất) — cần chốt và cấp qua `/admin/roles/{id}/permissions`.

## Học phần (SUB, FR-SUB-001, 002, 003)

- Bảng `subjects`: mã (duy nhất, không cấp lại), tên vi/en, **bộ môn quản lý** (`department_id`), mô tả, ngôn ngữ giảng dạy (`TeachingLanguage`), tín chỉ tổng / lý thuyết / thực hành, số tiết lý thuyết / thực hành, giờ tự học, loại bắt buộc / tự chọn (`SubjectRequirement`), khối kiến thức (`KnowledgeBlock`: đại cương, cơ sở ngành, chuyên ngành, thực tập, đồ án / khóa luận), cờ `counts_toward_gpa` và `is_graduation_requirement`, trạng thái Hoạt động / Ngừng.
- **Tín chỉ (BR-SUB-02):** lý thuyết + thực hành = tổng (kiểm tra ở `SubjectService` và ràng buộc CHECK của MySQL). Bỏ trống thì phần còn thiếu tự tính; số tiết, giờ tự học bỏ trống thì tính theo định mức `studentmanager.subject` (15 tiết / tín chỉ lý thuyết, 30 tiết / tín chỉ thực hành, 30 giờ tự học / tín chỉ). Chỉ học phần điều kiện (không tính GPA) mới được 0 tín chỉ; tổng tối đa `SUBJECT_MAX_CREDITS` (mặc định 20).
- **Học phần điều kiện (BR-SUB-06):** `counts_toward_gpa = false` (ví dụ giáo dục thể chất, quốc phòng – an ninh) thì GPA bỏ qua, nhưng `is_graduation_requirement = true` thì vẫn phải Đạt khi xét tốt nghiệp. Dùng `$subject->isPassFailOnly()`.
- **Quan hệ học phần (FR-SUB-004, #92):** bảng `subject_relations`, dòng (B, A, loại) đọc là "A là {loại} của B"; loại `RelationType`: tiên quyết (phải Đạt A), học trước (đã học A), song hành (A học trước hoặc cùng kỳ), tương đương (hai chiều, có `effective_from`/`effective_to`, số quyết định của khoa; BR-SUB-07). `SubjectRelationService::add()` chặn tự tham chiếu và **vòng lặp kể cả gián tiếp** (BR-SUB-03; vòng chỉ gồm song hành thì hợp lệ), mỗi cặp chỉ một quan hệ thứ tự. **ENR gọi `requirementsOf($subject)`** (điều kiện trực tiếp theo loại); **CUR gọi `allPrerequisites($subject)`** (mọi học phần phải học trước, kể cả gián tiếp); GRA/STU gọi `equivalentsOf($subject, $ngay)`. API: `/admin/subjects/{id}/relations` (+ `/prerequisites`), `/admin/subject-relations/{id}/end`, quyền SUB.update trên học phần B. Seeder `SubjectRelationSeeder`.
- **Module lưu `subject_id` (CUR, CLS, GRD…) phải đăng ký với `ReferenceRegistry`** trong ServiceProvider của mình (khai báo `active:` cho dữ liệu đang diễn ra, ví dụ lớp đang mở). Khi đó học phần đã có dữ liệu: không đổi mã (BR-SUB-01), không đổi tín chỉ hay cờ tính GPA (BR-SUB-05: phải qua phiên bản học phần, FR-SUB-007 chưa làm), không xóa, chỉ ngừng; còn dữ liệu đang diễn ra thì chưa ngừng được.
- **Cơ cấu điểm (FR-SUB-005, #93):** bảng `grade_component_templates` (tên, loại `ComponentKind`, trọng số %, điểm sàn thang 10, bắt buộc, đánh giá từ xa). Lưu cả cơ cấu một lần bằng `GradeStructureService::replace()`: tối thiểu `grading.min_components` thành phần, tổng đúng 100% (báo thiếu/thừa bao nhiêu), thi cuối kỳ ≥ `grading.final_exam_min_weight`, từ xa ≤ `grading.remote_max_weight` (BR-SUB-04; ngưỡng lấy từ bộ quy chế của khóa năm hiện tại, chưa có bộ nào thì dùng mặc định phụ lục C). Học phần đã có lớp/điểm thì không đổi cơ cấu (BR-SUB-05). **GRD gọi `templateFor($subject)`** để sao chép sang lớp học phần và `validate($components, $khoa)` khi lớp điều chỉnh. API `GET|PUT /admin/subjects/{id}/grade-components`. Seeder `GradeStructureSeeder` (10/30/60, thực tập – đồ án 30/20/50).
- Bộ môn còn học phần đang hoạt động thì không ngừng được; đã có học phần thì không xóa, không đổi mã (SUB đăng ký với `ReferenceRegistry`).
- Phạm vi: DEAN (FACULTY) xem, thêm, sửa học phần của bộ môn mình quản lý, không chuyển học phần ra ngoài phạm vi; **LEC (SECTION) tạm thời không thấy học phần nào** cho tới khi CLS (#107) bổ sung điều kiện "học phần của lớp được phân công" trong `Subject::applyDataScope`; các vai trò khác xem toàn trường.
- API: `/admin/subjects` (lọc `q` không dấu, `department_id`, `faculty_id`, `knowledge_block`, `requirement`, `status`, `credits`, `counts_toward_gpa`, `is_graduation_requirement`; `+ /{id}`, `/export`). Quyền `SUB.*`, xuất cần `SUB.export` (ACAD). Seeder: 60 học phần mẫu, gồm 7 học phần điều kiện; khoa Khoa học cơ bản (không có ngành) quản lý học phần đại cương.
- **Đề cương (FR-SUB-006, BR-SUB-08, #90):** bảng `syllabi` có phiên bản (mục tiêu, chuẩn đầu ra, nội dung theo tuần/chương, phương pháp, đánh giá, tài liệu, tệp PDF). `SyllabusService`: mỗi học phần một phiên bản đang soạn; nháp → gửi duyệt → duyệt / trả lại (không tự duyệt); bản duyệt mới thay bản cũ. Tệp PDF kiểm tra chữ ký `%PDF` thật, tối đa `SYLLABUS_MAX_KB`, tên ngẫu nhiên trên disk riêng tư `SYLLABUS_DISK`, chỉ tải qua API có kiểm tra quyền (NFR-SEC-07). **CLS gọi `assertUsableForNewSection($subject)`** khi mở lớp mới (tắt yêu cầu duyệt bằng `SYLLABUS_REQUIRES_APPROVAL=false`). Phạm vi SECTION của LEC đi qua `App\Modules\Subject\Contracts\SubjectAssignments`: TCH tạm cài theo bộ môn chính / kiêm nhiệm của giảng viên, **CLS (#107) bind lại theo lớp được phân công**. API `/admin/subjects/{id}/syllabi`, `/admin/syllabi/{id}` (+ `/file`, `/submit`, `/approve`, `/reject`).
## Dữ liệu đang được tham chiếu (`ReferenceRegistry`)

Quy tắc "đã được dùng thì chỉ ngừng, không xóa / không đổi mã" (BR-SYS-09, BR-FAC-01, BR-ROM-04, GC-02) dùng chung `App\Support\References\ReferenceRegistry`. **Module nào thêm khóa ngoại tới bảng của module khác thì đăng ký trong ServiceProvider của mình**, để module kia biết mà chặn xóa:

```php
// ví dụ trong StudentServiceProvider::boot()
app(ReferenceRegistry::class)->register(LookupValue::class, 'students', 'gender_id', 'sinh viên');
```

`usages($model)` trả `['sinh viên' => 120]` (tính cả bản ghi đã xóa mềm), `ReferenceRegistry::describe()` ghép thành "120 sinh viên" để đưa vào thông báo lỗi. Bảng chưa tồn tại (module chưa cài) được bỏ qua.

## Chương trình đào tạo (CUR, FR-CUR-001, 002, 003)

- Bảng `curricula` (ngành, chuyên ngành tùy chọn, hệ đào tạo, khóa áp dụng `cohort_from`–`cohort_to`, tổng tín chỉ, số học kỳ chuẩn, `version`, trạng thái nháp / đã ban hành / ngừng), `curriculum_blocks` (khối kiến thức, tín chỉ yêu cầu), `curriculum_elective_groups` (chọn tối thiểu x tín chỉ), `curriculum_subjects` (học phần, khối, nhóm tự chọn hoặc bắt buộc, học kỳ dự kiến, thứ tự).
- `CurriculumService`: chỉ CTĐT **nháp** mới sửa được, đã ban hành thì phải tạo phiên bản (BR-CUR-04, #86); thời gian chuẩn khớp ngành (BR-CUR-06); mỗi học phần một lần trong CTĐT; thêm/sửa học phần trả về **cảnh báo tiên quyết** (tiên quyết / học trước phải có trong CTĐT và ở học kỳ sớm hơn, song hành cùng kỳ hoặc sớm hơn; được xác nhận "tiên quyết ngoài CTĐT" thì bỏ qua) (BR-CUR-03).
- **Ban hành** (`publish`, quyền CUR.approve toàn trường = ACAD): từng khối đủ tín chỉ, các khối cộng lại đủ tổng, CTĐT đủ tổng, nhóm tự chọn có đủ học phần để chọn, không còn cảnh báo tiên quyết (BR-CUR-02, 03); không trùng CTĐT đang áp dụng cùng ngành – hệ – khóa (BR-CUR-01). Thiếu thì báo rõ thiếu bao nhiêu tín chỉ (`CurriculumAnalyzer::publishProblems`, API `GET /admin/curricula/{id}/check`). Học phần điều kiện (không tính GPA) không cộng vào tổng tín chỉ.
- **Module khác dùng `CurriculumCatalog`:** CLS gọi `isInForce($subject)` trước khi mở lớp (BR-CUR-07); STU gọi `forStudent($majorId, $khoa, $heDaoTao, $chuyenNganh)` để gán CTĐT khi nhập học.
- Học phần thuộc CTĐT đã ban hành thì không đổi mã / tín chỉ, không xóa; CTĐT đang áp dụng thì học phần chưa ngừng được. Ngành, chuyên ngành, hệ đào tạo còn CTĐT đang áp dụng thì không ngừng được (đăng ký `ReferenceRegistry`).
- Phạm vi: DEAN (FACULTY) xây dựng CTĐT của ngành thuộc khoa mình, không ban hành; STU (OWN) chờ STU gán CTĐT cho sinh viên nên tạm thời không thấy; các vai trò khác xem. API `/admin/curricula` (+ `/{id}`, `/check`, `/publish`, `/blocks`, `/elective-groups`, `/subjects`), `/admin/curriculum-blocks/{id}`, `/admin/curriculum-elective-groups/{id}`, `/admin/curriculum-subjects/{id}`. Seeder: 6 CTĐT mẫu rút gọn (mỗi ngành một), khóa 2026, đã ban hành.
- **Điều kiện tốt nghiệp (FR-CUR-004, BR-CUR-05, #86):** bảng `curriculum_graduation_requirements` (một dòng mỗi phiên bản CTĐT): số tín chỉ tối thiểu (trống = tổng tín chỉ CTĐT), CGPA tối thiểu (trống = `graduation.min_cgpa` của bộ quy chế theo khóa, mặc định 2,00), chuẩn ngoại ngữ, tin học, bắt buộc thực tập / khóa luận (mặc định theo khối của CTĐT), cho phép học phần thay khóa luận, không đang bị đình chỉ. Chỉ sửa ở bản nháp; ban hành thì lưu cố định; thiếu khối thực tập / khóa luận mà điều kiện yêu cầu thì chưa ban hành được. **GRA gọi `GraduationRequirementService::effectiveFor($ctdt, $khoa)`** (kèm danh sách học phần điều kiện phải Đạt). API `GET|PUT /admin/curricula/{id}/graduation-requirements`.
- **Phiên bản CTĐT (FR-CUR-005, BR-CUR-04, #86):** CTĐT đã ban hành muốn đổi thì `CurriculumVersionService::newVersion($banDangApDung, $tuKhoa)` tạo bản nháp cùng mã, `version` + 1, chép khối, nhóm tự chọn, học phần, điều kiện tốt nghiệp; khóa áp dụng phải sau khóa bắt đầu của bản cũ; mỗi mã chỉ một bản nháp. Ban hành bản mới thì bản cũ tự dừng ở khóa liền trước (`cohort_to`), nên `CurriculumCatalog::forStudent()` trả đúng phiên bản theo khóa và sinh viên khóa cũ không đổi. `curricula.effective_from` = ngày hiệu lực (mặc định ngày ban hành). API `GET|POST /admin/curricula/{id}/versions`.
