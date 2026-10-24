/**
 * Chi tiết lớp học phần (FE #18, mock admin/lop-hoc-phan-chi-tiet): thông tin lớp, sĩ số, lịch học và phòng, giảng
 * viên (API CLS / TTB), danh sách sinh viên đăng ký có lớp hành chính / ngành / khóa, xuất Excel (ENR.export), hủy đăng ký
 * thay kèm lý do (ENR.delete toàn trường), danh sách buổi học.
 */
import { api } from '../api.js';
import { dmy, tpl } from '../lookups.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const STATUS_TONE = {
    planned: 'bg-slate-100 text-slate-700',
    registration_open: 'bg-blue-50 text-blue-800',
    registration_closed: 'bg-amber-50 text-amber-800',
    in_progress: 'bg-emerald-50 text-emerald-800',
    finished: 'bg-violet-50 text-violet-800',
    locked: 'bg-slate-200 text-slate-800',
    cancelled: 'bg-red-50 text-red-800',
};
const MODES = { in_person: 'Tập trung (trực tiếp)', online: 'Trực tuyến', hybrid: 'Kết hợp' };
const TYPES = { theory: 'Lý thuyết', practice: 'Thực hành', mixed: 'Lý thuyết + thực hành' };

export default function adminCourseSectionDetail(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const input = (name) => root.querySelector(`[data-input="${name}"]`);
    const state = { section: null, enrollments: [] };

    root.querySelectorAll('[data-tab]').forEach((tab) => tab.addEventListener('click', () => showTab(tab.dataset.tab)));
    input('q').addEventListener('input', renderStudents);
    input('class').addEventListener('change', renderStudents);
    input('all').addEventListener('change', loadStudents);
    root.querySelector('[data-action="export"]')?.addEventListener('click', (e) => exportList(e.currentTarget));

    showTab('students');
    loadSection();
    loadStudents();

    function showTab(name) {
        root.querySelectorAll('[data-tab]').forEach((tab) => {
            tab.className = `rounded-lg px-4 py-2 text-sm font-semibold ${tab.dataset.tab === name ? 'bg-primary text-white' : 'border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low'}`;
        });
        root.querySelectorAll('[data-panel]').forEach((panel) => { panel.hidden = panel.dataset.panel !== name; });

        if (name === 'sessions' && !state.sessionsLoaded) {
            loadSessions();
        }
    }

    async function loadSection() {
        try {
            const [s, schedule] = await Promise.all([
                api.get(page.api.show),
                page.can.timetable ? api.get(page.api.schedule).catch(() => null) : Promise.resolve(null),
            ]);
            state.section = s;
            field('term').textContent = `${s.term?.name ?? ''} ${s.term?.academic_year ?? ''}`.trim();
            field('status').textContent = s.status_label;
            field('status').className = `rounded-full px-2.5 py-0.5 text-xs font-semibold ${STATUS_TONE[s.status] ?? ''}`;
            field('subject').textContent = s.subject?.name ?? s.code;
            field('subject-meta').textContent = `Mã học phần: ${s.subject?.code ?? ''} • ${s.subject?.credits ?? '?'} tín chỉ • ${TYPES[s.section_type] ?? s.section_type ?? ''}`;
            field('credits').textContent = `${s.subject?.credits ?? '?'} tín chỉ`;
            field('mode').textContent = MODES[s.delivery_mode] ?? s.delivery_mode ?? '—';
            field('note').textContent = s.note ?? (s.cancel_reason ? `Lý do hủy: ${s.cancel_reason}` : '');
            region('instructors').replaceChildren(...((s.instructors ?? []).length
                ? s.instructors.map((i) => el('p', {}, i.lecturer?.full_name, el('span', { class: 'text-xs font-normal text-secondary' }, ` (${i.role_label})`)))
                : [el('p', { class: 'text-amber-800' }, 'Chưa phân công giảng viên')]));
            const rules = schedule?.data ?? [];
            region('schedule').replaceChildren(...(rules.length
                ? rules.map((r) => el('p', {}, `${r.day_label}, tiết ${r.period_from}-${r.period_to}`, el('span', { class: 'block text-xs font-normal text-secondary' }, `Phòng ${r.room?.code ?? '—'} • tuần ${r.week_from}-${r.week_to} (${r.session_count} buổi)`)))
                : [el('p', { class: 'text-secondary font-normal' }, s.room?.code ? `Phòng ${s.room.code}, chưa xếp lịch` : 'Chưa xếp lịch')]));
            field('periods').textContent = schedule ? `${schedule.scheduled_periods ?? 0} / ${schedule.required_periods ?? 0} tiết đã xếp` : '';
            renderSize();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    function renderSize() {
        const s = state.section;

        if (!s) {
            return;
        }

        const active = state.enrollments.filter((e) => ['registered', 'confirmed', 'completed'].includes(e.status)).length;
        const pct = s.max_size ? Math.min(100, Math.round((active * 100) / s.max_size)) : 0;
        field('size').textContent = `${active} / ${s.max_size} sinh viên (${pct}% chỉ tiêu)`;
        field('remaining').textContent = `Còn trống ${Math.max(0, s.max_size - active)} chỗ · tối thiểu ${s.min_size ?? '?'}`;
        field('bar').style.width = `${pct}%`;
    }

    async function loadStudents() {
        if (!page.can.enrollments) {
            region('students').replaceChildren(emptyBlock('Bạn không có quyền xem danh sách đăng ký', '', 'lock'));

            return;
        }

        region('students').replaceChildren(loadingBlock(4));

        try {
            const data = await api.get(page.api.enrollments, { all: input('all').checked });
            state.enrollments = data.data ?? [];
            field('count').textContent = `(${data.count ?? state.enrollments.length})`;
            const classes = [...new Set(state.enrollments.map((e) => e.student?.admin_class).filter(Boolean))].sort();
            const current = input('class').value;
            input('class').replaceChildren(el('option', { value: '' }, 'Tất cả lớp hành chính'), ...classes.map((c) => el('option', { value: c }, c)));
            input('class').value = classes.includes(current) ? current : '';
            renderStudents();
            renderSize();
        } catch (error) {
            region('students').replaceChildren(errorBlock(error, loadStudents));
        }
    }

    function renderStudents() {
        const q = input('q').value.trim().toLowerCase();
        const list = state.enrollments.filter((e) => (!input('class').value || e.student?.admin_class === input('class').value)
            && (!q || `${e.student?.student_code} ${e.student?.full_name}`.toLowerCase().includes(q)));
        const th = (t, extra = '') => el('th', { scope: 'col', class: `py-3 px-4 font-semibold ${extra}` }, t);
        region('students').replaceChildren(list.length
            ? el('table', { class: 'w-full text-sm min-w-[960px]' },
                el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] text-left text-xs uppercase tracking-wider text-secondary border-b border-outline-variant/30' },
                    th('STT', 'w-12'), th('MSSV'), th('Họ và tên'), th('Lớp hành chính'), th('Ngành đào tạo'), th('Khóa học'), th('Thời gian đăng ký'), th('Trạng thái', 'text-center'), th('Thao tác', 'text-right print:hidden'))),
                el('tbody', { class: 'divide-y divide-outline-variant/20' }, list.map((e, i) => {
                    const active = ['registered', 'confirmed', 'completed'].includes(e.status);
                    const cancellable = ['registered', 'confirmed'].includes(e.status);

                    return el('tr', { class: 'hover:bg-[#F8FAFC]' },
                        el('td', { class: 'py-2.5 px-4 text-secondary' }, i + 1),
                        el('td', { class: 'py-2.5 px-4 font-mono font-bold text-primary' }, e.student?.student_code),
                        el('td', { class: 'py-2.5 px-4 font-semibold' }, e.student?.full_name, e.type !== 'normal' ? el('span', { class: 'ml-1 text-[11px] text-violet-700' }, e.type === 'retake' ? '(học lại)' : '(cải thiện)') : null),
                        el('td', { class: 'py-2.5 px-4' }, e.student?.admin_class ?? '—'),
                        el('td', { class: 'py-2.5 px-4' }, e.student?.major ?? '—'),
                        el('td', { class: 'py-2.5 px-4' }, e.student?.cohort ? `K${e.student.cohort}` : '—'),
                        el('td', { class: 'py-2.5 px-4 text-xs' }, e.registered_at ? `${dmy(e.registered_at.slice(0, 10))} ${e.registered_at.slice(11, 16)}` : '—'),
                        el('td', { class: 'py-2.5 px-4 text-center' }, el('span', { class: `inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold ${active ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-slate-100 text-slate-600'}` }, e.status_label)),
                        el('td', { class: 'py-2.5 px-4 text-right whitespace-nowrap print:hidden' },
                            page.can.students ? el('a', { href: tpl(page.links.student, e.student?.id), class: 'p-1 text-secondary hover:text-primary', title: 'Xem hồ sơ', 'aria-label': 'Xem hồ sơ' }, icon('visibility')) : null,
                            page.can.cancel && cancellable ? el('button', { type: 'button', class: 'p-1 text-secondary hover:text-red-700', title: 'Hủy đăng ký thay', 'aria-label': 'Hủy đăng ký thay', onclick: (ev) => cancel(ev.currentTarget, e) }, icon('person_remove')) : null));
                })))
            : emptyBlock('Chưa có sinh viên đăng ký', '', 'group_off'));
    }

    async function cancel(button, enrollment) {
        const answer = await confirmDialog({
            title: `Hủy đăng ký của ${enrollment.student?.full_name}?`,
            message: 'Hủy thay sinh viên (cán bộ), ghi nhật ký đăng ký và thông báo cho sinh viên.',
            confirmText: 'Hủy đăng ký',
            danger: true,
            input: { label: 'Lý do', required: true },
        });

        if (!answer) {
            return;
        }

        try {
            await withBusy(button, () => api.post(tpl(page.api.cancelEnrollment, enrollment.id), { reason: answer.value }));
            toast('Đã hủy đăng ký.', 'success');
            await loadStudents();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function exportList(button) {
        try {
            const result = await withBusy(button, () => api.download(page.api.export, { format: 'xlsx' }, `lop-${state.section?.code ?? 'hoc-phan'}.xlsx`), 'Đang xuất…');
            toast(result.queued ? 'Danh sách đang được xuất nền.' : `Đã tải ${result.filename}.`, 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function loadSessions() {
        if (!page.can.timetable) {
            region('sessions').replaceChildren(emptyBlock('Bạn không có quyền xem buổi học', '', 'lock'));

            return;
        }

        region('sessions').replaceChildren(loadingBlock(3));

        try {
            const data = await api.get(page.api.sessions);
            state.sessionsLoaded = true;
            const list = data.data ?? [];
            region('sessions').replaceChildren(list.length
                ? el('div', { class: 'space-y-3' },
                    el('p', { class: 'text-sm text-secondary' }, `${data.total} buổi · ${data.counted} buổi tính chuyên cần · ${data.needs_makeup} buổi nghỉ / hủy cần dạy bù`),
                    el('div', { class: 'grid sm:grid-cols-2 xl:grid-cols-4 gap-2' }, list.map((s) => el('div', { class: `rounded-lg border p-2.5 text-xs ${s.counts_for_attendance ? 'border-outline-variant/60' : 'border-red-200 bg-red-50'}` },
                        el('p', { class: 'font-semibold' }, `Tuần ${s.week ?? '?'} · ${dmy(s.date)}`),
                        el('p', {}, `Tiết ${s.period_from}-${s.period_to} · Phòng ${s.room ?? '—'}`),
                        el('p', { class: 'text-secondary' }, s.status_label, s.off_reason ? ` · ${s.off_reason}` : '')))))
                : emptyBlock('Chưa sinh buổi học', 'Buổi học được sinh từ thời khóa biểu ở trang Xếp thời khóa biểu / Dạy bù.', 'event_busy'));
        } catch (error) {
            region('sessions').replaceChildren(errorBlock(error, loadSessions));
        }
    }
}
