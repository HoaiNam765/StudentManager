{{--
    Menu bên dạng ngăn kéo cho màn hình nhỏ (dưới md). Cách dùng ở trang có menu bên:
    - thẻ <aside id="app-sidebar"> có lớp `-translate-x-full md:translate-x-0 transition-transform`
    - nút mở menu trên thanh trên cùng có thuộc tính data-sidebar-toggle
    - nội dung chính dùng `md:ml-64`
    - @include('partials.sidebar-toggle') ở cuối trang
--}}
<div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-black/40 md:hidden" aria-hidden="true"></div>

<script>
    (function () {
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const toggles = document.querySelectorAll('[data-sidebar-toggle]');
        if (!sidebar || !backdrop) return;

        function setOpen(open) {
            sidebar.classList.toggle('-translate-x-full', !open);
            backdrop.classList.toggle('hidden', !open);
            toggles.forEach(function (button) { button.setAttribute('aria-expanded', open ? 'true' : 'false'); });
        }

        toggles.forEach(function (button) {
            button.addEventListener('click', function () { setOpen(sidebar.classList.contains('-translate-x-full')); });
        });
        backdrop.addEventListener('click', function () { setOpen(false); });
        document.addEventListener('keydown', function (event) { if (event.key === 'Escape') setOpen(false); });
    })();
</script>
