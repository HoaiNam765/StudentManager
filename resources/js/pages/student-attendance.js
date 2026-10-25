/**
 * Chuyên cần của sinh viên (FE #38, mock student/chuyen-can; FR-ATT-007): ngưỡng cảnh báo / cấm thi theo quy chế khóa,
 * thẻ thống kê, từng học phần (tỷ lệ vắng so với ngưỡng, số buổi) và chi tiết từng buổi (trạng thái bằng chữ và màu).
 */
import { api } from '../api.js';
import { dmy } from '../lookups.js';
import { el, emptyBlock, errorBlock, icon, loadingBlock, pageData } from '../ui.js';

const TONE = {
    present: 'bg-[#DCFCE7] text-[#15803D]',
    excused: 'bg-amber-50 text-amber-800',
    absent: 'bg-red-50 text-red-700',
    late: 'bg-violet-50 text-violet-800',
};

export default function studentAttendance(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const stat = (name) => root.querySelector(`[data-stat="${name}"]`);

    load();

    async function load() {
        region('sections').replaceChildren(loadingBlock(3));

        try {
            const data = await api.get(page.api.mine);
            const { warning, ban } = data.thresholds;
            root.querySelector('[data-field="rule"]').textContent = `Sinh viên vắng vượt quá ${ban}% tổng số buổi của một học phần sẽ không đủ điều kiện dự thi học phần đó. Cảnh báo tự động khi tỷ lệ vắng đạt từ ${warning}%. Buổi nghỉ do nhà trường không tính vào tổng số buổi.`;
            const list = data.sections ?? [];
            const level = (s) => s.summary?.level ?? 'normal';
            stat('total').textContent = String(list.length).padStart(2, '0');
            stat('good').textContent = String(list.filter((s) => level(s) === 'normal').length).padStart(2, '0');
            stat('good-note').textContent = `Vắng dưới ${warning}%`;
            stat('warning').textContent = String(list.filter((s) => level(s) === 'warning').length).padStart(2, '0');
            stat('banned').textContent = String(list.filter((s) => level(s) === 'banned').length).padStart(2, '0');
            region('sections').replaceChildren(...(list.length
                ? list.sort((a, b) => (b.summary?.absence_rate ?? 0) - (a.summary?.absence_rate ?? 0)).map((s) => card(s, ban))
                : [emptyBlock('Chưa có học phần đang học', 'Chuyên cần hiện khi bạn có lớp học phần đang học.', 'event_available')]));
        } catch (error) {
            region('sections').replaceChildren(errorBlock(error, load));
        }
    }

    function card(s, ban) {
        const sum = s.summary;
        const lvl = sum?.level ?? 'normal';
        const head = {
            banned: ['Không đủ điều kiện dự thi', 'border-red-300', 'bg-red-600 text-white'],
            warning: ['Cảnh báo: gần ngưỡng cấm thi', 'border-amber-300', 'bg-amber-500 text-white'],
            normal: ['An toàn', 'border-outline-variant/50', 'bg-emerald-600 text-white'],
        }[lvl];
        const rate = sum?.absence_rate ?? 0;
        const detail = el('div', { hidden: true, class: 'border-t border-outline-variant/40 pt-3' }, s.records.length
            ? el('div', { class: 'overflow-x-auto' }, el('table', { class: 'w-full text-sm min-w-[520px]' },
                el('thead', {}, el('tr', { class: 'text-left text-xs uppercase text-secondary' }, ['Buổi học / Ngày', 'Tiết học', 'Trạng thái', 'Ghi chú'].map((t) => el('th', { class: 'py-2 px-3 font-semibold' }, t)))),
                el('tbody', { class: 'divide-y divide-outline-variant/20' }, s.records.map((r) => el('tr', {},
                    el('td', { class: 'py-2 px-3' }, `Buổi ${r.week ?? '?'} • ${dmy(r.date)}`),
                    el('td', { class: 'py-2 px-3' }, r.periods ? `Tiết ${r.periods}` : '—'),
                    el('td', { class: 'py-2 px-3' }, el('span', { class: `rounded-full px-2.5 py-0.5 text-xs font-semibold ${TONE[r.status] ?? ''}` }, r.status_label, r.status === 'late' && r.minutes_late ? ` (${r.minutes_late} phút)` : '')),
                    el('td', { class: 'py-2 px-3 text-xs text-secondary' }, r.note ?? ''))))))
            : el('p', { class: 'text-sm text-secondary' }, 'Chưa có buổi nào được điểm danh.'));
        const toggle = el('button', { type: 'button', 'aria-expanded': 'false', class: 'inline-flex items-center gap-1 text-xs font-semibold text-primary', onclick: () => {
            detail.hidden = !detail.hidden;
            toggle.setAttribute('aria-expanded', detail.hidden ? 'false' : 'true');
        } }, 'Chi tiết buổi', icon('expand_more', 'text-[18px]'));

        return el('article', { class: `bg-surface-container-lowest rounded-xl border ${head[1]} shadow-xs p-4 sm:p-5 space-y-3` },
            el('div', { class: 'flex flex-col sm:flex-row sm:items-start justify-between gap-3' },
                el('div', { class: 'space-y-1' },
                    el('span', { class: `inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold ${head[2]}` }, head[0]),
                    el('p', { class: 'text-xs text-secondary font-mono' }, `Mã LHP: ${s.section?.code ?? ''}`),
                    el('h2', { class: 'font-bold' }, `${s.subject?.name ?? ''} (${s.subject?.credits ?? '?'} tín chỉ)`)),
                el('div', { class: 'text-right' },
                    el('p', { class: 'text-xs text-secondary' }, 'Tỷ lệ vắng / Ngưỡng'),
                    el('p', { class: `text-xl font-bold ${lvl === 'banned' ? 'text-red-700' : lvl === 'warning' ? 'text-amber-700' : 'text-emerald-700'}` }, `${rate.toFixed(1)}% / ${ban}%`),
                    el('p', { class: 'text-xs text-secondary' }, sum ? `Vắng ${sum.absences}/${sum.total_sessions} buổi (${sum.present_count} buổi có mặt)` : 'Chưa có dữ liệu'))),
            el('div', { class: 'h-2 rounded-full bg-surface-container overflow-hidden', role: 'progressbar', 'aria-valuenow': rate, 'aria-valuemin': 0, 'aria-valuemax': 100, 'aria-label': 'Tỷ lệ vắng' },
                el('div', { class: `h-full rounded-full ${lvl === 'banned' ? 'bg-red-600' : lvl === 'warning' ? 'bg-amber-500' : 'bg-emerald-600'}`, style: `width:${Math.min(100, (rate / Math.max(ban, 1)) * 100)}%` })),
            sum?.exempted ? el('p', { class: 'text-xs text-violet-800' }, `Được miễn cấm thi: ${sum.exemption_reason ?? ''}`) : null,
            toggle, detail);
    }
}
