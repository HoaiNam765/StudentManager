/**
 * Lịch giảng dạy (FE #24, mock teacher/lich-giang-day): buổi dạy hôm nay và thời khóa biểu tuần của chính giảng viên
 * (API TTB, chỉ học kỳ đã công bố), lớp phụ trách học kỳ hiện tại (CLS), việc cần xử lý / hạn chót / thông báo (RPT).
 */
import { api } from '../api.js';
import { dmy, rows } from '../lookups.js';
import { el, emptyBlock, errorBlock, icon, loadingBlock, pageData } from '../ui.js';

const DAYS = ['Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ nhật'];

/** Ngày theo giờ Việt Nam dạng Y-m-d. */
function ymd(date) {
    return new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
}

function addDays(value, days) {
    const date = new Date(`${value}T00:00:00`);
    date.setDate(date.getDate() + days);

    return ymd(date);
}

/** Trạng thái theo giờ hiện tại của một buổi `HH:MM–HH:MM`. */
function liveStatus(session) {
    const [from, to] = String(session.time ?? '').split('–');
    const now = new Date().toTimeString().slice(0, 5);

    if (!['planned', 'makeup', 'taught'].includes(session.status)) {
        return ['off', session.status_label, 'bg-red-50 text-red-700'];
    }

    if (from && to && now >= from && now <= to) {
        return ['ongoing', 'Đang diễn ra', 'bg-emerald-100 text-emerald-800'];
    }

    return from && now < from ? ['upcoming', 'Sắp bắt đầu', 'bg-blue-100 text-blue-800'] : ['done', 'Đã kết thúc', 'bg-slate-100 text-slate-700'];
}

export default function teacherSchedule(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const today = ymd(new Date());
    const state = { date: today, sections: [] };
    // Lớp phụ trách tải một lần, dùng cho cả thẻ lớp và việc cần xử lý (lọc lớp còn được nhập điểm)
    const sectionsRequest = api.get(page.api.sections).then(rows);

    field('today').textContent = new Date().toLocaleDateString('vi-VN', { weekday: 'long', day: '2-digit', month: '2-digit', year: 'numeric' });
    root.querySelectorAll('[data-week]').forEach((button) => button.addEventListener('click', () => {
        const step = Number(button.dataset.week);
        state.date = step === 0 ? today : addDays(state.date, step * 7);
        loadWeek();
    }));

    loadDashboard();
    loadWeek();
    loadSections();

    async function loadDashboard() {
        region('tasks').replaceChildren(loadingBlock(2));
        region('notifications').replaceChildren(loadingBlock(2));
        region('today').replaceChildren(loadingBlock(2));

        try {
            const [data, sections] = await Promise.all([api.get(page.api.dashboard), sectionsRequest.catch(() => [])]);
            const editable = new Set(sections.filter((s) => s.grade_status?.editable).map((s) => s.id));
            renderToday(data.today_schedule ?? []);
            renderTasks((data.pending_grade_sections ?? []).filter((s) => !sections.length || editable.has(s.id)), data.deadlines ?? []);
            renderNotifications(data.notifications?.latest ?? []);
        } catch (error) {
            region('tasks').replaceChildren(errorBlock(error, loadDashboard));
            region('notifications').replaceChildren();
            region('today').replaceChildren(errorBlock(error, loadDashboard));
        }
    }

    function renderToday(sessions) {
        region('today').replaceChildren(sessions.length
            ? el('div', { class: 'space-y-3' }, sessions.map((s) => {
                const [key, label, tone] = liveStatus(s);

                return el('article', { class: `p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 ${key === 'ongoing' ? 'border-primary/30 bg-[#EEF2FD]/50' : 'border-outline-variant/60'}` },
                    el('div', { class: 'space-y-1' },
                        el('div', { class: 'flex flex-wrap items-center gap-2' },
                            el('span', { class: `px-2 py-0.5 rounded text-xs font-bold ${key === 'ongoing' ? 'bg-primary text-white' : 'bg-secondary-container text-on-secondary-fixed'}` }, `${s.time} (Tiết ${s.period_from} - ${s.period_to})`),
                            el('span', { class: `px-2 py-0.5 rounded-full text-[11px] font-semibold ${tone}` }, label)),
                        el('h3', { class: 'text-sm font-bold text-on-surface' }, s.subject),
                        el('p', { class: 'text-xs text-on-surface-variant' }, `Lớp học: ${s.section} · Phòng: ${s.room ?? '—'}${s.note ? ` · ${s.note}` : ''}`)),
                    el('a', { href: page.links.sections, class: 'self-start sm:self-center px-3 py-1.5 rounded-lg border border-primary/20 bg-white text-primary text-xs font-semibold' }, 'Xem danh sách'));
            }))
            : emptyBlock('Hôm nay không có lịch dạy', 'Lịch dạy hiện khi thời khóa biểu học kỳ đã công bố.', 'event_available'));
    }

    function renderTasks(sections, deadlines) {
        const items = [
            ...sections.map((s) => ({ title: `Nhập / nộp điểm lớp ${s.code}`, detail: `${s.subject ?? ''} · ${s.status_label ?? ''}`, href: `${page.links.gradebook}?course_section_id=${s.id}`, tone: 'bg-red-50 text-red-700', badge: 'Cần xử lý' })),
            ...deadlines.slice(0, 5).map((d) => ({ title: d.label, detail: `Hạn: ${dmy(d.date)}${d.time ? ` ${d.time}` : ''}${d.term ? ` · ${d.term}` : ''}`, href: null, tone: d.date === today ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-800', badge: d.date === today ? 'Hôm nay' : 'Sắp tới' })),
        ];
        field('task-count').textContent = `${items.length} việc`;
        region('tasks').replaceChildren(items.length
            ? el('ul', { class: 'space-y-2' }, items.map((t) => el('li', { class: 'rounded-lg border border-outline-variant/60 p-3 flex items-start justify-between gap-2' },
                el('div', { class: 'min-w-0' }, t.href ? el('a', { href: t.href, class: 'text-sm font-semibold text-on-surface hover:text-primary hover:underline' }, t.title) : el('p', { class: 'text-sm font-semibold' }, t.title), el('p', { class: 'text-xs text-secondary' }, t.detail)),
                el('span', { class: `shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold ${t.tone}` }, t.badge))))
            : emptyBlock('Không có việc cần xử lý', '', 'task_alt'));
    }

    function renderNotifications(list) {
        region('notifications').replaceChildren(list.length
            ? el('ul', { class: 'space-y-2' }, list.map((n) => el('li', { class: 'rounded-lg border border-outline-variant/60 p-3' },
                el('p', { class: 'text-[11px] text-secondary' }, n.sent_at ? new Date(n.sent_at).toLocaleDateString('vi-VN') : ''),
                n.link ? el('a', { href: n.link, class: 'text-sm font-semibold hover:underline' }, n.title) : el('p', { class: 'text-sm font-semibold' }, n.title))))
            : emptyBlock('Không có thông báo chưa đọc', '', 'notifications'));
    }

    async function loadWeek() {
        region('week').replaceChildren(loadingBlock(2));

        try {
            const data = await api.get(page.api.timetable, { view: 'week', date: state.date });
            field('range').textContent = `${dmy(data.from)} – ${dmy(data.to)}`;

            if (data.message) {
                region('week').replaceChildren(emptyBlock(data.message, 'Lịch hiện khi Phòng Đào tạo công bố thời khóa biểu.', 'event_busy'));

                return;
            }

            const byDay = Object.fromEntries(DAYS.map((_, i) => [i + 1, []]));
            (data.sessions ?? []).forEach((s) => byDay[s.day_of_week]?.push(s));
            region('week').replaceChildren(el('div', { class: 'grid grid-cols-7 gap-2 min-w-[840px]' }, DAYS.map((day, i) => {
                const date = addDays(data.from, i);

                return el('div', { class: `rounded-lg border p-2 space-y-2 ${date === today ? 'border-primary bg-[#EEF2FD]/40' : 'border-outline-variant/50'}` },
                    el('p', { class: 'text-xs font-bold text-center' }, day, el('span', { class: 'block font-normal text-secondary' }, dmy(date).slice(0, 5))),
                    byDay[i + 1].length
                        ? byDay[i + 1].sort((a, b) => a.period_from - b.period_from).map((s) => el('div', { class: `rounded-md p-2 text-[11px] ${['planned', 'makeup', 'taught'].includes(s.status) ? 'bg-primary/10 text-on-surface' : 'bg-red-50 text-red-800 line-through'}` },
                            el('p', { class: 'font-bold' }, `Tiết ${s.period_from}-${s.period_to}`),
                            el('p', { class: 'font-semibold leading-tight' }, s.subject),
                            el('p', { class: 'text-secondary' }, `${s.section} · ${s.room ?? '—'}`),
                            s.status === 'makeup' ? el('p', { class: 'text-violet-700 font-semibold' }, 'Dạy bù') : null))
                        : el('p', { class: 'text-[11px] text-center text-outline py-2' }, '—'));
            })));
        } catch (error) {
            region('week').replaceChildren(errorBlock(error, loadWeek));
        }
    }

    async function loadSections() {
        region('sections').replaceChildren(loadingBlock(2));

        try {
            const list = await sectionsRequest;
            const termIds = [...new Set(list.map((s) => s.term?.id))];
            // Học kỳ hiện tại = học kỳ có lớp đang học / đang đăng ký, nếu không có thì học kỳ mới nhất
            const current = list.find((s) => ['in_progress', 'registration_closed', 'registration_open'].includes(s.status))?.term?.id ?? termIds.at(-1);
            const shown = list.filter((s) => s.term?.id === current);
            field('term').textContent = shown[0] ? `(${shown[0].term?.name} · ${shown[0].academic_year?.name ?? ''})` : '';
            field('section-count').textContent = `${shown.length} lớp`;
            region('sections').replaceChildren(...(shown.length ? shown.map(card) : [emptyBlock('Chưa được phân công lớp', '', 'school')]));
        } catch (error) {
            region('sections').replaceChildren(errorBlock(error, loadSections));
        }
    }

    function card(s) {
        const schedule = (s.schedule ?? []).map((r) => `${r.day_label}, tiết ${r.period_from}-${r.period_to}${r.room ? ` · ${r.room}` : ''}`).join('; ');

        return el('article', { class: 'rounded-xl border border-outline-variant/60 p-4 space-y-2' },
            el('p', { class: 'text-[11px] font-semibold text-primary' }, `${s.subject?.code ?? ''} • ${s.subject?.credits ?? '?'} tín chỉ`),
            el('h3', { class: 'text-sm font-bold' }, s.subject?.name ?? s.code),
            el('p', { class: 'text-xs text-secondary font-mono' }, s.code),
            el('p', { class: 'text-xs text-on-surface-variant flex items-center gap-1' }, icon('groups', 'text-[16px]'), `${s.students_count} / ${s.max_size} sinh viên`),
            schedule ? el('p', { class: 'text-xs text-on-surface-variant flex items-center gap-1' }, icon('schedule', 'text-[16px]'), schedule) : null,
            s.grade_status ? el('p', { class: `text-[11px] font-semibold ${s.grade_status.editable ? 'text-amber-700' : 'text-emerald-700'}` }, `Điểm: ${s.grade_status.label}`) : null,
            el('div', { class: 'flex gap-3 pt-1' },
                el('a', { href: `${page.links.sections}#lop-${s.id}`, class: 'text-xs font-semibold text-primary hover:underline' }, 'Xem danh sách'),
                el('a', { href: `${page.links.gradebook}?course_section_id=${s.id}`, class: 'text-xs font-semibold text-primary hover:underline' }, 'Quản lý điểm')));
    }
}
