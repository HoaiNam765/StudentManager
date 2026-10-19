/**
 * Trang kết quả đăng ký học phần của sinh viên (resources/views/student/enrollment/result.blade.php).
 *
 * API: GET result?term_id= (học phần, tổng tín chỉ, khung tín chỉ, thời khóa biểu tuần), GET result/slip (PDF, máy
 * chủ ghi nhật ký tải), POST enrollments/{id}/cancel | withdraw, GET /admin/academic-years (chọn học kỳ).
 * Không gửi term_id thì máy chủ lấy học kỳ của đợt đăng ký mới nhất.
 */
import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const STATUS_STYLE = {
    registered: 'bg-blue-50 text-primary border-primary/30',
    confirmed: 'bg-emerald-50 text-emerald-800 border-emerald-200',
    withdrawn: 'bg-slate-100 text-slate-600 border-slate-300',
};

export default function studentRegistrationResult(root) {
    const page = pageData();
    const state = { termId: page.termId ?? null, result: null, terms: null };
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const setText = (name, value) => root.querySelectorAll(`[data-field="${name}"]`).forEach((node) => { node.textContent = value; });
    const url = (template, id) => template.replace('__ID__', encodeURIComponent(id));

    root.querySelector('[data-action="reload"]').addEventListener('click', () => load());
    root.querySelector('[data-action="slip"]').addEventListener('click', (event) => downloadSlip(event.currentTarget));
    root.querySelector('[data-input="term"]').addEventListener('change', (event) => {
        state.termId = Number(event.target.value) || null;
        const query = new URLSearchParams(window.location.search);
        query.set('term_id', state.termId);
        window.history.replaceState(null, '', `${window.location.pathname}?${query}`);
        load();
    });

    load();

    async function load() {
        ['items', 'timetable'].forEach((name) => region(name).replaceChildren(loadingBlock(2)));
        region('summary').replaceChildren();
        region('slip').replaceChildren(loadingBlock(1, 'Đang dựng phiếu…'));
        showAlert(region('alert'), null);

        try {
            const [result, terms] = await Promise.all([
                api.get(page.api.result, { term_id: state.termId }),
                state.terms ? Promise.resolve(state.terms) : api.get(page.api.terms).catch(() => null),
            ]);
            state.result = result;
            state.terms = terms;
            render();
        } catch (error) {
            setText('subtitle', 'Không tải được kết quả đăng ký.');
            region('items').replaceChildren(errorBlock(error, () => load()));
            region('timetable').replaceChildren();
            region('slip').replaceChildren(emptyBlock('Chưa có dữ liệu để dựng phiếu', '', 'description'));
        }
    }

    function render() {
        const { result } = state;
        const academicYear = result.term.academic_year ? ` – năm học ${result.term.academic_year}` : '';
        setText('subtitle', `${result.student.full_name} · MSSV ${result.student.student_code} · ${result.term.name}${academicYear}`);
        renderTermPicker();
        renderSummary();
        renderItems();
        renderTimetable();
        renderSlip();
    }

    function renderTermPicker() {
        const picker = root.querySelector('[data-field="term-picker"]');
        const select = root.querySelector('[data-input="term"]');
        const terms = (state.terms?.data ?? []).flatMap((year) => (year.terms ?? []).map((term) => ({ id: term.id, label: `${term.name} – ${year.name}` })));

        if (terms.length === 0) {
            picker.hidden = true;

            return;
        }

        select.replaceChildren(...terms.map((term) => el('option', { value: term.id, selected: term.id === state.result.term.id }, term.label)));
        picker.hidden = false;
    }

    function renderSummary() {
        const { result } = state;
        const active = result.items.filter((item) => item.counts_for_gpa);
        const withdrawn = result.items.length - active.length;
        const limits = result.credit_limits;
        const card = (label, value, hint = '') => el('div', { class: 'bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-4' },
            el('p', { class: 'text-label-sm text-secondary uppercase tracking-wider' }, label),
            el('p', { class: 'text-headline-lg font-bold text-on-surface mt-1' }, value),
            hint ? el('p', { class: 'text-[11px] text-outline mt-0.5' }, hint) : null);

        region('summary').replaceChildren(
            card('Tổng tín chỉ', result.total_credits, 'Học phần còn hiệu lực'),
            card('Khung tín chỉ', limits ? `${limits.min}–${limits.max}` : '—', limits?.basis ?? 'Chưa có đợt đăng ký của học kỳ này'),
            card('Học phần đang học', active.length),
            card('Đã rút', withdrawn, 'Không tính GPA'),
        );
    }

    function renderItems() {
        const { items } = state.result;
        setText('items-count', `${items.length} học phần`);

        if (items.length === 0) {
            region('items').replaceChildren(emptyBlock('Chưa đăng ký học phần nào trong học kỳ này', 'Vào trang Đăng ký học phần khi đợt đăng ký đang mở.', 'inventory_2'));

            return;
        }

        const th = (text, extra = '') => el('th', { scope: 'col', class: `px-3 py-2.5 font-semibold text-on-surface-variant ${extra}` }, text);
        const table = el(
            'table',
            { class: 'w-full min-w-[900px] border-collapse text-left text-sm' },
            el('caption', { class: 'sr-only' }, 'Danh sách học phần đã đăng ký'),
            el('thead', { class: 'bg-surface-container-low' }, el('tr', {}, th('STT', 'text-center'), th('Lớp học phần'), th('Học phần'), th('TC', 'text-center'), th('Lịch học & phòng'), th('Giảng viên'), th('Ngày đăng ký'), th('Trạng thái'), th('Thao tác'))),
            el('tbody', {}, items.map((item, index) => {
                const alertBox = el('div', { class: 'mt-1', hidden: true });

                return el('tr', { class: 'border-t border-outline-variant align-top' },
                    el('td', { class: 'px-3 py-3 text-center text-secondary' }, index + 1),
                    el('td', { class: 'px-3 py-3 font-semibold text-on-surface whitespace-nowrap' }, item.section),
                    el('td', { class: 'px-3 py-3' }, el('p', { class: 'font-semibold text-on-surface' }, item.subject.name), el('p', { class: 'text-xs text-secondary' }, `${item.subject.code} · ${item.type_label}`)),
                    el('td', { class: 'px-3 py-3 text-center' }, item.subject.credits),
                    el('td', { class: 'px-3 py-3 text-xs text-on-surface space-y-0.5' }, item.schedule.length ? item.schedule.map((line) => el('p', {}, line)) : el('span', { class: 'text-outline' }, item.counts_for_gpa ? 'Chưa xếp lịch' : '—')),
                    el('td', { class: 'px-3 py-3 text-xs' }, item.instructors.length ? item.instructors.join(', ') : el('span', { class: 'text-outline' }, 'Chưa phân công')),
                    el('td', { class: 'px-3 py-3 text-xs whitespace-nowrap' }, item.registered_at),
                    el('td', { class: 'px-3 py-3' }, el('span', { class: `inline-flex rounded-full border px-2 py-0.5 text-[11px] font-semibold ${STATUS_STYLE[item.status] ?? 'bg-slate-100 text-slate-700 border-slate-300'}` }, item.status_label)),
                    el('td', { class: 'px-3 py-3' }, el('div', { class: 'flex flex-col gap-1.5 min-w-[110px]' }, actions(item, alertBox)), alertBox),
                );
            })),
        );

        region('items').replaceChildren(el('div', { class: 'overflow-x-auto rounded-lg border border-outline-variant', tabindex: '0', role: 'region', 'aria-label': 'Bảng học phần đã đăng ký' }, table));
    }

    function actions(item, alertBox) {
        if (!page.can.cancel || !['registered', 'confirmed'].includes(item.status)) {
            return [el('span', { class: 'text-xs text-outline' }, '—')];
        }

        const buttons = [];
        const small = 'px-2.5 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center justify-center gap-1';

        if (item.status === 'registered') {
            const cancel = el('button', { type: 'button', class: `${small} border border-red-300 bg-red-50 text-red-700 hover:bg-red-100` }, icon('delete_outline', 'text-[16px]'), 'Hủy');
            cancel.addEventListener('click', () => submit(cancel, alertBox, item, 'cancel'));
            buttons.push(cancel);
        }

        const withdraw = el('button', { type: 'button', class: `${small} border border-outline-variant text-on-surface hover:bg-surface-container-low` }, icon('logout', 'text-[16px]'), 'Rút học phần');
        withdraw.addEventListener('click', () => submit(withdraw, alertBox, item, 'withdraw'));
        buttons.push(withdraw);

        return buttons;
    }

    async function submit(button, alertBox, item, action) {
        const isCancel = action === 'cancel';
        const ok = await confirmDialog({
            title: isCancel ? 'Hủy đăng ký học phần?' : 'Rút học phần?',
            message: `${item.section} – ${item.subject.name}\n${isCancel ? 'Chỗ được trả lại cho lớp, học phần không ghi vào kết quả.' : 'Học phần ghi "Đã rút" (W), không tính GPA. Không hoàn tác được.'}`,
            confirmText: isCancel ? 'Hủy đăng ký' : 'Rút học phần',
            danger: true,
        });

        if (!ok) {
            return;
        }

        showAlert(alertBox, null);

        try {
            await withBusy(button, () => api.post(url(isCancel ? page.api.cancel : page.api.withdraw, item.id)));
            toast(isCancel ? `Đã hủy đăng ký lớp ${item.section}.` : `Đã rút học phần ở lớp ${item.section}.`, 'success');
            await load();
        } catch (error) {
            showAlert(alertBox, error);
            toast(error.message, 'error', 7000);
        }
    }

    function renderTimetable() {
        const rows = state.result.timetable;

        if (rows.length === 0) {
            region('timetable').replaceChildren(emptyBlock('Chưa có buổi học nào', 'Lớp chưa xếp lịch hoặc chưa đăng ký lớp nào.', 'calendar_month'));

            return;
        }

        const days = [...new Set(rows.map((row) => row.day_of_week))];
        region('timetable').replaceChildren(el('div', { class: 'grid gap-3 sm:grid-cols-2 xl:grid-cols-3' }, days.map((day) => {
            const items = rows.filter((row) => row.day_of_week === day);

            return el('div', { class: 'rounded-lg border border-outline-variant p-3' },
                el('p', { class: 'text-label-lg font-bold text-primary mb-2' }, items[0].day_label),
                items.map((row) => el('div', { class: 'border-l-4 border-primary bg-primary/5 rounded px-2.5 py-1.5 mb-2 text-xs space-y-0.5' },
                    el('p', { class: 'font-semibold text-on-surface' }, `${row.subject} (${row.section})`),
                    el('p', { class: 'text-secondary' }, `Tiết ${row.periods} · ${row.time}${row.room ? ` · phòng ${row.room}` : ''}`),
                    el('p', { class: 'text-outline' }, `Tuần ${row.weeks}`))));
        })));
    }

    /** Bản xem trước phiếu, cùng nội dung với PDF (resources/views/pdf/enrollment-slip.blade.php). */
    function renderSlip() {
        const { result } = state;
        const cell = 'border border-slate-400 px-2 py-1.5';
        const sum = result.items.filter((item) => item.counts_for_gpa).reduce((total, item) => total + item.subject.credits, 0);

        region('slip').replaceChildren(
            el('div', { class: 'grid grid-cols-2 gap-4 text-center text-xs' },
                el('div', {}, el('p', { class: 'font-bold uppercase' }, page.school ?? ''), el('p', {}, 'PHÒNG ĐÀO TẠO')),
                el('div', {}, el('p', { class: 'font-bold' }, 'CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM'), el('p', {}, 'Độc lập – Tự do – Hạnh phúc'))),
            el('h3', { class: 'mt-8 text-center text-xl font-bold uppercase tracking-wide' }, 'Phiếu đăng ký học phần'),
            el('p', { class: 'text-center text-sm' }, `${result.term.name}${result.term.academic_year ? ` – Năm học ${result.term.academic_year}` : ''}`),
            el('dl', { class: 'mt-6 grid grid-cols-2 gap-x-6 gap-y-1 text-sm' },
                el('div', {}, el('dt', { class: 'inline' }, 'Họ và tên: '), el('dd', { class: 'inline font-semibold' }, result.student.full_name)),
                el('div', {}, el('dt', { class: 'inline' }, 'MSSV: '), el('dd', { class: 'inline font-semibold' }, result.student.student_code)),
                el('div', {}, el('dt', { class: 'inline' }, 'Ngành: '), el('dd', { class: 'inline' }, result.student.major ?? '—')),
                el('div', {}, el('dt', { class: 'inline' }, 'Khóa: '), el('dd', { class: 'inline' }, result.student.cohort ?? '—'))),
            el('table', { class: 'mt-4 w-full border-collapse text-xs' },
                el('thead', { class: 'bg-slate-100' }, el('tr', {}, ['STT', 'Mã HP', 'Tên học phần', 'TC', 'Lớp học phần', 'Loại', 'Lịch học', 'Trạng thái'].map((text) => el('th', { class: cell }, text)))),
                el('tbody', {},
                    result.items.length
                        ? result.items.map((item, index) => el('tr', {},
                            el('td', { class: `${cell} text-center` }, index + 1),
                            el('td', { class: cell }, item.subject.code),
                            el('td', { class: cell }, item.subject.name),
                            el('td', { class: `${cell} text-center` }, item.subject.credits),
                            el('td', { class: cell }, item.section),
                            el('td', { class: cell }, item.type_label),
                            el('td', { class: cell }, item.schedule.length ? item.schedule.map((line) => el('div', {}, line)) : 'Chưa xếp lịch'),
                            el('td', { class: cell }, item.status_label + (item.counts_for_gpa ? '' : ' (không tính GPA)'))))
                        : el('tr', {}, el('td', { class: `${cell} text-center text-slate-500`, colspan: 8 }, 'Chưa đăng ký học phần nào')),
                    el('tr', { class: 'font-semibold' }, el('td', { class: `${cell} text-right`, colspan: 3 }, 'Tổng số tín chỉ'), el('td', { class: `${cell} text-center` }, sum), el('td', { class: cell, colspan: 4 }, ''))),
            ),
            el('div', { class: 'mt-10 grid grid-cols-2 text-center text-sm' },
                el('div', {}, el('p', { class: 'font-semibold' }, 'Sinh viên'), el('p', { class: 'text-xs italic text-slate-500' }, '(ký, ghi rõ họ tên)')),
                el('div', {}, el('p', { class: 'font-semibold' }, 'Phòng Đào tạo'), el('p', { class: 'text-xs italic text-slate-500' }, '(ký, đóng dấu)'))),
        );
    }

    async function downloadSlip(button) {
        try {
            const outcome = await withBusy(button, () => api.download(page.api.slip, { term_id: state.result?.term.id ?? state.termId }, 'phieu-dang-ky.pdf'), 'Đang tạo phiếu…');
            toast(outcome.queued ? 'Phiếu đang được tạo, thử tải lại sau ít phút.' : `Đã tải ${outcome.filename}.`, outcome.queued ? 'info' : 'success');
        } catch (error) {
            toast(error.message, 'error', 7000);
        }
    }
}
