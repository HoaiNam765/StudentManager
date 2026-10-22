import { initNotificationBell } from './notification-bell.js';

// Mỗi trang gọi API khai báo <div data-page="ten-trang"> và có tệp resources/js/pages/ten-trang.js (xuất mặc định
// một hàm nhận phần tử gốc). Vite tách mỗi trang thành một tệp riêng, chỉ tải khi trang cần.
const pages = import.meta.glob('./pages/*.js');

document.querySelectorAll('[data-page]').forEach((root) => {
    const load = pages[`./pages/${root.dataset.page}.js`];

    if (!load) {
        console.error(`Không có mã cho trang "${root.dataset.page}".`);

        return;
    }

    load().then((module) => module.default(root));
});

initNotificationBell();
