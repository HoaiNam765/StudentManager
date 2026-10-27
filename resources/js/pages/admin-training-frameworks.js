import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';
import { dmy, fillSelect, formDialog, formJson, listAll, rows, tpl } from '../lookups.js';

/**
 * Chương trình khung theo khoa – khóa – học kỳ: danh sách khung, chi tiết học phần và gợi ý từ CTĐT.
 * Việc mở lớp / công bố được thực hiện ở trang Đăng ký học phần.
 */
export default function adminTrainingFrameworks(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const filters = {
        term: root.querySelector('[data-filter="term"]'),
        faculty: root.querySelector('[data-filter="faculty"]'),
        cohort: root.querySelector('[data-filter="cohort"]'),
    };
    const state = { terms: [], faculties: [], cohorts: [], subjects: [], selectedId: null };
    const termLabel = (t) => `${t.name} · ${t.academic_year}`;

    root.querySelector('[data-action="reload"]').addEventListener('click', loadList);
    root.querySelector('[data-action="create"]')?.addEventListener('click', createFramework);
    Object.values(filters).forEach((select) => select.addEventListener('change', loadList));

    bootstrap();

    async function bootstrap() {
        region('list').replaceChildren(loadingBlock(4));
        region('detail').replaceChildren(emptyBlock('Chọn một chương trình khung', 'Hoặc lập chương trình khung mới cho khoa – khóa – học kỳ.', 'account_tree'));
        try {
            const [years, faculties, cohorts] = await Promise.all([
                api.get(page.api.terms), listAll(page.api.faculties), listAll(page.api.cohorts),
            ]);
            state.terms = rows(years).flatMap((year) => (year.terms ?? []).map((term) => ({ ...term, academic_year: year.name })));
            state.faculties = faculties;
            state.cohorts = cohorts;
            fillSelect(filters.term, state.terms, termLabel);
            fillSelect(filters.faculty, state.faculties, (f) => `${f.code} – ${f.name}`);
            fillSelect(filters.cohort, state.cohorts, (c) => c.code);
            await loadList();
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, bootstrap));
        }
    }

    async function loadList() {
        region('list').replaceChildren(loadingBlock(4));
        showAlert(region('alert'), null);
        try {
            const list = rows(await api.get(page.api.list, { term_id: filters.term.value, faculty_id: filters.faculty.value, cohort_id: filters.cohort.value }));
            region('list').replaceChildren(list.length
                ? el('div', { class: 'space-y-2' }, list.map(card))
                : emptyBlock('Chưa có chương trình khung', page.can.create ? 'Bấm "Lập chương trình khung" để bắt đầu.' : 'Thay bộ lọc để xem khung khác.', 'account_tree'));
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, loadList));
        }
    }

    function card(item) {
        const active = state.selectedId === item.id;

        return el('button', {
            type: 'button',
            class: `w-full text-left rounded-xl border p-4 transition hover:border-primary/50 hover:bg-surface-container-low ${active ? 'border-primary bg-primary/5' : 'border-outline-variant'}`,
            onclick: () => { state.selectedId = item.id; loadList(); loadDetail(item.id); },
        },
        el('p', { class: 'font-bold text-on-surface' }, `${item.faculty?.name ?? '—'} · Khóa ${item.cohort?.code ?? '—'}`),
        el('p', { class: 'text-sm text-on-surface-variant' }, `${item.term?.name ?? '—'} · ${item.term?.academic_year ?? ''}`),
        el('p', { class: 'mt-1 text-xs text-on-surface-variant' }, `${item.items_count} học phần`));
    }

    async function loadDetail(id) {
        region('detail').replaceChildren(loadingBlock(4));
        try {
            region('detail').replaceChildren(renderDetail(await api.get(tpl(page.api.show, id))));
        } catch (error) {
            region('detail').replaceChildren(errorBlock(error, () => loadDetail(id)));
        }
    }

    function renderDetail(fw, problems = null) {
        const alert = el('div', { hidden: true });
        if (problems) showAlert(alert, problems, 'warning');
        const actions = el('div', { class: 'flex flex-wrap gap-2' });
        const button = (label, iconName, onClick, primary = false) => el('button', {
            type: 'button',
            class: primary
                ? 'inline-flex items-center gap-1 rounded-lg bg-primary text-white px-3 py-2 text-sm font-semibold hover:bg-primary-container'
                : 'inline-flex items-center gap-1 rounded-lg border border-outline-variant px-3 py-2 text-sm font-semibold hover:bg-surface-container-low',
            onclick: (event) => onClick(event, fw, alert),
        }, icon(iconName, 'text-[18px]'), label);

        if (page.can.create) {
            actions.append(
                button('Gợi ý từ CTĐT', 'auto_awesome', suggest),
                button('Thêm học phần', 'add', addItem),
            );
        }
        if (page.can.create) { actions.append(button('Sửa ghi chú', 'edit', editFramework)); actions.append(button('Xóa khung', 'delete', removeFramework)); }

        return el('div', { class: 'space-y-5' },
            el('div', {},
                el('h3', { class: 'text-lg font-bold text-on-surface' }, `${fw.faculty?.name ?? '—'} · Khóa ${fw.cohort?.code ?? '—'}`),
                el('p', { class: 'text-sm text-on-surface-variant' },
                    `${fw.term?.name ?? '—'} · ${fw.term?.academic_year ?? ''} · bắt đầu học ${dmy(fw.term?.start_date)}`),
                el('div', { class: 'mt-3 grid sm:grid-cols-3 gap-2 text-sm' },
                    stat('Học kỳ thứ của khóa', fw.planned_term ?? '—'),
                    stat('Sinh viên khoa – khóa', fw.students),
                    stat('Học phần', fw.items.length)),
                fw.note ? el('p', { class: 'mt-2 text-sm text-on-surface' }, fw.note) : null),
            actions,
            alert,
            itemsTable(fw, alert));
    }

    function stat(label, value) {
        return el('div', { class: 'rounded-lg border border-outline-variant p-3' },
            el('p', { class: 'text-xs text-on-surface-variant' }, label),
            el('p', { class: 'text-base font-bold text-on-surface' }, String(value)));
    }

    function itemsTable(fw, alert) {
        if (!fw.items.length) {
            return emptyBlock('Chưa có học phần', page.can.create ? 'Bấm "Gợi ý từ CTĐT" hoặc "Thêm học phần".' : '', 'menu_book');
        }

        const th = (text, extra = '') => el('th', { scope: 'col', class: `px-3 py-2 text-left font-semibold text-on-surface-variant ${extra}` }, text);

        return el('div', { class: 'overflow-x-auto rounded-lg border border-outline-variant' },
            el('table', { class: 'w-full min-w-[760px] text-sm' },
                el('caption', { class: 'sr-only' }, 'Học phần trong chương trình khung'),
                el('thead', { class: 'bg-surface-container-low' }, el('tr', {}, th('Học phần'), th('TC', 'text-center'), th('Loại'), page.can.create ? th('') : null)),
                el('tbody', {}, fw.items.map((item) => el('tr', { class: 'border-t border-outline-variant align-top' },
                    el('td', { class: 'px-3 py-2' }, el('p', { class: 'font-semibold' }, item.subject.name), el('p', { class: 'text-xs text-secondary' }, item.subject.code), item.note ? el('p', { class: 'text-xs text-on-surface-variant' }, item.note) : null),
                    el('td', { class: 'px-3 py-2 text-center' }, item.subject.credits),
                    el('td', { class: 'px-3 py-2' }, item.compulsory ? 'Bắt buộc' : 'Tự chọn'),
                    page.can.create ? el('td', { class: 'px-3 py-2 whitespace-nowrap text-right' },
                        el('button', { type: 'button', class: 'text-primary text-xs font-semibold hover:underline mr-3', onclick: () => editItem(fw, item) }, 'Sửa'),
                        el('button', { type: 'button', class: 'text-error text-xs font-semibold hover:underline', onclick: () => removeItem(fw, item, alert) }, 'Xóa')) : null)))));
    }

    async function createFramework() {
        const done = await formDialog({
            title: 'Lập chương trình khung',
            description: 'Mỗi khoa – khóa – học kỳ có một chương trình khung.',
            fields: [
                { name: 'term_id', label: 'Học kỳ', type: 'select', required: true, value: filters.term.value, options: state.terms.map((t) => ({ value: t.id, label: termLabel(t) })) },
                { name: 'faculty_id', label: 'Khoa', type: 'select', required: true, value: filters.faculty.value, options: state.faculties.map((f) => ({ value: f.id, label: `${f.code} – ${f.name}` })) },
                { name: 'cohort_id', label: 'Khóa', type: 'select', required: true, value: filters.cohort.value, options: state.cohorts.map((c) => ({ value: c.id, label: c.code })) },
                { name: 'note', label: 'Ghi chú', type: 'textarea', full: true },
            ],
            submitText: 'Lập khung',
            onSubmit: (data) => api.post(page.api.store, formJson(data)),
        });

        if (done) {
            state.selectedId = done.id;
            toast('Đã lập chương trình khung. Bấm "Gợi ý từ CTĐT" để lấy học phần.', 'success');
            await loadList();
            await loadDetail(done.id);
        }
    }

    async function suggest(event, fw, alert) {
        try {
            const result = await withBusy(event.currentTarget, () => api.post(tpl(page.api.suggest, fw.id)));
            region('detail').replaceChildren(renderDetail(result.framework));
            toast(result.added
                ? `Đã thêm ${result.added} học phần của học kỳ ${result.planned_term} theo CTĐT (${result.students} sinh viên).`
                : (result.message ?? 'Không có học phần mới để thêm.'), result.added ? 'success' : 'info');
            loadList();
        } catch (error) {
            showAlert(alert, error);
        }
    }

    async function addItem(event, fw) {
        if (!state.subjects.length) {
            state.subjects = await listAll(page.api.subjects, { status: 'active' });
        }
        const existing = new Set(fw.items.map((i) => i.subject.id));
        const done = await formDialog({
            title: 'Thêm học phần vào chương trình khung',
            fields: [
                { name: 'subject_id', label: 'Học phần', type: 'select', required: true, full: true, options: state.subjects.filter((s) => !existing.has(s.id)).map((s) => ({ value: s.id, label: `${s.code} – ${s.name} (${s.credits} TC)` })) },
                { name: 'compulsory', label: 'Học phần bắt buộc', type: 'checkbox', value: true },
                { name: 'note', label: 'Ghi chú', type: 'textarea', full: true },
            ],
            submitText: 'Thêm',
            onSubmit: (data) => api.post(tpl(page.api.itemStore, fw.id), { ...formJson(data), compulsory: data.get('compulsory') === '1' }),
        });

        if (done) {
            region('detail').replaceChildren(renderDetail(done));
            loadList();
        }
    }

    async function editItem(fw, item) {
        const done = await formDialog({
            title: `Sửa ${item.subject.code} – ${item.subject.name}`,
            fields: [
                { name: 'compulsory', label: 'Học phần bắt buộc', type: 'checkbox', value: item.compulsory },
                { name: 'note', label: 'Ghi chú', type: 'textarea', full: true, value: item.note ?? '' },
            ],
            onSubmit: (data) => api.put(tpl(page.api.itemUpdate, item.id), { ...formJson(data, ['max_size', 'note']), compulsory: data.get('compulsory') === '1' }),
        });

        if (done) region('detail').replaceChildren(renderDetail(done));
    }

    async function removeItem(fw, item, alert) {
        const ok = await confirmDialog({ title: `Xóa ${item.subject.code} khỏi chương trình khung?`, confirmText: 'Xóa', danger: true });
        if (!ok) return;
        try {
            region('detail').replaceChildren(renderDetail(await api.del(tpl(page.api.itemDestroy, item.id))));
            loadList();
        } catch (error) {
            showAlert(alert, error);
        }
    }

    async function editFramework(event, fw) {
        const done = await formDialog({ title: 'Sửa chương trình khung', fields: [
            { name: 'note', label: 'Ghi chú', type: 'textarea', full: true, value: fw.note ?? '' },
        ], onSubmit: (data) => api.put(tpl(page.api.update, fw.id), formJson(data, ['note'])) });
        if (done) { region('detail').replaceChildren(renderDetail(done)); loadList(); }
    }

    async function removeFramework(event, fw, alert) {
        const ok = await confirmDialog({ title: 'Xóa chương trình khung này?', message: 'Chỉ xóa được khi khung chưa được dùng trong đợt đăng ký.', confirmText: 'Xóa', danger: true });
        if (!ok) return;
        try {
            await api.del(tpl(page.api.destroy, fw.id));
            state.selectedId = null;
            region('detail').replaceChildren(emptyBlock('Đã xóa chương trình khung', '', 'account_tree'));
            loadList();
        } catch (error) {
            showAlert(alert, error);
        }
    }
}
