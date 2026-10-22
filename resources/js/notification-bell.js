// Chuông thông báo trên thanh trên cùng của layouts/portal (FR-NOT-001): hỏi số chưa đọc ngay khi tải trang rồi định kỳ
// (data-poll giây), dừng khi tab bị ẩn. Trang trung tâm thông báo phát sự kiện "notifications:changed" để cập nhật ngay.
import { api } from './api.js';

export function initNotificationBell() {
    const bell = document.querySelector('[data-notification-bell]');

    if (!bell) {
        return;
    }

    const badge = bell.querySelector('[data-bell-count]');
    const poll = Math.max(15, Number(bell.dataset.poll) || 60) * 1000;

    const show = (count) => {
        badge.hidden = !count;
        badge.textContent = count > 99 ? '99+' : String(count);
        bell.setAttribute('aria-label', count ? `Thông báo, ${count} chưa đọc` : 'Thông báo');
    };

    const refresh = async () => {
        if (document.hidden) {
            return;
        }

        try {
            show((await api.get(bell.dataset.countUrl)).unread ?? 0);
        } catch {
            // Lỗi mạng tạm thời: giữ số cũ, lần sau hỏi lại
        }
    };

    window.addEventListener('notifications:changed', (event) => show(event.detail?.unread ?? 0));
    document.addEventListener('visibilitychange', refresh);
    window.setInterval(refresh, poll);
    refresh();
}
