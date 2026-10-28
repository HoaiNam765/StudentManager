{{--
    Chế độ ngày / đêm: đặt <html data-theme> trước khi trang hiện ra (không nháy trắng). Lựa chọn lưu ở localStorage
    `sm-theme` (nút ở header, resources/js/theme.js); chưa chọn thì theo cài đặt sáng / tối của máy. Đặt trong <head>.
--}}
<script>
    (function () {
        var theme = null;
        try { theme = localStorage.getItem('sm-theme'); } catch (e) {}
        if (theme !== 'light' && theme !== 'dark') {
            theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.dataset.theme = theme;
    })();
</script>
