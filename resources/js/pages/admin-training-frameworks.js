import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';
import { dmy, fillSelect, formDialog, formJson, listAll, rows, tpl } from '../lookups.js';

/**
 * Chương trình khung theo khoa – khóa – học kỳ: danh sách khung, chi tiết học phần, gợi ý từ CTĐT và tạo đợt đăng ký
 * kèm số lớp + giảng viên của khoa. Lịch học, thông báo xem trước và công bố làm tiếp ở trang Đăng ký học phần.
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
        // Chọn học kỳ + khoa (chưa chọn khóa): hiện từng khóa của khoa, bấm vào là mở / lập khung
        if (filters.term.value && filters.faculty.value && !filters.cohort.value) {
            return loadCohorts();
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
            toast(fw.items.length ? `Đã lập khung khóa ${option.cohort.code}: ${fw.items.length} môn của học kỳ ${fw.planned_term} theo CTĐT. Bấm "Tạo đợt đăng ký" để nhập số lớp và giảng viên.` : (fw.message ?? 'Đã lập khung; thêm học phần thủ công.'), fw.items.length ? 'success' : 'info');
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
        if (page.can.openRegistration && fw.items.length) actions.prepend(button('Tạo đợt đăng ký', 'event_available', openRegistration, true));

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

    /**
     * Tạo đợt đăng ký từ khung: thông tin đợt + mỗi học phần số lớp, sĩ số, giảng viên từng lớp (giảng viên của khoa,
     * kèm số tiết đã nhận trong học kỳ). Lịch học xếp tiếp ở trang Đăng ký học phần.
     */
    async function openRegistration(event, fw, alert) {
        const button = event.currentTarget;
        let lecturers;
        try { lecturers = await withBusy(button, () => api.get(tpl(page.api.lecturers, fw.id))); }
        catch (error) { showAlert(alert, error); return; }
        if (!lecturers.length) { showAlert(alert, 'Khoa chưa có giảng viên đang công tác để phân công.', 'warning'); return; }

        const inputClass = 'mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm';
        const smallInput = 'w-20 rounded-lg border border-outline-variant bg-white px-2 py-1.5 text-sm';
        const lecturerLabel = (l) => `${l.full_name} (${l.code}) — ${l.load}/${l.limit} tiết`;
        // Ngày theo giờ máy người dùng (toISOString đổi sang UTC sẽ lệch một ngày)
        const ymd = (d) => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        const tomorrow = ymd(new Date(Date.now() + 86_400_000));
        const start = fw.term?.start_date ? new Date(`${fw.term.start_date}T00:00:00`) : null;
        const closeDay = start ? ymd(new Date(start.getTime() - 10 * 86_400_000)) : '';
        const field = (label, control) => el('label', { class: 'text-sm' }, label, control);
        const name = el('input', { name: 'name', required: true, class: inputClass, value: `Đăng ký ${fw.term?.name ?? ''} ${fw.term?.academic_year ?? ''} — ${fw.faculty?.code ?? ''} ${fw.cohort?.code ?? ''}`.replace(/\s+/g, ' ').trim() });
        const round = el('select', { name: 'round', class: inputClass },
            [['round_1', 'Vòng 1'], ['round_2', 'Vòng 2'], ['supplementary', 'Bổ sung']].map(([v, l]) => el('option', { value: v }, l)));
        const opens = el('input', { type: 'datetime-local', required: true, class: inputClass, value: `${tomorrow}T08:00` });
        const closes = el('input', { type: 'datetime-local', required: true, class: inputClass, value: closeDay ? `${closeDay}T23:00` : '' });
        const minCredits = el('input', { type: 'number', min: 0, class: inputClass });
        const maxCredits = el('input', { type: 'number', min: 1, class: inputClass });
        const overload = el('input', { type: 'checkbox', class: 'mr-2' });

        // Mỗi học phần: số lớp → bấy nhiêu ô chọn giảng viên (chia vòng tròn theo số tiết đang nhận, ít trước)
        const byLoad = [...lecturers].sort((a, b) => a.load - b.load);
        let turn = 0;
        const rowsOf = fw.items.map((item) => {
            const count = el('input', { type: 'number', min: 0, max: 20, value: item.planned_sections, class: smallInput, 'aria-label': `Số lớp ${item.subject.code}` });
            const size = el('input', { type: 'number', min: 1, value: item.max_size ?? item.default_max_size, class: smallInput, 'aria-label': `Sĩ số ${item.subject.code}` });
            const picks = el('div', { class: 'space-y-1' });
            const render = () => {
                const keep = [...picks.querySelectorAll('select')].map((s) => s.value);
                picks.replaceChildren(...Array.from({ length: Math.max(0, Number(count.value) || 0) }, (_, i) => {
                    const chosen = keep[i] ?? String(byLoad[turn++ % byLoad.length].id);
                    return el('select', { class: 'w-full rounded-lg border border-outline-variant bg-white px-2 py-1.5 text-sm', 'aria-label': `Giảng viên lớp ${i + 1} ${item.subject.code}` },
                        lecturers.map((l) => el('option', { value: l.id, selected: String(l.id) === chosen }, `Lớp ${i + 1}: ${lecturerLabel(l)}`)));
                }));
                if (!picks.children.length) picks.append(el('span', { class: 'text-xs text-on-surface-variant' }, 'Không mở lớp'));
            };
            count.addEventListener('input', render);
            render();

            return { item, count, size, picks, row: el('tr', { class: 'border-t border-outline-variant align-top' },
                el('td', { class: 'px-2 py-2' }, el('p', { class: 'font-semibold' }, item.subject.code), el('p', { class: 'text-xs text-on-surface-variant' }, `${item.subject.name} · ${item.subject.credits} TC`)),
                el('td', { class: 'px-2 py-2' }, count), el('td', { class: 'px-2 py-2' }, size), el('td', { class: 'px-2 py-2 min-w-[260px]' }, picks)) };
        });

        const message = el('div', { hidden: true });
        const save = el('button', { type: 'submit', class: 'rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold' }, 'Tạo đợt và mở lớp');
        const th = (text) => el('th', { scope: 'col', class: 'px-2 py-2 text-left text-xs font-semibold text-on-surface-variant' }, text);
        const form = el('form', { class: 'space-y-4' },
            el('h3', { class: 'font-bold text-base' }, `Tạo đợt đăng ký — ${fw.faculty?.name ?? ''} · Khóa ${fw.cohort?.code ?? ''}`),
            el('p', { class: 'text-xs text-on-surface-variant' }, `${fw.term?.name ?? ''} · ${fw.term?.academic_year ?? ''}, bắt đầu học ${dmy(fw.term?.start_date)}. Đợt chỉ dành cho sinh viên của khoa – khóa này. Sau khi tạo, xếp lịch từng lớp, thông báo xem trước và công bố ở trang Đăng ký học phần.`),
            message,
            el('div', { class: 'grid sm:grid-cols-2 gap-3' },
                el('div', { class: 'sm:col-span-2' }, field('Tên đợt', name)), field('Vòng', round), el('span'),
                field('Mở lúc (giờ Việt Nam)', opens), field('Đóng lúc — trước ngày bắt đầu học', closes),
                field('Tín chỉ tối thiểu (trống = theo quy chế)', minCredits), field('Tín chỉ tối đa (trống = theo quy chế)', maxCredits)),
            el('div', { class: 'overflow-x-auto rounded-lg border border-outline-variant' },
                el('table', { class: 'w-full min-w-[640px] text-sm' },
                    el('thead', { class: 'bg-surface-container-low' }, el('tr', {}, th('Học phần'), th('Số lớp'), th('Sĩ số tối đa'), th('Giảng viên của khoa (đã nhận / định mức tiết)'))),
                    el('tbody', {}, rowsOf.map((r) => r.row)))),
            el('label', { class: 'flex items-center text-sm' }, overload, 'Đồng ý phân công dù giảng viên vượt định mức tiết'),
            el('div', { class: 'flex justify-end gap-2' },
                el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold', onclick: () => dialog.close() }, 'Hủy'), save));
        const dialog = el('dialog', { class: 'm-auto w-[min(96vw,900px)] max-h-[92vh] overflow-y-auto rounded-xl border border-outline-variant bg-white p-5 shadow-xl backdrop:bg-black/40' }, form);
        dialog.addEventListener('close', () => dialog.remove());
        document.body.append(dialog);
        dialog.showModal();

        form.addEventListener('submit', async (submitEvent) => {
            submitEvent.preventDefault();
            showAlert(message, null);
            const classes = rowsOf.flatMap((r) => [...r.picks.querySelectorAll('select')].map((s) => ({ item_id: r.item.id, lecturer_id: Number(s.value), max_size: Number(r.size.value) || null })));
            const body = {
                name: name.value, round: round.value,
                opens_at: opens.value.replace('T', ' '), closes_at: closes.value.replace('T', ' '),
                min_credits: minCredits.value === '' ? null : Number(minCredits.value),
                max_credits: maxCredits.value === '' ? null : Number(maxCredits.value),
                classes, confirm_overload: overload.checked,
            };
            try {
                const period = await withBusy(save, () => api.post(tpl(page.api.openRegistration, fw.id), body));
                dialog.close();
                toast(`Đã tạo đợt "${period.name}" với ${period.sections} lớp. Xếp lịch từng lớp rồi thông báo xem trước.`, 'success');
                window.location.assign(`${page.links.enrollment}?term_id=${period.term_id}&period_id=${period.id}`);
            } catch (error) {
                showAlert(message, error);
                message.scrollIntoView({ block: 'nearest' });
            }
        });
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
