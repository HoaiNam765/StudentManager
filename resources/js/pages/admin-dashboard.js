/**
 * Dashboard quản trị theo vai trò (resources/views/admin/dashboard.blade.php).
 *
 * API: GET data → {term, scope, refresh_seconds, widgets: [{code, title, definition, data: {total, items: [{label, value, min?, max?}]}}]}.
 * Tự làm mới theo refresh_seconds (mặc định 5 phút, BR-RPT-03), bỏ qua khi tab bị ẩn.
 */
import { api } from '../api.js';
import { el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert } from '../ui.js';

const ICONS = {
    students_by_status: 'groups',
    new_admissions: 'person_add',
    section_fill: 'event_seat',
    grading_progress: 'grading',
    admin_class_sizes: 'school',
    pending_grade_changes: 'rule',
};

export default function adminDashboard(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    let timer;

    root.querySelector('[data-action="reload"]').addEventListener('click', () => load());
    load();

    async function load() {
        window.clearTimeout(timer);

        if (!region('widgets').children.length) {
            region('widgets').replaceChildren(loadingBlock(4));
        }

        try {
            const data = await api.get(page.api.data);
            render(data);
            timer = window.setTimeout(function tick() {
                if (document.hidden) {
                    timer = window.setTimeout(tick, 30_000);
                } else {
                    load();
                }
            }, data.refresh_seconds * 1000);
        } catch (error) {
            region('widgets').replaceChildren(errorBlock(error, () => load()));
        }
    }

    function render(data) {
        const time = new Date(data.generated_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        root.querySelector('[data-field="subtitle"]').textContent = `${data.term ? `${data.term.name} · ` : ''}${data.scope} · Cập nhật lúc ${time}, tự làm mới mỗi ${Math.round(data.refresh_seconds / 60)} phút`;
        showAlert(region('alert'), null);

        region('widgets').replaceChildren(...(data.widgets.length === 0
            ? [emptyBlock('Vai trò của bạn chưa có widget nào', 'Cấu hình ở studentmanager.dashboard.widgets.', 'dashboard')]
            : data.widgets.map(widget)));
    }

    function widget(w) {
        const definition = el('p', { class: 'text-[11px] text-outline', hidden: true }, w.definition);

        return el(
            'section',
            { class: 'bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm' },
            el('div', { class: 'px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-2' },
                el('h2', { class: 'text-headline-sm font-bold text-on-surface flex items-center gap-2' }, icon(ICONS[w.code] ?? 'monitoring', 'text-[20px] text-primary'), w.title),
                el('button', { type: 'button', class: 'text-outline hover:text-primary', title: 'Định nghĩa chỉ số', 'aria-label': `Định nghĩa: ${w.title}`, onclick: () => { definition.hidden = !definition.hidden; } }, icon('info', 'text-[18px]'))),
            el('div', { class: 'p-5 space-y-3' }, definition, summary(w), bars(w)),
        );
    }

    function summary(w) {
        const d = w.data;
        const big = (value, label) => el('div', {}, el('p', { class: 'text-headline-lg font-bold text-on-surface' }, value.toLocaleString('vi-VN')), el('p', { class: 'text-[11px] text-secondary uppercase tracking-wider' }, label));
        // Hàm theo từng widget: chỉ đọc trường của widget đó
        const parts = ({
            students_by_status: () => [big(d.total, 'Tổng hồ sơ')],
            new_admissions: () => [big(d.total, `Từ ${d.since}`)],
            section_fill: () => [big(d.total, 'Lớp học phần'), big(d.under_min, 'Dưới sĩ số tối thiểu'), big(d.full, 'Đã đầy')],
            grading_progress: () => [big(d.total, 'Lớp cần điểm'), big(d.published, 'Đã công bố / khóa')],
            admin_class_sizes: () => [big(d.total, 'Lớp hành chính'), big(d.students, 'Sinh viên')],
            pending_grade_changes: () => [big(d.total, 'Đang chờ')],
        }[w.code] ?? (() => []))();

        if (w.code === 'pending_grade_changes' && page.links?.gradeChanges) {
            parts.push(el('a', { href: page.links.gradeChanges, class: 'self-center text-label-md font-semibold text-primary hover:underline' }, 'Xem và duyệt'));
        }

        return el('div', { class: 'flex flex-wrap gap-8' }, parts);
    }

    function bars(w) {
        const items = w.data.items ?? [];

        if (items.length === 0) {
            return el('p', { class: 'text-sm text-outline' }, 'Chưa có dữ liệu.');
        }

        const max = Math.max(1, ...items.map((i) => i.max ?? i.value));

        return el('ul', { class: 'space-y-2' }, items.map((item) => {
            const capacity = item.max ?? null;
            const width = `${Math.min(100, Math.round(((item.value) / max) * 100))}%`;
            const warn = item.min !== undefined && item.value < item.min;

            return el(
                'li',
                { class: 'text-xs' },
                el('div', { class: 'flex justify-between gap-2 mb-0.5' },
                    el('span', { class: 'truncate text-on-surface' }, item.label),
                    el('span', { class: `shrink-0 font-semibold ${warn ? 'text-red-800' : 'text-on-surface'}` }, capacity !== null ? `${item.value}/${capacity}${item.min !== undefined ? ` (tối thiểu ${item.min})` : ''}` : item.value.toLocaleString('vi-VN'))),
                el('div', { class: 'h-2 rounded-full bg-surface-container-low overflow-hidden' },
                    el('div', { class: `h-full rounded-full ${warn ? 'bg-red-600' : 'bg-primary'}`, style: `width: ${width}` })),
            );
        }));
    }
}
