/**
 * Điểm danh (FE #42, mock teacher/diem-danh): chọn lớp / buổi, mỗi sinh viên chọn một chạm (Có mặt, Có phép, Vắng,
 * Đi muộn), "Tất cả có mặt" rồi chỉnh ngoại lệ, lưu (API ATT, chỉ giảng viên được phân công). Quá hạn sửa hoặc sổ đã khóa
 * thì gửi đề nghị sửa từng bản ghi cho Phòng Đào tạo. Sổ đầu bài, tổng hợp chuyên cần lớp, chuyển điểm chuyên cần.
 */
import { api } from '../api.js';
import { dmy, formDialog, rows, tpl } from '../lookups.js';
import { el, emptyBlock, errorBlock, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const BUTTONS = [
    ['present', 'Có mặt', 'bg-emerald-600 text-white border-emerald-600'],
    ['excused', 'Có phép', 'bg-amber-500 text-white border-amber-500'],
    ['absent', 'Vắng', 'bg-red-600 text-white border-red-600'],
    ['late', 'Đi muộn', 'bg-violet-600 text-white border-violet-600'],
];

export default function teacherAttendance(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const sectionSelect = root.querySelector('[data-input="section"]');
    const sessionSelect = root.querySelector('[data-input="session"]');
    const state = { sections: [], sessions: [], sheet: null, marks: new Map(), tab: 'sheet' };
    // Từ trang Tổng quan (?section=&session=): mở đúng lớp và buổi, chỉ dùng cho lần tải đầu
    const params = new URLSearchParams(window.location.search);
    const preset = { section: params.get('section'), session: params.get('session') };

    root.querySelectorAll('[data-tab]').forEach((tab) => tab.addEventListener('click', () => showTab(tab.dataset.tab)));
    sectionSelect.addEventListener('change', loadSessions);
    sessionSelect.addEventListener('change', loadSheet);
    root.querySelector('[data-action="all-present"]').addEventListener('click', allPresent);
    root.querySelector('[data-action="save"]').addEventListener('click', (e) => save(e.currentTarget));
    root.querySelector('[data-form="log"]').addEventListener('submit', saveLog);
    root.querySelector('[data-action="push-grades"]')?.addEventListener('click', (e) => pushGrades(e.currentTarget));

    showTab('sheet');
    loadSections();

    function showTab(name) {
        state.tab = name;
        root.querySelectorAll('[data-tab]').forEach((tab) => {
            tab.className = `rounded-lg px-4 py-2 text-sm font-semibold ${tab.dataset.tab === name ? 'bg-primary text-white' : 'border border-outline-variant bg-white hover:bg-surface-container-low'}`;
        });
        root.querySelectorAll('[data-panel]').forEach((panel) => { panel.hidden = panel.dataset.panel !== name; });

        if (name === 'summary') {
            loadSummary();
        }
    }

    async function loadSections() {
        region('rows').replaceChildren(loadingBlock(4));

        try {
            state.sections = rows(await api.get(page.api.sections)).filter((s) => ['in_progress', 'finished'].includes(s.status));

            if (!state.sections.length) {
                region('rows').replaceChildren(emptyBlock('Không có lớp đang học', 'Lớp ở trạng thái "Đang học" mới điểm danh được.', 'school'));

                return;
            }

            sectionSelect.replaceChildren(...state.sections.map((s) => el('option', { value: s.id }, `${s.code} - ${s.subject?.name ?? ''}`)));
            if (preset.section && state.sections.some((s) => String(s.id) === preset.section)) sectionSelect.value = preset.section;
            preset.section = null;
            await loadSessions();
        } catch (error) {
            region('rows').replaceChildren(errorBlock(error, loadSections));
        }
    }

    async function loadSessions() {
        region('rows').replaceChildren(loadingBlock(4));

        try {
            const data = await api.get(tpl(page.api.sessions, sectionSelect.value));
            state.sessions = data.sessions ?? [];
            const today = new Date().toISOString().slice(0, 10);
            sessionSelect.replaceChildren(...state.sessions.map((s, i) => el('option', { value: s.id },
                `Buổi ${s.week ?? i + 1} • ${s.date === today ? 'Hôm nay ' : ''}${dmy(s.date)} • Tiết ${s.period_from}-${s.period_to}${s.open ? (s.within_edit ? (s.recorded ? ' (đã điểm danh)' : ' - Đang mở') : ' (quá hạn sửa)') : ` (${s.status === 'planned' ? 'chưa diễn ra' : s.status_label})`}`)));
            // Mặc định: buổi gần nhất đã mở
            const latest = [...state.sessions].reverse().find((s) => s.open);
            const wanted = state.sessions.find((s) => String(s.id) === preset.session);
            sessionSelect.value = String(wanted?.id ?? latest?.id ?? state.sessions[0]?.id ?? '');
            preset.session = null;
            await loadSheet();
        } catch (error) {
            region('rows').replaceChildren(errorBlock(error, loadSessions));
        }
    }

    async function loadSheet() {
        if (!sessionSelect.value) {
            region('rows').replaceChildren(emptyBlock('Lớp chưa có buổi học', 'Buổi học được sinh từ thời khóa biểu.', 'event_busy'));

            return;
        }

        region('rows').replaceChildren(loadingBlock(4));

        try {
            state.sheet = await api.get(tpl(page.api.sheet, sessionSelect.value));
            state.marks = new Map(state.sheet.rows.map((r) => [r.enrollment_id, { status: r.record?.status ?? null, minutes_late: r.record?.minutes_late ?? null, note: r.record?.note ?? '' }]));
            renderSheet();
            renderLog();
        } catch (error) {
            region('rows').replaceChildren(errorBlock(error, loadSheet));
        }
    }

    function renderSheet() {
        const s = state.sheet;
        const can = s.can;
        field('window').textContent = !s.window.open ? s.window.reason
            : s.locked ? 'Sổ điểm danh lớp này đã khóa: gửi đề nghị sửa cho Phòng Đào tạo.'
                : !s.window.within_edit ? `Đã quá ${page.editDays} ngày kể từ buổi học: gửi đề nghị sửa cho từng sinh viên để Phòng Đào tạo duyệt.`
                    : `Buổi ${dmy(s.session.date)}, tiết ${s.session.period_from}-${s.session.period_to}. Chọn trạng thái rồi bấm "Lưu điểm danh".`;
        root.querySelector('[data-action="all-present"]').disabled = !can.record;
        root.querySelector('[data-action="save"]').disabled = !can.record;
        renderCounts();
        region('rows').replaceChildren(...(s.rows.length ? s.rows.map((r, i) => row(r, i, can)) : [emptyBlock('Lớp chưa có sinh viên', '', 'group_off')]));
    }

    function renderCounts() {
        const counts = { present: 0, excused: 0, absent: 0, late: 0, none: 0 };
        state.marks.forEach((m) => { counts[m.status ?? 'none']++; });
        const chip = (label, n, tone) => el('span', { class: `rounded-full px-2.5 py-1 ${tone}` }, `${label}: ${n}`);
        region('counts').replaceChildren(
            chip('Có mặt', counts.present, 'bg-emerald-50 text-emerald-800'),
            chip('Có phép', counts.excused, 'bg-amber-50 text-amber-800'),
            chip('Vắng', counts.absent, 'bg-red-50 text-red-700'),
            chip('Đi muộn', counts.late, 'bg-violet-50 text-violet-800'),
            el('span', { class: 'rounded-full px-2.5 py-1 bg-surface-container text-secondary' }, `Chưa chọn: ${counts.none} / ${state.marks.size} SV`));
    }

    function row(r, index, can) {
        const mark = state.marks.get(r.enrollment_id);
        const buttons = el('div', { class: 'grid grid-cols-4 gap-1.5 w-full sm:w-auto', role: 'group', 'aria-label': `Trạng thái ${r.student.full_name}` });
        const minutes = el('input', { type: 'number', min: 0, max: 300, value: mark.minutes_late ?? '', placeholder: 'phút', 'aria-label': 'Số phút đi muộn', class: `h-9 w-20 rounded-lg border border-outline-variant px-2 text-sm ${mark.status === 'late' ? '' : 'hidden'}`, disabled: !can.record });
        minutes.addEventListener('input', () => { mark.minutes_late = minutes.value === '' ? null : Number(minutes.value); });

        const paint = () => {
            buttons.querySelectorAll('button').forEach((b) => {
                const tone = BUTTONS.find((x) => x[0] === b.dataset.status)[2];
                b.className = `h-10 rounded-lg border text-xs font-semibold ${mark.status === b.dataset.status ? tone : 'bg-white text-on-surface border-outline-variant'} disabled:opacity-60`;
                b.setAttribute('aria-pressed', mark.status === b.dataset.status ? 'true' : 'false');
            });
            minutes.classList.toggle('hidden', mark.status !== 'late');
        };

        BUTTONS.forEach(([value, label]) => buttons.append(el('button', { type: 'button', dataset: { status: value }, disabled: !can.record, onclick: () => {
            mark.status = value;
            paint();
            renderCounts();
        } }, label)));
        paint();

        return el('article', { class: 'bg-surface-container-lowest rounded-xl border border-outline-variant/50 p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3' },
            el('div', { class: 'flex items-center gap-3 min-w-0' },
                el('span', { class: 'w-7 text-xs font-bold text-secondary' }, String(index + 1).padStart(2, '0')),
                r.student.photo_url ? el('img', { src: r.student.photo_url, alt: '', class: 'w-10 h-10 rounded-full object-cover' }) : null,
                el('div', { class: 'min-w-0' },
                    el('p', { class: 'font-semibold text-sm' }, r.student.full_name),
                    el('p', { class: 'text-xs text-secondary font-mono' }, r.student.student_code),
                    r.record?.pending_change ? el('p', { class: 'text-[11px] text-amber-700' }, 'Đang chờ Phòng Đào tạo duyệt đề nghị sửa') : null)),
            el('div', { class: 'flex flex-wrap items-center gap-2' }, buttons, minutes,
                can.request_change && r.record && !r.record.pending_change ? el('button', { type: 'button', class: 'text-xs font-semibold text-primary hover:underline', onclick: () => requestChange(r) }, 'Đề nghị sửa') : null));
    }

    function allPresent() {
        state.marks.forEach((m) => { m.status = 'present'; m.minutes_late = null; });
        renderSheet();
    }

    async function save(button) {
        const items = [...state.marks.entries()].filter(([, m]) => m.status).map(([id, m]) => ({ enrollment_id: id, status: m.status, minutes_late: m.status === 'late' ? (m.minutes_late ?? 0) : null, note: m.note || null }));

        if (items.length < state.marks.size) {
            showAlert(region('alert'), `Còn ${state.marks.size - items.length} sinh viên chưa chọn trạng thái. Bấm "Tất cả có mặt" rồi chỉnh ngoại lệ.`, 'warning');

            return;
        }

        try {
            const result = await withBusy(button, () => api.put(tpl(page.api.record, sessionSelect.value), { items }));
            showAlert(region('alert'), null);
            toast(result.message, 'success');
            state.sheet = result;
            await loadSessionsKeepSelection();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function loadSessionsKeepSelection() {
        const current = sessionSelect.value;
        const data = await api.get(tpl(page.api.sessions, sectionSelect.value));
        state.sessions = data.sessions ?? [];
        sessionSelect.value = current;
        await loadSheet();
    }

    async function requestChange(r) {
        const saved = await formDialog({
            title: `Đề nghị sửa điểm danh: ${r.student.full_name}`,
            description: `Hiện ghi nhận: ${r.record.status_label}. Phòng Đào tạo duyệt thì thay đổi mới có hiệu lực.`,
            fields: [
                { name: 'status', label: 'Trạng thái đề nghị', type: 'select', required: true, options: BUTTONS.filter(([v]) => v !== r.record.status).map(([v, l]) => ({ value: v, label: l })) },
                { name: 'minutes_late', label: 'Số phút đi muộn (nếu đi muộn)', type: 'number', min: 0 },
                { name: 'reason', label: 'Lý do', type: 'textarea', required: true, full: true },
            ],
            submitText: 'Gửi đề nghị',
            onSubmit: (data) => api.post(tpl(page.api.requestChange, r.record.id), {
                status: data.get('status'), minutes_late: data.get('minutes_late') ? Number(data.get('minutes_late')) : null, reason: data.get('reason'),
            }),
        });

        if (saved) {
            toast('Đã gửi đề nghị sửa cho Phòng Đào tạo.', 'success');
            await loadSheet();
        }
    }

    function renderLog() {
        const session = state.sessions.find((s) => String(s.id) === sessionSelect.value);
        const form = root.querySelector('[data-form="log"]');
        field('log-session').textContent = session ? `Buổi ${dmy(session.date)}, tiết ${session.period_from}-${session.period_to}${session.teaching_log ? ' — đã ghi sổ đầu bài' : ''}` : '';
        form.elements.periods.value = session?.teaching_log?.periods ?? (session ? session.period_to - session.period_from + 1 : '');
        form.elements.content.value = session?.teaching_log?.content ?? '';
        form.querySelector('button[type="submit"]').disabled = !session?.open;
    }

    async function saveLog(event) {
        const form = event.currentTarget;
        showAlert(region('log-alert'), null);

        try {
            await withBusy(form.querySelector('button[type="submit"]'), () => api.put(tpl(page.api.teachingLog, sessionSelect.value), {
                periods: Number(form.elements.periods.value), content: form.elements.content.value,
            }));
            toast('Đã ghi sổ đầu bài.', 'success');
            await loadSessionsKeepSelection();
        } catch (error) {
            showAlert(region('log-alert'), error);
        }
    }

    async function loadSummary() {
        region('summary').replaceChildren(loadingBlock(3));

        try {
            const data = await api.get(tpl(page.api.summary, sectionSelect.value));
            const list = data.data ?? [];
            field('summary-meta').textContent = `${list.length} sinh viên · ${list.filter((x) => x.warning && !x.banned).length} cảnh báo · ${list.filter((x) => x.banned).length} không đủ điều kiện dự thi${data.locked ? ' · Sổ đã khóa' : ''}`;
            const tone = { banned: 'bg-red-50 text-red-700', warning: 'bg-amber-50 text-amber-800', normal: 'bg-[#DCFCE7] text-[#15803D]' };
            region('summary').replaceChildren(list.length
                ? el('table', { class: 'w-full text-sm min-w-[760px]' },
                    el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] text-left text-xs uppercase text-secondary' }, ['MSSV', 'Họ và tên', 'Có mặt', 'Vắng', 'Có phép', 'Muộn', 'Tỷ lệ vắng', 'Điểm CC', 'Phân loại'].map((t) => el('th', { class: 'py-2.5 px-3 font-semibold' }, t)))),
                    el('tbody', { class: 'divide-y divide-outline-variant/20' }, list.map((x) => el('tr', {},
                        el('td', { class: 'py-2 px-3 font-mono text-primary' }, x.student?.student_code),
                        el('td', { class: 'py-2 px-3' }, x.student?.full_name),
                        el('td', { class: 'py-2 px-3' }, x.present_count),
                        el('td', { class: 'py-2 px-3' }, x.absent_count),
                        el('td', { class: 'py-2 px-3' }, x.excused_count),
                        el('td', { class: 'py-2 px-3' }, x.late_count),
                        el('td', { class: 'py-2 px-3 font-semibold' }, `${x.absence_rate}% (${x.absences}/${x.total_sessions})`),
                        el('td', { class: 'py-2 px-3' }, x.attendance_score ?? '—'),
                        el('td', { class: 'py-2 px-3' }, el('span', { class: `rounded-full px-2 py-0.5 text-xs font-semibold ${tone[x.level]}` }, x.level_label))))))
                : emptyBlock('Chưa có dữ liệu chuyên cần', 'Tổng hợp được tính sau khi điểm danh.', 'insights'));
        } catch (error) {
            region('summary').replaceChildren(errorBlock(error, loadSummary));
        }
    }

    async function pushGrades(button) {
        try {
            const result = await withBusy(button, () => api.post(tpl(page.api.pushGrades, sectionSelect.value), {}));
            toast(result.message, 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }
}
