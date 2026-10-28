import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';
import { dmy, fillSelect, formDialog, formJson, listAll, rows, tpl } from '../lookups.js';

/**
 * Chương trình khung theo khoa – khóa – học kỳ: danh sách khung, chi tiết học phần, gợi ý từ CTĐT. Mở lớp, giảng viên,
 * lịch và công bố làm ở đợt đăng ký cả học kỳ (bảng Khoa × Khóa, trang Đăng ký học phần).
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
    Object.values(filters).forEach((select) => select.addEventListener('change', () => { state.selectedId = null; loadList(); }));

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
        // Chọn học kỳ + khoa (chưa chọn khóa): hiện từng khóa của khoa, bấm vào là mở / lập khung
        if (filters.term.value && filters.faculty.value && !filters.cohort.value) {
            return loadCohorts();
        }
        // Chọn học kỳ + khóa, "Tất cả khoa": hiện mọi khoa với môn theo học kỳ thứ n của khóa
        if (filters.term.value && filters.cohort.value && !filters.faculty.value) {
            return loadFaculties();
        }
        try {
            const list = rows(await api.get(page.api.list, { term_id: filters.term.value, faculty_id: filters.faculty.value, cohort_id: filters.cohort.value }));
            // Chọn đủ học kỳ – khoa – khóa thì mở luôn khung tương ứng
            if (list.length === 1 && Object.values(filters).every((f) => f.value) && state.selectedId !== list[0].id) {
                state.selectedId = list[0].id;
                loadDetail(list[0].id);
            }
            region('list').replaceChildren(list.length
                ? el('div', { class: 'space-y-2' }, list.map(card))
                : emptyBlock('Chưa có chương trình khung', page.can.create ? 'Bấm "Lập chương trình khung" để bắt đầu.' : 'Thay bộ lọc để xem khung khác.', 'account_tree'));
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, loadList));
        }
    }

    async function loadFaculties() {
        try {
            const query = { term_id: filters.term.value, cohort_id: filters.cohort.value };
            const rowsOf = await api.get(page.api.facultyOverview, query);
            const missing = rowsOf.filter((r) => !r.framework).length;
            const cohort = state.cohorts.find((c) => String(c.id) === filters.cohort.value);
            const createAll = page.can.create && missing
                ? el('button', {
                    type: 'button',
                    class: 'w-full inline-flex items-center justify-center gap-1 rounded-lg bg-primary text-white px-3 py-2 text-sm font-semibold hover:bg-primary-container',
                    onclick: (event) => createAllFaculties(event.currentTarget, query),
                }, icon('playlist_add', 'text-[18px]'), `Lập khung cho ${missing} khoa còn thiếu`)
                : null;

            region('list').replaceChildren(rowsOf.length
                ? el('div', { class: 'space-y-2' },
                    el('p', { class: 'text-xs text-on-surface-variant' }, `Khóa ${cohort?.code ?? ''}: mỗi khoa học các môn của học kỳ thứ ${rowsOf[0].planned_term ?? '—'} theo CTĐT. Bấm một khoa để mở khung; lớp và giảng viên nhập ở đợt đăng ký của học kỳ.`),
                    createAll,
                    rowsOf.map((row) => facultyCard(row)))
                : emptyBlock('Không có khoa nào', 'Khóa này chưa có CTĐT đang áp dụng.', 'account_tree'));

            if (!state.selectedId) region('detail').replaceChildren(facultyOverview(rowsOf, cohort));
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, loadFaculties));
        }
    }

    function facultyCard(row) {
        const fw = row.framework;
        const active = fw && state.selectedId === fw.id;

        return el('button', {
            type: 'button',
            class: `w-full text-left rounded-xl border p-4 transition hover:border-primary/50 hover:bg-surface-container-low ${active ? 'border-primary bg-primary/5' : 'border-outline-variant'}`,
            onclick: (event) => openFaculty(event.currentTarget, row),
        },
        el('div', { class: 'flex items-start justify-between gap-2' },
            el('p', { class: 'font-bold text-on-surface' }, row.faculty.name),
            el('span', { class: `rounded-full px-2 py-0.5 text-[11px] font-semibold ${fw ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700'}` }, fw ? `${fw.items_count} môn` : 'Chưa lập')),
        el('p', { class: 'text-sm text-on-surface-variant' }, `Học kỳ thứ ${row.planned_term ?? '—'} · ${row.students} sinh viên · ${row.subjects.length} môn theo CTĐT`));
    }

    /** Khung chi tiết khi chưa chọn khoa nào: môn của từng khoa theo học kỳ thứ n của khóa. */
    function facultyOverview(rowsOf, cohort) {
        if (!rowsOf.length) return emptyBlock('Chọn một khoa', '', 'account_tree');
        const th = (text, extra = '') => el('th', { scope: 'col', class: `px-3 py-2 text-left font-semibold text-on-surface-variant ${extra}` }, text);

        return el('div', { class: 'space-y-5' },
            el('h3', { class: 'text-lg font-bold text-on-surface' }, `Môn học theo khoa — Khóa ${cohort?.code ?? ''}`),
            rowsOf.map((row) => el('section', { class: 'rounded-xl border border-outline-variant' },
                el('div', { class: 'flex flex-wrap items-center justify-between gap-2 px-4 py-3 bg-surface-container-low rounded-t-xl' },
                    el('div', {},
                        el('p', { class: 'font-bold text-on-surface' }, row.faculty.name),
                        el('p', { class: 'text-xs text-on-surface-variant' }, `Học kỳ thứ ${row.planned_term ?? '—'} của khóa · ${row.students} sinh viên · ${row.subjects.reduce((n, x) => n + x.credits, 0)} TC${row.framework ? '' : ' · xem trước từ CTĐT'}`)),
                    el('button', {
                        type: 'button',
                        class: 'rounded-lg border border-outline-variant bg-white px-3 py-1.5 text-xs font-semibold text-primary hover:bg-surface-container-low',
                        onclick: (event) => openFaculty(event.currentTarget, row),
                    }, row.framework ? 'Mở khung' : (page.can.create ? 'Lập khung khoa này' : 'Chưa lập'))),
                el('div', { class: 'overflow-x-auto' },
                    el('table', { class: 'w-full min-w-[520px] text-sm' },
                        el('thead', {}, el('tr', {}, th('Mã HP'), th('Tên học phần'), th('TC', 'text-center'), th('Loại'))),
                        el('tbody', {}, row.subjects.map((x) => el('tr', { class: 'border-t border-outline-variant' },
                            el('td', { class: 'px-3 py-2 font-mono text-secondary' }, x.code),
                            el('td', { class: 'px-3 py-2' }, x.name),
                            el('td', { class: 'px-3 py-2 text-center' }, x.credits),
                            el('td', { class: 'px-3 py-2' }, x.compulsory ? 'Bắt buộc' : 'Tự chọn')))))))));
    }

    async function openFaculty(button, row) {
        if (row.framework) {
            state.selectedId = row.framework.id;
            await loadDetail(row.framework.id);
            return loadFaculties();
        }
        if (!page.can.create) {
            return showAlert(region('alert'), 'Khoa này chưa có chương trình khung. Phòng Đào tạo lập khung trước.', 'info');
        }
        try {
            const fw = await withBusy(button, () => api.post(page.api.store, { term_id: Number(filters.term.value), faculty_id: row.faculty.id, cohort_id: Number(filters.cohort.value) }));
            state.selectedId = fw.id;
            region('detail').replaceChildren(renderDetail(fw));
            toast(`Đã lập khung ${row.faculty.name}: ${fw.items.length} môn. Mở lớp ở đợt đăng ký của học kỳ.`, 'success');
            await loadFaculties();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function createAllFaculties(button, query) {
        try {
            const result = await withBusy(button, () => api.post(page.api.storeAll, { term_id: Number(query.term_id), cohort_id: Number(query.cohort_id) }));
            toast(`Đã lập khung cho ${result.created} khoa, môn lấy theo CTĐT.`, 'success');
            state.selectedId = null;
            await loadFaculties();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function loadCohorts() {
        try {
            const options = await api.get(page.api.cohortOptions, { term_id: filters.term.value, faculty_id: filters.faculty.value });
            region('list').replaceChildren(options.length
                ? el('div', { class: 'space-y-2' },
                    el('p', { class: 'text-xs text-on-surface-variant' }, 'Các khóa đang học của khoa. Bấm một khóa để xem chương trình khung; khóa chưa có khung thì hệ thống lập và lấy môn từ CTĐT.'),
                    options.map(cohortCard))
                : emptyBlock('Khoa chưa có sinh viên', 'Chọn khoa khác.', 'groups'));
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, loadCohorts));
        }
    }

    function cohortCard(option) {
        const fw = option.framework;
        const active = fw && state.selectedId === fw.id;
        const note = option.planned_term ? `Học kỳ thứ ${option.planned_term} của khóa` : 'Học kỳ hè / trước khi khóa nhập học';

        return el('button', {
            type: 'button',
            class: `w-full text-left rounded-xl border p-4 transition hover:border-primary/50 hover:bg-surface-container-low ${active ? 'border-primary bg-primary/5' : 'border-outline-variant'}`,
            onclick: (event) => openCohort(event.currentTarget, option),
        },
        el('div', { class: 'flex items-start justify-between gap-2' },
            el('p', { class: 'font-bold text-on-surface' }, `Khóa ${option.cohort.code}`),
            el('span', { class: `rounded-full px-2 py-0.5 text-[11px] font-semibold ${fw ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700'}` }, fw ? `${fw.items_count} môn` : 'Chưa lập')),
        el('p', { class: 'text-sm text-on-surface-variant' }, `${note} · ${option.students} sinh viên`));
    }

    async function openCohort(button, option) {
        if (option.framework) {
            state.selectedId = option.framework.id;
            await loadDetail(option.framework.id);
            return loadCohorts();
        }
        if (!page.can.create) {
            return showAlert(region('alert'), 'Khóa này chưa có chương trình khung. Phòng Đào tạo lập khung trước.', 'info');
        }
        try {
            const fw = await withBusy(button, () => api.post(page.api.store, { term_id: Number(filters.term.value), faculty_id: Number(filters.faculty.value), cohort_id: option.cohort.id }));
            state.selectedId = fw.id;
            region('detail').replaceChildren(renderDetail(fw));
            toast(fw.items.length ? `Đã lập khung khóa ${option.cohort.code}: ${fw.items.length} môn của học kỳ ${fw.planned_term} theo CTĐT. Mở lớp ở đợt đăng ký của học kỳ.` : (fw.message ?? 'Đã lập khung; thêm học phần thủ công.'), fw.items.length ? 'success' : 'info');
            await loadCohorts();
        } catch (error) {
            showAlert(region('alert'), error);
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
        // Lớp, giảng viên, lịch và công bố làm ở đợt đăng ký cả học kỳ (bảng Khoa × Khóa)
        if (page.can.enrollment) actions.prepend(el('a', { href: `${page.links.enrollment}?term_id=${fw.term?.id ?? ''}`, class: 'inline-flex items-center gap-1 rounded-lg bg-primary text-white px-3 py-2 text-sm font-semibold hover:bg-primary-container' }, icon('event_available', 'text-[18px]'), 'Mở lớp ở đợt đăng ký của học kỳ'));

        return el('div', { class: 'space-y-5' },
            el('div', {},
                el('h3', { class: 'text-lg font-bold text-on-surface' }, `${fw.faculty?.name ?? '—'} · Khóa ${fw.cohort?.code ?? '—'}`),
                el('p', { class: 'text-sm text-on-surface-variant' },
                    `${fw.term?.name ?? '—'} · ${fw.term?.academic_year ?? ''} · bắt đầu học ${dmy(fw.term?.start_date)}`),
                el('div', { class: 'mt-3 grid sm:grid-cols-3 gap-2 text-sm' },
                    stat('Kỳ CTĐT của khung', fw.planned_term ?? '—'),
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
                el('thead', { class: 'bg-surface-container-low' }, el('tr', {}, th('Học phần'), th('TC', 'text-center'), th('Loại'), th('Số lớp dự kiến', 'text-center'), th('Lớp đã mở'), page.can.create ? th('') : null)),
                el('tbody', {}, fw.items.map((item) => el('tr', { class: 'border-t border-outline-variant align-top' },
                    el('td', { class: 'px-3 py-2' }, el('p', { class: 'font-semibold' }, item.subject.name), el('p', { class: 'text-xs text-secondary' }, item.subject.code), item.note ? el('p', { class: 'text-xs text-on-surface-variant' }, item.note) : null),
                    el('td', { class: 'px-3 py-2 text-center' }, item.subject.credits),
                    el('td', { class: 'px-3 py-2' }, item.compulsory ? 'Bắt buộc' : 'Tự chọn'),
                    el('td', { class: 'px-3 py-2 text-center' }, `${item.planned_sections} × ${item.max_size ?? item.default_max_size} SV`),
                    el('td', { class: 'px-3 py-2 text-xs' }, item.sections.length
                        ? item.sections.map((s) => el('p', { class: s.status === 'cancelled' ? 'line-through text-on-surface-variant' : '' }, `${s.code} · ${s.status_label}`))
                        : el('span', { class: 'text-on-surface-variant' }, 'Chưa mở')),
                    page.can.create ? el('td', { class: 'px-3 py-2 whitespace-nowrap text-right' },
                        el('button', { type: 'button', class: 'text-primary text-xs font-semibold hover:underline mr-3', onclick: () => editItem(fw, item) }, 'Sửa'),
                        el('button', { type: 'button', class: 'text-error text-xs font-semibold hover:underline', onclick: () => removeItem(fw, item, alert) }, 'Xóa')) : null)))));
    }

    async function createFramework() {
        const done = await formDialog({
            title: 'Lập chương trình khung',
            description: 'Mỗi khoa – khóa – học kỳ có một chương trình khung; hệ thống lấy luôn các môn của học kỳ đó theo CTĐT.',
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
            toast(done.items?.length ? `Đã lập chương trình khung với ${done.items.length} môn theo CTĐT.` : (done.message ?? 'Đã lập chương trình khung; thêm học phần thủ công.'), done.items?.length ? 'success' : 'info');
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
                { name: 'planned_sections', label: 'Số lớp dự kiến', type: 'number', required: true, value: item.planned_sections, min: 1 },
                { name: 'max_size', label: 'Sĩ số tối đa mỗi lớp', type: 'number', value: item.max_size ?? '', placeholder: `Mặc định ${item.default_max_size}`, min: 1 },
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
