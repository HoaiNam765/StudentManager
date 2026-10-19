# Đối chiếu giao diện (FE) với API (BE)

Tài liệu này ghép từng màn hình mock ở `/__fe-preview/*` với API thật, để nối giao diện vào backend theo từng nhóm.

- Nguồn: `php artisan route:list --json` trên `main` (`e9b2c78`, 19/10/2026): **284 route** ngoài `__fe-preview`, **88 màn hình mock** (`config/fe_preview.php`). Quyền lấy từ middleware `permission:` của route; vai trò lấy từ ma trận mặc định `database/seeders/AuthSeeder.php` (BA mục 4.2).
- Phụ lục A liệt kê **đủ 284 route**, mỗi route đã gán vào một nhóm và một màn hình (hoặc ghi rõ "không có mock").
- Tham số lọc / phân trang ghi theo đúng controller (đọc `$request->...`, `validate()`, FormRequest), không suy đoán.

Ký hiệu vai trò: ADMIN quản trị, ACAD đào tạo, EXAM khảo thí, CTSV công tác SV, FIN tài chính, DEAN lãnh đạo khoa, LEC giảng viên, ADV cố vấn học tập, STU sinh viên. `(faculty)`, `(section)`, `(own)` là phạm vi dữ liệu. Tài khoản dùng thử: `<vai trò viết thường>.test`, mật khẩu `password`.

## 1. Tóm tắt

| Trạng thái | Số màn hình | Màn hình |
|---|---|---|
| Đã dùng dữ liệu thật (trang của BE) | 6 | `public/dang-nhap`, `public/quen-mat-khau`, `public/dat-lai-mat-khau`, `student/doi-mat-khau`, `student/phien-dang-nhap`, `student/cai-dat-tai-khoan` |
| **Nối được (BE đủ hoặc gần đủ)** | 32 | nhóm (a)–(f) ở mục 3 |
| Nối một phần (phần còn lại của màn hình chưa có BE) | 4 | `student/chuong-trinh-dao-tao`, `student/ho-so-ca-nhan`, `teacher/lich-giang-day`, `admin/sao-luu-khoa-so-import` |
| Có module BE nhưng thiếu route / Importer | 3 | `admin/nhat-ky-thao-tac`, `admin/nhat-ky-thao-tac-chi-tiet` (có `AuditLogSearch`, chưa có route — E1), `admin/sinh-vien-import` (chưa có Importer `students` — C1) |
| Không cần BE (chỉ là trang lỗi) | 5 | `errors/loi-403`, `loi-404`, `loi-419`, `loi-500`, `loi-503` |
| **Chưa có BE — giữ nguyên mock** | 38 | mục 4 |
| Tổng | 88 | |

## 2. Cách nối thống nhất (đề xuất)

### 2.1. Một module gọi API dùng chung: `resources/js/api.js`

- `api.get(url, params)`, `api.post/put/del(url, body)`, `api.download(url, params)` (PDF/Excel: tải theo `Content-Disposition`).
- Luôn gửi `Accept: application/json`, `X-CSRF-TOKEN` (đọc từ `<meta name="csrf-token">`), `X-Requested-With: XMLHttpRequest`, `credentials: 'same-origin'`; thân yêu cầu JSON (`Content-Type: application/json`) hoặc `FormData` khi tải tệp.
- Xử lý mã trả về:

| Mã | Ý nghĩa | Giao diện |
|---|---|---|
| 401 | hết phiên | chuyển về `/login` |
| 419 | hết hạn CSRF | thông báo "Phiên làm việc đã hết hạn, tải lại trang" kèm nút tải lại |
| 428 | phải đổi mật khẩu tạm (`password.changed`) | chuyển về `/password/change` |
| 403 | không đủ quyền | thông báo "Bạn không có quyền thực hiện thao tác này" (BE đã ghi nhật ký) |
| 404 | không tìm thấy / không thuộc phạm vi | thông báo, không lộ chi tiết |
| 422 | lỗi kiểm tra dữ liệu (`errors.{trường}`) hoặc lỗi nghiệp vụ (`message` + `reasons[]`) | hiện lỗi dưới từng ô; lỗi nghiệp vụ hiện khung cảnh báo với lý do và cách khắc phục đúng như BE trả |
| 202 | xuất dữ liệu chạy nền (`{id, row_count}`) | thông báo "đang xử lý" (chưa có API theo dõi, xem E7) |
| 5xx / lỗi mạng | | khung lỗi có nút "Thử lại" |

- Thành phần dùng lại trong `resources/js/ui/`: trạng thái bảng (đang tải dạng skeleton, rỗng, lỗi + thử lại), phân trang theo `meta` của `paginate()` của Laravel (`current_page`, `last_page`, `total`), gắn lỗi 422 vào form, hộp xác nhận cho thao tác ghi, thông báo ngắn (toast). Viết bằng JavaScript thuần (ES module, Vite gom), **không thêm thư viện**.
- Mọi bảng / form có đủ ba trạng thái: đang tải, rỗng, lỗi. Mock hiện có nút giả lập các trạng thái này ("Bình thường / Đang tải / Rỗng / Lỗi kết nối"): thay bằng trạng thái thật, bỏ nút giả lập.

### 2.2. Trang thật

- Mỗi màn hình mock thành một route trang trong cổng tương ứng, URL tiếng Việt không dấu để **không trùng URL của API** (API dùng tiếng Anh): ví dụ trang `/admin/dang-ky-hoc-phan/dot` gọi API `/admin/registration-periods`. Tên route: `<cổng>.pages.<tên>`.
- Controller trang chỉ trả view (không truy vấn dữ liệu nghiệp vụ), đặt ở `app/Http/Controllers/Pages/{Admin,Student,Teacher}`. Middleware: nhóm cổng đã có `web` + `password.changed`; thêm `auth` và **cùng quyền với API chính của trang** (ví dụ `permission:ENR.view`).
- Controller trang truyền cho view một khối JSON `<script type="application/json" id="page-data">`: các cờ quyền tính ở máy chủ bằng `AccessControl` (ví dụ `can.create`, `can.approve`) và ngữ cảnh (học kỳ hiện hành, mã sinh viên của người đăng nhập). Nút không đủ quyền thì ẩn hoặc vô hiệu; **BE vẫn kiểm tra lại**, ẩn nút không phải là bảo mật.
- Bố cục dùng chung `layouts/portal.blade.php` (kế thừa `layouts.app`, Vite + Tailwind 4) với thanh bên theo cổng; mục menu chỉ hiện trang đã nối và đủ quyền. Mock đang dùng Tailwind CDN và cấu hình màu riêng: khi chuyển sang trang thật thì bỏ CDN, dùng token màu trong `resources/css/app.css` (thêm token còn thiếu như `primary-hover`, `primary-tint`).
- Giữ route `__fe-preview` cho tới khi nhóm đó xong; trang thật xong thì mock tương ứng đổi liên kết sang trang thật trong `fe_preview/index`.
- Không bịa số liệu: ô KPI nào API không trả thì bỏ hoặc hiện "—" kèm chú thích "chưa có dữ liệu", không giữ số giả.

## 3. Bảng đối chiếu theo màn hình

Cột "Trang thật" là đề xuất; cột "Quyền trang" là middleware của route trang.

### (a) Đợt đăng ký và đăng ký học phần — làm trước

| Màn hình mock | Trang thật | API | Quyền trang | Vai trò dùng | Ghi chú / thiếu |
|---|---|---|---|---|---|
| `admin/dot-dang-ky-hoc-phan` | `/admin/dang-ky-hoc-phan/dot` | `GET /admin/registration-periods?term_id=` (không phân trang, không có `q`/`status`); `GET/PUT/DELETE /admin/registration-periods/{id}`; `POST /admin/registration-periods` (`term_id, name, round, opens_at, closes_at` dạng `Y-m-d H:i` giờ VN, `min_credits, max_credits, targets[].cohort_id/major_id/admin_class_id`); `POST …/{id}/publish`; chọn học kỳ `GET /admin/academic-years`; đối tượng `GET /admin/cohorts`, `/admin/majors`, `/admin/admin-classes`; thẻ KPI `GET /admin/enrollment-stats?term_id=` | `ENR.view` | xem: mọi cán bộ; tạo/sửa/xóa/công bố: **ACAD** (`acad.test`). ADMIN chỉ xem | Lọc trạng thái / tìm tên làm ở trình duyệt vì API trả cả danh sách (ít bản ghi). Cột "Tiến độ đăng ký" theo từng đợt: **thiếu** (A1). "Xuất báo cáo đợt": **thiếu** (A5). Đợt nháp chỉ người có ENR toàn trường thấy (BE lọc) |
| `admin/chot-dang-ky-hoc-phan` | `/admin/dang-ky-hoc-phan/chot` | `GET /admin/terms/{term}/registration/finalize` (xem trước: `already_finalized, open_periods, under_min_sections, students_under_min_credits, students_not_registered, to_confirm`); `POST …/finalize` (`acknowledge_under_min`); `GET /admin/terms/{term}/under-min-sections`; xử lý lớp: `POST /admin/course-sections/{id}/merge` (`target_id, reason`), `POST /admin/course-sections/{id}/status/cancelled` (`reason`) | `ENR.approve,ALL` | **ACAD** | Lỗi 422 khi còn đợt mở / lớp dưới ngưỡng / SV thiếu tín chỉ hiển thị đúng lý do của BE. "Xuất dữ liệu XLS": **thiếu** (A4). Hủy / gộp lớp cần `CLS.update,ALL` (ACAD, CTSV) |
| `admin/dang-ky-ngoai-le` | `/admin/dang-ky-hoc-phan/ngoai-le` | tìm SV `GET /admin/students?q=`; `GET /admin/students/{id}/registration-options?period_id=`; `GET /admin/students/{id}/registration-result?term_id=` (+ `/slip` PDF); `POST /admin/students/{id}/enrollments` (`course_section_id, type, skip[]` ∈ `period, capacity, prerequisite, schedule, credits, debt`, `reason` bắt buộc khi có `skip`); `POST /admin/enrollments/{id}/cancel` (`reason`) | `ENR.view` | xem: cán bộ trong phạm vi; đăng ký / hủy thay: **ACAD** | Bảng tổng hợp mỗi SV (số HP, tổng TC, lần đăng ký cuối, trạng thái cảnh báo) **thiếu** (A3) → tạm thời: tìm SV rồi mở chi tiết từng SV |
| `student/dang-ky-hoc-phan` | `/student/dang-ky-hoc-phan` | `GET /student/registration/options?period_id=` (đợt, `server_time`, `credit_limits`, `problems[]`, `sections[]` có `eligible`, `reasons[]`, `remaining`, `schedule`, `instructors`); `GET /student/registration/enrollments?term_id=`; `POST /student/registration/enrollments` (`course_section_id, type` ∈ `normal\|retake\|improve`); `POST …/{id}/cancel`, `…/withdraw`, `…/change` (`course_section_id`) | `ENR.view` | **STU** | Mock chọn nhiều lớp rồi bấm "Xác nhận đăng ký" một lần; API đăng ký **từng lớp** (đúng BR chống tranh chấp chỗ) → nút "Chọn" gọi API ngay, giỏ tạm bỏ. Tìm theo tên GV / lọc lớp làm ở trình duyệt. Tab "Học phần học lại / cải thiện": **chưa có BE** (cần GRD) |
| `student/dang-ky-hoc-phan-mobile` | (xem câu hỏi Q1) | như trên | `ENR.view` | STU | Hai mock cùng một API |
| `student/ket-qua-dang-ky` | `/student/ket-qua-dang-ky` | `GET /student/registration/result?term_id=` (`items[]`, `total_credits`, `credit_limits`, `timetable[]`); `GET /student/registration/result/slip` (PDF); hủy / rút: `POST /student/registration/enrollments/{id}/cancel\|withdraw` | `ENR.view` | STU | Bản xem trước phiếu in dựng từ `result`; nút "Tải phiếu PDF" gọi `/slip` (BE ghi nhật ký tải) |

### (b) Lớp học phần và thời khóa biểu

| Màn hình mock | Trang thật | API | Quyền trang | Vai trò dùng | Ghi chú / thiếu |
|---|---|---|---|---|---|
| `admin/lop-hoc-phan` | `/admin/lop-hoc-phan` | `GET /admin/course-sections` (`q, term_id, subject_id, status, lecturer_id, without_main_instructor`, `paginate(50)`); `POST` mở lớp; `PUT/DELETE /{id}`; `POST /{id}/status/{status}` (`reason`); `POST /{id}/instructors` (`lecturer_id, role`), `DELETE /admin/section-instructors/{id}`; `POST /{id}/unlock` (`reason`) | `CLS.view` | xem: mọi cán bộ; mở/sửa/hủy: **ACAD**; mở khóa: ACAD | "Xuất Excel" danh sách lớp, "Nhập từ file": **thiếu** (B5, B6). Thẻ KPI: đếm bằng `meta.total` theo `status` |
| `admin/lop-hoc-phan-chi-tiet` | `/admin/lop-hoc-phan/{id}` | `GET /admin/course-sections/{id}`; `GET /{id}/enrollments?all=` (danh sách SV); `GET /{id}/enrollments/export?format=xlsx\|csv\|pdf`; `GET /{id}/schedule`, `GET /{id}/sessions`; gộp lớp `POST /{id}/merge` | `CLS.view` | ACAD; DEAN(faculty); LEC(section) | Tab "Bảng điểm": **chưa có BE** (GRD) |
| `admin/xep-thoi-khoa-bieu` | `/admin/thoi-khoa-bieu` | `GET /admin/timetable?target=&id=&term_id=&date=&view=week\|month\|term` (sinh viên / giảng viên / lớp HP / lớp HC / phòng / khoa); `GET /admin/terms/{term}/timetable` (trạng thái nháp/công bố/khóa), `POST …/publish`, `…/lock`; mẫu lịch `GET/POST /admin/course-sections/{id}/schedule`, `PUT/DELETE /admin/schedule-rules/{id}` (`day_of_week, period_from, period_to, room_id, week_from, week_to, week_parity, note`; sau công bố cần `reason`, `urgent`); xung đột `GET /admin/course-sections/{id}/schedule-conflicts`; khung tiết `GET /admin/periods?campus_id=` | `TTB.view` | xem: cán bộ (trừ FIN); sửa / công bố / khóa: **ACAD** | Nút gợi ý "Đổi sang phòng…/thứ…" trong mock: BE chỉ báo xung đột, **không gợi ý phương án** → bỏ nút gợi ý, cho sửa tay |
| `admin/lich-day-bu-phong` | `/admin/thoi-khoa-bieu/day-bu` | `GET /admin/course-sections/{id}/sessions`; `POST /admin/class-sessions/{id}/makeup` (`date, period_from, period_to, room_id, note`), `POST …/cancel` (`reason`); sinh buổi `POST /admin/course-sections/{id}/sessions/generate`; lịch phòng `GET /admin/timetable?target=room&id=` | `TTB.view` | **ACAD** ghi; cán bộ xem | "Tra cứu phòng trống theo tiết": **thiếu** (B3; `rooms/{id}/availability` chỉ kiểm tra bảo trì) |
| `admin/xep-lich-tu-dong` | — | — | — | — | **Chưa có BE** (xếp lịch tự động) |
| `student/thoi-khoa-bieu` | `/student/thoi-khoa-bieu` | `GET /student/timetable?view=week\|month\|term&date=&term_id=` (chỉ khi đã công bố) | `TTB.view` | STU | "Xuất lịch .ics": **thiếu** (B2). In A4 dùng CSS in của trình duyệt |
| `teacher/lich-giang-day` | `/teacher/lich-giang-day` | `GET /teacher/timetable?view=&date=&term_id=`; lớp phụ trách `GET /admin/course-sections?term_id=` (BE lọc phạm vi SECTION) | `TTB.view` | LEC | Mock là trang tổng quan: phần "Công việc cần xử lý", "Thông báo", nút nhập điểm: **chưa có BE** |
| `teacher/lop-hoc-phan` | `/teacher/lop-hoc-phan` | `GET /admin/course-sections` (phạm vi SECTION); `GET /admin/course-sections/{id}/enrollments`; `…/enrollments/export` | `CLS.view` | LEC | Cột "Số buổi vắng", nút "Nhập điểm / Điểm quá trình": **chưa có BE** (ATT, GRD). Cổng giảng viên gọi API tiền tố `/admin` (xem Q3) |

### (c) Sinh viên, nhập học, khóa và lớp hành chính

| Màn hình mock | Trang thật | API | Quyền trang | Vai trò dùng | Ghi chú / thiếu |
|---|---|---|---|---|---|
| `admin/sinh-vien` | `/admin/sinh-vien` | `GET /admin/students` (`q, status, major_id, faculty_id, cohort, training_type_id`, `paginate(50)`); `POST /admin/students`; ô chọn khoa / ngành `GET /admin/faculties`, `/admin/majors`; `GET /danh-muc/{MÃ}` | `STU.view` | xem: mọi cán bộ; thêm: **ACAD, CTSV** | Cột "ĐTB tích lũy / TC": **chưa có BE** (GRD). "Xuất Excel": **thiếu** (C2). Lọc theo lớp HC: **thiếu** (C3). KPI: `meta.total` theo `status` |
| `admin/sinh-vien-chi-tiet` | `/admin/sinh-vien/{id}` | `GET /admin/students/{id}` (số giấy tờ đã che); `GET /{id}/sensitive` (xem đầy đủ, ghi `view_sensitive`, cần `STU.update`); `GET /{id}/change-logs`; liên hệ `POST /{id}/contacts`, `PUT/DELETE /admin/student-contacts/{id}`; CTĐT `GET /{id}/curricula`; tab đăng ký `GET /{id}/registration-result` | `STU.view` | ACAD, CTSV; DEAN(faculty) xem | Tab "Kết quả & điểm", "Tiến độ CTĐT %": **chưa có BE** (GRD) |
| `admin/sinh-vien-doi-trang-thai` | `/admin/sinh-vien/{id}/sua` | `PUT /admin/students/{id}` (`StudentRequest`: họ tên, giấy tờ, liên hệ, địa chỉ `permanent_unit_id`…, `reason` bắt buộc với trường nhạy cảm); nhập học `POST /{id}/admit` (`admin_class_id, admission_date`), `POST /{id}/cancel-admission` (`reason`); gán CTĐT `POST /{id}/curricula` (`curriculum_id, effective_from, reason`); địa chỉ `GET /danh-muc/don-vi-hanh-chinh?parent_id=` | `STU.update` | **ACAD, CTSV** | Nút "Đổi trạng thái học vụ" (bảo lưu, thôi học…): **chưa có BE** (C6). Xóa hồ sơ `DELETE /admin/students/{id}` cần `STU.delete` mà **ma trận chưa cấp cho vai trò nào** (F2) |
| `admin/sinh-vien-import` | — | trung tâm import `/admin/imports` có, nhưng **chưa có Importer `students`** | — | — | Giữ mock, ghi "chưa có BE" (C1) |
| `admin/khoa-va-lop-hanh-chinh` | `/admin/lop-hanh-chinh` | `GET /admin/cohorts?q=&status=`, `POST/PUT/DELETE /admin/cohorts/{id}` (`code, name, start_year, expected_end_year, policy_set_id, status`); `GET /admin/admin-classes` (`q, cohort_id, major_id, faculty_id, status`, `paginate(50)`), `POST/PUT/DELETE /admin/admin-classes/{id}` (`code, name, cohort_id, major_id, specialization_id, training_type_id, max_size, status`) | `CLS.view` | xem: cán bộ; khóa: ACAD; lớp: ACAD (sửa thêm CTSV) | Cột "Cố vấn học tập": **chưa có BE** (C4). "Danh sách SV" của lớp: **thiếu** lọc (C3). "Nhập từ Excel / Lịch sử điều chuyển": **chưa có BE** |
| `student/ho-so-ca-nhan` | `/student/ho-so` (một phần) | `GET /admin/students/{id-của-tôi}` (STU xem hồ sơ của mình, phạm vi OWN); danh mục `GET /danh-muc/{MÃ}` | `STU.view` | STU | "Yêu cầu chỉnh sửa" và lịch sử yêu cầu: **chưa có BE** (REQ). Cần xác nhận STU được sửa trường nào trước khi nối nút "Cập nhật liên hệ" |

### (d) Học phần, chương trình đào tạo, giảng viên

| Màn hình mock | Trang thật | API | Quyền trang | Vai trò dùng | Ghi chú / thiếu |
|---|---|---|---|---|---|
| `admin/hoc-phan` | `/admin/hoc-phan` | `GET /admin/subjects` (`q, department_id, faculty_id, knowledge_block, requirement, status, credits, counts_toward_gpa, is_graduation_requirement`, `paginate(50)`); `POST/PUT/DELETE /admin/subjects/{id}` (`SubjectRequest`); `GET /admin/subjects/export` (cùng bộ lọc + `format`); quan hệ `GET/POST /{id}/relations`, `GET /{id}/prerequisites`, `POST /admin/subject-relations/{id}/end`, `DELETE`; cơ cấu điểm `GET/PUT /{id}/grade-components`; đề cương `GET /{id}/syllabi` | `SUB.view` | xem: mọi vai trò; thêm/sửa: **ACAD**, DEAN(faculty); xuất: ACAD | "Nhập từ Excel": **thiếu** Importer `subjects` (D2) |
| `admin/chuong-trinh-dao-tao` | `/admin/chuong-trinh-dao-tao` | `GET /admin/curricula` (`q, major_id, faculty_id, status, cohort`, `paginate(50)`); `POST /admin/curricula` | `CUR.view` | ACAD; DEAN(faculty) | "Xuất file Excel": **thiếu** (D1) |
| `admin/chuong-trinh-dao-tao-chi-tiet` | `/admin/chuong-trinh-dao-tao/{id}` | `GET /admin/curricula/{id}`, `PUT`, `DELETE`; kiểm tra `GET /{id}/check`; khối `POST /{id}/blocks`, `PUT/DELETE /admin/curriculum-blocks/{id}`; nhóm tự chọn `POST /{id}/elective-groups`, `PUT/DELETE /admin/curriculum-elective-groups/{id}`; học phần `POST /{id}/subjects`, `PUT/DELETE /admin/curriculum-subjects/{id}`; ban hành `POST /{id}/publish` (**ACAD**); phiên bản `GET/POST /{id}/versions` (`cohort_from, effective_from`); điều kiện tốt nghiệp `GET/PUT /{id}/graduation-requirements` | `CUR.view` | ACAD; DEAN(faculty) soạn | "Lịch sử cập nhật": cần API nhật ký (E1). "Xuất khung CTĐT": **thiếu** (D1) |
| `admin/giang-vien` | `/admin/giang-vien` | `GET /admin/lecturers` (`q, department_id, faculty_id, status, academic_title, academic_degree`, `paginate(50)`); `POST`, `PUT /{id}` (`reason` khi đổi họ tên / chức danh / học vị); `POST /{id}/status` (`status, on, reason`), `GET /{id}/handover?on=`; `POST /{id}/account` (`email`); kiêm nhiệm `GET/POST /{id}/units`, `POST /admin/lecturer-units/{id}/end`, `DELETE`; `GET /admin/lecturers/export?format=`; duyệt đề nghị đổi hồ sơ `GET /admin/lecturer-change-requests?status=`, `POST …/approve\|reject` (`note`); nghỉ phép `GET /admin/lecturer-leaves?status=&lecturer_id=`, `POST …/approve\|reject`; `GET /admin/lecturers/{id}/availability`; `GET /admin/teaching-preferences?term_id=&subject_id=` | `TCH.view` | ACAD; DEAN(faculty) | Cột "Lớp HP (HK1)", "Giờ chuẩn / thực": **thiếu** trong API danh sách (D3) |
| `admin/giang-vien-import` | `/admin/giang-vien/nhap-file` | `GET /admin/imports/importers` (có `template_url`); `POST /admin/imports` (`importer=lecturers`, `file`); `POST /{batch}/validate`, `GET /{batch}/preview`, `GET /{batch}/progress`, `POST /{batch}/save` (`mode`), `POST /{batch}/rollback`, `DELETE`; `GET /admin/imports?importer=&status=` | `SYS.view` | **chỉ ADMIN** tải lên (`SYS.create`) | ACAD không có `SYS.create` nên không import được giảng viên theo ma trận hiện tại (xem Q5) |
| `teacher/ho-so-lich-ban` | `/teacher/ho-so` | `GET/PUT /teacher/profile` (`phone, email` sửa ngay; `full_name, academic_title, academic_degree` + `reason` thành đề nghị); `POST /teacher/profile/photo`; `DELETE /teacher/profile/change-requests/{id}`; lịch bận `GET/POST /teacher/availability` (`day_of_week, period_from, period_to, starts_on, ends_on, reason`), `DELETE /{id}`; nguyện vọng `GET/PUT /teacher/preferences` (`term_id, subjects[]`); nghỉ phép `GET/POST /teacher/leaves` (`type, starts_on, ends_on, reason`), `POST /{id}/cancel` | đăng nhập (controller kiểm tra hồ sơ GV) | LEC, DEAN, ADV | "Xuất lịch bận PDF": **thiếu** |
| `teacher/de-cuong-hoc-phan` | `/teacher/de-cuong` | `GET /admin/subjects` (LEC: phạm vi SECTION); `GET/POST /admin/subjects/{id}/syllabi`; `GET/PUT/DELETE /admin/syllabi/{id}` (`objectives, learning_outcomes[], content[], teaching_methods, assessment, materials[]`); `POST /{id}/submit`; tệp `POST/GET /{id}/file`; duyệt `POST /{id}/approve\|reject` (`note`, DEAN/ACAD) | `SUB.view` | LEC soạn; DEAN, ACAD duyệt | Mock có ma trận CLO–PLO, mức Bloom, kế hoạch 15 tuần: chỉ nối phần BE có (mục tiêu, chuẩn đầu ra, nội dung, đánh giá, tài liệu). "Xuất PDF/Word" tự sinh: **thiếu** (D8) |
| `student/chuong-trinh-dao-tao` | `/student/chuong-trinh-dao-tao` (một phần) | `GET /admin/students/{id-của-tôi}/curricula` → `GET /admin/curricula/{id}` (STU xem CTĐT của mình) | `CUR.view` | STU | Cột "Điểm số / Điểm chữ / Trạng thái", tiến độ tích lũy: **chưa có BE** (GRD). "Tải khung chương trình (PDF)": **thiếu** (D6) |

### (e) Quản trị

| Màn hình mock | Trang thật | API | Quyền trang | Vai trò dùng | Ghi chú / thiếu |
|---|---|---|---|---|---|
| `admin/nguoi-dung` | `/admin/nguoi-dung` | `GET /admin/users` (`q, status, profile_type, role`, `paginate(50)`); `POST /{id}/lock` (`reason`), `/unlock`, `/deactivate` (`reason`), `/activate`, `/temporary-password`; nhập file qua `/admin/imports` (`importer=users`) | `AUTH.view,ALL` | **ADMIN** | "Xuất dữ liệu": **thiếu** (E5). Thẻ KPI: `meta.total` theo `status`. "Thao tác hàng loạt": **thiếu** |
| `admin/nguoi-dung-form` | `/admin/nguoi-dung/tao`, `/admin/nguoi-dung/{id}/sua` | `POST /admin/users`, `PUT /admin/users/{id}` (`username, name, email, profile_type, profile_id, roles[], status`); vai trò có thời hạn `GET/POST /{id}/roles` (`role_id, valid_from, valid_to`), `DELETE /{id}/roles/{role}` | `AUTH.create,ALL` / `AUTH.update,ALL` | ADMIN | |
| `admin/phan-quyen` | `/admin/phan-quyen` | `GET /admin/roles?q=`, `POST/PUT/DELETE /admin/roles/{id}`; `GET /admin/permissions` (danh mục quyền); `PUT /admin/roles/{id}/permissions` (`permissions[]`) | `AUTH.view,ALL` | ADMIN | Mock là ma trận minh họa 5 cột; trang thật dựng ma trận đủ 24 module × 6 hành động × phạm vi từ API |
| `admin/nhat-ky-thao-tac` | `/admin/nhat-ky` | **thiếu route** — dịch vụ `AuditLogSearch::query()` có sẵn | `AUDIT.view,ALL` | ADMIN | E1: thêm `GET /admin/audit-logs`, `GET /admin/audit-logs/{id}`, `GET /admin/audit-logs/export`. Thêm tab "Lịch sử đăng nhập" dùng `GET /admin/login-histories` (`user_id, login, successful, unusual, ip, from, to`, `paginate(50)`) — API này hiện không có mock |
| `admin/nhat-ky-thao-tac-chi-tiet` | `/admin/nhat-ky/{id}` | như trên | `AUDIT.view,ALL` | ADMIN | |
| `admin/tham-so-he-thong` | `/admin/tham-so` | `GET /admin/settings`; `PUT /admin/settings` (`values`, `reason`); `POST /admin/settings/logo` | `SYS.view` | ADMIN, ACAD | Chỉ hiện những tham số BE trả; ô nào mock có mà BE không có thì bỏ. "Học kỳ hiện hành" đặt ở trang năm học (`POST /admin/academic-years/terms/{term}/current`) |
| `admin/xac-nhan-thay-doi-cau-hinh` | (hộp xác nhận trong `/admin/tham-so`) | `PUT /admin/settings` có `reason` | `SYS.update` | ADMIN, ACAD | Gộp vào trang tham số, không làm trang riêng |
| `admin/quy-che-hoc-vu` | `/admin/quy-che` | `GET /admin/policy-sets?status=&code=`, `GET /admin/policy-sets/definitions` (danh sách tham số quy chế), `GET/PUT/DELETE /{id}`, `POST` (`code, name, cohort_from, cohort_to, effective_from, based_on_id` = sao chép từ bộ cũ), `PUT /{id}/items` (`items`), `POST /{id}/publish`; `GET /admin/policy-sets/resolve?cohort=&date=` | `SYS.view` | ADMIN, ACAD | "Sao chép quy chế từ khóa trước" = `POST` với `based_on_id`; "Lịch sử phiên bản" = danh sách theo `code`. Bảng quy đổi điểm, điều kiện phụ tốt nghiệp: chỉ hiện nếu có trong `definitions` |

### (f) Danh mục nền (đề xuất làm sau (e))

| Màn hình mock | Trang thật | API | Quyền trang | Vai trò dùng | Ghi chú / thiếu |
|---|---|---|---|---|---|
| `admin/nam-hoc-hoc-ky` | `/admin/nam-hoc` | `GET /admin/academic-years` (`paginate(20)`), `POST` (`name, start_date, end_date`), `GET/PUT /{id}`; học kỳ `POST /admin/academic-years/terms` (`academic_year_id, name, type, start_date, end_date, weeks`), `POST /terms/{id}/current`, `POST /terms/{id}/status/{status}`, `GET /admin/academic-years/current-term`; mốc học vụ `GET/PUT /admin/terms/{id}/milestones`, `POST …/copy` (`from_term_id`); đề nghị sửa mốc `GET /admin/milestone-change-requests`, `POST …/approve\|reject`; ngày nghỉ `GET /admin/holidays?from=&to=&term_id=`, `POST/PUT/DELETE` | `ACY.view,ALL` | xem: mọi vai trò; ghi: **ACAD** | Mock chỉ là bảng 4 cột + form mẫu; trang thật cần thêm phần mốc học vụ và ngày nghỉ |
| `admin/phong-hoc` | `/admin/phong-hoc` | `GET /admin/rooms` (`campus_id, building_id, room_type_id, status, min_capacity, min_exam_capacity, q`, `paginate(50)`), `POST/PUT/DELETE /{id}` (`RoomRequest`); cơ sở `GET/POST/PUT/DELETE /admin/campuses`, tòa nhà `/admin/buildings`, loại phòng `/admin/room-types`; bảo trì `GET/POST /admin/rooms/{id}/maintenances`, `PUT/DELETE /admin/room-maintenances/{id}`; `GET /admin/rooms/{id}/availability?from=&to=` | `ROM.view` | ghi: **ADMIN, ACAD** | |
| `admin/danh-muc-nen` | `/admin/khoa-bo-mon-nganh` | `GET /admin/{faculties\|departments\|majors\|specializations}` (`q, status, faculty_id, major_id, education_level`, `paginate(50)`), `POST/PUT/DELETE /{id}`; `GET /admin/{loại}/export` (`FAC.export`: **ma trận chưa cấp cho ai**, F1); hệ đào tạo `GET/POST/PUT /admin/training-types`; lãnh đạo đơn vị `GET /admin/leadership-terms`, `POST`, `POST /{id}/end`, `DELETE` | `FAC.view` | ghi: **ADMIN, ACAD**; DEAN sửa thông tin mô tả khoa mình | |
| `admin/sao-luu-khoa-so-import` | `/admin/nhap-du-lieu` (chỉ tab Import) | tab Import: `/admin/imports/*` như trên; tab Khóa sổ học kỳ: `POST /admin/academic-years/terms/{id}/status/locked` (`ACY.approve,ALL`, chỉ ACAD); bản "xem trước khi khóa" trong mock: **thiếu** | `SYS.view` | ADMIN (import); ACAD (khóa sổ) | Tab "Sao lưu & phục hồi": **chưa có BE** |

### Không cần BE

| Màn hình mock | Làm gì |
|---|---|
| `errors/loi-403`, `loi-404`, `loi-419`, `loi-500`, `loi-503` | Chuyển thành `resources/views/errors/{403,404,419,500,503}.blade.php` (Laravel tự dùng). Có thể làm kèm nhóm (e) |

## 4. Màn hình chưa có BE — giữ nguyên mock, ghi "chưa có BE" trong PR

| Module | Màn hình |
|---|---|
| Dashboard / KPI (RPT) | `admin/tong-quan`, `admin/bao-cao`, `student/tong-quan`, `student/tong-quan-mobile`, `teacher/tong-quan` (trang chủ thật `admin.home`, `student.home`, `teacher.home` vẫn dùng số liệu giả trong `DashboardController`) |
| Điểm, GPA (GRD) | `admin/quan-ly-diem`, `student/ket-qua-hoc-tap`, `teacher/nhap-diem` |
| Điểm danh (ATT) | `admin/diem-danh-chuyen-can`, `student/chuyen-can`, `teacher/diem-danh` |
| Học phí (FEE) | `admin/hoc-phi`, `student/hoc-phi`, `student/thanh-toan-online` |
| Thi, khảo thí (EXM) | `admin/ky-thi`, `student/lich-thi`, `teacher/lich-coi-thi` |
| Thông báo (NOT) | `admin/thong-bao`, `admin/mau-thong-bao`, `student/thong-bao`, `student/tuy-chon-thong-bao` (trang tài khoản `/tai-khoan/thong-bao` hiện là trang tĩnh), `teacher/thong-bao-lop`, `public/bang-tin` |
| Đơn từ (REQ) | `admin/don-tu`, `student/don-tu`, `teacher/duyet-don` |
| Hồ sơ, giấy tờ (DOC) | `admin/ho-so-giay-to`, `student/ho-so-giay-to`, `public/xac-thuc-van-ban` |
| Học bổng, rèn luyện, kỷ luật (SCH) | `admin/hoc-bong-ren-luyen-ky-luat`, `student/hoc-bong-ren-luyen` |
| Khảo sát (EVA) | `admin/khao-sat`, `student/khao-sat`, `teacher/ket-qua-khao-sat` |
| Học vụ, tốt nghiệp (GRA) | `admin/hoc-vu-tot-nghiep` |
| Cố vấn học tập (phạm vi ADVISEE chưa cài) | `teacher/lop-co-van-hoc-tap` |
| Xếp lịch tự động (TTB) | `admin/xep-lich-tu-dong` |
| Đăng nhập SSO | `public/dang-nhap-sso` |

Tổng: 38 màn hình (đếm cả hai trang tổng quan sinh viên).

## 5. Thiếu API (ghi nhận, chưa sửa BE)

Chỉ sửa BE khi thiếu nhỏ và phải ghi rõ trong PR của nhóm đó.

| Mã | Màn hình | Thiếu | Gợi ý endpoint | Trên FE tạm thời |
|---|---|---|---|---|
| A1 | `admin/dot-dang-ky-hoc-phan` | Tiến độ theo từng đợt (SV đã đăng ký / SV thuộc đối tượng, tổng TC) | thêm `progress` vào `GET /admin/registration-periods` hoặc `GET /admin/registration-periods/{id}/stats` | cột hiện "—"; KPI học kỳ lấy từ `enrollment-stats` |
| A3 | `admin/dang-ky-ngoai-le` | Danh sách SV kèm tóm tắt đăng ký (số HP, TC, lần cuối, cảnh báo) | `GET /admin/terms/{term}/registration/students?q=&state=&page=` | tìm SV qua `/admin/students?q=` rồi xem từng SV |
| A4 | `admin/chot-dang-ky-hoc-phan` | Xuất danh sách lớp dưới ngưỡng | `GET /admin/terms/{term}/under-min-sections/export?format=` | ẩn nút |
| A5 | `admin/dot-dang-ky-hoc-phan` | Xuất báo cáo đợt | `GET /admin/enrollment-stats/export?term_id=&format=` | ẩn nút |
| B2 | `student/thoi-khoa-bieu` | Xuất `.ics` | `GET /student/timetable.ics?term_id=` | ẩn nút |
| B3 | `admin/lich-day-bu-phong` | Tìm phòng trống theo ngày + tiết | `GET /admin/rooms/free?date=&period_from=&period_to=&min_capacity=` | chọn phòng từ `/admin/rooms`; BE vẫn báo trùng khi lưu dạy bù |
| B5, B6 | `admin/lop-hoc-phan` | Xuất danh sách lớp HP; Importer lớp HP | `GET /admin/course-sections/export`; Importer `course_sections` | ẩn nút |
| C1 | `admin/sinh-vien-import` | Importer `students` | đăng ký `StudentImporter` vào `ImportRegistry` | giữ mock |
| C2 | `admin/sinh-vien` | Xuất danh sách SV | `GET /admin/students/export?format=` (dùng `ExportService`, cột nhạy cảm) | ẩn nút |
| C3 | `admin/khoa-va-lop-hanh-chinh`, `admin/sinh-vien` | Lọc SV theo lớp hành chính | thêm `admin_class_id` vào `GET /admin/students` | ẩn nút "Danh sách SV" |
| C4 | `admin/khoa-va-lop-hanh-chinh` | Cố vấn học tập của lớp | (module ADV chưa làm) | bỏ cột |
| C6 | `admin/sinh-vien-doi-trang-thai` | Đổi tình trạng học vụ (bảo lưu, thôi học…) | (issue STU tương ứng) | ẩn nút |
| D1, D6 | CTĐT | Xuất khung CTĐT Excel/PDF | `GET /admin/curricula/{id}/export?format=` | ẩn nút |
| D2 | `admin/hoc-phan` | Importer `subjects` | đăng ký Importer | ẩn nút |
| D3 | `admin/giang-vien` | Số lớp HP, giờ chuẩn / thực trong danh sách | thêm `sections_count`, `periods` theo `term_id` vào `GET /admin/lecturers` | bỏ cột |
| D8 | `teacher/de-cuong-hoc-phan` | Sinh PDF/Word đề cương | `GET /admin/syllabi/{id}/export` | chỉ tải tệp đã đính kèm (`/file`) |
| E1 | `admin/nhat-ky-thao-tac*` | Route tra cứu nhật ký kiểm toán | `GET /admin/audit-logs`, `GET /admin/audit-logs/{id}`, `GET /admin/audit-logs/export` (`AUDIT.view,ALL` / `AUDIT.export,ALL`) dùng `AuditLogSearch` | **thiếu nhỏ, đề xuất làm trong PR nhóm (e)** |
| E5 | `admin/nguoi-dung` | Xuất danh sách người dùng | `GET /admin/users/export` | ẩn nút |
| E7 | mọi nút xuất | Theo dõi / tải tệp xuất chạy nền (khi trả 202) | `GET /admin/exports/{id}`, `GET /admin/exports/{id}/download` (gọi `ExportService::status/download`) | Ngưỡng nền mặc định 50.000 dòng (PDF 300) nên dữ liệu demo không chạm tới; nếu gặp 202 thì báo "đang xử lý" |

Vấn đề ma trận quyền (BE, không phải thiếu API):

- **F1** `FAC.export` chưa cấp cho vai trò nào → nút xuất danh mục khoa luôn 403.
- **F2** `STU.delete` chưa cấp cho vai trò nào → nút xóa hồ sơ sinh viên luôn 403.
- **F3** Phạm vi ADVISEE (ADV) và SECTION trên hồ sơ sinh viên chưa cài trong `Student::applyDataScope` → ADV, LEC không xem được hồ sơ SV nào.
- Nhóm (a): ADMIN chỉ có quyền **xem** ENR; thao tác đợt / chốt / đăng ký thay chỉ ACAD làm được. Demo dùng `acad.test`.

Dữ liệu demo:

- **D-1** `stu.test` không gắn hồ sơ sinh viên → mọi API đăng ký của sinh viên trả 422 "Tài khoản này không gắn với hồ sơ sinh viên" (đúng quy tắc). 12 sinh viên đã nhập học trong seeder (tên đăng nhập là MSSV, ví dụ `2602010001`) có **mật khẩu tạm ngẫu nhiên** gửi qua mail log và bị buộc đổi ở lần đầu → không đăng nhập demo được bằng mật khẩu `password` (xem Q2).
- Đợt đăng ký mẫu mở từ hôm qua tới 30 ngày sau lúc chạy seeder; seed lại thì thời gian dịch theo.

## 6. Quyết định (19/10/2026)

- **Q1** Hai mock `student/dang-ky-hoc-phan` và `student/dang-ky-hoc-phan-mobile` cùng gọi một API → **gộp một trang đáp ứng** `/student/dang-ky-hoc-phan`.
- **Q2** Tài khoản sinh viên để demo → `DevRoleUsersSeeder` (đã chặn ở production) đặt mật khẩu `password` và bỏ buộc đổi mật khẩu **chỉ cho sinh viên đã nhập học** (Đang học, có tài khoản) mà còn mật khẩu tạm; đăng nhập bằng MSSV, ví dụ `2602010001`. `stu.test` giữ nguyên (không gắn hồ sơ) để thử trường hợp đó.
- **Q3** Cổng giảng viên gọi API `/admin/...` → **đạt, không cần thêm route `/teacher/...`**. Đã kiểm tra trên `sm_demo` qua đủ chuỗi middleware (`web`, `password.changed`, `permission`, Policy) với GV0001 (dạy lớp INT102.262.01):

  | Gọi | Kết quả |
  |---|---|
  | `GET /admin/course-sections` | 200, chỉ 1 lớp của mình (toàn trường 14 lớp) |
  | `GET /admin/course-sections/11` (lớp mình) / `…/1` (lớp khác) | 200 / **403** |
  | `…/{id}/enrollments`, `…/{id}/schedule`, `…/{id}/sessions` lớp mình / lớp khác | 200 / **403** |
  | `…/11/enrollments/export` | 403 (LEC không có `ENR.export`, đúng ma trận) |
  | `GET /admin/subjects` | 200, 10 học phần trong phạm vi |
  | `GET /admin/subjects/34/syllabi` (HP mình dạy) / `…/59/syllabi` | 200 / **403** |
  | `GET /admin/users` | 403 (`AUTH.view,ALL`) |
  | cùng tài khoản nhưng còn mật khẩu tạm | 428 "Bạn cần đổi mật khẩu trước khi tiếp tục." |
  | `lec.test` (không có hồ sơ GV): danh sách / chi tiết lớp / `GET /teacher/profile` | 200 rỗng / 403 / 404 "Tài khoản chưa gắn với hồ sơ giảng viên." |

  Ghi chú: 403 từ `Gate::authorize` trả thông điệp tiếng Anh "This action is unauthorized." (403 từ middleware `permission` là tiếng Việt); `api.js` thay bằng thông điệp tiếng Việt. Tài khoản giảng viên mẫu (GV0001…) còn mật khẩu tạm nên khi làm nhóm (b) cần cách đăng nhập demo cho giảng viên (sẽ hỏi lại lúc đó).
- **Q4** Thêm nhóm **(f)** danh mục nền, làm sau (e).
- **Q5** Giữ nguyên: chỉ ADMIN import giảng viên.

## 7. Kế hoạch PR

Mỗi nhóm một nhánh tách từ `main` mới nhất, mỗi PR tối đa khoảng 10 file view/JS chính, tiêu đề `feat(fe): …`.

| Thứ tự | Nhánh | Nội dung |
|---|---|---|
| 1 | `feature/fe-dang-ky-hoc-phan` | `resources/js/api.js` + `resources/js/ui/*`, `layouts/portal`, file này (`docs/fe-be-mapping.md`); trang sinh viên: đăng ký học phần, kết quả đăng ký + phiếu PDF |
| 2 | `feature/fe-quan-ly-dot-dang-ky` | trang cán bộ: đợt đăng ký, đăng ký ngoại lệ, chốt đăng ký |
| 3 | `feature/fe-lop-hoc-phan` | lớp học phần (danh sách, chi tiết), cổng GV: lớp học phần |
| 4 | `feature/fe-thoi-khoa-bieu` | xếp TKB, xung đột, công bố / khóa, dạy bù; TKB sinh viên, lịch dạy GV |
| 5 | `feature/fe-sinh-vien` | sinh viên (danh sách, chi tiết, sửa, nhập học), khóa – lớp hành chính |
| 6 | `feature/fe-hoc-phan-ctdt` | học phần, CTĐT (danh sách, chi tiết) |
| 7 | `feature/fe-giang-vien` | giảng viên, import giảng viên, cổng GV: hồ sơ – lịch bận, đề cương |
| 8 | `feature/fe-quan-tri` | người dùng, phân quyền, nhật ký (kèm E1 nếu đồng ý), tham số, quy chế, trang lỗi |
| 9 | `feature/fe-danh-muc-nen` | năm học – học kỳ, phòng học, khoa – bộ môn – ngành, nhập dữ liệu |

Nhóm (a) tách làm hai PR (1, 2) để mỗi PR không quá 10 file chính.

Kiểm tra mỗi nhóm: CSDL riêng `sm_demo` (`$env:DB_DATABASE='sm_demo'; php artisan migrate:fresh --seed --force`), `$env:DB_DATABASE='sm_demo'; php artisan serve --port=8010 --no-reload` (thiếu `--no-reload` thì `serve` bỏ biến `DB_DATABASE` của shell và chạy trên CSDL trong `.env`), thao tác thật trên trình duyệt với đúng / sai quyền và lỗi 422, đối chiếu bảng trong `sm_demo`, `composer lint`, `npm run build`.

## Phụ lục A. Toàn bộ route API (284) theo nhóm

Sinh tự động từ `route:list` và `AuthSeeder::MATRIX`. Cột "Quyền" trống nghĩa là route không có middleware `permission:` (controller tự kiểm tra Policy / phạm vi, hoặc chỉ cần đăng nhập).

### (a) Đợt đăng ký và đăng ký học phần — 24 route

| Phương thức | URI | Quyền (middleware) | Vai trò có quyền theo ma trận mặc định | Màn hình mock dùng |
|---|---|---|---|---|
| `POST` | `/admin/course-sections/{courseSection}/merge` | `CLS.update,ALL` | ACAD, CTSV | admin/chot-dang-ky-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `GET` | `/admin/enrollment-stats` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/dot-dang-ky-hoc-phan, admin/chot-dang-ky-hoc-phan |
| `POST` | `/admin/enrollments/{enrollment}/cancel` | `ENR.delete,ALL` | ACAD | admin/dang-ky-ngoai-le |
| `GET` | `/admin/registration-periods` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/dot-dang-ky-hoc-phan |
| `POST` | `/admin/registration-periods` | `ENR.create,ALL` | ACAD | admin/dot-dang-ky-hoc-phan |
| `GET` | `/admin/registration-periods/{registrationPeriod}` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/dot-dang-ky-hoc-phan |
| `PUT` | `/admin/registration-periods/{registrationPeriod}` | `ENR.update,ALL` | ACAD | admin/dot-dang-ky-hoc-phan |
| `DELETE` | `/admin/registration-periods/{registrationPeriod}` | `ENR.delete,ALL` | ACAD | admin/dot-dang-ky-hoc-phan |
| `POST` | `/admin/registration-periods/{registrationPeriod}/publish` | `ENR.update,ALL` | ACAD | admin/dot-dang-ky-hoc-phan |
| `POST` | `/admin/students/{student}/enrollments` | `ENR.create,ALL` | ACAD | admin/dang-ky-ngoai-le |
| `GET` | `/admin/students/{student}/registration-options` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/dang-ky-ngoai-le |
| `GET` | `/admin/students/{student}/registration-result` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/dang-ky-ngoai-le, admin/sinh-vien-chi-tiet |
| `GET` | `/admin/students/{student}/registration-result/slip` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/dang-ky-ngoai-le, admin/sinh-vien-chi-tiet |
| `GET` | `/admin/terms/{term}/registration/finalize` | `ENR.approve,ALL` | ACAD | admin/chot-dang-ky-hoc-phan |
| `POST` | `/admin/terms/{term}/registration/finalize` | `ENR.approve,ALL` | ACAD | admin/chot-dang-ky-hoc-phan |
| `GET` | `/admin/terms/{term}/under-min-sections` | `CLS.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/chot-dang-ky-hoc-phan |
| `GET` | `/student/registration/enrollments` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | student/dang-ky-hoc-phan, student/dang-ky-hoc-phan-mobile |
| `POST` | `/student/registration/enrollments` | `ENR.create` | ACAD, STU(own) | student/dang-ky-hoc-phan, student/dang-ky-hoc-phan-mobile |
| `POST` | `/student/registration/enrollments/{enrollment}/cancel` | `ENR.delete` | ACAD, STU(own) | student/dang-ky-hoc-phan, student/dang-ky-hoc-phan-mobile |
| `POST` | `/student/registration/enrollments/{enrollment}/change` | `ENR.create` | ACAD, STU(own) | student/dang-ky-hoc-phan, student/dang-ky-hoc-phan-mobile |
| `POST` | `/student/registration/enrollments/{enrollment}/withdraw` | `ENR.delete` | ACAD, STU(own) | student/dang-ky-hoc-phan, student/dang-ky-hoc-phan-mobile |
| `GET` | `/student/registration/options` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | student/dang-ky-hoc-phan, student/dang-ky-hoc-phan-mobile |
| `GET` | `/student/registration/result` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | student/ket-qua-dang-ky |
| `GET` | `/student/registration/result/slip` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | student/ket-qua-dang-ky |

### (b) Lớp học phần và thời khóa biểu — 30 route

| Phương thức | URI | Quyền (middleware) | Vai trò có quyền theo ma trận mặc định | Màn hình mock dùng |
|---|---|---|---|---|
| `POST` | `/admin/class-sessions/{classSession}/cancel` | `TTB.update,ALL` | ACAD | admin/lich-day-bu-phong |
| `POST` | `/admin/class-sessions/{classSession}/makeup` | `TTB.create,ALL` | ACAD | admin/lich-day-bu-phong |
| `GET` | `/admin/course-sections` | `CLS.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/lop-hoc-phan, teacher/lop-hoc-phan |
| `POST` | `/admin/course-sections` | `CLS.create,ALL` | ACAD | admin/lop-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `GET` | `/admin/course-sections/{courseSection}` | `CLS.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/lop-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `PUT` | `/admin/course-sections/{courseSection}` | `CLS.update,ALL` | ACAD, CTSV | admin/lop-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `DELETE` | `/admin/course-sections/{courseSection}` | `CLS.delete,ALL` | ACAD | admin/lop-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `GET` | `/admin/course-sections/{courseSection}/enrollments` | `ENR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/lop-hoc-phan-chi-tiet, teacher/lop-hoc-phan |
| `GET` | `/admin/course-sections/{courseSection}/enrollments/export` | `ENR.export` | ACAD | admin/lop-hoc-phan-chi-tiet, teacher/lop-hoc-phan |
| `POST` | `/admin/course-sections/{courseSection}/instructors` | `CLS.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/lop-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `GET` | `/admin/course-sections/{courseSection}/schedule` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | admin/xep-thoi-khoa-bieu |
| `POST` | `/admin/course-sections/{courseSection}/schedule` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | admin/xep-thoi-khoa-bieu |
| `GET` | `/admin/course-sections/{courseSection}/schedule-conflicts` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | admin/xep-thoi-khoa-bieu |
| `GET` | `/admin/course-sections/{courseSection}/sessions` | `TTB.view` | ADMIN, ACAD, EXAM, CTSV, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/lich-day-bu-phong, admin/lop-hoc-phan-chi-tiet |
| `POST` | `/admin/course-sections/{courseSection}/sessions/generate` | `TTB.update,ALL` | ACAD | admin/lich-day-bu-phong, admin/lop-hoc-phan-chi-tiet |
| `POST` | `/admin/course-sections/{courseSection}/status/{status}` | `CLS.update,ALL` | ACAD, CTSV | admin/lop-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `POST` | `/admin/course-sections/{courseSection}/unlock` | `CLS.approve,ALL` | ACAD | admin/lop-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `GET` | `/admin/periods` | `TTB.view` | ADMIN, ACAD, EXAM, CTSV, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/xep-thoi-khoa-bieu |
| `POST` | `/admin/periods` | `TTB.create,ALL` | ACAD | admin/xep-thoi-khoa-bieu |
| `PUT` | `/admin/periods/{period}` | `TTB.update,ALL` | ACAD | admin/xep-thoi-khoa-bieu |
| `DELETE` | `/admin/periods/{period}` | `TTB.delete,ALL` | ACAD | admin/xep-thoi-khoa-bieu |
| `PUT` | `/admin/schedule-rules/{scheduleRule}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | admin/xep-thoi-khoa-bieu |
| `DELETE` | `/admin/schedule-rules/{scheduleRule}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | admin/xep-thoi-khoa-bieu |
| `DELETE` | `/admin/section-instructors/{sectionInstructor}` | `CLS.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/lop-hoc-phan, admin/lop-hoc-phan-chi-tiet |
| `GET` | `/admin/terms/{term}/timetable` | `TTB.view` | ADMIN, ACAD, EXAM, CTSV, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/xep-thoi-khoa-bieu |
| `POST` | `/admin/terms/{term}/timetable/lock` | `TTB.approve,ALL` | ACAD | admin/xep-thoi-khoa-bieu |
| `POST` | `/admin/terms/{term}/timetable/publish` | `TTB.approve,ALL` | ACAD | admin/xep-thoi-khoa-bieu |
| `GET` | `/admin/timetable` | `TTB.view` | ADMIN, ACAD, EXAM, CTSV, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/xep-thoi-khoa-bieu |
| `GET` | `/student/timetable` | `TTB.view` | ADMIN, ACAD, EXAM, CTSV, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | student/thoi-khoa-bieu |
| `GET` | `/teacher/timetable` | `TTB.view` | ADMIN, ACAD, EXAM, CTSV, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | teacher/lich-giang-day, teacher/lop-hoc-phan |

### (c) Sinh viên, nhập học, khóa và lớp hành chính — 23 route

| Phương thức | URI | Quyền (middleware) | Vai trò có quyền theo ma trận mặc định | Màn hình mock dùng |
|---|---|---|---|---|
| `GET` | `/admin/admin-classes` | `CLS.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/khoa-va-lop-hanh-chinh |
| `POST` | `/admin/admin-classes` | `CLS.create` | ACAD | admin/khoa-va-lop-hanh-chinh |
| `GET` | `/admin/admin-classes/{adminClass}` | `CLS.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/khoa-va-lop-hanh-chinh |
| `PUT` | `/admin/admin-classes/{adminClass}` | `CLS.update` | ACAD, CTSV | admin/khoa-va-lop-hanh-chinh |
| `DELETE` | `/admin/admin-classes/{adminClass}` | `CLS.delete` | ACAD | admin/khoa-va-lop-hanh-chinh |
| `GET` | `/admin/cohorts` | `CLS.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/khoa-va-lop-hanh-chinh |
| `POST` | `/admin/cohorts` | `CLS.create,ALL` | ACAD | admin/khoa-va-lop-hanh-chinh |
| `PUT` | `/admin/cohorts/{cohort}` | `CLS.update,ALL` | ACAD, CTSV | admin/khoa-va-lop-hanh-chinh |
| `DELETE` | `/admin/cohorts/{cohort}` | `CLS.delete,ALL` | ACAD | admin/khoa-va-lop-hanh-chinh |
| `PUT` | `/admin/student-contacts/{studentContact}` | `STU.update` | ACAD, CTSV, ADV(advisee), STU(own) | admin/sinh-vien-chi-tiet |
| `DELETE` | `/admin/student-contacts/{studentContact}` | `STU.update` | ACAD, CTSV, ADV(advisee), STU(own) | admin/sinh-vien-chi-tiet |
| `GET` | `/admin/students` | `STU.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/sinh-vien |
| `POST` | `/admin/students` | `STU.create` | ACAD, CTSV | admin/sinh-vien |
| `GET` | `/admin/students/{student}` | `STU.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/sinh-vien-chi-tiet, student/ho-so-ca-nhan |
| `DELETE` | `/admin/students/{student}` | `STU.delete` | **không ai** | admin/sinh-vien-doi-trang-thai |
| `PUT` | `/admin/students/{student}` | `STU.update` | ACAD, CTSV, ADV(advisee), STU(own) | admin/sinh-vien-doi-trang-thai |
| `POST` | `/admin/students/{student}/admit` | `STU.approve` | ACAD, CTSV | admin/sinh-vien-doi-trang-thai |
| `POST` | `/admin/students/{student}/cancel-admission` | `STU.approve` | ACAD, CTSV | admin/sinh-vien-doi-trang-thai |
| `GET` | `/admin/students/{student}/change-logs` | `STU.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/sinh-vien-chi-tiet |
| `POST` | `/admin/students/{student}/contacts` | `STU.update` | ACAD, CTSV, ADV(advisee), STU(own) | admin/sinh-vien-chi-tiet |
| `GET` | `/admin/students/{student}/curricula` | `STU.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV(advisee), STU(own) | admin/sinh-vien-chi-tiet, student/chuong-trinh-dao-tao |
| `POST` | `/admin/students/{student}/curricula` | `STU.update` | ACAD, CTSV, ADV(advisee), STU(own) | admin/sinh-vien-chi-tiet, student/chuong-trinh-dao-tao |
| `GET` | `/admin/students/{student}/sensitive` | `STU.update` | ACAD, CTSV, ADV(advisee), STU(own) | admin/sinh-vien-chi-tiet |

### (d) Học phần, CTĐT, giảng viên, import — 85 route

| Phương thức | URI | Quyền (middleware) | Vai trò có quyền theo ma trận mặc định | Màn hình mock dùng |
|---|---|---|---|---|
| `GET` | `/admin/curricula` | `CUR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU(own) | admin/chuong-trinh-dao-tao |
| `POST` | `/admin/curricula` | `CUR.create` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao |
| `GET` | `/admin/curricula/{curriculum}` | `CUR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU(own) | admin/chuong-trinh-dao-tao-chi-tiet, student/chuong-trinh-dao-tao |
| `DELETE` | `/admin/curricula/{curriculum}` | `CUR.delete` | ACAD | admin/chuong-trinh-dao-tao-chi-tiet |
| `PUT` | `/admin/curricula/{curriculum}` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `POST` | `/admin/curricula/{curriculum}/blocks` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `GET` | `/admin/curricula/{curriculum}/check` | `CUR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU(own) | admin/chuong-trinh-dao-tao-chi-tiet |
| `POST` | `/admin/curricula/{curriculum}/elective-groups` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `GET` | `/admin/curricula/{curriculum}/graduation-requirements` | `CUR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU(own) | admin/chuong-trinh-dao-tao-chi-tiet |
| `PUT` | `/admin/curricula/{curriculum}/graduation-requirements` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `POST` | `/admin/curricula/{curriculum}/publish` | `CUR.approve,ALL` | ACAD | admin/chuong-trinh-dao-tao-chi-tiet |
| `POST` | `/admin/curricula/{curriculum}/subjects` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `GET` | `/admin/curricula/{curriculum}/versions` | `CUR.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU(own) | admin/chuong-trinh-dao-tao-chi-tiet |
| `POST` | `/admin/curricula/{curriculum}/versions` | `CUR.create` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `PUT` | `/admin/curriculum-blocks/{curriculumBlock}` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `DELETE` | `/admin/curriculum-blocks/{curriculumBlock}` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `PUT` | `/admin/curriculum-elective-groups/{electiveGroup}` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `DELETE` | `/admin/curriculum-elective-groups/{electiveGroup}` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `PUT` | `/admin/curriculum-subjects/{curriculumSubject}` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `DELETE` | `/admin/curriculum-subjects/{curriculumSubject}` | `CUR.update` | ACAD, DEAN(faculty) | admin/chuong-trinh-dao-tao-chi-tiet |
| `GET` | `/admin/imports` | `SYS.view` | ADMIN, ACAD | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `POST` | `/admin/imports` | `SYS.create` | ADMIN | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `GET` | `/admin/imports/importers` | `SYS.view` | ADMIN, ACAD | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `GET` | `/admin/imports/{batch}` | `SYS.view` | ADMIN, ACAD | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `DELETE` | `/admin/imports/{batch}` | `SYS.delete` | ADMIN | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `GET` | `/admin/imports/{batch}/preview` | `SYS.view` | ADMIN, ACAD | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `GET` | `/admin/imports/{batch}/progress` | `SYS.view` | ADMIN, ACAD | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `POST` | `/admin/imports/{batch}/rollback` | `SYS.delete` | ADMIN | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `POST` | `/admin/imports/{batch}/save` | `SYS.create` | ADMIN | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `POST` | `/admin/imports/{batch}/validate` | `SYS.create` | ADMIN | admin/giang-vien-import, admin/sao-luu-khoa-so-import (tab Import) |
| `GET` | `/admin/lecturer-change-requests` | `TCH.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(own), ADV(own), STU | admin/giang-vien |
| `POST` | `/admin/lecturer-change-requests/{lecturerChangeRequest}/approve` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `POST` | `/admin/lecturer-change-requests/{lecturerChangeRequest}/reject` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `GET` | `/admin/lecturer-leaves` | `TCH.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(own), ADV(own), STU | admin/giang-vien |
| `POST` | `/admin/lecturer-leaves/{lecturerLeave}/approve` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `POST` | `/admin/lecturer-leaves/{lecturerLeave}/reject` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `DELETE` | `/admin/lecturer-units/{lecturerUnit}` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `POST` | `/admin/lecturer-units/{lecturerUnit}/end` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `GET` | `/admin/lecturers` | `TCH.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(own), ADV(own), STU | admin/giang-vien |
| `POST` | `/admin/lecturers` | `TCH.create` | ACAD, DEAN(faculty) | admin/giang-vien |
| `GET` | `/admin/lecturers/export` | `TCH.view + TCH.export` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(own), ADV(own), STU · ACAD, DEAN(faculty) | admin/giang-vien |
| `GET` | `/admin/lecturers/{lecturer}` | `TCH.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(own), ADV(own), STU | admin/giang-vien |
| `PUT` | `/admin/lecturers/{lecturer}` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `POST` | `/admin/lecturers/{lecturer}/account` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `GET` | `/admin/lecturers/{lecturer}/availability` | `TCH.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(own), ADV(own), STU | admin/giang-vien |
| `GET` | `/admin/lecturers/{lecturer}/handover` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `POST` | `/admin/lecturers/{lecturer}/status` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `GET` | `/admin/lecturers/{lecturer}/units` | `TCH.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(own), ADV(own), STU | admin/giang-vien |
| `POST` | `/admin/lecturers/{lecturer}/units` | `TCH.update` | ACAD, DEAN(faculty), LEC(own), ADV(own) | admin/giang-vien |
| `DELETE` | `/admin/subject-relations/{subjectRelation}` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | admin/hoc-phan |
| `POST` | `/admin/subject-relations/{subjectRelation}/end` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | admin/hoc-phan |
| `GET` | `/admin/subjects` | `SUB.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV, STU | admin/hoc-phan |
| `POST` | `/admin/subjects` | `SUB.create` | ACAD, DEAN(faculty) | admin/hoc-phan |
| `GET` | `/admin/subjects/export` | `SUB.export` | ACAD | admin/hoc-phan |
| `GET` | `/admin/subjects/{subject}` | `SUB.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV, STU | admin/hoc-phan |
| `PUT` | `/admin/subjects/{subject}` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | admin/hoc-phan |
| `DELETE` | `/admin/subjects/{subject}` | `SUB.delete` | ACAD | admin/hoc-phan |
| `GET` | `/admin/subjects/{subject}/grade-components` | `SUB.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV, STU | admin/hoc-phan |
| `PUT` | `/admin/subjects/{subject}/grade-components` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | admin/hoc-phan |
| `GET` | `/admin/subjects/{subject}/prerequisites` | `SUB.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV, STU | admin/hoc-phan |
| `GET` | `/admin/subjects/{subject}/relations` | `SUB.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV, STU | admin/hoc-phan |
| `POST` | `/admin/subjects/{subject}/relations` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | admin/hoc-phan |
| `GET` | `/admin/subjects/{subject}/syllabi` | `SUB.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV, STU | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `POST` | `/admin/subjects/{subject}/syllabi` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `GET` | `/admin/syllabi/{syllabus}` | `SUB.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV, STU | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `PUT` | `/admin/syllabi/{syllabus}` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `DELETE` | `/admin/syllabi/{syllabus}` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `POST` | `/admin/syllabi/{syllabus}/approve` | `SUB.approve` | ACAD, DEAN(faculty) | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `GET` | `/admin/syllabi/{syllabus}/file` | `SUB.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(section), ADV, STU | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `POST` | `/admin/syllabi/{syllabus}/file` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `POST` | `/admin/syllabi/{syllabus}/reject` | `SUB.approve` | ACAD, DEAN(faculty) | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `POST` | `/admin/syllabi/{syllabus}/submit` | `SUB.update` | ACAD, DEAN(faculty), LEC(section) | teacher/de-cuong-hoc-phan, admin/hoc-phan |
| `GET` | `/admin/teaching-preferences` | `TCH.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC(own), ADV(own), STU | admin/giang-vien |
| `GET` | `/teacher/availability` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `POST` | `/teacher/availability` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `DELETE` | `/teacher/availability/{lecturerAvailability}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `GET` | `/teacher/leaves` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `POST` | `/teacher/leaves` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `POST` | `/teacher/leaves/{lecturerLeave}/cancel` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `GET` | `/teacher/preferences` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `PUT` | `/teacher/preferences` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `GET` | `/teacher/profile` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `PUT` | `/teacher/profile` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `DELETE` | `/teacher/profile/change-requests/{lecturerChangeRequest}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |
| `POST` | `/teacher/profile/photo` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | teacher/ho-so-lich-ban |

### (e) Quản trị: người dùng, phân quyền, tham số, quy chế — 32 route

| Phương thức | URI | Quyền (middleware) | Vai trò có quyền theo ma trận mặc định | Màn hình mock dùng |
|---|---|---|---|---|
| `GET` | `/admin/login-histories` | `AUTH.view,ALL` | ADMIN | — (không có mock; đề xuất thành tab trong admin/nhat-ky-thao-tac) |
| `GET` | `/admin/permissions` | `AUTH.view,ALL` | ADMIN | admin/phan-quyen |
| `GET` | `/admin/policy-sets` | `SYS.view` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `POST` | `/admin/policy-sets` | `SYS.update` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `GET` | `/admin/policy-sets/definitions` | `SYS.view` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `GET` | `/admin/policy-sets/resolve` | `SYS.view` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `GET` | `/admin/policy-sets/{policySet}` | `SYS.view` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `PUT` | `/admin/policy-sets/{policySet}` | `SYS.update` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `DELETE` | `/admin/policy-sets/{policySet}` | `SYS.update` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `PUT` | `/admin/policy-sets/{policySet}/items` | `SYS.update` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `POST` | `/admin/policy-sets/{policySet}/publish` | `SYS.update` | ADMIN, ACAD | admin/quy-che-hoc-vu |
| `GET` | `/admin/roles` | `AUTH.view,ALL` | ADMIN | admin/phan-quyen |
| `POST` | `/admin/roles` | `AUTH.create,ALL` | ADMIN | admin/phan-quyen |
| `GET` | `/admin/roles/{role}` | `AUTH.view,ALL` | ADMIN | admin/phan-quyen |
| `PUT` | `/admin/roles/{role}` | `AUTH.update,ALL` | ADMIN | admin/phan-quyen |
| `DELETE` | `/admin/roles/{role}` | `AUTH.delete,ALL` | ADMIN | admin/phan-quyen |
| `PUT` | `/admin/roles/{role}/permissions` | `AUTH.update,ALL` | ADMIN | admin/phan-quyen |
| `GET` | `/admin/settings` | `SYS.view` | ADMIN, ACAD | admin/tham-so-he-thong, admin/xac-nhan-thay-doi-cau-hinh |
| `PUT` | `/admin/settings` | `SYS.update` | ADMIN, ACAD | admin/tham-so-he-thong, admin/xac-nhan-thay-doi-cau-hinh |
| `POST` | `/admin/settings/logo` | `SYS.update` | ADMIN, ACAD | admin/tham-so-he-thong, admin/xac-nhan-thay-doi-cau-hinh |
| `GET` | `/admin/users` | `AUTH.view,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `POST` | `/admin/users` | `AUTH.create,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `GET` | `/admin/users/{user}` | `AUTH.view,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `PUT` | `/admin/users/{user}` | `AUTH.update,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `POST` | `/admin/users/{user}/activate` | `AUTH.update,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `POST` | `/admin/users/{user}/deactivate` | `AUTH.update,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `POST` | `/admin/users/{user}/lock` | `AUTH.approve,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `GET` | `/admin/users/{user}/roles` | `AUTH.view,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `POST` | `/admin/users/{user}/roles` | `AUTH.update,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `DELETE` | `/admin/users/{user}/roles/{role}` | `AUTH.update,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `POST` | `/admin/users/{user}/temporary-password` | `AUTH.update,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |
| `POST` | `/admin/users/{user}/unlock` | `AUTH.approve,ALL` | ADMIN | admin/nguoi-dung, admin/nguoi-dung-form |

### (f) Danh mục nền: năm học – học kỳ, phòng học, khoa – bộ môn – ngành, danh mục dùng chung — 68 route

| Phương thức | URI | Quyền (middleware) | Vai trò có quyền theo ma trận mặc định | Màn hình mock dùng |
|---|---|---|---|---|
| `GET` | `/admin/academic-years` | `ACY.view,ALL` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN, LEC, ADV, STU | admin/nam-hoc-hoc-ky |
| `POST` | `/admin/academic-years` | `ACY.create,ALL` | ACAD | admin/nam-hoc-hoc-ky |
| `GET` | `/admin/academic-years/current-term` | `ACY.view,ALL` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN, LEC, ADV, STU | admin/nam-hoc-hoc-ky |
| `POST` | `/admin/academic-years/terms` | `ACY.create,ALL` | ACAD | admin/nam-hoc-hoc-ky |
| `POST` | `/admin/academic-years/terms/{term}/current` | `ACY.approve,ALL` | ACAD | admin/nam-hoc-hoc-ky |
| `POST` | `/admin/academic-years/terms/{term}/status/{status}` | `ACY.approve,ALL` | ACAD | admin/nam-hoc-hoc-ky |
| `GET` | `/admin/academic-years/{academicYear}` | `ACY.view,ALL` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN, LEC, ADV, STU | admin/nam-hoc-hoc-ky |
| `PUT` | `/admin/academic-years/{academicYear}` | `ACY.update,ALL` | ACAD | admin/nam-hoc-hoc-ky |
| `GET` | `/admin/administrative-units` | `SYS.view` | ADMIN, ACAD | — (không có mock) |
| `POST` | `/admin/administrative-units` | `SYS.create` | ADMIN | — (không có mock) |
| `GET` | `/admin/administrative-units/{administrativeUnit}` | `SYS.view` | ADMIN, ACAD | — (không có mock) |
| `PUT` | `/admin/administrative-units/{administrativeUnit}` | `SYS.update` | ADMIN, ACAD | — (không có mock) |
| `DELETE` | `/admin/administrative-units/{administrativeUnit}` | `SYS.delete` | ADMIN | — (không có mock) |
| `GET` | `/admin/buildings` | `ROM.view` | ADMIN, ACAD, EXAM, CTSV, DEAN, LEC, ADV, STU | admin/phong-hoc |
| `POST` | `/admin/buildings` | `ROM.create` | ADMIN, ACAD | admin/phong-hoc |
| `PUT` | `/admin/buildings/{building}` | `ROM.update` | ADMIN, ACAD | admin/phong-hoc |
| `DELETE` | `/admin/buildings/{building}` | `ROM.delete` | ADMIN, ACAD | admin/phong-hoc |
| `GET` | `/admin/campuses` | `ROM.view` | ADMIN, ACAD, EXAM, CTSV, DEAN, LEC, ADV, STU | admin/phong-hoc |
| `POST` | `/admin/campuses` | `ROM.create` | ADMIN, ACAD | admin/phong-hoc |
| `PUT` | `/admin/campuses/{campus}` | `ROM.update` | ADMIN, ACAD | admin/phong-hoc |
| `DELETE` | `/admin/campuses/{campus}` | `ROM.delete` | ADMIN, ACAD | admin/phong-hoc |
| `GET` | `/admin/holidays` | `ACY.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN, LEC, ADV, STU | admin/nam-hoc-hoc-ky |
| `POST` | `/admin/holidays` | `ACY.create` | ACAD | admin/nam-hoc-hoc-ky |
| `GET` | `/admin/holidays/days-off` | `ACY.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN, LEC, ADV, STU | admin/nam-hoc-hoc-ky |
| `PUT` | `/admin/holidays/{holiday}` | `ACY.update` | ACAD | admin/nam-hoc-hoc-ky |
| `DELETE` | `/admin/holidays/{holiday}` | `ACY.delete` | ACAD | admin/nam-hoc-hoc-ky |
| `GET` | `/admin/leadership-terms` | `FAC.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU | admin/danh-muc-nen |
| `POST` | `/admin/leadership-terms` | `FAC.update,ALL` | ADMIN, ACAD | admin/danh-muc-nen |
| `DELETE` | `/admin/leadership-terms/{leadershipTerm}` | `FAC.update,ALL` | ADMIN, ACAD | admin/danh-muc-nen |
| `POST` | `/admin/leadership-terms/{leadershipTerm}/end` | `FAC.update,ALL` | ADMIN, ACAD | admin/danh-muc-nen |
| `PUT` | `/admin/lookup-values/{lookupValue}` | `SYS.update` | ADMIN, ACAD | — (không có mock) |
| `DELETE` | `/admin/lookup-values/{lookupValue}` | `SYS.delete` | ADMIN | — (không có mock) |
| `GET` | `/admin/lookups` | `SYS.view` | ADMIN, ACAD | — (không có mock) |
| `POST` | `/admin/lookups` | `SYS.create` | ADMIN | — (không có mock) |
| `PUT` | `/admin/lookups/{lookupCategory:code}` | `SYS.update` | ADMIN, ACAD | — (không có mock) |
| `DELETE` | `/admin/lookups/{lookupCategory:code}` | `SYS.delete` | ADMIN | — (không có mock) |
| `GET` | `/admin/lookups/{lookupCategory:code}/values` | `SYS.view` | ADMIN, ACAD | — (không có mock) |
| `POST` | `/admin/lookups/{lookupCategory:code}/values` | `SYS.create` | ADMIN | — (không có mock) |
| `GET` | `/admin/milestone-change-requests` | `ACY.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN, LEC, ADV, STU | admin/nam-hoc-hoc-ky |
| `POST` | `/admin/milestone-change-requests/{milestoneChangeRequest}/approve` | `ACY.approve,ALL` | ACAD | admin/nam-hoc-hoc-ky |
| `POST` | `/admin/milestone-change-requests/{milestoneChangeRequest}/reject` | `ACY.approve,ALL` | ACAD | admin/nam-hoc-hoc-ky |
| `PUT` | `/admin/room-maintenances/{roomMaintenance}` | `ROM.update` | ADMIN, ACAD | admin/phong-hoc |
| `DELETE` | `/admin/room-maintenances/{roomMaintenance}` | `ROM.update` | ADMIN, ACAD | admin/phong-hoc |
| `GET` | `/admin/room-types` | `ROM.view` | ADMIN, ACAD, EXAM, CTSV, DEAN, LEC, ADV, STU | admin/phong-hoc |
| `POST` | `/admin/room-types` | `ROM.create` | ADMIN, ACAD | admin/phong-hoc |
| `PUT` | `/admin/room-types/{roomType}` | `ROM.update` | ADMIN, ACAD | admin/phong-hoc |
| `GET` | `/admin/rooms` | `ROM.view` | ADMIN, ACAD, EXAM, CTSV, DEAN, LEC, ADV, STU | admin/phong-hoc |
| `POST` | `/admin/rooms` | `ROM.create` | ADMIN, ACAD | admin/phong-hoc |
| `GET` | `/admin/rooms/{room}` | `ROM.view` | ADMIN, ACAD, EXAM, CTSV, DEAN, LEC, ADV, STU | admin/phong-hoc |
| `PUT` | `/admin/rooms/{room}` | `ROM.update` | ADMIN, ACAD | admin/phong-hoc |
| `DELETE` | `/admin/rooms/{room}` | `ROM.delete` | ADMIN, ACAD | admin/phong-hoc |
| `GET` | `/admin/rooms/{room}/availability` | `ROM.view` | ADMIN, ACAD, EXAM, CTSV, DEAN, LEC, ADV, STU | admin/phong-hoc, admin/lich-day-bu-phong |
| `GET` | `/admin/rooms/{room}/maintenances` | `ROM.view` | ADMIN, ACAD, EXAM, CTSV, DEAN, LEC, ADV, STU | admin/phong-hoc |
| `POST` | `/admin/rooms/{room}/maintenances` | `ROM.update` | ADMIN, ACAD | admin/phong-hoc |
| `GET` | `/admin/terms/{term}/milestones` | `ACY.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN, LEC, ADV, STU | admin/nam-hoc-hoc-ky |
| `PUT` | `/admin/terms/{term}/milestones` | `ACY.update` | ACAD | admin/nam-hoc-hoc-ky |
| `POST` | `/admin/terms/{term}/milestones/copy` | `ACY.update` | ACAD | admin/nam-hoc-hoc-ky |
| `GET` | `/admin/training-types` | `FAC.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU | admin/danh-muc-nen |
| `POST` | `/admin/training-types` | `FAC.create` | ADMIN, ACAD | admin/danh-muc-nen |
| `PUT` | `/admin/training-types/{trainingType}` | `FAC.update,ALL` | ADMIN, ACAD | admin/danh-muc-nen |
| `GET` | `/admin/{unitType}` | `FAC.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU | admin/danh-muc-nen |
| `POST` | `/admin/{unitType}` | `FAC.create` | ADMIN, ACAD | admin/danh-muc-nen |
| `GET` | `/admin/{unitType}/export` | `FAC.export` | **không ai** | admin/danh-muc-nen |
| `GET` | `/admin/{unitType}/{id}` | `FAC.view` | ADMIN, ACAD, EXAM, CTSV, FIN, DEAN(faculty), LEC, ADV, STU | admin/danh-muc-nen |
| `PUT` | `/admin/{unitType}/{id}` | `FAC.update` | ADMIN, ACAD, DEAN(faculty) | admin/danh-muc-nen |
| `DELETE` | `/admin/{unitType}/{id}` | `FAC.delete` | ADMIN, ACAD | admin/danh-muc-nen |
| `GET` | `/danh-muc/don-vi-hanh-chinh` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | dùng chung cho ô chọn trong biểu mẫu |
| `GET` | `/danh-muc/{category}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | dùng chung cho ô chọn trong biểu mẫu |

### Đã dùng dữ liệu thật / hệ thống — 22 route

| Phương thức | URI | Quyền (middleware) | Vai trò có quyền theo ma trận mặc định | Màn hình mock dùng |
|---|---|---|---|---|
| `GET` | `/` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | hệ thống (không phải màn hình) |
| `GET` | `/admin` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | trang chủ cổng — dữ liệu giả trong DashboardController (dashboard chưa có BE) |
| `GET` | `/forgot-password` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `POST` | `/forgot-password` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `GET` | `/login` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `POST` | `/login` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `POST` | `/logout` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `PUT` | `/password` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `GET` | `/password/change` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `POST` | `/reset-password` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `GET` | `/reset-password/{token}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | public/dang-nhap, public/quen-mat-khau, public/dat-lai-mat-khau, student/doi-mat-khau (đã thật) |
| `GET` | `/storage/{path}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | hệ thống (không phải màn hình) |
| `PUT` | `/storage/{path}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | hệ thống (không phải màn hình) |
| `GET` | `/student` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | trang chủ cổng — dữ liệu giả trong DashboardController (dashboard chưa có BE) |
| `GET` | `/student/mobile` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | trang chủ cổng — dữ liệu giả trong DashboardController (dashboard chưa có BE) |
| `GET` | `/tai-khoan/cai-dat` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | student/phien-dang-nhap, student/cai-dat-tai-khoan, student/tuy-chon-thong-bao (đã thật) |
| `GET` | `/tai-khoan/phien-dang-nhap` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | student/phien-dang-nhap, student/cai-dat-tai-khoan, student/tuy-chon-thong-bao (đã thật) |
| `DELETE` | `/tai-khoan/phien-dang-nhap` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | student/phien-dang-nhap, student/cai-dat-tai-khoan, student/tuy-chon-thong-bao (đã thật) |
| `DELETE` | `/tai-khoan/phien-dang-nhap/{handle}` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | student/phien-dang-nhap, student/cai-dat-tai-khoan, student/tuy-chon-thong-bao (đã thật) |
| `GET` | `/tai-khoan/thong-bao` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | student/phien-dang-nhap, student/cai-dat-tai-khoan, student/tuy-chon-thong-bao (đã thật) |
| `GET` | `/teacher` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | trang chủ cổng — dữ liệu giả trong DashboardController (dashboard chưa có BE) |
| `GET` | `/up` | — | (kiểm tra trong controller / chỉ cần đăng nhập) | hệ thống (không phải màn hình) |
