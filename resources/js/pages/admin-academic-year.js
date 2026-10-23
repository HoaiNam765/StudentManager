/**
 * #9 — Năm học, học kỳ, lịch học vụ và ngày nghỉ.
 * Tất cả URL lấy từ pageData để không viết cứng endpoint; quyền vẫn được kiểm tra lại ở máy chủ.
 */
import { api } from '../api.js';
import { applyFieldErrors, clearFieldErrors, confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const TERM_STATUS = {
    planned: 'Dự kiến',
    registration: 'Đang đăng ký',
    in_progress: 'Đang diễn ra',
    exam_grading: 'Thi & nhập điểm',
    completed: 'Đã hoàn thành',
    locked: 'Đã khóa',
};

const STATUS_STYLE = {
    planned: 'bg-slate-50 text-slate-700 border-slate-200',
    registration: 'bg-blue-50 text-blue-800 border-blue-200',
    in_progress: 'bg-emerald-50 text-emerald-800 border-emerald-200',
    exam_grading: 'bg-amber-50 text-amber-900 border-amber-200',
    completed: 'bg-violet-50 text-violet-800 border-violet-200',
    locked: 'bg-slate-100 text-slate-900 border-slate-300',
};

export default function adminAcademicYear(root) {
    const page = pageData();
    const state = { years: [], terms: [], currentTerm: null, holidays: [] };
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const url = (template, id) => template.replace('__ID__', encodeURIComponent(id));
    const statusUrl = (id, status) => url(page.api.termStatus, id).replace('__STATUS__', encodeURIComponent(status));

    initTabs();
    applyPermissions();
    bindForms();
    root.querySelector('[data-action="reload"]').addEventListener('click', () => loadAll());
    root.querySelector('[data-action="reload-requests"]').addEventListener('click', () => loadRequests());
    root.querySelector('[data-input="milestone-term"]').addEventListener('change', () => loadMilestones());
    root.querySelector('[data-input="holiday-term"]').addEventListener('change', () => loadHolidays());
    root.querySelector('[data-action="copy-milestones"]').addEventListener('click', (event) => copyMilestones(event.currentTarget));
    root.querySelector('[data-action="year-cancel"]').addEventListener('click', resetYearForm);
    root.querySelector('[data-action="holiday-cancel"]').addEventListener('click', resetHolidayForm);

    loadAll();

    function initTabs() {
        root.querySelectorAll('[data-tab]').forEach((button) => {
            button.addEventListener('click', () => {
                const tab = button.dataset.tab;
                root.querySelectorAll('[data-panel]').forEach((panel) => {
                    panel.hidden = panel.dataset.panel !== tab;
                });
                root.querySelectorAll('[data-tab]').forEach((item) => {
                    const active = item.dataset.tab === tab;
                    item.className = active
                        ? 'rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white'
                        : 'rounded-lg border border-outline-variant bg-white px-4 py-2 text-sm font-semibold text-on-surface hover:bg-surface-container-low';
                });
                if (tab === 'requests') loadRequests();
            });
        });
    }

    function applyPermissions() {
        if (!page.can?.create) {
            root.querySelectorAll('[data-write="create"]').forEach((node) => node.hidden = true);
        }
        if (!page.can?.update) {
            root.querySelectorAll('[data-write="update"]').forEach((node) => node.hidden = true);
        }
        if (!page.can?.approve) {
            root.querySelector('[data-tab="requests"]').hidden = true;
        }
    }

    function bindForms() {
        root.querySelector('[data-form="year"]').addEventListener('submit', saveYear);
        root.querySelector('[data-form="term"]').addEventListener('submit', saveTerm);
        root.querySelector('[data-form="milestones"]').addEventListener('submit', saveMilestones);
        root.querySelector('[data-form="holiday"]').addEventListener('submit', saveHoliday);
    }

    async function loadAll() {
        showAlert(region('alert'), null);
        region('years').replaceChildren(loadingBlock(4));
        field('current-term').textContent = 'Đang tải…';

        try {
            const [yearsResponse, currentTerm] = await Promise.all([
                api.get(page.api.academicYears),
                api.get(page.api.currentTerm),
            ]);
            state.years = yearsResponse?.data ?? [];
            state.terms = state.years.flatMap((year) => (year.terms ?? []).map((term) => ({ ...term, academic_year_name: year.name })));
            state.currentTerm = currentTerm;
            renderCurrentTerm();
            renderYears();
            populateTermSelects();
            await Promise.all([loadMilestones(), loadHolidays()]);
        } catch (error) {
            region('years').replaceChildren(errorBlock(error, () => loadAll()));
            field('current-term').textContent = 'Không tải được';
            showAlert(region('alert'), error);
        }
    }

    function renderCurrentTerm() {
        field('current-term').textContent = state.currentTerm
            ? `${state.currentTerm.name} · ${state.currentTerm.academic_year?.name ?? ''}`.replace(/ · $/, '')
            : 'Chưa đặt';
    }

    function renderYears() {
        field('year-count').textContent = `${state.years.length} năm học`;
        if (state.years.length === 0) {
            region('years').replaceChildren(emptyBlock('Chưa có năm học', 'Tạo năm học đầu tiên để bắt đầu cấu hình học kỳ.', 'calendar_add_on'));
            return;
        }

        region('years').replaceChildren(el('div', { class: 'space-y-4' }, state.years.map(yearCard)));
    }

    function yearCard(year) {
        const terms = year.terms ?? [];
        const actions = el('div', { class: 'flex items-center gap-2' });
        if (page.can?.update) {
            actions.append(el('button', {
                type: 'button',
                class: 'rounded-lg border border-outline-variant px-2.5 py-1.5 text-xs font-semibold hover:bg-surface-container-low',
                onclick: () => editYear(year),
            }, 'Sửa năm học'));
        }

        return el('article', { class: 'rounded-xl border border-outline-variant bg-white p-4 space-y-3' },
            el('div', { class: 'flex flex-wrap items-start justify-between gap-3' },
                el('div', {},
                    el('h3', { class: 'font-bold text-on-surface' }, year.name),
                    el('p', { class: 'text-xs text-on-surface-variant' }, `${dateText(year.start_date)} → ${dateText(year.end_date)}`)),
                actions),
            terms.length === 0
                ? emptyBlock('Chưa có học kỳ', 'Tạo học kỳ bằng biểu mẫu bên phải.', 'event_busy')
                : el('div', { class: 'grid gap-2' }, terms.map(termRow)));
    }

    function termRow(term) {
        const current = Number(state.currentTerm?.id) === Number(term.id);
        const actions = el('div', { class: 'flex flex-wrap gap-1.5 justify-end' });

        if (page.can?.approve) {
            if (term.status === 'planned') actions.append(actionButton('Mở đăng ký', () => changeTermStatus(term, 'registration')));
            if (term.status === 'registration' && term.type === 'main') actions.append(actionButton('Đặt hiện hành', () => setCurrentTerm(term), true));
            if (term.status === 'in_progress') actions.append(actionButton('Thi & nhập điểm', () => changeTermStatus(term, 'exam_grading')));
            if (term.status === 'exam_grading') actions.append(actionButton('Hoàn thành', () => changeTermStatus(term, 'completed')));
        }

        return el('div', { class: 'rounded-lg border border-outline-variant bg-surface-container-low/40 px-3 py-3 flex flex-col lg:flex-row lg:items-center justify-between gap-3' },
            el('div', { class: 'min-w-0' },
                el('div', { class: 'flex flex-wrap items-center gap-2' },
                    el('strong', { class: 'text-sm text-on-surface' }, term.name),
                    el('span', { class: `rounded-full border px-2 py-0.5 text-[11px] font-semibold ${STATUS_STYLE[term.status] ?? ''}` }, TERM_STATUS[term.status] ?? term.status),
                    current ? el('span', { class: 'rounded-full bg-primary px-2 py-0.5 text-[11px] font-semibold text-white' }, 'Hiện hành') : null),
                el('p', { class: 'mt-1 text-xs text-on-surface-variant' }, `${term.type === 'summer' ? 'Học kỳ hè' : 'Học kỳ chính'} · ${dateText(term.start_date)} → ${dateText(term.end_date)} · ${term.weeks} tuần`)),
            actions);
    }

    function actionButton(label, onclick, primary = false) {
        return el('button', {
            type: 'button',
            class: primary
                ? 'rounded-lg bg-primary px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-primary-container'
                : 'rounded-lg border border-outline-variant bg-white px-2.5 py-1.5 text-xs font-semibold hover:bg-surface-container-low',
            onclick,
        }, label);
    }

    function editYear(year) {
        const form = root.querySelector('[data-form="year"]');
        form.elements.id.value = year.id;
        form.elements.name.value = year.name ?? '';
        form.elements.start_date.value = isoDate(year.start_date);
        form.elements.end_date.value = isoDate(year.end_date);
        root.querySelector('[data-action="year-cancel"]').hidden = false;
        form.elements.name.focus();
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetYearForm() {
        const form = root.querySelector('[data-form="year"]');
        form.reset();
        form.elements.id.value = '';
        root.querySelector('[data-action="year-cancel"]').hidden = true;
        clearFieldErrors(form);
        showAlert(region('year-form-alert'), null);
    }

    async function saveYear(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const button = form.querySelector('[data-action="year-save"]');
        const id = form.elements.id.value;
        const body = {
            name: form.elements.name.value.trim(),
            start_date: form.elements.start_date.value,
            end_date: form.elements.end_date.value,
        };
        clearFieldErrors(form);
        showAlert(region('year-form-alert'), null);

        try {
            await withBusy(button, () => id ? api.put(url(page.api.academicYear, id), body) : api.post(page.api.academicYears, body));
            toast(id ? 'Đã cập nhật năm học.' : 'Đã tạo năm học.', 'success');
            resetYearForm();
            await loadAll();
        } catch (error) {
            handleFormError(form, region('year-form-alert'), error);
        }
    }

    async function saveTerm(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const button = form.querySelector('[data-action="term-save"]');
        clearFieldErrors(form);
        showAlert(region('term-form-alert'), null);
        const body = {
            academic_year_id: Number(form.elements.academic_year_id.value),
            name: form.elements.name.value.trim(),
            type: form.elements.type.value,
            start_date: form.elements.start_date.value,
            end_date: form.elements.end_date.value,
            weeks: Number(form.elements.weeks.value),
        };

        try {
            await withBusy(button, () => api.post(page.api.termsStore, body));
            toast('Đã tạo học kỳ.', 'success');
            form.reset();
            form.elements.weeks.value = 15;
            await loadAll();
        } catch (error) {
            handleFormError(form, region('term-form-alert'), error);
        }
    }

    async function setCurrentTerm(term) {
        const confirmed = await confirmDialog({
            title: `Đặt ${term.name} làm học kỳ hiện hành?`,
            message: 'Backend chỉ cho phép học kỳ chính ở trạng thái Đang đăng ký và đã tới ngày bắt đầu. Sau thao tác, trạng thái chuyển sang Đang diễn ra.',
            confirmText: 'Đặt hiện hành',
        });
        if (!confirmed) return;

        try {
            await api.post(url(page.api.termCurrent, term.id));
            toast('Đã đặt học kỳ hiện hành.', 'success');
            await loadAll();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function changeTermStatus(term, status) {
        const confirmed = await confirmDialog({
            title: `Chuyển ${term.name} sang “${TERM_STATUS[status]}”?`,
            message: 'Trạng thái học kỳ chỉ được chuyển theo đúng workflow của backend và có thể không hoàn tác trực tiếp.',
            confirmText: 'Chuyển trạng thái',
        });
        if (!confirmed) return;

        try {
            await api.post(statusUrl(term.id, status));
            toast('Đã chuyển trạng thái học kỳ.', 'success');
            await loadAll();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    function populateTermSelects() {
        const options = state.terms.map((term) => ({ value: String(term.id), label: `${term.academic_year_name} · ${term.name}` }));
        fillSelect(root.querySelector('[data-input="milestone-term"]'), options, false);
        fillSelect(root.querySelector('[data-input="copy-from"]'), options, true, 'Chọn học kỳ nguồn');
        fillSelect(root.querySelector('[data-input="holiday-term"]'), options, true, 'Tất cả học kỳ');
        fillSelect(root.querySelector('[data-form="holiday"] [name="term_id"]'), options, true, 'Toàn trường');

        const yearOptions = state.years.map((year) => ({ value: String(year.id), label: year.name }));
        fillSelect(root.querySelector('[data-form="term"] [name="academic_year_id"]'), yearOptions, false);
    }

    function fillSelect(select, options, allowEmpty, emptyLabel = '—') {
        const previous = select.value;
        select.replaceChildren();
        if (allowEmpty) select.append(el('option', { value: '' }, emptyLabel));
        options.forEach((item) => select.append(el('option', { value: item.value }, item.label)));
        if ([...select.options].some((option) => option.value === previous)) select.value = previous;
    }

    async function loadMilestones() {
        const termId = root.querySelector('[data-input="milestone-term"]').value;
        if (!termId) {
            region('milestones').replaceChildren(emptyBlock('Chưa có học kỳ', 'Tạo học kỳ trước khi cấu hình lịch học vụ.', 'event_busy'));
            field('milestone-note').textContent = '';
            field('pending-count').textContent = '';
            return;
        }
        region('milestones').replaceChildren(loadingBlock(4));
        showAlert(region('milestone-form-alert'), null);

        try {
            const data = await api.get(url(page.api.milestones, termId));
            field('milestone-note').textContent = data.started
                ? 'Học kỳ đã bắt đầu: thay đổi sẽ tạo đề nghị chờ người có quyền duyệt.'
                : 'Học kỳ chưa bắt đầu: thay đổi hợp lệ được áp dụng ngay.';
            field('pending-count').textContent = data.pending_requests ? `${data.pending_requests} đề nghị đang chờ` : '';
            region('milestones').replaceChildren(el('div', { class: 'grid gap-3 sm:grid-cols-2 xl:grid-cols-3' }, (data.milestones ?? []).map((item) =>
                el('label', { class: 'block rounded-lg border border-outline-variant bg-white p-3 text-sm font-medium text-on-surface' },
                    item.label,
                    el('input', {
                        type: 'date',
                        value: item.date ?? '',
                        dataset: { milestone: item.type },
                        disabled: !page.can?.update,
                        class: 'mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm disabled:bg-surface-container-low disabled:text-on-surface-variant',
                    }))));
            refreshCopyOptions(termId);
        } catch (error) {
            region('milestones').replaceChildren(errorBlock(error, () => loadMilestones()));
        }
    }

    function refreshCopyOptions(targetId) {
        const select = root.querySelector('[data-input="copy-from"]');
        const options = state.terms
            .filter((term) => String(term.id) !== String(targetId))
            .map((term) => ({ value: String(term.id), label: `${term.academic_year_name} · ${term.name}` }));
        fillSelect(select, options, true, 'Chọn học kỳ nguồn');
    }

    async function saveMilestones(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const termId = root.querySelector('[data-input="milestone-term"]').value;
        if (!termId) return;
        const button = form.querySelector('[data-action="milestones-save"]');
        const milestones = {};
        form.querySelectorAll('[data-milestone]').forEach((input) => milestones[input.dataset.milestone] = input.value || null);
        showAlert(region('milestone-form-alert'), null);

        try {
            const response = await withBusy(button, () => api.put(url(page.api.milestones, termId), {
                milestones,
                reason: form.elements.reason.value.trim() || null,
            }));
            toast(response?.message ?? 'Đã lưu lịch học vụ.', response?.request ? 'info' : 'success');
            form.elements.reason.value = '';
            await Promise.all([loadMilestones(), loadRequests()]);
        } catch (error) {
            showAlert(region('milestone-form-alert'), error);
        }
    }

    async function copyMilestones(button) {
        const termId = root.querySelector('[data-input="milestone-term"]').value;
        const fromId = root.querySelector('[data-input="copy-from"]').value;
        if (!termId || !fromId) {
            toast('Chọn học kỳ đích và học kỳ nguồn trước khi sao chép.', 'warning');
            return;
        }
        const confirmed = await confirmDialog({
            title: 'Sao chép lịch học vụ?',
            message: 'Các mốc được dời theo chênh lệch ngày bắt đầu giữa hai học kỳ. Hãy kiểm tra lại các ngày sau khi sao chép.',
            confirmText: 'Sao chép',
        });
        if (!confirmed) return;

        try {
            await withBusy(button, () => api.post(url(page.api.milestonesCopy, termId), { from_term_id: Number(fromId) }));
            toast('Đã sao chép lịch học vụ.', 'success');
            await loadMilestones();
        } catch (error) {
            showAlert(region('milestone-form-alert'), error);
        }
    }

    async function loadHolidays() {
        const termId = root.querySelector('[data-input="holiday-term"]').value;
        region('holidays').replaceChildren(loadingBlock(3));
        try {
            state.holidays = await api.get(page.api.holidays, { term_id: termId || undefined });
            renderHolidays();
        } catch (error) {
            region('holidays').replaceChildren(errorBlock(error, () => loadHolidays()));
        }
    }

    function renderHolidays() {
        if ((state.holidays ?? []).length === 0) {
            region('holidays').replaceChildren(emptyBlock('Chưa có ngày nghỉ', 'Thêm ngày nghỉ để hệ thống loại trừ khi sinh buổi học.', 'event_available'));
            return;
        }
        region('holidays').replaceChildren(el('div', { class: 'space-y-2' }, state.holidays.map((holiday) => {
            const actions = el('div', { class: 'flex flex-wrap gap-1.5' });
            if (page.can?.update) actions.append(actionButton('Sửa', () => editHoliday(holiday)));
            if (page.can?.delete) actions.append(el('button', {
                type: 'button',
                class: 'rounded-lg border border-red-300 px-2.5 py-1.5 text-xs font-semibold text-red-800 hover:bg-red-50',
                onclick: () => deleteHoliday(holiday),
            }, 'Xóa'));
            return el('article', { class: 'rounded-lg border border-outline-variant bg-white p-3 flex flex-col sm:flex-row sm:items-start justify-between gap-3' },
                el('div', {},
                    el('p', { class: 'font-semibold text-on-surface' }, holiday.name),
                    el('p', { class: 'text-xs text-on-surface-variant' }, `${holiday.type_label ?? holiday.type} · ${dateText(holiday.starts_on)} → ${dateText(holiday.ends_on)}`),
                    holiday.term_id ? el('p', { class: 'mt-1 text-xs text-primary' }, `Áp dụng học kỳ #${holiday.term_id}`) : el('p', { class: 'mt-1 text-xs text-secondary' }, 'Áp dụng toàn trường'),
                    holiday.note ? el('p', { class: 'mt-1 text-sm text-on-surface' }, holiday.note) : null),
                actions);
        })));
    }

    function editHoliday(holiday) {
        const form = root.querySelector('[data-form="holiday"]');
        form.elements.id.value = holiday.id;
        form.elements.name.value = holiday.name ?? '';
        form.elements.type.value = holiday.type ?? 'public_holiday';
        form.elements.term_id.value = holiday.term_id ?? '';
        form.elements.starts_on.value = isoDate(holiday.starts_on);
        form.elements.ends_on.value = isoDate(holiday.ends_on);
        form.elements.note.value = holiday.note ?? '';
        root.querySelector('[data-action="holiday-cancel"]').hidden = false;
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetHolidayForm() {
        const form = root.querySelector('[data-form="holiday"]');
        form.reset();
        form.elements.id.value = '';
        root.querySelector('[data-action="holiday-cancel"]').hidden = true;
        clearFieldErrors(form);
        showAlert(region('holiday-form-alert'), null);
    }

    async function saveHoliday(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const button = form.querySelector('[data-action="holiday-save"]');
        const id = form.elements.id.value;
        const body = {
            name: form.elements.name.value.trim(),
            type: form.elements.type.value,
            term_id: form.elements.term_id.value ? Number(form.elements.term_id.value) : null,
            starts_on: form.elements.starts_on.value,
            ends_on: form.elements.ends_on.value,
            note: form.elements.note.value.trim() || null,
        };
        clearFieldErrors(form);
        showAlert(region('holiday-form-alert'), null);

        try {
            await withBusy(button, () => id ? api.put(url(page.api.holiday, id), body) : api.post(page.api.holidays, body));
            toast(id ? 'Đã cập nhật ngày nghỉ.' : 'Đã thêm ngày nghỉ.', 'success');
            resetHolidayForm();
            await loadHolidays();
        } catch (error) {
            handleFormError(form, region('holiday-form-alert'), error);
        }
    }

    async function deleteHoliday(holiday) {
        const confirmed = await confirmDialog({
            title: `Xóa “${holiday.name}”?`,
            message: 'Ngày nghỉ bị xóa có thể ảnh hưởng việc sinh / cập nhật buổi học. Chỉ tiếp tục khi bạn chắc chắn.',
            confirmText: 'Xóa',
            danger: true,
        });
        if (!confirmed) return;
        try {
            await api.del(url(page.api.holidayDelete, holiday.id));
            toast('Đã xóa ngày nghỉ.', 'success');
            await loadHolidays();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function loadRequests() {
        if (!page.can?.approve) {
            region('requests').replaceChildren(emptyBlock('Bạn không có quyền duyệt', 'Trang này chỉ dành cho người có quyền ACY.approve.', 'lock'));
            return;
        }
        region('requests').replaceChildren(loadingBlock(3));
        try {
            const response = await api.get(page.api.changeRequests, { status: 'pending' });
            const items = response?.data ?? [];
            region('requests').replaceChildren(items.length === 0
                ? emptyBlock('Không có đề nghị đang chờ', 'Các thay đổi lịch của học kỳ đã bắt đầu sẽ xuất hiện tại đây.', 'task_alt')
                : el('div', { class: 'space-y-3' }, items.map(requestCard)));
        } catch (error) {
            region('requests').replaceChildren(errorBlock(error, () => loadRequests()));
        }
    }

    function requestCard(item) {
        const changes = Object.entries(item.changes ?? {}).map(([type, value]) =>
            el('li', { class: 'text-sm' }, `${type}: ${value?.old ?? '—'} → ${value?.new ?? '—'}`));
        return el('article', { class: 'rounded-xl border border-outline-variant bg-white p-4 space-y-3' },
            el('div', { class: 'flex flex-wrap items-start justify-between gap-2' },
                el('div', {},
                    el('p', { class: 'font-semibold text-on-surface' }, item.term?.name ?? `Học kỳ #${item.term?.id ?? ''}`),
                    el('p', { class: 'text-xs text-on-surface-variant' }, `Người đề nghị: ${item.requested_by?.name ?? '—'}`)),
                el('span', { class: 'rounded-full border border-amber-300 bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-900' }, 'Đang chờ')),
            item.reason ? el('p', { class: 'text-sm text-on-surface' }, el('strong', {}, 'Lý do: '), item.reason) : null,
            changes.length ? el('ul', { class: 'list-disc pl-5 space-y-1' }, changes) : null,
            el('div', { class: 'flex flex-wrap gap-2' },
                actionButton('Từ chối', () => rejectRequest(item)),
                actionButton('Duyệt', () => approveRequest(item), true)));
    }

    async function approveRequest(item) {
        const confirmed = await confirmDialog({ title: 'Duyệt thay đổi lịch học vụ?', message: 'Các mốc mới sẽ được áp dụng vào học kỳ.', confirmText: 'Duyệt' });
        if (!confirmed) return;
        try {
            await api.post(url(page.api.changeApprove, item.id), { note: null });
            toast('Đã duyệt đề nghị.', 'success');
            await Promise.all([loadRequests(), loadMilestones()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function rejectRequest(item) {
        const confirmed = await confirmDialog({
            title: 'Từ chối thay đổi lịch học vụ?',
            confirmText: 'Từ chối',
            danger: true,
            input: { label: 'Lý do từ chối', required: true, maxlength: 1000 },
        });
        if (!confirmed) return;
        try {
            await api.post(url(page.api.changeReject, item.id), { note: confirmed.value });
            toast('Đã từ chối đề nghị.', 'success');
            await loadRequests();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    function handleFormError(form, alertBox, error) {
        if (error?.isValidation) {
            const unmatched = applyFieldErrors(form, error.errors);
            showAlert(alertBox, unmatched.length ? unmatched.join('\n') : error.message);
            return;
        }
        showAlert(alertBox, error);
    }

    function dateText(value) {
        const date = isoDate(value);
        if (!date) return '—';
        const [year, month, day] = date.split('-');
        return `${day}/${month}/${year}`;
    }

    function isoDate(value) {
        return typeof value === 'string' ? value.slice(0, 10) : '';
    }
}
