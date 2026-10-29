# Thiết kế giao diện

Thư mục này chứa mọi tài liệu và sản phẩm thiết kế giao diện của StudentManager. Nguồn yêu cầu là `docs/BA.md` mục 8 (phân hệ, danh sách màn hình, nguyên tắc UX).

Công việc đang làm: Issue "[FE] Thiết kế giao diện P1: khung layout 3 cổng và trang đăng nhập". Danh sách đầy đủ các màn hình nằm ở [danh-sach-man-hinh.md](danh-sach-man-hinh.md).

## Công nghệ

Blade + Tailwind CSS 4, build bằng Vite. Muốn đổi sang Bootstrap hoặc stack khác, bình luận ở Issue để chủ repo chốt trước khi làm.

Cách cài và chạy dự án: xem mục "Cài đặt và chạy thử" trong [README gốc](../../README.md).

## Nơi đặt sản phẩm thiết kế

| Thư mục | Nội dung |
|---|---|
| `wireframes/` | Wireframe (PNG hoặc PDF) |
| `mockups/` | Mockup chi tiết (PNG hoặc PDF) |

File thiết kế gốc (Figma và các công cụ tương tự) không lưu trong repo. Ghi đường dẫn vào bảng dưới đây:

| Nội dung | Đường dẫn | Người phụ trách |
|---|---|---|
| *(chưa có)* | | |

Đặt tên file ảnh theo `<cổng>-<màn-hình>-v<số>.png`, ví dụ `student-enrollment-register-v1.png`.

## Quy ước viết Blade

- Đường dẫn view: `resources/views/<cổng>/<module>/<ten-man-hinh>.blade.php`, tên tiếng Anh, viết thường, nối bằng dấu gạch ngang. Ví dụ `student/enrollment/register.blade.php`.
- `<cổng>` là một trong `auth`, `student`, `teacher`, `admin`; `<module>` là mã module trong BA viết thành tên thư mục (xem `app/Modules/README.md`).
- Khung trang đặt ở `layouts/`, thành phần dùng lại đặt ở `components/` và gọi bằng `<x-ten-thanh-phan />`, đoạn dùng chung (menu, breadcrumb) đặt ở `partials/`.
- Nội dung hiển thị bằng tiếng Việt, dùng đúng thuật ngữ ở BA mục 1.4.
- Giai đoạn này chỉ làm giao diện tĩnh với dữ liệu giả. Chưa nối backend.

## Nguyên tắc UX bắt buộc

Đầy đủ ở BA mục 8.3 (UX-01 → UX-14). Những điểm hay bị bỏ sót:

| Mã | Nguyên tắc |
|---|---|
| UX-03 | Responsive từ chiều rộng 360 px; Cổng Sinh viên ưu tiên điện thoại |
| UX-04 | Danh sách có tìm kiếm, lọc, sắp xếp, phân trang |
| UX-05 | Biểu mẫu báo lỗi ngay tại trường nhập và giữ nguyên dữ liệu đã nhập |
| UX-06 | Thao tác nguy hiểm (xóa, hủy, chốt) có hộp thoại xác nhận nêu rõ hệ quả |
| UX-07 | Trạng thái dùng nhãn chữ **và** màu; tương phản đạt WCAG AA |
| UX-08 | Có trạng thái đang tải, danh sách rỗng và lỗi |
| UX-10 | Điều hướng nhất quán: menu theo vai trò, breadcrumb, nút quay lại |
| UX-13 | Dùng được bằng bàn phím và trình đọc màn hình |

## Quy trình làm việc

1. Nhận một việc trong Issue, bình luận để mọi người biết bạn đang làm.
2. Tạo nhánh từ `main` mới nhất: `feature/fe-<việc>`.
3. Commit nhỏ, thông điệp theo dạng `feat(ui): ...` hoặc `docs(ui): ...`.
4. Đẩy nhánh và mở Pull Request, điền đủ mẫu PR, ghi `Closes #<số Issue>` nếu PR hoàn thành Issue đó.
5. Chờ chủ repo duyệt. **Không push trực tiếp lên `main`** và không tự merge.
