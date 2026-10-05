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
| Phân quyền | `extends App\Support\Policies\DenyByDefaultPolicy`, chỉ ghi đè thao tác được phép |
| Hiển thị ngày, giờ, tiền (GC-09) | `App\Support\Format::date()`, `dateTime()`, `money()` |

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

- `search_text` chỉ tự cập nhật khi lưu bằng Eloquent. Import hoặc cập nhật hàng loạt bằng query builder phải tự điền cột này (dùng `App\Support\Text\Vietnamese::fold()`).
- Không dựa vào collation để tìm không dấu: cả `utf8mb4_unicode_ci` lẫn `utf8mb4_vietnamese_ci` đều coi `d` khác `đ`.
- `HasActiveStatus` chỉ dành cho danh mục. Sinh viên, lớp học phần… có máy trạng thái riêng.
- Bảng có hiệu lực theo thời gian: ràng buộc unique phải gồm cả `effective_from`.
- Test tự tạo bảng (DDL) dùng `DatabaseMigrations` thay cho `RefreshDatabase`, vì MySQL tự commit khi tạo bảng.
