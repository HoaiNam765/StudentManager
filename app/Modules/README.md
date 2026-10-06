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
- **Khóa hẳn / ngừng tài khoản**: gọi `RoleService::ensureNotLastAdmin()` trước, rồi thêm điều kiện chặn vào `LoginService::attempt()` (một chỗ duy nhất).
- Mật khẩu mới luôn kiểm tra bằng `Password::defaults()` (chính sách trong `config/studentmanager.php`, mục `auth.password`).
- Mọi route của 3 cổng đã có middleware `password.changed`: chưa đổi mật khẩu tạm thì bị đưa về trang đổi mật khẩu (JSON trả mã 428).

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