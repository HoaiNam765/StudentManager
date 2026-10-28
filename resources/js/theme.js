/**
 * Nút chuyển chế độ ngày / đêm (`[data-theme-toggle]`, resources/views/partials/theme-toggle.blade.php). Chế độ ban
 * đầu do partials/theme-init đặt trong <head>; bấm nút thì lưu lựa chọn, chưa chọn thì đi theo cài đặt của máy.
 */
const KEY = 'sm-theme';
const media = window.matchMedia?.('(prefers-color-scheme: dark)');

function stored() {
    try {
        const value = localStorage.getItem(KEY);

        return value === 'light' || value === 'dark' ? value : null;
    } catch {
        return null;
    }
}

function apply(theme) {
    document.documentElement.dataset.theme = theme;
    const next = theme === 'dark' ? 'sáng' : 'tối';
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-label', `Chuyển sang giao diện ${next}`);
        button.title = `Chuyển sang giao diện ${next}`;
        const icon = button.querySelector('[data-theme-icon]');
        if (icon) icon.textContent = theme === 'dark' ? 'light_mode' : 'dark_mode';
    });
}

export function initThemeToggle() {
    apply(document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            try {
                localStorage.setItem(KEY, theme);
            } catch {
                // Trình duyệt chặn lưu trữ: vẫn đổi cho trang hiện tại
            }
            apply(theme);
        });
    });

    media?.addEventListener?.('change', (event) => {
        if (!stored()) apply(event.matches ? 'dark' : 'light');
    });
}
