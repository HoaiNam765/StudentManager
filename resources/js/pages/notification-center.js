/**
 * Trung tâm thông báo (resources/views/notification/center.blade.php).
 *
 * API: GET list?status=&category=&q=&archived=&page= (chỉ thông báo của người đang đăng nhập), POST read (đánh dấu đã đọc,
 * trả liên kết để mở), POST readAll. Sau mỗi thay đổi phát "notifications:changed" để chuông cập nhật số chưa đọc.
 */
import { api } from '../api.js';
import { el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const CATEGORY_ICON = {
    academic: 'school',
    enrollment: 'how_to_reg',
    timetable: 'calendar_month',
    grade: 'grading',
    account: 'shield_person',
    announcement: 'campaign',
    general: 'notifications',
};

export default function notificationCenter(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const input = (name) => root.querySelector(`[data-input="${name}"]`);
    const state = { page: 1, categoriesLoaded: false };
    let searchTimer;

    input('status').addEventListener('change', () => reload());
    input('category').addEventListener('change', () => reload());
    input('q').addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => reload(), 350);
    });
    root.querySelector('[data-action="read-all"]').addEventListener('click', (event) => readAll(event.currentTarget));

    load();

    function reload() {
        state.page = 1;
        load();
    }

    async function load() {
        region('list').replaceChildren(loadingBlock(4));
        showAlert(region('alert'), null);
        const status = input('status').value;

        try {
            const result = await api.get(page.api.list, {
                status: status === 'unread' ? 'unread' : 'all',
                archived: status === 'archived' ? 1 : 0,
                category: input('category').value || undefined,
                q: input('q').value.trim() || undefined,
                page: state.page,
            });
            render(result);
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, () => load()));
        }
    }

    function render(result) {
        changed(result.unread);
        root.querySelector('[data-field="subtitle"]').textContent = result.unread
            ? `Bạn có ${result.unread} thông báo chưa đọc.`
            : 'Bạn đã đọc hết thông báo.';

        if (!state.categoriesLoaded) {
            input('category').append(...Object.entries(result.categories ?? {}).map(([value, label]) => el('option', { value }, label)));
            state.categoriesLoaded = true;
        }

        region('list').replaceChildren(...(result.data.length === 0
            ? [emptyBlock('Không có thông báo', 'Thông báo mới sẽ hiện ở đây.', 'notifications_off')]
            : result.data.map(item)));
        renderPager(result.meta);
    }

    function item(n) {
        const unread = !n.read_at;
        const time = n.sent_at ? new Date(n.sent_at).toLocaleString('vi-VN') : '';
        const tags = [
            n.pinned ? el('span', { class: 'inline-flex items-center gap-0.5 text-[11px] font-semibold text-primary' }, icon('push_pin', 'text-[14px]'), 'Ghim') : null,
            n.priority === 'urgent' ? el('span', { class: 'rounded-full bg-red-50 border border-red-300 px-2 text-[11px] font-semibold text-red-800' }, 'Khẩn') : null,
            n.mandatory ? el('span', { class: 'rounded-full bg-amber-50 border border-amber-300 px-2 text-[11px] font-semibold text-amber-900' }, 'Bắt buộc') : null,
        ];

        return el(
            'article',
            { class: `flex gap-3 p-4 ${unread ? 'bg-[#EEF2FD]/60' : ''}` },
            el('div', { class: 'w-9 h-9 rounded-full bg-secondary-container text-primary flex items-center justify-center shrink-0' }, icon(CATEGORY_ICON[n.category] ?? 'notifications', 'text-[20px]')),
            el(
                'div',
                { class: 'min-w-0 flex-1 space-y-1' },
                el('div', { class: 'flex flex-wrap items-center gap-2' },
                    el('h2', { class: `text-sm ${unread ? 'font-bold' : 'font-semibold'} text-on-surface` }, n.title),
                    ...tags),
                el('p', { class: 'text-sm text-on-surface-variant whitespace-pre-line' }, n.body),
                el('p', { class: 'text-[11px] text-outline' }, [n.category_label, n.sender ? `Người gửi: ${n.sender}` : null, time].filter(Boolean).join(' · ')),
                el('div', { class: 'flex flex-wrap gap-2 pt-1' },
                    n.link ? el('button', { type: 'button', class: 'px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-white text-label-md font-semibold inline-flex items-center gap-1', onclick: (e) => open(e.currentTarget, n) }, icon('open_in_new', 'text-[16px]'), 'Xem chi tiết') : null,
                    unread ? el('button', { type: 'button', class: 'px-3 py-1.5 rounded-lg border border-outline-variant text-label-md font-semibold hover:bg-surface-container-low', onclick: (e) => markRead(e.currentTarget, n) }, 'Đánh dấu đã đọc') : null),
            ),
            unread ? el('span', { class: 'w-2.5 h-2.5 rounded-full bg-primary mt-1.5 shrink-0', title: 'Chưa đọc' }) : null,
        );
    }

    function renderPager(meta) {
        const pager = region('pager');

        if (!meta || meta.last_page <= 1) {
            pager.hidden = true;

            return;
        }

        const button = (label, target, disabled) => el('button', { type: 'button', class: 'px-3 py-1.5 rounded-lg border border-outline-variant font-semibold hover:bg-surface-container-low disabled:opacity-40', disabled, onclick: () => { state.page = target; load(); } }, label);
        pager.replaceChildren(
            el('span', {}, `Trang ${meta.current_page}/${meta.last_page} · ${meta.total} thông báo`),
            el('div', { class: 'flex gap-2' }, button('Trước', meta.current_page - 1, meta.current_page <= 1), button('Sau', meta.current_page + 1, meta.current_page >= meta.last_page)),
        );
        pager.hidden = false;
    }

    async function open(button, n) {
        try {
            await withBusy(button, () => api.post(url(page.api.read, n.id)), 'Đang mở…');
        } catch {
            // Đánh dấu đọc lỗi thì vẫn mở trang liên quan
        }

        // Chỉ mở đường dẫn trong hệ thống (máy chủ cũng chỉ lưu đường dẫn tương đối)
        if (n.link.startsWith('/') && !n.link.startsWith('//')) {
            window.location.assign(n.link);
        }
    }

    async function markRead(button, n) {
        try {
            await withBusy(button, () => api.post(url(page.api.read, n.id)));
            load();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function readAll(button) {
        try {
            const result = await withBusy(button, () => api.post(page.api.readAll));
            toast(result.marked ? `Đã đánh dấu ${result.marked} thông báo là đã đọc.` : 'Không còn thông báo chưa đọc.', 'success');
            load();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    function changed(unread) {
        window.dispatchEvent(new CustomEvent('notifications:changed', { detail: { unread } }));
    }

    function url(template, id) {
        return template.replace('__ID__', encodeURIComponent(id));
    }
}
