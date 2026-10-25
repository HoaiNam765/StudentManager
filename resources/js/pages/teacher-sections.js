/**
 * Lớp học phần đang dạy (FE #25, mock teacher/lop-hoc-phan): thẻ lớp được phân công (sĩ số, lịch, trạng thái điểm),
 * danh sách sinh viên từng lớp (API CLS phạm vi SECTION), lọc theo lớp hành chính, in A4, xuất CSV ngay trên trình duyệt.
 * Cột số buổi vắng của mock chờ module điểm danh (ATT, P2) nên chưa hiện.
 */
import { api } from '../api.js';
import { dmy, listAll, rows, statusBadge, tpl } from '../lookups.js';
import { el, emptyBlock, errorBlock, icon, loadingBlock, pageData } from '../ui.js';

export default function teacherSections(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const stat = (name) => root.querySelector(`[data-stat="${name}"]`);
    const termSelect = root.querySelector('[data-input="term"]');
    const search = root.querySelector('[data-input="q"]');
    const classFilter = root.querySelector('[data-input="class"]');
    const state = { sections: [], selected: null, students: [] };

    termSelect.addEventListener('change', render);
    search.addEventListener('input', renderRoster);
    classFilter.addEventListener('change', renderRoster);
    root.querySelector('[data-action="csv"]').addEventListener('click', exportCsv);

    load();

    async function load() {
        region('sections').replaceChildren(loadingBlock(3));

        try {
            state.sections = rows(await api.get(page.api.sections));
            const terms = [];
            state.sections.forEach((s) => {
                if (s.term && !terms.some((t) => t.id === s.term.id)) {
                    terms.push({ ...s.term, year: s.academic_year?.name });
                }
            });
            termSelect.replaceChildren(...terms.map((t) => el('option', { value: t.id }, `${t.name} · ${t.year ?? ''}`)));
            const current = state.sections.find((s) => ['in_progress', 'registration_closed', 'registration_open'].includes(s.status))?.term?.id;
            const fromHash = state.sections.find((s) => `#lop-${s.id}` === window.location.hash);
            termSelect.value = String(fromHash?.term?.id ?? current ?? terms.at(-1)?.id ?? '');
            render();

            if (fromHash) {
                openRoster(fromHash);
            }
        } catch (error) {
            region('sections').replaceChildren(errorBlock(error, load));
        }
    }

    function render() {
        const list = state.sections.filter((s) => String(s.term?.id) === termSelect.value);
        const students = list.reduce((sum, s) => sum + (s.students_count ?? 0), 0);
        const credits = list.reduce((sum, s) => sum + (s.subject?.credits ?? 0), 0);
        const pending = list.filter((s) => s.grade_status?.editable).length;
        stat('sections').textContent = String(list.length);
        stat('term').textContent = termSelect.selectedOptions[0]?.textContent ?? '';
        stat('students').textContent = students.toLocaleString('vi-VN');
        stat('average').textContent = list.length ? `Trung bình: ${Math.round(students / list.length)} SV/lớp` : '';
        stat('credits').textContent = String(credits);
        stat('periods').textContent = `${list.reduce((sum, s) => sum + (s.schedule ?? []).reduce((n, r) => n + (r.period_to - r.period_from + 1) * (r.week_to - r.week_from + 1), 0), 0)} tiết theo lịch`;
        stat('pending').textContent = String(pending);
        stat('submitted').textContent = `${list.length - pending} lớp đã nộp / đang duyệt / công bố`;
        region('sections').replaceChildren(...(list.length ? list.map(card) : [emptyBlock('Không có lớp trong học kỳ này', '', 'school')]));
    }

    function card(s) {
        const active = state.selected?.id === s.id;

        return el('article', { id: `lop-${s.id}`, class: `rounded-xl border p-4 space-y-2 ${active ? 'border-primary bg-[#EEF2FD]/40' : 'border-outline-variant/60'}` },
            el('div', { class: 'flex items-start justify-between gap-2' },
                el('p', { class: 'font-mono text-xs font-bold text-primary' }, s.code),
                active ? el('span', { class: 'rounded-full bg-primary px-2 py-0.5 text-[10px] font-semibold text-white' }, 'Đang xem') : el('span', { class: 'rounded-full bg-surface-container px-2 py-0.5 text-[10px] font-semibold' }, s.status_label)),
            el('h3', { class: 'text-sm font-bold' }, s.subject?.name),
            el('p', { class: 'text-xs text-secondary' }, `${s.subject?.credits ?? '?'} tín chỉ`),
            ...(s.schedule ?? []).map((r) => el('p', { class: 'text-xs text-on-surface-variant flex items-center gap-1' }, icon('schedule', 'text-[15px]'), `${r.day_label}, tiết ${r.period_from}-${r.period_to}${r.room ? ` · Phòng ${r.room}` : ''} (tuần ${r.week_from}-${r.week_to})`)),
            el('p', { class: 'text-xs text-on-surface-variant flex items-center gap-1' }, icon('groups', 'text-[15px]'), `Sĩ số: ${s.students_count} / ${s.max_size} SV (${s.max_size ? Math.round((s.students_count * 100) / s.max_size) : 0}%)`),
            s.grade_status ? el('p', { class: `text-xs font-semibold flex items-center gap-1 ${s.grade_status.editable ? 'text-amber-700' : 'text-emerald-700'}` }, icon(s.grade_status.editable ? 'warning' : 'check_circle', 'text-[15px]'), s.grade_status.editable ? `Chưa nộp điểm (${s.grade_status.label})` : s.grade_status.label) : null,
            el('div', { class: 'flex flex-wrap gap-2 pt-1' },
                el('button', { type: 'button', class: 'rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white', onclick: () => openRoster(s) }, 'Xem danh sách SV'),
                el('a', { href: `${page.links.gradebook}?course_section_id=${s.id}`, class: 'rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold' }, 'Nhập điểm'),
                el('a', { href: page.links.announcements, class: 'rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold flex items-center gap-1', title: 'Gửi thông báo cho lớp' }, icon('mail', 'text-[15px]'))));
    }

    async function openRoster(s) {
        state.selected = s;
        render();
        const box = region('roster-box');
        box.hidden = false;
        field('roster-title').textContent = `Danh sách sinh viên lớp: ${s.code} - ${s.subject?.name ?? ''}`;
        field('roster-meta').textContent = [`Sĩ số: ${s.students_count} SV`, ...(s.schedule ?? []).map((r) => `${r.day_label} (Tiết ${r.period_from}-${r.period_to})${r.room ? ` • Phòng ${r.room}` : ''}`)].join(' • ');
        region('roster').replaceChildren(loadingBlock(4));
        box.scrollIntoView({ behavior: 'smooth', block: 'start' });

        try {
            state.students = (await listAll(tpl(page.api.students, s.id))).map((r) => r.student).filter(Boolean);
            const classes = [...new Set(state.students.map((x) => x.admin_class?.code).filter(Boolean))].sort();
            classFilter.replaceChildren(el('option', { value: '' }, 'Tất cả'), ...classes.map((c) => el('option', { value: c }, `${c} (${state.students.filter((x) => x.admin_class?.code === c).length} sinh viên)`)));
            renderRoster();
        } catch (error) {
            region('roster').replaceChildren(errorBlock(error, () => openRoster(s)));
        }
    }

    function filtered() {
        const q = search.value.trim().toLowerCase();

        return state.students.filter((x) => (!classFilter.value || x.admin_class?.code === classFilter.value)
            && (!q || `${x.student_code} ${x.full_name}`.toLowerCase().includes(q)));
    }

    function renderRoster() {
        const list = filtered();
        const th = (t, extra = '') => el('th', { scope: 'col', class: `py-3 px-4 font-semibold ${extra}` }, t);
        region('roster').replaceChildren(list.length
            ? el('table', { class: 'w-full text-sm min-w-[820px]' },
                el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] text-left text-xs uppercase tracking-wider text-secondary border-b border-outline-variant/30' },
                    th('STT', 'w-12'), th('MSSV'), th('Họ và tên sinh viên'), th('Lớp HC'), th('Ngày sinh'), th('Email'), th('Trạng thái học vụ', 'text-center'))),
                el('tbody', { class: 'divide-y divide-outline-variant/20' }, list.map((x, i) => el('tr', { class: 'hover:bg-[#F8FAFC]' },
                    el('td', { class: 'py-2.5 px-4 text-secondary' }, i + 1),
                    el('td', { class: 'py-2.5 px-4 font-mono font-bold text-primary' }, x.student_code),
                    el('td', { class: 'py-2.5 px-4 font-semibold' }, x.full_name),
                    el('td', { class: 'py-2.5 px-4' }, x.admin_class?.code ?? '—'),
                    el('td', { class: 'py-2.5 px-4' }, dmy(x.date_of_birth)),
                    el('td', { class: 'py-2.5 px-4 text-xs text-secondary' }, x.school_email ?? '—'),
                    el('td', { class: 'py-2.5 px-4 text-center' }, statusBadge(x.status))))))
            : emptyBlock('Không có sinh viên phù hợp', '', 'person_search'));
    }

    /** Xuất CSV (UTF-8 có BOM để Excel đọc đúng tiếng Việt) từ danh sách đang hiển thị. */
    function exportCsv() {
        const cell = (value) => `"${String(value ?? '').replaceAll('"', '""')}"`;
        const lines = [['STT', 'MSSV', 'Họ và tên', 'Lớp HC', 'Ngày sinh', 'Email'], ...filtered().map((x, i) => [i + 1, x.student_code, x.full_name, x.admin_class?.code, dmy(x.date_of_birth), x.school_email])];
        const blob = new Blob(['﻿', lines.map((line) => line.map(cell).join(',')).join('\r\n')], { type: 'text/csv;charset=utf-8' });
        const link = el('a', { href: URL.createObjectURL(blob), download: `danh-sach-${state.selected?.code ?? 'lop'}.csv` });
        document.body.append(link);
        link.click();
        link.remove();
    }
}
