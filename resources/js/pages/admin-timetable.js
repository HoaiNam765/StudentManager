import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const STATUS_CLASS = { draft: 'bg-slate-100 text-slate-800', published: 'bg-emerald-50 text-emerald-800', locked: 'bg-red-50 text-red-800' };

export default function adminTimetable(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const termSelect = root.querySelector('[data-filter="term"]');
    const sectionSelect = root.querySelector('[data-filter="section"]');
    const form = root.querySelector('[data-form="rule"]');
    const state = { terms: [], sections: [], rooms: [], periods: [], editId: null, timetableStatus: null };
    const url = (template, id) => template.replace('__ID__', encodeURIComponent(id)).replace('__TERM__', encodeURIComponent(id));

    termSelect.addEventListener('change', termChanged);
    sectionSelect.addEventListener('change', loadSection);
    root.querySelector('[data-action="reload"]').addEventListener('click', loadSection);
    root.querySelector('[data-action="check-conflicts"]').addEventListener('click', loadConflicts);
    root.querySelector('[data-action="generate"]')?.addEventListener('click', generateSessions);
    root.querySelector('[data-action="cancel-edit"]')?.addEventListener('click', resetForm);
    form?.addEventListener('submit', saveRule);

    bootstrap();

    async function bootstrap() {
        region('rules').replaceChildren(loadingBlock(4));
        try {
            const [years, rooms, periods] = await Promise.all([api.get(page.api.terms), api.get(page.api.rooms), api.get(page.api.periods)]);
            state.terms = (years?.data ?? []).flatMap((year) => (year.terms ?? []).map((term) => ({ ...term, academic_year: year.name })));
            state.rooms = rooms?.data ?? [];
            state.periods = periods?.data ?? [];
            fill(termSelect, state.terms, (x) => `${x.name} · ${x.academic_year}`);
            fill(form?.elements.room_id, state.rooms, (x) => `${x.code} · ${x.capacity} chỗ`, true, 'Không chọn / trực tuyến');
            fillPeriod(form?.elements.period_from); fillPeriod(form?.elements.period_to);
            await termChanged();
        } catch (error) { region('rules').replaceChildren(errorBlock(error, bootstrap)); }
    }

    async function termChanged() {
        const termId = termSelect.value;
        if (!termId) return;
        resetForm();
        region('rules').replaceChildren(loadingBlock(3));
        try {
            const [sections] = await Promise.all([api.get(page.api.sections, { term_id: termId })]);
            state.sections = sections?.data ?? [];
            fill(sectionSelect, state.sections, (x) => `${x.code} — ${x.subject?.code ?? ''} ${x.subject?.name ?? ''}`);
            await Promise.all([loadTermStatus(), loadSection()]);
        } catch (error) { showAlert(region('alert'), error); region('rules').replaceChildren(errorBlock(error, termChanged)); }
    }

    async function loadTermStatus() {
        try {
            const data = await api.get(url(page.api.timetableStatus, termSelect.value));
            state.timetableStatus = data.status;
            const actions = [];
            if (page.can.approve && data.status === 'draft') actions.push(el('button', { type: 'button', class: 'rounded-lg bg-primary text-white px-3 py-1.5 text-xs font-semibold', onclick: publishTimetable }, 'Công bố thời khóa biểu'));
            if (page.can.approve && data.status === 'published') actions.push(el('button', { type: 'button', class: 'rounded-lg bg-red-700 text-white px-3 py-1.5 text-xs font-semibold', onclick: lockTimetable }, 'Khóa thời khóa biểu'));
            region('term-status').replaceChildren(el('div', { class: 'flex flex-wrap items-center justify-between gap-3 rounded-xl bg-surface-container-low px-4 py-3' },
                el('div', { class: 'flex items-center gap-2' }, el('span', { class: `rounded-full px-2 py-1 text-xs font-semibold ${STATUS_CLASS[data.status] ?? ''}` }, data.status_label), el('span', { class: 'text-xs text-on-surface-variant' }, `Công bố: ${data.published_at ?? '—'} · Khóa: ${data.locked_at ?? '—'} · Báo trước ${data.change_notice_hours}h`)),
                el('div', { class: 'flex gap-2' }, actions)));
        } catch (error) { region('term-status').replaceChildren(errorBlock(error, loadTermStatus)); }
    }

    async function loadSection() {
        const id = sectionSelect.value;
        if (!id) { region('rules').replaceChildren(emptyBlock('Không có lớp trong học kỳ này')); region('conflicts').replaceChildren(); return; }
        region('rules').replaceChildren(loadingBlock(3));
        try {
            const data = await api.get(url(page.api.schedule, id));
            root.querySelector('[data-field="summary"]').textContent = `Đã xếp ${data.scheduled_periods}/${data.required_periods} tiết${data.warnings?.length ? ` · ${data.warnings.join(' · ')}` : ''}`;
            const rows = data.data ?? [];
            region('rules').replaceChildren(rows.length ? el('div', { class: 'space-y-3' }, rows.map(ruleCard)) : emptyBlock('Chưa xếp lịch cho lớp này', 'Thêm dòng lịch ở biểu mẫu bên trái.', 'calendar_add_on'));
            await loadConflicts();
        } catch (error) { region('rules').replaceChildren(errorBlock(error, loadSection)); }
    }

    function ruleCard(rule) {
        const actions = [];
        if (page.can.update) actions.push(el('button', { type: 'button', class: 'text-xs text-primary font-semibold hover:underline', onclick: () => beginEdit(rule) }, 'Sửa'));
        if (page.can.delete) actions.push(el('button', { type: 'button', class: 'text-xs text-red-700 font-semibold hover:underline', onclick: (e) => deleteRule(e.currentTarget, rule) }, 'Xóa'));
        return el('article', { class: 'rounded-xl border border-outline-variant p-4' },
            el('div', { class: 'flex items-start justify-between gap-3' },
                el('div', {}, el('p', { class: 'font-semibold' }, rule.description), el('p', { class: 'mt-1 text-xs text-on-surface-variant' }, `${rule.first_date} → ${rule.last_date} · ${rule.session_count} buổi${rule.note ? ` · ${rule.note}` : ''}`)),
                el('div', { class: 'flex gap-2' }, actions)));
    }

    function beginEdit(rule) {
        state.editId = rule.id;
        const values = ['day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'week_parity', 'note'];
        values.forEach((key) => { if (form.elements[key]) form.elements[key].value = rule[key] ?? ''; });
        form.elements.room_id.value = rule.room?.id ?? '';
        root.querySelector('[data-form-title]').textContent = `Sửa dòng lịch #${rule.id}`;
        root.querySelector('[data-action="cancel-edit"]').hidden = false;
        form.querySelector('button[type="submit"]').textContent = 'Lưu thay đổi';
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetForm() {
        if (!form) return;
        state.editId = null; form.reset();
        if (form.elements.week_from) form.elements.week_from.value = 1;
        if (form.elements.week_to) form.elements.week_to.value = 15;
        if (form.elements.week_parity) form.elements.week_parity.value = 'all';
        root.querySelector('[data-form-title]').textContent = 'Lịch học của lớp';
        root.querySelector('[data-action="cancel-edit"]').hidden = true;
        form.querySelector('button[type="submit"]').textContent = 'Lưu lịch';
    }

    async function saveRule(event) {
        event.preventDefault();
        const sectionId = sectionSelect.value; if (!sectionId) return;
        const button = form.querySelector('button[type="submit"]');
        const body = Object.fromEntries(new FormData(form).entries());
        ['day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'room_id'].forEach((k) => { if (body[k] !== '') body[k] = Number(body[k]); else delete body[k]; });
        body.urgent = Boolean(form.elements.urgent.checked);
        try {
            await withBusy(button, () => state.editId ? api.put(url(page.api.scheduleUpdate, state.editId), body) : api.post(url(page.api.scheduleStore, sectionId), body));
            toast(state.editId ? 'Đã cập nhật lịch.' : 'Đã thêm lịch.', 'success'); resetForm(); await loadSection();
        } catch (error) { showAlert(region('alert'), error); }
    }

    async function deleteRule(button, rule) {
        const prompt = state.timetableStatus === 'published'
            ? await confirmDialog({ title: 'Xóa lịch đã công bố?', danger: true, confirmText: 'Xóa', input: { label: 'Lý do thay đổi', required: true, maxlength: 500 } })
            : await confirmDialog({ title: 'Xóa dòng lịch này?', danger: true, confirmText: 'Xóa' });
        if (!prompt) return;
        try { await withBusy(button, () => api.del(url(page.api.scheduleDelete, rule.id), { reason: prompt.value ?? null })); toast('Đã xóa dòng lịch.', 'success'); await loadSection(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function loadConflicts() {
        const id = sectionSelect.value;
        if (!id) return;
        region('conflicts').replaceChildren(loadingBlock(2));
        try {
            const data = await api.get(url(page.api.conflicts, id));
            // ScheduleConflictService::sectionConflicts returns { block: string[], warn: string[] }.
            const rows = [
                ...(data.block ?? []).map((message) => ({ message, severity: 'block' })),
                ...(data.warn ?? []).map((message) => ({ message, severity: 'warn' })),
            ];
            region('conflicts').replaceChildren(rows.length
                ? el('div', { class: 'space-y-2' }, rows.map((x) => el('div', {
                    class: `rounded-lg border p-3 text-sm ${x.severity === 'block' ? 'border-red-300 bg-red-50 text-red-900' : 'border-amber-300 bg-amber-50 text-amber-900'}`,
                }, `${x.severity === 'block' ? 'Xung đột chặn' : 'Cảnh báo'}: ${x.message}`)))
                : emptyBlock('Không phát hiện xung đột', 'Không có xung đột mà backend nhận diện.', 'task_alt'));
        } catch (error) { region('conflicts').replaceChildren(errorBlock(error, loadConflicts)); }
    }

    async function publishTimetable(event) {
        const ok = await confirmDialog({ title: 'Công bố thời khóa biểu?', message: 'Sau khi công bố, sinh viên và giảng viên mới xem được lịch. Thay đổi tiếp theo phải có lý do.', confirmText: 'Công bố' });
        if (!ok) return;
        try { await withBusy(event.currentTarget, () => api.post(url(page.api.timetablePublish, termSelect.value), {})); toast('Đã công bố thời khóa biểu.', 'success'); await loadTermStatus(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function lockTimetable(event) {
        const ok = await confirmDialog({ title: 'Khóa thời khóa biểu?', message: 'Sau khi khóa không thể thêm, sửa, xóa lịch hoặc dạy bù bằng luồng thông thường.', danger: true, confirmText: 'Khóa' });
        if (!ok) return;
        try { await withBusy(event.currentTarget, () => api.post(url(page.api.timetableLock, termSelect.value), {})); toast('Đã khóa thời khóa biểu.', 'success'); await loadTermStatus(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function generateSessions(event) {
        const id = sectionSelect.value; if (!id) return;
        const ok = await confirmDialog({ title: 'Sinh lại các buổi học?', message: 'Backend giữ các buổi đã diễn ra/đã dạy và sinh lại các buổi tương lai theo mẫu lịch, có xét ngày nghỉ.', confirmText: 'Sinh buổi' });
        if (!ok) return;
        try { await withBusy(event.currentTarget, () => api.post(url(page.api.generateSessions, id), {})); toast('Đã sinh các buổi học.', 'success'); }
        catch (error) { showAlert(region('alert'), error); }
    }

    function fill(select, items, label, blank = false, blankText = '—') { if (!select) return; select.replaceChildren(); if (blank) select.append(el('option', { value: '' }, blankText)); items.forEach((x) => select.append(el('option', { value: x.id }, label(x)))); }
    function fillPeriod(select) { if (!select) return; select.replaceChildren(); state.periods.forEach((p) => select.append(el('option', { value: p.number }, `Tiết ${p.number} · ${p.starts_at}–${p.ends_at}`))); }
}
