/**
 * Điểm danh và chuyên cần (FE #43, mock admin/diem-danh-chuyen-can): tổng hợp theo phạm vi ATT.view, lọc học kỳ / khoa /
 * lớp / phân loại / sinh viên, điều chỉnh bản ghi điểm danh có lý do (Phòng Đào tạo), miễn cấm thi (BR-ATT-03),
 * duyệt yêu cầu sửa quá hạn (FR-ATT-005), khóa / mở khóa sổ (FR-ATT-013), xuất Excel (FR-ATT-010).
 */
import { api } from '../api.js';
import { debounce, dmy, fillSelect, formDialog, listAll, pager, rows, tpl } from '../lookups.js';
import { confirmDialog, el, emptyBlock, errorBlock, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const TONE = { banned: 'bg-red-50 text-red-700', warning: 'bg-amber-50 text-amber-800', normal: 'bg-[#DCFCE7] text-[#15803D]' };
const STATUSES = [['present', 'Có mặt'], ['excused', 'Vắng có phép'], ['absent', 'Vắng không phép'], ['late', 'Đi muộn']];

export default function adminAttendance(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const stat = (name) => root.querySelector(`[data-stat="${name}"]`);
    const form = root.querySelector('[data-form="filters"]');
    const state = { page: 1, terms: [], locked: null };

    root.querySelectorAll('[data-tab]').forEach((tab) => tab.addEventListener('click', () => showTab(tab.dataset.tab)));
    form.elements.term_id.addEventListener('change', async () => { await loadSections(); load(1); });
    ['faculty_id', 'course_section_id', 'flag'].forEach((n) => form.elements[n].addEventListener('change', () => load(1)));
    form.elements.q.addEventListener('input', debounce(() => load(1), 350));
    root.querySelector('[data-action="export"]')?.addEventListener('click', (e) => exportList(e.currentTarget));

    showTab('list');
    bootstrap();

    function showTab(name) {
        root.querySelectorAll('[data-tab]').forEach((tab) => {
            tab.className = `rounded-lg px-4 py-2 text-sm font-semibold ${tab.dataset.tab === name ? 'bg-primary text-white' : 'border border-outline-variant bg-white hover:bg-surface-container-low'}`;
        });
        root.querySelectorAll('[data-panel]').forEach((panel) => { panel.hidden = panel.dataset.panel !== name; });

        if (name === 'requests') {
            loadRequests();
        }
    }

    async function bootstrap() {
        try {
            const [years, faculties] = await Promise.all([api.get(page.api.terms), listAll(page.api.faculties)]);
            state.terms = rows(years).flatMap((y) => (y.terms ?? []).map((t) => ({ ...t, label: `${t.name} · ${y.name}` })));
            fillSelect(form.elements.term_id, state.terms, (t) => t.label, 'id', false);
            const current = state.terms.find((t) => t.status === 'in_progress') ?? state.terms[0];
            form.elements.term_id.value = String(current?.id ?? '');
            fillSelect(form.elements.faculty_id, faculties, (f) => f.name);
            await loadSections();
        } catch (error) {
            showAlert(region('alert'), error);
        }

        await Promise.all([load(1), countRequests()]);
    }

    async function loadSections() {
        const sections = await listAll(page.api.sections, { term_id: form.elements.term_id.value }).catch(() => []);
        form.elements.course_section_id.value = '';
        fillSelect(form.elements.course_section_id, sections, (s) => `${s.code} - ${s.subject?.name ?? ''}`);
    }

    function filters() {
        return Object.fromEntries([...new FormData(form).entries()].filter(([, v]) => v !== ''));
    }

    async function load(target = state.page) {
        state.page = target;
        region('list').replaceChildren(loadingBlock(4));

        try {
            const data = await api.get(page.api.summaries, { ...filters(), page: target });
            stat('total').textContent = (data.counts?.total ?? 0).toLocaleString('vi-VN');
            stat('term').textContent = form.elements.term_id.selectedOptions[0]?.textContent ?? '';
            stat('avg').textContent = `${(100 - (data.counts?.avg_rate ?? 0)).toFixed(1)}%`;
            stat('warning').textContent = `${data.counts?.warning ?? 0} SV`;
            stat('banned').textContent = `${data.counts?.banned ?? 0} SV`;
            const list = rows(data);
            region('list').replaceChildren(list.length ? table(list) : emptyBlock('Không có dữ liệu chuyên cần phù hợp', 'Thử đổi học kỳ hoặc bỏ bớt bộ lọc.', 'event_available'));
            pager(region('pager'), data, load);
            renderLock();
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, () => load(target)));
        }
    }

    function table(list) {
        const th = (t, extra = '') => el('th', { scope: 'col', class: `py-3 px-4 font-semibold ${extra}` }, t);

        return el('table', { class: 'w-full text-sm min-w-[920px]' },
            el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] text-left text-xs uppercase tracking-wider text-secondary border-b border-outline-variant/30' },
                th('MSSV'), th('Họ và tên'), th('Lớp HP'), th('Buổi vắng / Tổng', 'text-center'), th('Tỷ lệ vắng', 'text-center'), th('Phân loại', 'text-center'), th('Thao tác', 'text-right'))),
            el('tbody', { class: 'divide-y divide-outline-variant/20' }, list.map((x) => el('tr', { class: 'hover:bg-[#F8FAFC]' },
                el('td', { class: 'py-2.5 px-4 font-mono font-bold text-primary' }, x.student?.student_code),
                el('td', { class: 'py-2.5 px-4 font-semibold' }, x.student?.full_name),
                el('td', { class: 'py-2.5 px-4' }, el('p', {}, x.section?.code), el('p', { class: 'text-[11px] text-secondary' }, x.section?.subject?.name ?? '')),
                el('td', { class: 'py-2.5 px-4 text-center' }, `${x.absences} / ${x.total_sessions} buổi`),
                el('td', { class: `py-2.5 px-4 text-center font-bold ${x.level === 'banned' ? 'text-red-700' : x.level === 'warning' ? 'text-amber-700' : ''}` }, `${x.absence_rate}%`),
                el('td', { class: 'py-2.5 px-4 text-center' }, el('span', { class: `rounded-full px-2.5 py-0.5 text-xs font-bold ${TONE[x.level]}` }, x.level_label)),
                el('td', { class: 'py-2.5 px-4 text-right whitespace-nowrap' },
                    page.can.approve ? el('button', { type: 'button', class: 'text-xs font-semibold text-primary hover:underline', onclick: () => adjust(x) }, 'Điều chỉnh') : null,
                    page.can.approve && (x.banned || x.exempted) ? el('button', { type: 'button', class: 'ml-3 text-xs font-semibold text-violet-700 hover:underline', onclick: () => exempt(x) }, x.exempted ? 'Bỏ miễn' : 'Miễn cấm thi') : null)))));
    }

    /** Điều chỉnh một bản ghi điểm danh của sinh viên ở một buổi, kèm lý do (BR-ATT-07). */
    async function adjust(x) {
        let sessions = [];

        try {
            sessions = ((await api.get(tpl(page.api.sessions, x.section.id))).sessions ?? []).filter((s) => s.open);
        } catch (error) {
            showAlert(region('alert'), error);

            return;
        }

        const saved = await formDialog({
            title: `Điều chỉnh bản ghi chuyên cần: ${x.student?.full_name} (${x.student?.student_code})`,
            description: 'Mọi thay đổi ghi nhật ký kiểm toán (giá trị cũ – mới, người sửa, lý do).',
            fields: [
                { name: 'session_id', label: 'Buổi học cần điều chỉnh', type: 'select', required: true, full: true, options: sessions.map((s) => ({ value: s.id, label: `Buổi ${s.week ?? '?'} (${dmy(s.date)}) - tiết ${s.period_from}-${s.period_to}` })) },
                { name: 'status', label: 'Trạng thái mới', type: 'select', required: true, options: STATUSES.map(([v, l]) => ({ value: v, label: l })) },
                { name: 'minutes_late', label: 'Số phút đi muộn', type: 'number', min: 0 },
                { name: 'reason', label: 'Lý do điều chỉnh (bắt buộc)', type: 'textarea', required: true, full: true },
            ],
            submitText: 'Lưu thay đổi & ghi log',
            onSubmit: (data) => api.put(tpl(page.api.record, data.get('session_id')), {
                items: [{ enrollment_id: x.enrollment_id, status: data.get('status'), minutes_late: data.get('status') === 'late' ? Number(data.get('minutes_late') || 0) : null }],
                reason: data.get('reason'),
            }),
        });

        if (saved) {
            toast('Đã điều chỉnh bản ghi điểm danh.', 'success');
            await load();
        }
    }

    async function exempt(x) {
        const turnOn = !x.exempted;
        const answer = await confirmDialog({
            title: turnOn ? `Miễn cấm thi cho ${x.student?.full_name}?` : `Bỏ miễn cấm thi của ${x.student?.full_name}?`,
            message: turnOn ? 'Ngoại lệ do Phòng Đào tạo xét (BR-ATT-03), ghi nhật ký.' : 'Sinh viên sẽ bị xét cấm thi lại theo tỷ lệ vắng.',
            confirmText: turnOn ? 'Miễn cấm thi' : 'Bỏ miễn',
            input: turnOn ? { label: 'Lý do / số văn bản', required: true } : null,
        });

        if (!answer) {
            return;
        }

        try {
            await api.post(tpl(page.api.exempt, x.id), { exempted: turnOn, reason: answer.value || null });
            toast(turnOn ? 'Đã miễn cấm thi.' : 'Đã bỏ miễn cấm thi.', 'success');
            await load();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    /** Khóa / mở khóa sổ khi đang lọc một lớp học phần. */
    async function renderLock() {
        const box = region('lock');
        box.replaceChildren();
        const sectionId = form.elements.course_section_id.value;

        if (!page.can.approve || !sectionId) {
            return;
        }

        const data = await api.get(tpl(page.api.sessions, sectionId)).catch(() => null);

        if (!data) {
            return;
        }

        box.append(el('button', { type: 'button', class: `h-9 px-3 rounded-lg border text-sm font-semibold ${data.locked ? 'border-amber-300 text-amber-800' : 'border-outline-variant'}`, onclick: (e) => toggleLock(e.currentTarget, sectionId, data.locked) },
            data.locked ? 'Mở khóa sổ điểm danh' : 'Khóa sổ điểm danh lớp'));
    }

    async function toggleLock(button, sectionId, locked) {
        const answer = await confirmDialog({
            title: locked ? 'Mở khóa sổ điểm danh?' : 'Khóa sổ điểm danh lớp này?',
            message: locked ? 'Giảng viên sẽ sửa lại được trong khung thời gian cho phép.' : 'Sau khi khóa, chỉ Phòng Đào tạo sửa được và phải ghi lý do.',
            confirmText: locked ? 'Mở khóa' : 'Khóa sổ',
            input: locked ? { label: 'Lý do mở khóa', required: true } : null,
        });

        if (!answer) {
            return;
        }

        try {
            await withBusy(button, () => api.post(tpl(locked ? page.api.unlock : page.api.lock, sectionId), locked ? { reason: answer.value } : {}));
            toast(locked ? 'Đã mở khóa sổ.' : 'Đã khóa sổ điểm danh.', 'success');
            renderLock();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function countRequests() {
        try {
            const data = await api.get(page.api.requests);
            root.querySelector('[data-field="request-count"]').textContent = data.total ? `(${data.total})` : '';
        } catch {
            // Không có quyền xem thì bỏ qua
        }
    }

    async function loadRequests() {
        region('requests').replaceChildren(loadingBlock(2));

        try {
            const list = rows(await api.get(page.api.requests));
            region('requests').replaceChildren(list.length
                ? el('div', { class: 'space-y-3' }, list.map((r) => el('article', { class: 'rounded-xl border border-outline-variant p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-3' },
                    el('div', { class: 'space-y-1 text-sm' },
                        el('p', { class: 'font-semibold' }, `${r.student?.full_name ?? ''} `, el('span', { class: 'font-mono text-xs text-primary' }, r.student?.student_code ?? '')),
                        el('p', { class: 'text-xs text-secondary' }, `Lớp ${r.section?.code ?? ''} · buổi ${dmy(r.session?.date)} (tiết ${r.session?.periods ?? ''}) · ${r.from_label} → ${r.to_label}${r.minutes_late ? ` (${r.minutes_late} phút)` : ''}`),
                        el('p', { class: 'text-xs' }, `Lý do: ${r.reason} · ${r.requested_by ?? ''} gửi lúc ${r.created_at ?? ''}`)),
                    page.can.approve ? el('div', { class: 'flex gap-2' },
                        el('button', { type: 'button', class: 'rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white', onclick: (e) => decide(e.currentTarget, r, true) }, 'Duyệt'),
                        el('button', { type: 'button', class: 'rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700', onclick: (e) => decide(e.currentTarget, r, false) }, 'Từ chối')) : null)))
                : emptyBlock('Không có yêu cầu sửa điểm danh chờ duyệt', '', 'task_alt'));
        } catch (error) {
            region('requests').replaceChildren(errorBlock(error, loadRequests));
        }
    }

    async function decide(button, r, approve) {
        const answer = await confirmDialog({
            title: approve ? 'Duyệt yêu cầu sửa điểm danh?' : 'Từ chối yêu cầu?',
            confirmText: approve ? 'Duyệt' : 'Từ chối',
            danger: !approve,
            input: { label: approve ? 'Ghi chú (không bắt buộc)' : 'Lý do từ chối', required: !approve },
        });

        if (!answer) {
            return;
        }

        try {
            await withBusy(button, () => api.post(tpl(approve ? page.api.approve : page.api.reject, r.id), { note: answer.value || null }));
            toast(approve ? 'Đã duyệt yêu cầu.' : 'Đã từ chối yêu cầu.', 'success');
            await Promise.all([loadRequests(), countRequests(), load()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function exportList(button) {
        try {
            const result = await withBusy(button, () => api.download(page.api.export, { ...filters(), format: 'xlsx' }, 'chuyen-can.xlsx'), 'Đang xuất…');
            toast(result.queued ? 'Danh sách đang được xuất nền.' : `Đã tải ${result.filename}.`, 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }
}
