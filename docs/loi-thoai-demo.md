# Lời thoại demo — Hệ thống Quản lý Sinh viên

Lời nói khi trình bày, đi kèm thao tác trong `docs/kich-ban-demo.md` (mỗi đoạn ghi phần tương ứng A–J). Khoảng 20–25 phút nói và thao tác, 5 phút hỏi đáp. Không cần đọc thuộc lòng — nắm ý chính in đậm rồi nói theo cách của mình.

Trước khi vào phòng: đã làm bước 0 (dựng dữ liệu, mở hai trình duyệt) và bước 0.5 (điền sẵn điểm thi lớp INT201).

---

## Mở đầu (1 phút)

> Em chào thầy/cô. Nhóm em xin trình bày đồ án **Hệ thống Quản lý Sinh viên theo học chế tín chỉ**.
>
> Bài toán của nhóm là: một trường đại học cần quản lý **cả vòng đời một học kỳ** — từ lập kế hoạch mở lớp, xếp thời khóa biểu, cho sinh viên đăng ký học phần, đến điểm danh, nhập điểm, duyệt và công bố điểm. Mỗi việc do một vai trò khác nhau làm: Phòng Đào tạo, trưởng khoa, giảng viên, sinh viên.
>
> Hệ thống làm bằng **Laravel 13, PHP 8.3, MySQL 8.4**. Nhóm phân tích yêu cầu trước thành tài liệu BA với 23 module, mã yêu cầu FR, BR, UAT; bản demo hôm nay là **giai đoạn P1** — phần lõi của đào tạo tín chỉ.
>
> Em sẽ demo theo đúng thứ tự một học kỳ diễn ra, đổi qua lại giữa các vai trò để thầy/cô thấy dữ liệu chảy từ người này sang người khác.

## A. Đăng nhập và phân quyền (1–2 phút) — `admin`

> Đầu tiên là đăng nhập. Mỗi người có **một tài khoản, vào đúng cổng của vai trò mình**: cán bộ vào cổng quản trị, giảng viên vào cổng giảng viên, sinh viên vào cổng sinh viên. Menu chỉ hiện chức năng người đó có quyền.
>
> Hệ thống có **9 vai trò** và một ma trận phân quyền lấy từ tài liệu BA. Ngoài quyền "được làm gì" còn có **phạm vi dữ liệu**: trưởng khoa chỉ thấy dữ liệu khoa mình, giảng viên chỉ thấy lớp mình dạy, sinh viên chỉ thấy của chính mình. Truy cập trái quyền bị chặn và **ghi vào nhật ký kiểm toán** — nhật ký này chỉ ghi thêm, không sửa, không xóa được.
>
> Về bảo mật đăng nhập: sai mật khẩu 5 lần thì khóa tạm 15 phút, có quên mật khẩu qua email với liên kết dùng một lần.

## B. Phòng Đào tạo chuẩn bị học kỳ (3 phút) — `acad.test`

> Giờ em đăng nhập vai trò **Phòng Đào tạo**.
>
> *(Tổng quan)* Trang tổng quan là số liệu thật lấy từ cơ sở dữ liệu: sinh viên theo trạng thái, sĩ số lớp, tiến độ nhập điểm.
>
> *(Năm học)* Ở đây khai báo năm học, học kỳ, các mốc như mở đăng ký, hạn hủy, hạn rút học phần, và ngày nghỉ lễ — ngày nghỉ lễ sẽ được dùng khi sinh buổi học.
>
> *(Học phần)* Danh mục học phần có số tín chỉ, **quan hệ tiên quyết, học trước, song hành**. Hệ thống chặn tạo vòng lặp tiên quyết, và cơ cấu điểm thành phần phải đủ 100%.
>
> *(Thêm sinh viên → Nhập học)* Em thêm một thí sinh mới… lưu, hồ sơ ở trạng thái **Chờ nhập học**. Giờ em bấm **Xác nhận nhập học**: chỉ một thao tác, hệ thống **cấp mã số sinh viên** theo quy tắc khóa + mã ngành + số thứ tự, cấp email trường, tạo tài khoản, gán chương trình đào tạo và xếp vào lớp hành chính. Tất cả nằm trong một giao dịch — bước nào lỗi thì hoàn tác hết.

## C. Chương trình khung → mở lớp → thông báo (3 phút) — `acad.test`

> Trước mỗi học kỳ, Phòng Đào tạo phải biết **mỗi khoa, mỗi khóa học những môn gì** để mở lớp. Nhóm làm chức năng **chương trình khung**.
>
> Em chọn: khoa Công nghệ thông tin, khóa 2026, học kỳ 2 năm 2026–2027 → lập khung.
>
> Bấm **Gợi ý từ chương trình đào tạo**: hệ thống tự tính học kỳ 2 năm nay là **học kỳ thứ 2 của khóa 2026**, rồi lấy đúng 9 học phần của học kỳ đó trong chương trình đào tạo các ngành thuộc khoa, kèm số sinh viên để đề xuất số lớp. Cán bộ vẫn sửa, thêm, bớt được.
>
> Bấm **Mở lớp**: hệ thống mở hàng loạt 9 lớp học phần. Mỗi lớp vẫn qua đủ kiểm tra như mở tay — học phần phải thuộc chương trình đào tạo đang áp dụng, có đề cương được duyệt, sĩ số hợp lệ. Bấm lại thì không mở trùng.
>
> Cuối cùng **Soạn và gửi thông báo**: nội dung soạn sẵn danh sách môn, mã lớp, ngày bắt đầu học kỳ, thời gian đăng ký; gửi đúng sinh viên **thuộc cả khoa đó và khóa đó**.

## D. Xếp lớp và thời khóa biểu (3 phút) — `acad.test`

> Lớp mới mở đang ở trạng thái **Dự kiến**. Em mở lớp Xác suất thống kê và **phân công giảng viên chính** — vượt định mức số tiết thì hệ thống cảnh báo.
>
> Sang **Xếp thời khóa biểu**: chọn thứ Bảy, tiết 1 đến 3, phòng A101, tuần 1 đến 15 → lưu → **Kiểm tra xung đột**: hệ thống kiểm tra trùng phòng, trùng giảng viên, phòng thiếu chỗ, sai loại phòng. Không có xung đột → **Sinh buổi học**: tạo 15 buổi cụ thể, tự bỏ ngày nghỉ lễ.
>
> Quay lại lớp, chuyển sang **Mở đăng ký**. Lớp chưa có giảng viên chính thì hệ thống không cho mở.
>
> Em cũng **công bố thời khóa biểu học kỳ 1** — có công bố thì giảng viên mới thấy lịch dạy; sau công bố, đổi lịch phải có lý do và báo trước.

## E. Sinh viên đăng ký học phần (3 phút) — `2601030001`

> Bây giờ em đổi sang **sinh viên** ở trình duyệt bên cạnh.
>
> Chuông thông báo có ngay thông báo mở lớp Phòng Đào tạo vừa gửi.
>
> Vào **Đăng ký học phần**: đợt đăng ký học kỳ 2 đang mở, có đồng hồ đếm ngược, khung tín chỉ tối thiểu – tối đa. Mỗi lớp ghi rõ lịch học, giảng viên, phòng, **ngày bắt đầu học**.
>
> Lớp không đăng ký được thì ghi lý do cụ thể và cách khắc phục — ví dụ lớp Kỹ thuật lập trình: **thiếu học phần tiên quyết** Nhập môn lập trình, vì em này đang học môn đó, chưa đạt. Các quy tắc khác: lớp hết chỗ, trùng lịch, vượt tín chỉ tối đa, và ngày học phải sau ngày đóng đăng ký.
>
> Em đăng ký lớp Xác suất thống kê → thành công. Sang **Kết quả đăng ký**: có phiếu đăng ký PDF để in. Sang **Thời khóa biểu** tuần đầu học kỳ 2: lớp vừa đăng ký **hiện ngay**, có ghi "lịch dự kiến". Nếu hủy đăng ký thì lịch mất theo.
>
> Một điểm kỹ thuật: khi nhiều sinh viên cùng tranh **chỗ cuối cùng**, hệ thống khóa dòng dữ liệu nên chỉ đúng một người đăng ký được — nhóm đã thử 12 tiến trình chạy song song.

## F. Giảng viên dạy học (3 phút) — `GV0001`

> Đổi sang **giảng viên**. Lịch giảng dạy hiện các buổi dạy trong tuần. Trang lớp học phần có danh sách 50 sinh viên, in được, xuất được.
>
> **Điểm danh**: chọn buổi vừa dạy, đánh một em vắng, lưu. Quá hạn sửa thì giảng viên không sửa thẳng được mà phải gửi **yêu cầu sửa** cho Phòng Đào tạo duyệt. Hệ thống tự tính tỷ lệ vắng theo quy chế, vượt ngưỡng thì **cảnh báo**, vượt nữa thì **cấm thi**, và gửi thông báo cho sinh viên.
>
> **Nhập điểm**: sổ điểm có các cột thành phần — chuyên cần, thực hành, thi cuối kỳ — nhập tay hoặc nhập từ Excel. Em **nộp điểm**. Nếu còn ô trống thì hệ thống không cho nộp và liệt kê mã số sinh viên còn thiếu. Nộp rồi thì sổ điểm khóa lại, chỉ xem.

## H. Duyệt và công bố điểm (2 phút) — `dean.test`, `acad.test`

> Điểm đi qua **nhiều cấp duyệt**. Em đăng nhập **trưởng khoa**: chỉ thấy lớp của khoa mình. Lớp vừa nộp đang "Chờ duyệt" → **Khoa duyệt**.
>
> Sang **Phòng Đào tạo** → **Xác nhận** → **Công bố**.
>
> Sinh viên trong lớp đăng nhập vào là thấy điểm thành phần, điểm hệ 10, điểm chữ, hệ 4, và nhận thông báo "đã có điểm". **Trước khi công bố thì sinh viên không thấy gì.** Sau công bố muốn sửa điểm phải gửi yêu cầu điều chỉnh có minh chứng, duyệt qua khoa và phòng; mọi phiên bản điểm được lưu lại.

## G. Sinh viên xem kết quả học tập (2 phút) — `2501010010`

> Đây là một sinh viên khóa 2025 đã học hai học kỳ. **Kết quả học tập**: điểm từng môn, **GPA học kỳ**, **GPA tích lũy 3,94 — Xuất sắc**. GPA tự tính lại mỗi khi điểm được công bố, học lại thì lấy điểm cao nhất theo quy chế.
>
> Ngoài ra sinh viên xem được **chuyên cần**, **tiến độ chương trình đào tạo**, và **tự cập nhật hồ sơ** — thông tin liên hệ thì sửa ngay; còn họ tên, ngày sinh, CCCD thì phải gửi yêu cầu kèm minh chứng để cán bộ duyệt.

## I. Thông báo (1 phút) — `acad.test`

> Ngoài các thông báo tự động theo sự kiện — mở lớp, công bố lịch, công bố điểm, cảnh báo vắng — Phòng Đào tạo gửi được thông báo thủ công: chọn đối tượng theo khoa, ngành, khóa, lớp, hay danh sách mã số sinh viên, **xem trước số người nhận** rồi gửi, hẹn giờ được. Người gửi chỉ chọn được đối tượng trong phạm vi quyền của mình.

## J. Nghiệm thu tự động (2 phút) — cửa sổ dòng lệnh

> Cuối cùng, để chứng minh các quy tắc nghiệp vụ chạy đúng, nhóm viết lệnh **nghiệm thu tự động** `php artisan uat:p1`. Lệnh chạy **23 kịch bản nghiệm thu** trong tài liệu BA ngay trên dữ liệu này rồi tự hoàn tác: khóa tài khoản, phạm vi dữ liệu, chặn vòng lặp tiên quyết, cấp mã số sinh viên, trùng phòng, trùng lịch, tính điểm khớp ví dụ trong BA — 9,0 / 7,0 / 6,5 ra 6,9 điểm C, 2,0 hệ 4 — tính GPA, khóa sửa điểm sau công bố. *(chạy lệnh)* Tất cả đạt.
>
> Ngoài ra dự án có **kiểm thử tự động** chạy trên GitHub mỗi lần có Pull Request, và quy trình làm việc nhóm qua Pull Request, không ai đẩy thẳng lên nhánh chính.

## Kết thúc (1 phút)

> Tóm lại, ở giai đoạn P1 nhóm đã làm xong vòng đời một học kỳ: **lập kế hoạch mở lớp → xếp lịch → đăng ký học phần → điểm danh → nhập, duyệt, công bố điểm → GPA**, với phân quyền theo vai trò và phạm vi dữ liệu, nhật ký kiểm toán, thông báo theo sự kiện.
>
> Các phần như học phí, lịch thi, khảo sát, học bổng thuộc giai đoạn P2–P4; hiện mới có bản thiết kế giao diện, nhóm sẽ làm tiếp.
>
> Em cảm ơn thầy/cô đã theo dõi, mời thầy/cô đặt câu hỏi.

---

## Chuẩn bị trả lời câu hỏi

| Thầy/cô có thể hỏi | Ý trả lời |
|---|---|
| Kiến trúc mã tổ chức thế nào? | Chia theo module nghiệp vụ trong `app/Modules` (Đăng ký, Điểm, Thời khóa biểu…); phần dùng chung (lớp nền, nhật ký, phân quyền) ở `app/Support`. Mỗi module có Model, Service chứa quy tắc nghiệp vụ, Controller, Policy. |
| Phân quyền làm sao? | Ma trận vai trò × module × thao tác (xem, thêm, sửa, xóa, duyệt, xuất) lấy từ BA; thêm phạm vi dữ liệu (toàn trường / khoa / lớp mình dạy / của mình). Kiểm tra ở máy chủ, không chỉ ẩn nút. |
| Chống hai người cùng đăng ký chỗ cuối? | Đăng ký chạy trong giao dịch, khóa dòng sinh viên rồi dòng lớp (`lockForUpdate`), kiểm tra điều kiện trên dữ liệu đã khóa rồi mới ghi; thử 12 tiến trình song song chỉ 1 thành công. |
| Điểm tính thế nào? | Tổng có trọng số các thành phần, làm tròn theo cấu hình, quy đổi điểm chữ / hệ 4 theo bộ quy chế của từng khóa; GPA chỉ tính điểm đã công bố. |
| Sửa điểm sau công bố? | Không sửa trực tiếp; giảng viên gửi yêu cầu kèm minh chứng → khoa → phòng duyệt; mỗi lần sửa tạo phiên bản mới, giữ lịch sử. |
| Dữ liệu demo ở đâu ra? | Lệnh `demo:seed` sinh 300 sinh viên, 2 học kỳ đã có điểm, 1 học kỳ đang học, dữ liệu giả cố định; không chạy được trên môi trường thật. |
| Kiểm thử ra sao? | Kiểm thử tự động bằng PHPUnit trên MySQL, chạy trên GitHub Actions mỗi Pull Request; lệnh `uat:p1` chạy 23 kịch bản nghiệm thu trong BA. |
| Nhóm phân công thế nào? | 3 bạn làm backend chia theo module, 1 bạn làm giao diện; mỗi việc là một issue trên GitHub, làm trên nhánh riêng, mở Pull Request để duyệt rồi mới gộp. |

## Nếu có sự cố khi demo

- **Bấm nút không phản hồi / trang báo lỗi**: tải lại trang (F5) rồi làm lại bước đó.
- **Xếp lịch báo trùng**: nói "đây chính là chức năng kiểm tra xung đột" rồi đổi sang thứ hoặc tiết khác.
- **Trưởng khoa thấy nút "Xác nhận"**: không bấm (chỉ Phòng Đào tạo xác nhận được — lỗi hiển thị đã biết).
- **Quên làm bước 0.5 (điểm thi trống)**: bấm "Nộp điểm" để cho thấy hệ thống chặn và liệt kê sinh viên thiếu điểm, rồi bỏ qua phần H, chuyển sang G.
