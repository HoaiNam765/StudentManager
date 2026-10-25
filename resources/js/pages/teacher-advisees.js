/**
 * Lớp cố vấn học tập (mock teacher/lop-co-van-hoc-tap): lớp giảng viên đang cố vấn (API CLS, phạm vi ADVISEE), sinh
 * viên kèm GPA / tín chỉ tích lũy, đếm cảnh báo (GPA tích lũy < 2.0), hộp tóm tắt hồ sơ. Ghi chú cố vấn chưa có BE.
 */
import { api } from '../api.js';
import { dmy, initials, listAll, rows, statusBadge, tpl } from '../lookups.js';
import { el, emptyBlock, errorBlock, loadingBlock, pageData } from '../ui.js';

const WARNING_GPA = 2.0;

export default function teacherAdvisees(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const picker = root.querySelector('[data-input="class"]');
    const search = root.querySelector('[data-input="q"]');
    const state = { classes: [], students: [] };

    picker.addEventListener('change', loadStudents);
    search.addEventListener('input', renderStudents);

    load();

    async function load() {
        region('students').replaceChildren(loadingBlock(3));

        try {
            state.classes = rows(await api.get(page.api.classes));

            if (!state.classes.length) {
                field('title').textContent = 'Chưa được phân công cố vấn';
                field('meta').textContent = 'Phòng Đào tạo / khoa phân công cố vấn học tập ở trang Khóa & Lớp hành chính.';
                region('students').replaceChildren(emptyBlock('Bạn chưa cố vấn lớp nào', '', 'supervisor_account'));

                return;
            }

            picker.replaceChildren(...state.classes.map((c) => el('option', { value: c.id }, c.code)));
            region('picker').hidden = state.classes.length < 2;
            await loadStudents();
        } catch (error) {
            region('students').replaceChildren(errorBlock(error, load));
        }
    }

    async function loadStudents() {
        const c = state.classes.find((x) => String(x.id) === picker.value) ?? state.classes[0];
        field('title').textContent = `${c.code} • ${c.major?.name ?? ''}`;
        field('meta').textContent = `${c.name} · ${c.cohort?.code ?? ''} · Sĩ số: ${c.current_students_count ?? 0} sinh viên`;
        region('students').replaceChildren(loadingBlock(3));

        try {
            state.students = await listAll(tpl(page.api.students, c.id));
            const gpas = state.students.map((r) => r.gpa?.gpa).filter((g) => g !== null && g !== undefined);
            field('avg').textContent = gpas.length ? (gpas.reduce((a, b) => a + b, 0) / gpas.length).toFixed(2) : '—';
            field('warnings').textContent = String(state.students.filter(warned).length).padStart(2, '0');
            renderStudents();
        } catch (error) {
            region('students').replaceChildren(errorBlock(error, loadStudents));
        }
    }

    function warned(row) {
        return row.gpa?.gpa !== null && row.gpa?.gpa !== undefined && row.gpa.gpa < WARNING_GPA;
    }

    function renderStudents() {
        const q = search.value.trim().toLowerCase();
        const list = state.students.filter((r) => !q || `${r.student?.student_code} ${r.student?.full_name}`.toLowerCase().includes(q));
        const th = (t, extra = '') => el('th', { scope: 'col', class: `py-3 px-4 font-semibold ${extra}` }, t);
        region('students').replaceChildren(list.length
            ? el('table', { class: 'w-full text-sm min-w-[760px]' },
                el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] text-left text-xs uppercase tracking-wider text-secondary border-b border-outline-variant/30' },
                    th('MSSV'), th('Họ và tên'), th('Tín chỉ TL', 'text-center'), th('GPA TL', 'text-center'), th('Tình trạng học vụ', 'text-center'), th('Thao tác', 'text-right'))),
                el('tbody', { class: 'divide-y divide-outline-variant/20' }, list.map((r) => el('tr', { class: 'hover:bg-[#F8FAFC]' },
                    el('td', { class: 'py-2.5 px-4 font-mono font-bold text-primary' }, r.student?.student_code),
                    el('td', { class: 'py-2.5 px-4 font-semibold' }, r.student?.full_name),
                    el('td', { class: 'py-2.5 px-4 text-center' }, r.gpa ? r.gpa.credits_earned : '—'),
                    el('td', { class: `py-2.5 px-4 text-center font-bold ${warned(r) ? 'text-red-700' : 'text-on-surface'}` }, r.gpa?.gpa === null || r.gpa?.gpa === undefined ? '—' : r.gpa.gpa.toFixed(2)),
                    el('td', { class: 'py-2.5 px-4 text-center' }, r.student?.status !== 'studying'
                        ? statusBadge(r.student?.status)
                        : el('span', { class: `inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold ${warned(r) ? 'bg-red-50 text-red-700' : 'bg-[#DCFCE7] text-[#15803D]'}` }, warned(r) ? 'Cảnh báo' : 'Bình thường')),
                    el('td', { class: 'py-2.5 px-4 text-right' }, el('button', { type: 'button', class: 'text-xs font-semibold text-primary hover:underline', onclick: () => summary(r) }, 'Xem hồ sơ'))))))
            : emptyBlock('Không có sinh viên', '', 'group_off'));
    }

    function summary(r) {
        const s = r.student ?? {};
        const close = el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold hover:bg-surface-container-low' }, 'Đóng');
        const dialog = el('dialog', { class: 'm-auto w-[min(92vw,460px)] rounded-xl border border-outline-variant p-5 shadow-xl backdrop:bg-black/40' },
            el('div', { class: 'space-y-4' },
                el('div', { class: 'flex items-center gap-3' },
                    s.photo_url ? el('img', { src: s.photo_url, alt: '', class: 'w-14 h-14 rounded-full object-cover' }) : el('div', { class: 'w-14 h-14 rounded-full bg-blue-100 text-primary font-bold flex items-center justify-center' }, initials(s.full_name)),
                    el('div', {}, el('h2', { class: 'text-base font-bold' }, s.full_name), el('p', { class: 'text-xs text-secondary' }, `${s.student_code} • Lớp ${s.admin_class?.code ?? ''}`))),
                el('dl', { class: 'grid grid-cols-2 gap-3 text-sm' },
                    el('div', {}, el('dt', { class: 'text-xs text-secondary' }, 'GPA tích lũy'), el('dd', { class: 'font-bold' }, r.gpa?.gpa?.toFixed(2) ?? '—')),
                    el('div', {}, el('dt', { class: 'text-xs text-secondary' }, 'Tín chỉ tích lũy'), el('dd', { class: 'font-bold' }, r.gpa?.credits_earned ?? '—')),
                    el('div', {}, el('dt', { class: 'text-xs text-secondary' }, 'Xếp loại'), el('dd', { class: 'font-semibold' }, r.gpa?.classification ?? '—')),
                    el('div', {}, el('dt', { class: 'text-xs text-secondary' }, 'Ngày sinh'), el('dd', { class: 'font-semibold' }, dmy(s.date_of_birth))),
                    el('div', { class: 'col-span-2' }, el('dt', { class: 'text-xs text-secondary' }, 'Email trường'), el('dd', { class: 'font-semibold break-all' }, s.school_email ?? '—'))),
                el('div', { class: 'flex justify-end' }, close)));
        close.addEventListener('click', () => {
            dialog.close();
            dialog.remove();
        });
        document.body.append(dialog);
        dialog.showModal();
    }
}
