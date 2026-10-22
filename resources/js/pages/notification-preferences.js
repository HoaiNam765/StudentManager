/**
 * Tùy chọn nhận thông báo (resources/views/account/notifications.blade.php).
 *
 * API: GET preferences (các loại thông báo đang bật, cờ bắt buộc, đang nhận hay không), PUT save {preferences: {mã: bool}}.
 * Loại bắt buộc hiện công tắc khóa ở trạng thái bật; máy chủ cũng từ chối tắt (BR-NOT-02).
 */
import { api } from '../api.js';
import { el, emptyBlock, errorBlock, loadingBlock, pageData, showAlert, toast } from '../ui.js';

export default function notificationPreferences(root) {
    const page = pageData();
    const list = root.querySelector('[data-region="list"]');
    const alertBox = root.querySelector('[data-region="alert"]');

    load();

    async function load() {
        list.replaceChildren(loadingBlock(4));

        try {
            render((await api.get(page.api.preferences)).data);
        } catch (error) {
            list.replaceChildren(errorBlock(error, () => load()));
        }
    }

    function render(items) {
        if (items.length === 0) {
            list.replaceChildren(emptyBlock('Chưa có loại thông báo nào', '', 'notifications_off'));

            return;
        }

        list.replaceChildren(...items.map((item) => {
            const toggle = el('input', { type: 'checkbox', class: 'w-5 h-5 accent-[var(--color-primary,#1d4ed8)]', checked: item.in_app, disabled: item.mandatory, 'aria-label': item.name });
            toggle.addEventListener('change', () => save(item, toggle));

            return el(
                'li',
                { class: 'p-4 sm:p-5 flex items-center gap-4' },
                el('div', { class: 'min-w-0 flex-1' },
                    el('p', { class: 'text-sm font-semibold text-on-surface' }, item.name),
                    el('p', { class: 'text-xs text-on-surface-variant mt-0.5' }, item.mandatory ? `${item.event_code} · Bắt buộc, không tắt được` : item.event_code)),
                toggle,
            );
        }));
    }

    async function save(item, toggle) {
        showAlert(alertBox, null);
        toggle.disabled = true;

        try {
            await api.put(page.api.save, { preferences: { [item.event_code]: toggle.checked } });
            toast(toggle.checked ? `Đã bật "${item.name}".` : `Đã tắt "${item.name}".`, 'success');
        } catch (error) {
            toggle.checked = !toggle.checked;
            showAlert(alertBox, error);
        } finally {
            toggle.disabled = item.mandatory;
        }
    }
}
