{{--
    Phông biểu tượng Material Symbols. Nạp vào lớp `base` để các lớp tiện ích của Tailwind (text-[18px]...)
    ghi đè được cỡ chữ 24px mặc định trong CSS của Google (CSS không thuộc lớp nào luôn thắng lớp có tên).
    Đặt trước @vite. Không đưa vào resources/css/app.css: máy chủ dev của Vite trả CSS rỗng khi có @import url().
--}}
<style>
    @layer theme, base, components, utilities;
    @import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap') layer(base);
</style>
