import { api } from '../api.js';
import { applyFieldErrors, clearFieldErrors, confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const NEXT = {
    planned: ['registration_open', 'Mở đăng ký'],
    registration_open: ['registration_closed', 'Đóng đăng ký'],
    registration_closed: ['in_progress', 'Bắt đầu học'],
    in_progress: ['finished', 'Kết thúc lớp'],
    finished: ['locked', 'Khóa lớp'],
};

const BADGE = {
    planned: 'bg-slate-100 text-slate-700',
    registration_open: 'bg-blue-50 text-blue-800',
    registration_closed: 'bg-amber-50 text-amber-800',
    in_progress: 'bg-emerald-50 text-emerald-800',
    finished: 'bg-violet-50 text-violet-800',
    locked: 'bg-slate-200 text-slate-800',
    cancelled: 'bg-red-50 text-red-800',
};

export default function adminCourseSections(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const tpl = (value, id, status = null) => value.replace('__ID__', encodeURIComponent(id)).replace('__STATUS__', encodeURIComponent(status ?? '')).replace('__TERM__', encodeURIComponent(id));
    const filters = {
        term: root.querySelector('[data-filter="term"]'),
        status: root.querySelector('[data-filter="status"]'),
        q: root.querySelector('[data-filter="q"]'),
    };
    const createForm = root.querySelector('[data-form="create"]');
    const state = { terms: [], subjects: [], lecturers: [], rooms: [], selectedId: null };

    root.querySelector('[data-action="reload"]').addEventListener('click', loadAll);
    filters.term.addEventListener('change', loadAll);
    filters.status.addEventListener('change', loadSections);
    filters.q.addEventListener('input', debounce(loadSections, 250));
    createForm?.addEventListener('submit', createSection);

    bootstrap();

    async function bootstrap() {
        region('list').replaceChildren(loadingBlock(4));
        region('under-min').replaceChildren(loadingBlock(2));
        region('detail').replaceChildren(emptyBlock('Chọn một lớp để xem chi tiết', '', 'groups'));
        try {
            const [years, subjects, lecturers, rooms] = await Promise.all([
                api.get(page.api.terms), api.get(page.api.subjects), api.get(page.api.lecturers), page.can.viewRooms ? api.get(page.api.rooms) : { data: [] },
            ]);
            state.terms = (years?.data ?? []).flatMap((year) => (year.terms ?? []).map((term) => ({ ...term, academic_year: year.name })));
            state.subjects = subjects?.data ?? [];
            state.lecturers = lecturers?.data ?? [];
            state.rooms = rooms?.data ?? [];
            fillSelects();
            await loadAll();
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, bootstrap));
            region('under-min').replaceChildren();
        }
    }

    function fillSelects() {
        fill(filters.term, state.terms, (x) => `${x.name} · ${x.academic_year}`, true);
        const termCreate = createForm?.elements.term_id;
        const subjectCreate = createForm?.elements.subject_id;
        if (termCreate) fill(termCreate, state.terms, (x) => `${x.name} · ${x.academic_year}`);
        if (subjectCreate) fill(subjectCreate, state.subjects, (x) => `${x.code} — ${x.name}`);
        if (!filters.term.value && state.terms[0]) filters.term.value = String(state.terms[0].id);
        if (termCreate && filters.term.value) termCreate.value = filters.term.value;
    }

    function fill(select, items, label, includeAll = false) {
        const current = select.value;
        select.replaceChildren();
        if (includeAll) select.append(el('option', { value: '' }, 'Tất cả học kỳ'));
        items.forEach((item) => select.append(el('option', { value: item.id }, label(item))));
        if ([...select.options].some((o) => o.value === current)) select.value = current;
    }

    async function loadAll() {
        await Promise.all([loadSections(), loadUnderMin()]);
    }

    async function loadSections() {
        region('list').replaceChildren(loadingBlock(4));
        showAlert(region('alert'), null);
        try {
            const result = await api.get(page.api.list, { term_id: filters.term.value, status: filters.status.value, q: filters.q.value.trim() });
            const rows = result?.data ?? [];
            region('list').replaceChildren(rows.length ? el('div', { class: 'grid md:grid-cols-2 gap-3' }, rows.map(sectionCard)) : emptyBlock('Chưa có lớp học phần', 'Thay bộ lọc hoặc mở lớp mới.', 'groups'));
            if (state.selectedId && rows.some((x) => x.id === state.selectedId)) loadDetail(state.selectedId);
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, loadSections));
        }
    }

    function sectionCard(item) {
        const main = item.instructors?.find((x) => x.role === 'main')?.lecturer;
        return el('button', {
            type: 'button',
            class: `text-left rounded-xl border p-4 hover:border-primary/50 hover:bg-surface-container-low transition ${state.selectedId === item.id ? 'border-primary bg-primary/5' : 'border-outline-variant'}`,
            onclick: () => { state.selectedId = item.id; loadDetail(item.id); loadSections(); },
        },
        el('div', { class: 'flex items-start justify-between gap-2' },
            el('div', {}, el('p', { class: 'font-bold text-on-surface' }, item.code), el('p', { class: 'text-sm text-on-surface-variant' }, `${item.subject?.code ?? ''} · ${item.subject?.name ?? ''}`)),
            el('span', { class: `rounded-full px-2 py-1 text-[11px] font-semibold ${BADGE[item.status] ?? 'bg-slate-100'}` }, item.status_label)),
        el('div', { class: 'mt-3 text-xs text-on-surface-variant space-y-1' },
            el('p', {}, `Học kỳ: ${item.term?.name ?? '—'} · ${item.term?.academic_year ?? ''}`),
            el('p', {}, `Sĩ số: ${item.min_size}–${item.max_size}`),
            el('p', {}, `Giảng viên chính: ${main ? `${main.code} · ${main.full_name}` : 'Chưa phân công'}`)));
    }

    async function loadDetail(id) {
        region('detail').replaceChildren(loadingBlock(3));
        try {
            const item = await api.get(tpl(page.api.show, id));
            region('detail').replaceChildren(renderDetail(item));
        } catch (error) {
            region('detail').replaceChildren(errorBlock(error, () => loadDetail(id)));
        }
    }

    function renderDetail(item) {
        const box = el('div', { class: 'space-y-5' });
        box.append(
            el('div', {},
                el('div', { class: 'flex items-start justify-between gap-2' },
                    el('div', {}, el('h3', { class: 'text-lg font-bold' }, item.code), el('p', { class: 'text-sm text-on-surface-variant' }, `${item.subject?.code ?? ''} · ${item.subject?.name ?? ''}`)),
                    el('span', { class: `rounded-full px-2 py-1 text-xs font-semibold ${BADGE[item.status] ?? ''}` }, item.status_label)),
                el('p', { class: 'mt-2 text-xs text-on-surface-variant' }, `${item.term?.name ?? '—'} · ${item.term?.academic_year ?? ''} · ${item.section_type} · ${item.delivery_mode}`),
                page.links?.detail ? el('a', { href: page.links.detail.replace('__ID__', encodeURIComponent(item.id)), class: 'mt-2 inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline' }, icon('open_in_new', 'text-[16px]'), 'Xem chi tiết lớp và danh sách sinh viên') : null),
        );

        if (page.can.update && ['planned', 'registration_open'].includes(item.status)) box.append(editForm(item));

        box.append(el('div', {}, el('h4', { class: 'font-semibold mb-2' }, 'Giảng viên'), instructorList(item)));
        if (page.can.update || page.can.approve) box.append(assignForm(item));

        const actions = el('div', { class: 'flex flex-wrap gap-2 pt-2 border-t border-outline-variant' });
        const next = NEXT[item.status];
        if (page.can.update && next) actions.append(el('button', { type: 'button', class: 'rounded-lg bg-primary text-white px-3 py-2 text-sm font-semibold', onclick: (e) => transition(e.currentTarget, item, next[0], next[1]) }, next[1]));
        if (page.can.update && ['planned', 'registration_open', 'registration_closed'].includes(item.status)) actions.append(el('button', { type: 'button', class: 'rounded-lg border border-red-300 text-red-800 px-3 py-2 text-sm font-semibold', onclick: (e) => cancelSection(e.currentTarget, item) }, 'Hủy lớp'));
        if (page.can.approve && item.status === 'locked') actions.append(el('button', { type: 'button', class: 'rounded-lg border border-amber-300 text-amber-800 px-3 py-2 text-sm font-semibold', onclick: (e) => unlockSection(e.currentTarget, item) }, 'Mở khóa'));
        if (page.can.delete && item.status === 'planned') actions.append(el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-3 py-2 text-sm font-semibold', onclick: (e) => destroySection(e.currentTarget, item) }, 'Xóa lớp nhập nhầm'));
        box.append(actions);
        return box;
    }

    function editForm(item) {
        const form = el('form', { class: 'grid grid-cols-2 gap-2 rounded-xl bg-surface-container-low p-3' },
            field('Sĩ số tối thiểu', 'min_size', item.min_size, 'number'), field('Sĩ số tối đa', 'max_size', item.max_size, 'number'),
            selectField('Loại lớp', 'section_type', [['theory', 'Lý thuyết'], ['practice', 'Thực hành'], ['mixed', 'Kết hợp']], item.section_type),
            selectField('Hình thức', 'delivery_mode', [['in_person', 'Trực tiếp'], ['online', 'Trực tuyến'], ['hybrid', 'Kết hợp']], item.delivery_mode),
            el('label', { class: 'col-span-2 text-xs' }, 'Ghi chú', el('textarea', { name: 'note', rows: 2, class: 'mt-1 w-full rounded-lg border border-outline-variant px-2 py-1.5' }, item.note ?? '')),
            el('div', { class: 'col-span-2 flex justify-end' }, el('button', { type: 'submit', class: 'rounded-lg border border-primary text-primary px-3 py-1.5 text-sm font-semibold' }, 'Lưu cấu hình')),
        );
        form.addEventListener('submit', (event) => updateSection(event, item));
        return form;
    }

    function instructorList(item) {
        if (!item.instructors?.length) return emptyBlock('Chưa phân công giảng viên', '', 'person_off');
        return el('div', { class: 'space-y-2' }, item.instructors.map((ins) => el('div', { class: 'flex items-center justify-between gap-2 rounded-lg border border-outline-variant p-2' },
            el('div', {}, el('p', { class: 'text-sm font-semibold' }, `${ins.lecturer?.code ?? ''} · ${ins.lecturer?.full_name ?? ''}`), el('p', { class: 'text-xs text-on-surface-variant' }, ins.role_label)),
            (page.can.update || page.can.approve) ? el('button', { type: 'button', class: 'text-xs text-red-700 hover:underline', onclick: (e) => unassign(e.currentTarget, item, ins) }, 'Gỡ') : null)));
    }

    function assignForm(item) {
        const form = el('form', { class: 'space-y-2 rounded-xl border border-outline-variant p-3' },
            el('p', { class: 'text-sm font-semibold' }, 'Phân công giảng viên'),
            selectFromItems('lecturer_id', state.lecturers, (x) => `${x.code} · ${x.full_name}`),
            selectField('', 'role', [['main', 'Giảng viên chính'], ['assistant', 'Trợ giảng']], 'main', true),
            el('label', { class: 'flex items-center gap-2 text-xs' }, el('input', { type: 'checkbox', name: 'confirm_overload', value: '1' }), 'Xác nhận nếu vượt định mức'),
            el('button', { type: 'submit', class: 'rounded-lg bg-secondary-container text-primary px-3 py-2 text-sm font-semibold' }, 'Phân công'),
        );
        form.addEventListener('submit', (event) => assign(event, item));
        return form;
    }

    async function createSection(event) {
        event.preventDefault();
        clearFieldErrors(createForm);
        const button = createForm.querySelector('button[type="submit"]');
        const body = formObject(createForm, ['min_size', 'max_size']);
        try {
            await withBusy(button, () => api.post(page.api.store, body));
            toast('Đã mở lớp học phần.', 'success');
            createForm.reset(); fillSelects(); await loadAll();
        } catch (error) {
            if (error.isValidation) applyFieldErrors(createForm, error.errors);
            showAlert(region('alert'), error);
        }
    }

    async function updateSection(event, item) {
        event.preventDefault();
        const form = event.currentTarget;
        const button = form.querySelector('button[type="submit"]');
        try {
            await withBusy(button, () => api.put(tpl(page.api.update, item.id), formObject(form, ['min_size', 'max_size'])));
            toast('Đã cập nhật lớp.', 'success'); await loadDetail(item.id); await loadSections();
        } catch (error) { showAlert(region('alert'), error); }
    }

    async function transition(button, item, status, label) {
        const ok = await confirmDialog({ title: `${label} ${item.code}?`, message: status === 'registration_open' ? 'Lớp phải có ít nhất một giảng viên chính trước khi mở đăng ký.' : 'Trạng thái chỉ được chuyển từng bước theo quy trình.', confirmText: label });
        if (!ok) return;
        try { await withBusy(button, () => api.post(tpl(page.api.status, item.id, status), {})); toast(`Đã chuyển: ${label}.`, 'success'); await loadAll(); await loadDetail(item.id); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function cancelSection(button, item) {
        const ok = await confirmDialog({ title: `Hủy lớp ${item.code}?`, danger: true, confirmText: 'Hủy lớp', input: { label: 'Lý do hủy', required: true, maxlength: 500 } });
        if (!ok) return;
        try { await withBusy(button, () => api.post(tpl(page.api.status, item.id, 'cancelled'), { reason: ok.value })); toast('Đã hủy lớp.', 'success'); await loadAll(); await loadDetail(item.id); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function unlockSection(button, item) {
        const ok = await confirmDialog({ title: `Mở khóa ${item.code}?`, confirmText: 'Mở khóa', input: { label: 'Lý do mở khóa', required: true, maxlength: 500 } });
        if (!ok) return;
        try { await withBusy(button, () => api.post(tpl(page.api.unlock, item.id), { reason: ok.value })); toast('Đã mở khóa lớp.', 'success'); await loadAll(); await loadDetail(item.id); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function destroySection(button, item) {
        const ok = await confirmDialog({ title: `Xóa lớp ${item.code}?`, message: 'Chỉ lớp Dự kiến chưa phát sinh dữ liệu liên quan mới xóa được.', danger: true, confirmText: 'Xóa' });
        if (!ok) return;
        try { await withBusy(button, () => api.del(tpl(page.api.destroy, item.id))); toast('Đã xóa lớp.', 'success'); state.selectedId = null; region('detail').replaceChildren(emptyBlock('Chọn một lớp để xem chi tiết')); await loadAll(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function assign(event, item) {
        event.preventDefault();
        const form = event.currentTarget; const button = form.querySelector('button[type="submit"]');
        const body = formObject(form, ['lecturer_id']); body.confirm_overload = Boolean(form.elements.confirm_overload.checked);
        try { await withBusy(button, () => api.post(tpl(page.api.assign, item.id), body)); toast('Đã phân công giảng viên.', 'success'); await loadDetail(item.id); await loadSections(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function unassign(button, item, ins) {
        const ok = await confirmDialog({ title: `Gỡ ${ins.lecturer?.full_name ?? 'giảng viên'}?`, message: 'Nếu đây là giảng viên chính cuối cùng của lớp đã mở đăng ký, hệ thống sẽ từ chối.' });
        if (!ok) return;
        try { await withBusy(button, () => api.del(tpl(page.api.unassign, ins.id))); toast('Đã gỡ phân công.', 'success'); await loadDetail(item.id); await loadSections(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function loadUnderMin() {
        const termId = filters.term.value;
        if (!termId) { region('under-min').replaceChildren(emptyBlock('Chọn một học kỳ để xem lớp thiếu sĩ số')); return; }
        region('under-min').replaceChildren(loadingBlock(2));
        try {
            const result = await api.get(tpl(page.api.underMin, termId));
            region('under-min').replaceChildren(result.data?.length ? el('div', { class: 'space-y-3' }, result.data.map(underMinCard)) : emptyBlock('Không có lớp dưới sĩ số tối thiểu', 'Có thể tiếp tục quy trình chốt đăng ký.', 'task_alt'));
        } catch (error) { region('under-min').replaceChildren(errorBlock(error, loadUnderMin)); }
    }

    function underMinCard(row) {
        const actions = el('div', { class: 'flex flex-wrap gap-2 mt-2' });
        if (page.can.update && row.merge_targets?.length) actions.append(el('button', { type: 'button', class: 'rounded-lg border border-primary text-primary px-3 py-1.5 text-xs font-semibold', onclick: (e) => mergeUnderMin(e.currentTarget, row) }, 'Gộp lớp'));
        if (page.can.update) actions.append(el('button', { type: 'button', class: 'rounded-lg border border-red-300 text-red-800 px-3 py-1.5 text-xs font-semibold', onclick: (e) => cancelUnderMin(e.currentTarget, row) }, 'Hủy lớp'));
        return el('article', { class: 'rounded-xl border border-outline-variant p-3' },
            el('div', { class: 'flex justify-between gap-3' }, el('div', {}, el('p', { class: 'font-semibold' }, `${row.code} · ${row.subject?.code ?? ''}`), el('p', { class: 'text-xs text-on-surface-variant' }, row.subject?.name ?? '')), el('span', { class: 'text-sm font-bold text-red-700' }, `${row.enrolled}/${row.min_size}`)),
            el('p', { class: 'mt-1 text-xs text-on-surface-variant' }, `Đề xuất: ${row.suggestion}`), actions);
    }

    async function mergeUnderMin(button, row) {
        const targetText = row.merge_targets.map((x) => `${x.id}: ${x.code} (${x.enrolled}/${x.max_size})`).join('\n');
        const ok = await confirmDialog({ title: `Gộp lớp ${row.code}`, message: `Lớp đích khả dụng:\n${targetText}\n\nNhập: ID lớp đích | lý do`, input: { label: 'Ví dụ: 12 | Gộp do thiếu sĩ số', required: true, maxlength: 600 }, confirmText: 'Gộp lớp' });
        if (!ok) return;
        const [rawId, ...reasonParts] = ok.value.split('|'); const targetId = Number(rawId.trim()); const reason = reasonParts.join('|').trim();
        if (!targetId || !reason) { toast('Nhập đúng định dạng: ID lớp đích | lý do', 'error'); return; }
        try { await withBusy(button, () => api.post(tpl(page.api.merge, row.id), { target_id: targetId, reason })); toast('Đã gộp lớp.', 'success'); await loadAll(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function cancelUnderMin(button, row) {
        const ok = await confirmDialog({ title: `Hủy lớp ${row.code}?`, danger: true, confirmText: 'Hủy lớp', input: { label: 'Lý do', required: true, maxlength: 500 } });
        if (!ok) return;
        try { await withBusy(button, () => api.post(tpl(page.api.status, row.id, 'cancelled'), { reason: ok.value })); toast('Đã hủy lớp.', 'success'); await loadAll(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    function formObject(form, numbers = []) {
        const data = Object.fromEntries(new FormData(form).entries());
        for (const key of numbers) if (data[key] !== '' && data[key] !== undefined) data[key] = Number(data[key]);
        for (const key of Object.keys(data)) if (data[key] === '') delete data[key];
        return data;
    }

    function field(label, name, value, type = 'text') { return el('label', { class: 'text-xs' }, label, el('input', { name, type, value, min: type === 'number' ? 1 : null, class: 'mt-1 w-full rounded-lg border border-outline-variant px-2 py-1.5' })); }
    function selectField(label, name, options, current, compact = false) { const select = el('select', { name, class: `${compact ? '' : 'mt-1 '}w-full rounded-lg border border-outline-variant px-2 py-1.5 text-sm` }, options.map(([v, l]) => el('option', { value: v, selected: v === current }, l))); return label ? el('label', { class: 'text-xs' }, label, select) : select; }
    function selectFromItems(name, items, label) { return el('select', { name, required: true, class: 'w-full rounded-lg border border-outline-variant px-2 py-1.5 text-sm' }, el('option', { value: '' }, 'Chọn giảng viên'), items.map((x) => el('option', { value: x.id }, label(x)))); }
    function debounce(fn, ms) { let t; return () => { clearTimeout(t); t = setTimeout(fn, ms); }; }
}
