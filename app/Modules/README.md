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
- **Quên mật khẩu** (#82) theo liên kết đặt lại một lần, hạn 30 phút (BR-AUTH-03): người dùng tự chọn mật khẩu mới, nên thêm hàm `reset()` vào `PasswordService` dùng lại `store()` (lịch sử mật khẩu, đổi `remember_token`) và đăng xuất mọi phiên.
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

## Giảng viên (TCH, FR-TCH-001, 003)

- Bảng `lecturers`: mã giảng viên (duy nhất, không cấp lại, cũng là tên đăng nhập), **đúng một đơn vị chính là bộ môn** (BR-FAC-03), chức danh (`AcademicTitle`), học vị (`AcademicDegree`), chuyên môn, liên hệ, ảnh, CCCD (nhạy cảm, che trong nhật ký), tình trạng (`LecturerStatus`: đang làm việc / nghỉ việc / nghỉ hưu). `lecturer_units`: kiêm nhiệm ở bộ môn hoặc khoa khác, có ngày hiệu lực. Chức vụ quản lý vẫn là nhiệm kỳ lãnh đạo của FAC.
- **Tạo hồ sơ (BR-TCH-01):** `LecturerService::create($data)` với `account` = `new` (mặc định: tạo tài khoản qua `UserService`, tên đăng nhập = mã, vai trò LEC, mật khẩu tạm gửi email), `link` (gắn `user_id` có sẵn, bổ sung LEC) hoặc `none`. Mỗi hồ sơ tối đa một tài khoản; cấp sau bằng `createAccount()`. Đổi họ tên trên hồ sơ thì tên tài khoản đổi theo.
- **Phân công (CLS, TTB, ATT, EXM, GRD) phải hỏi `$lecturer->isAssignable()`** (chỉ giảng viên đang làm việc, BR-TCH-02). Lấy hồ sơ của người đăng nhập: `Lecturer::query()->where('user_id', $user->id)`.
- **Nghỉ việc / nghỉ hưu (BR-TCH-02):** `changeStatus($lecturer, LecturerStatus::Resigned, $ngay, $lyDo)` bị từ chối khi còn lớp đang dạy hoặc chức vụ quản lý sau ngày nghỉ, kèm danh sách cần chuyển (`workToHandOver()`, API `GET /admin/lecturers/{id}/handover`). **CLS (#107) bind `App\Modules\Teacher\Contracts\TeachingAssignments`** bằng lớp trả về các lớp đang dạy; hiện bản mặc định trả rỗng. Kiêm nhiệm tự kết thúc vào ngày nghỉ, lịch sử giữ nguyên.
- **Giao chức vụ lãnh đạo đơn vị:** TCH bind `LeaderEligibility` bằng `TeacherLeaderEligibility`: giảng viên phải đang làm việc và thuộc đơn vị (đơn vị chính hoặc kiêm nhiệm; khoa tính cả bộ môn của khoa) trừ khi bật `studentmanager.faculty.allow_leader_outside_unit` (BR-FAC-04); mỗi người chỉ giữ chức vụ trưởng ở một đơn vị tại một thời điểm (BR-TCH-06). Tài khoản cán bộ chưa có hồ sơ nhân sự nên chưa kiểm tra đơn vị; tài khoản sinh viên bị từ chối.
- **Lịch bận, nguyện vọng, nghỉ phép (FR-TCH-008, 012; #89):** `LecturerScheduleService`. Lịch bận (`lecturer_availabilities`): thứ, tiết (1..`TEACHER_PERIODS_PER_DAY`), khoảng ngày, không chồng nhau; **TTB gọi `busySlot($gv, $ngay, $tietTu, $tietDen)`** để cảnh báo xếp vào giờ bận (FR-TTB-010). Nguyện vọng (`teaching_preferences`): học phần cho học kỳ chưa bắt đầu, có mức ưu tiên; CLS xem `GET /admin/teaching-preferences?term_id=&subject_id=`. Nghỉ phép / ốm / công tác (`lecturer_leaves`): không trùng đơn còn hiệu lực, DEAN trong đơn vị hoặc ACAD duyệt (không tự duyệt), duyệt xong phát sự kiện `LecturerLeaveApproved` cho TTB/NOT; **TTB, CLS gọi `isOnLeave($gv, $ngay)`**. Cổng giảng viên: `/teacher/availability`, `/teacher/preferences`, `/teacher/leaves`; quản trị: `/admin/lecturers/{id}/availability`, `/admin/lecturer-leaves` (+ `/approve`, `/reject`).
- **Giảng viên tự sửa hồ sơ (BR-TCH-07):** `PUT /teacher/profile` sửa ngay điện thoại, email; `POST /teacher/profile/photo` đổi ảnh (disk `public`, tối đa `TEACHER_PHOTO_MAX_KB`); họ tên, chức danh, học vị thành **đề nghị** (`LecturerChangeRequest`, tối đa một đề nghị đang chờ), do cán bộ khác có quyền xác nhận: DEAN trong đơn vị (TCH.approve) hoặc ACAD (TCH.update toàn trường), `POST /admin/lecturer-change-requests/{id}/approve|reject`. Trường khác bị từ chối kèm tên trường.
- Phạm vi: DEAN (FACULTY) quản lý giảng viên có đơn vị chính hoặc kiêm nhiệm đang hiệu lực thuộc đơn vị mình; LEC, ADV (OWN) chỉ hồ sơ của mình; các vai trò khác xem thông tin cơ bản. CCCD, ngày sinh, điện thoại… chỉ trả cho người sửa được hồ sơ; người khác xem CCCD được ghi nhật ký `view_sensitive`.
- API: `/admin/lecturers` (lọc `q` không dấu, `department_id`, `faculty_id`, `status`, `academic_title`, `academic_degree`; `+ /{id}`, `/status`, `/account`, `/units`, `/handover`), `/admin/lecturer-units/{id}/end`, `/admin/lecturer-change-requests`. Bộ môn, khoa còn giảng viên đang làm việc / kiêm nhiệm thì không ngừng được (đăng ký `ReferenceRegistry`). Seeder: 20 giảng viên mẫu có tài khoản. Import, xuất danh sách là #87.
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
- **Module lưu `subject_id` (CUR, CLS, GRD…) phải đăng ký với `ReferenceRegistry`** trong ServiceProvider của mình (khai báo `active:` cho dữ liệu đang diễn ra, ví dụ lớp đang mở). Khi đó học phần đã có dữ liệu: không đổi mã (BR-SUB-01), không đổi tín chỉ hay cờ tính GPA (BR-SUB-05: phải qua phiên bản học phần, FR-SUB-007 chưa làm), không xóa, chỉ ngừng; còn dữ liệu đang diễn ra thì chưa ngừng được.
- Bộ môn còn học phần đang hoạt động thì không ngừng được; đã có học phần thì không xóa, không đổi mã (SUB đăng ký với `ReferenceRegistry`).
- Phạm vi: DEAN (FACULTY) xem, thêm, sửa học phần của bộ môn mình quản lý, không chuyển học phần ra ngoài phạm vi; **LEC (SECTION) tạm thời không thấy học phần nào** cho tới khi CLS (#107) bổ sung điều kiện "học phần của lớp được phân công" trong `Subject::applyDataScope`; các vai trò khác xem toàn trường.
- API: `/admin/subjects` (lọc `q` không dấu, `department_id`, `faculty_id`, `knowledge_block`, `requirement`, `status`, `credits`, `counts_toward_gpa`, `is_graduation_requirement`; `+ /{id}`, `/export`). Quyền `SUB.*`, xuất cần `SUB.export` (ACAD). Seeder: 60 học phần mẫu, gồm 7 học phần điều kiện; khoa Khoa học cơ bản (không có ngành) quản lý học phần đại cương.
## Dữ liệu đang được tham chiếu (`ReferenceRegistry`)

Quy tắc "đã được dùng thì chỉ ngừng, không xóa / không đổi mã" (BR-SYS-09, BR-FAC-01, BR-ROM-04, GC-02) dùng chung `App\Support\References\ReferenceRegistry`. **Module nào thêm khóa ngoại tới bảng của module khác thì đăng ký trong ServiceProvider của mình**, để module kia biết mà chặn xóa:

```php
// ví dụ trong StudentServiceProvider::boot()
app(ReferenceRegistry::class)->register(LookupValue::class, 'students', 'gender_id', 'sinh viên');
```

`usages($model)` trả `['sinh viên' => 120]` (tính cả bản ghi đã xóa mềm), `ReferenceRegistry::describe()` ghép thành "120 sinh viên" để đưa vào thông báo lỗi. Bảng chưa tồn tại (module chưa cài) được bỏ qua.