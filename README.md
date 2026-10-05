# StudentManager — Hệ thống Quản Lý Sinh Viên

Hệ thống web quản lý trọn vòng đời học vụ của sinh viên đại học chính quy theo **học chế tín chỉ**: nhập học, lớp, đăng ký học phần, thời khóa biểu, điểm danh, thi, điểm, học phí, giấy tờ, thông báo và xét tốt nghiệp.

Đây là đồ án môn học. Hiện dự án đang ở **giai đoạn phân tích nghiệp vụ (BA)**; mã nguồn chưa bắt đầu.

## Trạng thái

| Hạng mục | Trạng thái |
|---|---|
| Phân tích nghiệp vụ (BA v1.0) | Hoàn thành, chờ duyệt — xem [docs/BA.md](docs/BA.md) |
| SRS / use case | Chưa bắt đầu |
| Thiết kế CSDL | Chưa bắt đầu |
| Lập trình | Chưa bắt đầu |
| Kiểm thử, nghiệm thu | Chưa bắt đầu |

## Phạm vi

23 module, chia thành 4 giai đoạn phát hành:

| Giai đoạn | Tên | Module chính |
|---|---|---|
| **P1** | MVP — Học vụ cốt lõi | AUTH, SYS, FAC, ACY, ROM, SUB, CUR, TCH, CLS, STU, ENR, TTB, GRD, NOT, RPT |
| **P2** | Vận hành | ATT, EXM, FEE, DOC, NOT (email), TTB nâng cao |
| **P3** | Hoàn thiện | GRA, REQ, RPT đầy đủ, SYS đầy đủ |
| **P4** | Mở rộng (tùy chọn) | SCH, EVA, cổng thanh toán, SSO, xếp lịch tự động |

Chi tiết từng module, quy tắc nghiệp vụ (BR-), yêu cầu chức năng (FR-) và phi chức năng (NFR-) nằm trong [docs/BA.md](docs/BA.md).

## Công nghệ (dự kiến)

- Backend: Laravel + PHP
- Cơ sở dữ liệu: MySQL (không dùng SQLite vì đăng ký học phần ghi đồng thời)
- Môi trường phát triển: Laragon trên Windows

## Cấu trúc thư mục

```
StudentManager/
├── docs/
│   └── BA.md              # Tài liệu phân tích nghiệp vụ
├── .github/
│   ├── ISSUE_TEMPLATE/    # Mẫu Issue
│   └── pull_request_template.md
├── .gitignore
└── README.md
```

## Cài đặt và chạy thử

Sẽ được bổ sung khi bắt đầu lập trình.

## Quy trình làm việc

- Nhánh `main` luôn ở trạng thái ổn định; không commit trực tiếp lên `main`.
- Mỗi việc làm trên một nhánh riêng, đặt tên `loai/mo-ta-ngan`, ví dụ `feature/enr-dang-ky-hoc-phan`, `fix/loi-dang-nhap`, `docs/cap-nhat-ba`.
- Mỗi việc gắn với một Issue; Pull Request ghi `Closes #<số issue>` để Issue tự đóng khi gộp.
- Thông điệp commit theo dạng `loại: mô tả ngắn` (`feat`, `fix`, `docs`, `refactor`, `test`, `chore`).

## Tác giả

- GitHub: [@HoaiNam765](https://github.com/HoaiNam765)
- Nhóm thực hiện: *[Họ tên — MSSV — Lớp]*
- Giảng viên hướng dẫn: *[Họ tên]*
