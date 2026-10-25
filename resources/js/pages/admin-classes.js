/**
 * Khóa tuyển sinh & lớp hành chính (FE #15, mock admin/khoa-va-lop-hanh-chinh): danh sách lớp theo phạm vi CLS.view,
 * thống kê sĩ số / CVHT, thêm – sửa – xóa lớp và khóa (FR-CLS-001, BR-CLS-01), thành viên lớp có xem trước khi thêm /
 * gỡ (FR-CLS-003), phân công / kết thúc cố vấn học tập (FR-CLS-004, BR-CLS-07, 08), xuất danh sách lớp.
 */
import { api } from '../api.js';
import { debounce, dmy, fillSelect, formDialog, formJson, listAll, pager, rows, statusBadge, tpl } from '../lookups.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

export default function adminClasses(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const stat = (name) => root.querySelector(`[data-stat="${name}"]`);
    const form = root.querySelector('[data-form="filters"]');
    const today = () => new Date().toISOString().slice(0, 10);
    const state = { page: 1, faculties: [], majors: [], cohorts: [], specializations: [], types: [], policySets: [], lecturers: null, selected: null };

    root.querySelectorAll('[data-tab]').forEach((tab) => tab.addEventListener('click', () => showTab(tab.dataset.tab)));
    form.addEventListener('submit', (event) => event.preventDefault());
    form.elements.q.addEventListener('input', debounce(() => loadClasses(1), 350));
    ['faculty_id', 'cohort_id', 'major_id', 'status'].forEach((name) => form.elements[name].addEventListener('change', () => loadClasses(1)));
    root.querySelector('[data-action="add-class"]')?.addEventListener('click', () => editClass(null));
    root.querySelector('[data-action="add-cohort"]')?.addEventListener('click', () => editCohort(null));

    showTab(window.location.hash === '#khoa' ? 'cohorts' : 'classes');
    bootstrap();

    function showTab(name) {
        root.querySelectorAll('[data-tab]').forEach((tab) => {
            tab.className = `rounded-lg px-4 py-2 text-sm font-semibold ${tab.dataset.tab === name ? 'bg-primary text-white' : 'border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low'}`;
        });
        root.querySelectorAll('[data-panel]').forEach((panel) => { panel.hidden = panel.dataset.panel !== name; });
    }

    async function bootstrap() {
        region('classes').replaceChildren(loadingBlock(4));

        try {
            const [faculties, majors, cohorts, specializations, types] = await Promise.all([
                listAll(page.api.faculties), listAll(page.api.majors), api.get(page.api.cohorts), listAll(page.api.specializations), api.get(page.api.trainingTypes),
            ]);
            Object.assign(state, { faculties, majors, cohorts: rows(cohorts), specializations, types: rows(types) });
            fillSelect(form.elements.faculty_id, faculties, (f) => f.name);
            fillSelect(form.elements.major_id, majors, (m) => m.name);
            fillSelect(form.elements.cohort_id, state.cohorts, (c) => `${c.code} (${c.start_year})`);
            state.policySets = rows(await api.get(page.api.policySets, { status: 'published' }).catch(() => []));
        } catch (error) {
            showAlert(region('alert'), error);
        }

        renderCohorts();
        await Promise.all([loadClasses(1), loadStats()]);
    }

    // ---- Thống kê -----------------------------------------------------------------------------------------------

    async function loadStats() {
        try {
            const [all, missing] = await Promise.all([listAll(page.api.adminClasses), api.get(page.api.missingAdvisors)]);
            const active = all.filter((c) => c.status === 'active');
            const students = all.reduce((sum, c) => sum + (c.current_students_count ?? 0), 0);
            const activeStudents = active.reduce((sum, c) => sum + (c.current_students_count ?? 0), 0);
            const missingCount = rows(missing).length;
            stat('classes').textContent = all.length.toLocaleString('vi-VN');
            stat('active').textContent = `${active.length} lớp đang hoạt động`;
            stat('students').textContent = students.toLocaleString('vi-VN');
            stat('average').textContent = active.length ? (activeStudents / active.length).toFixed(1) : '0';
            stat('coverage').textContent = active.length ? `${Math.round(((active.length - missingCount) * 100) / active.length)}%` : '—';
            stat('missing').textContent = missingCount === 0 ? 'Đã phân công đầy đủ' : `${missingCount} lớp chưa có CVHT`;
            root.querySelector('[data-count="classes"]').textContent = `(${all.length})`;
        } catch {
            stat('classes').textContent = '—';
        }
    }

    // ---- Lớp hành chính -----------------------------------------------------------------------------------------

    async function loadClasses(target = 1) {
        state.page = target;
        region('classes').replaceChildren(loadingBlock(4));

        try {
            const params = Object.fromEntries([...new FormData(form).entries()].filter(([, v]) => v !== ''));
            const data = await api.get(page.api.adminClasses, { ...params, page: target });
            const list = rows(data);
            root.querySelector('[data-field="range"]').textContent = data.total ? `Hiển thị ${data.from}–${data.to} trong tổng số ${data.total} lớp` : '';
            region('classes').replaceChildren(list.length ? classTable(list) : emptyBlock('Không có lớp hành chính trong phạm vi hoặc bộ lọc đã chọn.', '', 'meeting_room'));
            pager(region('pager'), data, loadClasses);
        } catch (error) {
            region('classes').replaceChildren(errorBlock(error, () => loadClasses(target)));
        }
    }

    function classTable(list) {
        const th = (t, extra = '') => el('th', { scope: 'col', class: `py-3 px-4 font-semibold ${extra}` }, t);

        return el('table', { class: 'w-full text-sm min-w-[980px]' },
            el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] text-left text-xs uppercase tracking-wider text-secondary border-b border-outline-variant/30' },
                th('Mã lớp'), th('Tên lớp'), th('Ngành đào tạo'), th('Khóa'), th('Cố vấn học tập (CVHT)'), th('Sĩ số thực tế / Tối đa', 'text-center'), th('Trạng thái', 'text-center'), th('Thao tác', 'text-right'))),
            el('tbody', { class: 'divide-y divide-outline-variant/20' }, list.map((c) => {
                const remaining = Math.max(0, (c.max_size ?? 0) - (c.current_students_count ?? 0));

                return el('tr', { class: `hover:bg-[#F8FAFC] ${state.selected?.id === c.id ? 'bg-[#EEF2FD]/60' : ''}` },
                    el('td', { class: 'py-3 px-4 font-mono font-bold text-primary' }, c.code),
                    el('td', { class: 'py-3 px-4' }, c.name),
                    el('td', { class: 'py-3 px-4' }, c.major?.name ?? '—'),
                    el('td', { class: 'py-3 px-4' }, c.cohort?.code ?? '—'),
                    el('td', { class: 'py-3 px-4' }, c.advisor
                        ? [el('p', { class: 'font-semibold' }, c.advisor.full_name), el('p', { class: 'text-[11px] text-secondary' }, c.advisor.code)]
                        : [el('p', { class: 'text-amber-800 font-semibold' }, 'Chưa có cố vấn'), el('p', { class: 'text-[11px] text-amber-700' }, 'Cần phân công CVHT')]),
                    el('td', { class: 'py-3 px-4 text-center' }, el('p', { class: 'font-semibold' }, `${c.current_students_count ?? 0} / ${c.max_size}`), el('p', { class: 'text-[11px] text-secondary' }, remaining === 0 ? 'Đủ sĩ số' : `Còn ${remaining} chỗ`)),
                    el('td', { class: 'py-3 px-4 text-center' }, el('span', { class: `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${c.status === 'active' ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-surface-container text-on-surface-variant'}` }, c.status_label)),
                    el('td', { class: 'py-3 px-4 text-right whitespace-nowrap' },
                        el('button', { type: 'button', class: 'text-xs font-semibold text-primary hover:underline', onclick: () => openClass(c) }, 'Danh sách SV'),
                        page.can.update ? el('button', { type: 'button', class: 'ml-3 text-xs font-semibold text-on-surface-variant hover:underline', onclick: () => editClass(c) }, 'Sửa') : null,
                        page.can.delete ? el('button', { type: 'button', class: 'ml-3 text-xs font-semibold text-red-700 hover:underline', onclick: (e) => removeClass(e.currentTarget, c) }, 'Xóa') : null));
            })));
    }

    async function editClass(c) {
        const saved = await formDialog({
            title: c ? `Sửa lớp ${c.code}` : 'Thêm lớp hành chính',
            fields: [
                { name: 'code', label: 'Mã lớp', required: true, value: c?.code, hint: 'Ví dụ 26DHCNTT01; không đổi khi lớp đã có sinh viên' },
                { name: 'name', label: 'Tên lớp', required: true, value: c?.name },
                { name: 'cohort_id', label: 'Khóa', type: 'select', required: true, value: c?.cohort?.id, placeholder: 'Chọn khóa', options: state.cohorts.map((x) => ({ value: x.id, label: `${x.code} (${x.start_year})` })) },
                { name: 'major_id', label: 'Ngành', type: 'select', required: true, value: c?.major?.id, placeholder: 'Chọn ngành', options: state.majors.map((x) => ({ value: x.id, label: x.name })) },
                { name: 'specialization_id', label: 'Chuyên ngành', type: 'select', value: c?.specialization?.id, placeholder: 'Không có', options: state.specializations.map((x) => ({ value: x.id, label: `${x.name}${x.major?.name ? ` (${x.major.name})` : ''}` })) },
                { name: 'training_type_id', label: 'Hệ đào tạo', type: 'select', value: c?.training_type?.id, placeholder: 'Mặc định', options: state.types.map((x) => ({ value: x.id, label: x.name })) },
                { name: 'max_size', label: 'Sĩ số tối đa', type: 'number', min: 1, value: c?.max_size },
                ...(c ? [{ name: 'status', label: 'Trạng thái', type: 'select', value: c.status, options: [{ value: 'active', label: 'Đang hoạt động' }, { value: 'inactive', label: 'Đã đóng' }] }] : []),
            ],
            onSubmit: (data) => {
                const body = formJson(data, ['specialization_id']);

                return c ? api.put(tpl(page.api.classUpdate, c.id), body) : api.post(page.api.classStore, body);
            },
        });

        if (saved) {
            toast(c ? 'Đã cập nhật lớp.' : 'Đã thêm lớp hành chính.', 'success');
            await Promise.all([loadClasses(state.page), loadStats()]);
        }
    }

    async function removeClass(button, c) {
        const ok = await confirmDialog({ title: `Xóa lớp ${c.code}?`, message: 'Chỉ xóa được lớp chưa có sinh viên / dữ liệu liên quan; lớp đã dùng thì chuyển sang "Đã đóng".', confirmText: 'Xóa', danger: true });

        if (!ok) {
            return;
        }

        try {
            await withBusy(button, () => api.del(tpl(page.api.classDestroy, c.id)));
            toast('Đã xóa lớp.', 'success');
            await Promise.all([loadClasses(state.page), loadStats()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    // ---- Chi tiết lớp: thành viên, CVHT ----------------------------------------------------------------------

    async function openClass(c) {
        state.selected = c;
        const box = region('detail');
        box.hidden = false;
        box.replaceChildren(loadingBlock(3));
        box.scrollIntoView({ behavior: 'smooth', block: 'start' });

        try {
            const [members, advisors] = await Promise.all([listAll(tpl(page.api.members, c.id)), api.get(tpl(page.api.advisors, c.id), { history: true })]);
            renderDetail(c, members, rows(advisors));
        } catch (error) {
            box.replaceChildren(errorBlock(error, () => openClass(c)));
        }
    }

    function renderDetail(c, members, advisors) {
        const current = advisors.find((a) => !a.effective_to || a.effective_to >= today());
        const selected = new Set();
        const toolbar = el('div', { class: 'flex flex-wrap gap-2' },
            page.can.updateAll ? el('button', { type: 'button', class: 'rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white', onclick: () => addMembers(c) }, 'Thêm sinh viên') : null,
            page.can.updateAll ? el('button', { type: 'button', class: 'rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700', onclick: () => removeMembers(c, [...selected]) }, 'Gỡ sinh viên đã chọn') : null,
            page.can.export ? el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold', onclick: (e) => exportMembers(e.currentTarget, c) }, 'Xuất Excel') : null);

        region('detail').replaceChildren(
            el('div', { class: 'flex flex-wrap items-start justify-between gap-3' },
                el('div', {}, el('h2', { class: 'font-headline-sm text-headline-sm font-semibold' }, `Lớp ${c.code} — ${c.name}`),
                    el('p', { class: 'text-xs text-secondary' }, `${c.major?.name ?? ''} · ${c.cohort?.code ?? ''} · ${members.length}/${c.max_size} sinh viên`)),
                el('button', { type: 'button', class: 'text-xs text-secondary hover:underline', onclick: () => { region('detail').hidden = true; state.selected = null; } }, 'Đóng')),
            el('div', { class: 'grid lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start' },
                el('div', { class: 'space-y-3' }, toolbar, members.length
                    ? el('div', { class: 'overflow-x-auto max-h-[480px] overflow-y-auto rounded-lg border border-outline-variant/50' }, el('table', { class: 'w-full text-sm' },
                        el('thead', { class: 'sticky top-0 bg-[#F8FAFC]' }, el('tr', { class: 'text-left text-xs uppercase text-secondary' }, ['', 'MSSV', 'Họ và tên', 'Từ ngày', 'Trạng thái'].map((t) => el('th', { class: 'py-2 px-3 font-semibold' }, t)))),
                        el('tbody', { class: 'divide-y divide-outline-variant/20' }, members.map((m) => el('tr', {},
                            el('td', { class: 'py-2 px-3' }, page.can.updateAll ? el('input', { type: 'checkbox', 'aria-label': `Chọn ${m.student?.full_name}`, class: 'h-4 w-4', onchange: (e) => (e.target.checked ? selected.add(m.student.id) : selected.delete(m.student.id)) }) : null),
                            el('td', { class: 'py-2 px-3 font-mono text-primary' }, el('a', { href: tpl(page.links.student, m.student?.id), class: 'hover:underline' }, m.student?.student_code ?? '—')),
                            el('td', { class: 'py-2 px-3' }, m.student?.full_name),
                            el('td', { class: 'py-2 px-3 text-xs' }, dmy(m.effective_from)),
                            el('td', { class: 'py-2 px-3' }, statusBadge(m.student?.status)))))))
                    : emptyBlock('Lớp chưa có sinh viên', '', 'group_off')),
                el('aside', { class: 'rounded-xl border border-outline-variant p-4 space-y-3' },
                    el('h3', { class: 'font-semibold flex items-center gap-1.5' }, icon('supervisor_account', 'text-[18px] text-primary'), 'Cố vấn học tập'),
                    current ? el('div', { class: 'text-sm' }, el('p', { class: 'font-semibold' }, current.lecturer?.full_name), el('p', { class: 'text-xs text-secondary' }, `${current.lecturer?.code} · từ ${dmy(current.effective_from)}`)) : el('p', { class: 'text-sm text-amber-800' }, 'Lớp chưa có cố vấn học tập.'),
                    el('div', { class: 'flex flex-wrap gap-2' },
                        el('button', { type: 'button', class: 'rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white', onclick: () => assignAdvisor(c) }, current ? 'Thay cố vấn' : 'Phân công CVHT'),
                        current ? el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold', onclick: () => endAdvisor(c, current) }, 'Kết thúc phân công') : null),
                    advisors.length > 1 ? el('div', {}, el('p', { class: 'text-xs font-semibold text-secondary mt-2' }, 'Lịch sử'), el('ul', { class: 'text-xs text-secondary space-y-0.5' }, advisors.map((a) => el('li', {}, `${a.lecturer?.full_name}: ${dmy(a.effective_from)} – ${a.effective_to ? dmy(a.effective_to) : 'nay'}`)))) : null)));
    }

    async function addMembers(c) {
        const saved = await formDialog({
            title: `Thêm sinh viên vào lớp ${c.code}`,
            description: 'Nhập MSSV, mỗi dòng một sinh viên. Sinh viên đang ở lớp khác sẽ được chuyển lớp từ ngày hiệu lực. Hệ thống xem trước trước khi lưu.',
            fields: [
                { name: 'codes', label: 'Danh sách MSSV', type: 'textarea', required: true, full: true },
                { name: 'effective_from', label: 'Ngày hiệu lực', type: 'date', required: true, value: today() },
                { name: 'reason', label: 'Lý do', maxlength: 500 },
            ],
            submitText: 'Xem trước',
            onSubmit: async (data) => {
                const codes = [...new Set(String(data.get('codes')).split(/[\s,;]+/).map((x) => x.trim()).filter(Boolean))];
                const found = (await Promise.all(codes.map((code) => api.get(page.api.students, { q: code, per_page: 5 }))))
                    .map((res, i) => rows(res).find((s) => s.student_code === codes[i]));
                const missing = codes.filter((code, i) => !found[i]);

                if (missing.length) {
                    throw Object.assign(new Error(`Không tìm thấy MSSV: ${missing.join(', ')}`), { name: 'ApiError', status: 422, reasons: [] });
                }

                const body = { student_ids: found.map((s) => s.id), effective_from: data.get('effective_from'), reason: data.get('reason') || null };
                const preview = await api.post(tpl(page.api.membersPreview, c.id), body);
                const ok = await confirmPreview('Xác nhận thêm sinh viên', preview.assignments ?? [], preview.errors ?? [],
                    (r) => `${r.student_code} ${r.full_name}: ${r.action === 'unchanged' ? 'đã ở lớp này' : r.from_class ? `chuyển từ ${r.from_class}` : 'thêm mới'}`);

                return ok ? api.post(tpl(page.api.membersStore, c.id), body) : false;
            },
        });

        if (saved) {
            toast(`Đã cập nhật ${saved.count ?? ''} sinh viên.`, 'success');
            await Promise.all([openClass(c), loadClasses(state.page), loadStats()]);
        }
    }

    async function removeMembers(c, ids) {
        if (!ids.length) {
            showAlert(region('alert'), 'Chọn ít nhất một sinh viên để gỡ khỏi lớp.', 'warning');

            return;
        }

        const saved = await formDialog({
            title: `Gỡ ${ids.length} sinh viên khỏi lớp ${c.code}`,
            fields: [
                { name: 'effective_to', label: 'Ngày kết thúc', type: 'date', required: true, value: today() },
                { name: 'reason', label: 'Lý do', maxlength: 500 },
            ],
            submitText: 'Xem trước',
            danger: true,
            onSubmit: async (data) => {
                const body = { student_ids: ids, effective_to: data.get('effective_to'), reason: data.get('reason') || null };
                const preview = await api.post(tpl(page.api.membersRemovePreview, c.id), body);
                const ok = await confirmPreview('Xác nhận gỡ sinh viên', preview.removals ?? [], preview.errors ?? [], (r) => `${r.student_code} ${r.full_name}: kết thúc ${dmy(r.effective_to)}`);

                return ok ? api.post(tpl(page.api.membersRemove, c.id), body) : false;
            },
        });

        if (saved) {
            toast('Đã gỡ sinh viên khỏi lớp.', 'success');
            await Promise.all([openClass(c), loadClasses(state.page), loadStats()]);
        }
    }

    async function confirmPreview(title, items, errors, label) {
        const message = [
            items.length ? `Sẽ thực hiện (${items.length}):\n${items.slice(0, 15).map(label).join('\n')}${items.length > 15 ? '\n…' : ''}` : 'Không có sinh viên hợp lệ.',
            errors.length ? `\nKhông thực hiện được (${errors.length}):\n${errors.slice(0, 10).map((e) => `${e.student_code}: ${e.reason}`).join('\n')}` : '',
        ].join('\n');

        if (!items.length) {
            await confirmDialog({ title, message, confirmText: 'Đóng' });

            return false;
        }

        return Boolean(await confirmDialog({ title, message, confirmText: 'Lưu' }));
    }

    async function exportMembers(button, c) {
        try {
            await withBusy(button, () => api.download(tpl(page.api.membersExport, c.id), { format: 'xlsx' }, `lop-${c.code}.xlsx`), 'Đang xuất…');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function assignAdvisor(c) {
        state.lecturers ??= await listAll(page.api.lecturers, { status: 'working' }).catch(() => listAll(page.api.lecturers));
        const saved = await formDialog({
            title: `Phân công cố vấn học tập lớp ${c.code}`,
            description: 'Cố vấn cũ (nếu có) kết thúc vào ngày trước ngày hiệu lực.',
            fields: [
                { name: 'lecturer_id', label: 'Giảng viên', type: 'select', required: true, placeholder: 'Chọn giảng viên', options: state.lecturers.map((l) => ({ value: l.id, label: `${l.code} — ${l.full_name}` })) },
                { name: 'effective_from', label: 'Ngày hiệu lực', type: 'date', required: true, value: today() },
                { name: 'reason', label: 'Lý do', maxlength: 500, full: true },
            ],
            onSubmit: async (data) => {
                const body = formJson(data, ['reason']);
                const preview = await api.post(tpl(page.api.advisorPreview, c.id), body);
                const change = preview.current_advisor
                    ? `Thay ${preview.current_advisor.full_name} bằng ${preview.new_advisor?.full_name}`
                    : `Phân công ${preview.new_advisor?.full_name}`;
                const warnings = (preview.warnings ?? []).join('\n');
                const ok = await confirmDialog({
                    title: 'Xác nhận phân công',
                    message: `${change} từ ${dmy(preview.effective_from)}.${warnings ? `\n${warnings}` : ''}`,
                    confirmText: 'Phân công',
                });

                return ok ? api.post(tpl(page.api.advisorStore, c.id), body) : false;
            },
        });

        if (saved) {
            toast('Đã phân công cố vấn học tập.', 'success');
            await Promise.all([openClass(c), loadClasses(state.page), loadStats()]);
        }
    }

    async function endAdvisor(c, assignment) {
        const saved = await formDialog({
            title: `Kết thúc phân công ${assignment.lecturer?.full_name}`,
            fields: [
                { name: 'effective_to', label: 'Ngày kết thúc', type: 'date', required: true, value: today() },
                { name: 'reason', label: 'Lý do', maxlength: 500 },
            ],
            danger: true,
            submitText: 'Kết thúc',
            onSubmit: (data) => api.post(tpl(page.api.advisorEnd, assignment.id), formJson(data, ['reason'])),
        });

        if (saved) {
            toast('Đã kết thúc phân công cố vấn.', 'success');
            await Promise.all([openClass(c), loadClasses(state.page), loadStats()]);
        }
    }

    // ---- Khóa tuyển sinh ----------------------------------------------------------------------------------------

    function renderCohorts() {
        root.querySelector('[data-count="cohorts"]').textContent = `(${state.cohorts.length})`;
        const th = (t, extra = '') => el('th', { scope: 'col', class: `py-3 px-4 font-semibold ${extra}` }, t);
        region('cohorts').replaceChildren(state.cohorts.length
            ? el('table', { class: 'w-full text-sm min-w-[760px]' },
                el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] text-left text-xs uppercase tracking-wider text-secondary border-b border-outline-variant/30' },
                    th('Mã khóa'), th('Tên khóa'), th('Năm bắt đầu – kết thúc'), th('Bộ quy chế áp dụng'), th('Lớp đang hoạt động', 'text-center'), th('Trạng thái', 'text-center'), th('', 'text-right'))),
                el('tbody', { class: 'divide-y divide-outline-variant/20' }, state.cohorts.map((x) => el('tr', { class: 'hover:bg-[#F8FAFC]' },
                    el('td', { class: 'py-3 px-4 font-mono font-bold text-primary' }, x.code),
                    el('td', { class: 'py-3 px-4' }, x.name ?? '—'),
                    el('td', { class: 'py-3 px-4' }, `${x.start_year} – ${x.expected_end_year ?? '?'}`),
                    el('td', { class: 'py-3 px-4 text-xs' }, x.effective_policy_set ? `${x.effective_policy_set.code} v${x.effective_policy_set.version}${x.policy_set ? '' : ' (tự chọn theo năm)'}` : 'Chưa có'),
                    el('td', { class: 'py-3 px-4 text-center' }, x.active_classes_count ?? 0),
                    el('td', { class: 'py-3 px-4 text-center' }, el('span', { class: `inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold ${x.status === 'active' ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-surface-container text-on-surface-variant'}` }, x.status_label)),
                    el('td', { class: 'py-3 px-4 text-right whitespace-nowrap' },
                        page.can.update ? el('button', { type: 'button', class: 'text-xs font-semibold text-primary hover:underline', onclick: () => editCohort(x) }, 'Sửa') : null,
                        page.can.delete ? el('button', { type: 'button', class: 'ml-3 text-xs font-semibold text-red-700 hover:underline', onclick: (e) => removeCohort(e.currentTarget, x) }, 'Xóa') : null)))))
            : emptyBlock('Chưa có khóa tuyển sinh', '', 'event_note'));
    }

    async function removeCohort(button, x) {
        const ok = await confirmDialog({ title: `Xóa khóa ${x.code}?`, message: 'Chỉ xóa được khóa chưa có lớp hành chính / sinh viên; khóa đã dùng thì chuyển sang "Ngừng".', confirmText: 'Xóa', danger: true });

        if (!ok) {
            return;
        }

        try {
            await withBusy(button, () => api.del(tpl(page.api.cohortDestroy, x.id)));
            toast(`Đã xóa khóa ${x.code}.`, 'success');
            state.cohorts = rows(await api.get(page.api.cohorts));
            fillSelect(form.elements.cohort_id, state.cohorts, (c) => `${c.code} (${c.start_year})`);
            renderCohorts();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function editCohort(x) {
        const saved = await formDialog({
            title: x ? `Sửa khóa ${x.code}` : 'Thêm khóa tuyển sinh',
            fields: [
                { name: 'code', label: 'Mã khóa', value: x?.code, hint: 'Để trống thì hệ thống đặt theo năm (ví dụ K2027)' },
                { name: 'name', label: 'Tên khóa', value: x?.name },
                { name: 'start_year', label: 'Năm bắt đầu', type: 'number', required: !x, value: x?.start_year },
                { name: 'expected_end_year', label: 'Năm kết thúc dự kiến', type: 'number', value: x?.expected_end_year },
                { name: 'policy_set_id', label: 'Bộ quy chế', type: 'select', value: x?.policy_set?.id, placeholder: 'Tự chọn theo năm bắt đầu', options: state.policySets.map((p) => ({ value: p.id, label: `${p.code} v${p.version} — ${p.name}` })), full: true },
                ...(x ? [{ name: 'status', label: 'Trạng thái', type: 'select', value: x.status, options: [{ value: 'active', label: 'Đang hoạt động' }, { value: 'inactive', label: 'Ngừng' }] }] : []),
            ],
            onSubmit: (data) => {
                const body = formJson(data, ['policy_set_id', 'name']);

                return x ? api.put(tpl(page.api.cohortUpdate, x.id), body) : api.post(page.api.cohortStore, body);
            },
        });

        if (saved) {
            toast(x ? 'Đã cập nhật khóa.' : 'Đã thêm khóa tuyển sinh.', 'success');
            state.cohorts = rows(await api.get(page.api.cohorts));
            fillSelect(form.elements.cohort_id, state.cohorts, (c) => `${c.code} (${c.start_year})`);
            renderCohorts();
        }
    }
}
