/**
 * Quản lý học phần (FE #10, mock admin/hoc-phan): danh sách theo phạm vi SUB.view (lọc khoa / khối / loại / trạng thái),
 * thẻ thống kê, thêm / sửa (tín chỉ LT + TH = tổng, BR-SUB-02), ngừng / mở lại, xóa (chỉ khi chưa được tham chiếu,
 * BR-SUB-05), quan hệ tiên quyết – học trước – song hành – tương đương (BR-SUB-03), cơ cấu điểm thành phần (BR-SUB-04),
 * xuất Excel. Bộ lọc và trang hiện tại ghi lên URL để tải lại / quay lại không mất.
 */
import { api } from '../api.js';
import { debounce, fillSelect, formDialog, formJson, listAll, pager, rows, tpl } from '../lookups.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const BLOCKS = { general: 'Giáo dục đại cương', foundation: 'Cơ sở ngành', specialized: 'Chuyên ngành', internship: 'Thực tập', thesis: 'Đồ án / khóa luận' };
const KINDS = { attendance: 'Chuyên cần', assignment: 'Bài tập', midterm: 'Giữa kỳ', practical: 'Thực hành', final_exam: 'Thi cuối kỳ', other: 'Khác' };
const RELATIONS = { prerequisite: 'Tiên quyết', prior: 'Học trước', corequisite: 'Song hành', equivalent: 'Tương đương' };

export default function adminSubjects(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const stat = (name) => root.querySelector(`[data-stat="${name}"]`);
    const form = root.querySelector('[data-form="filters"]');
    const state = { page: 1, departments: [] };

    // Khôi phục bộ lọc từ URL (?q=&faculty_id=&…&page=)
    const query = new URLSearchParams(window.location.search);
    ['q', 'knowledge_block', 'requirement', 'status'].forEach((name) => { form.elements[name].value = query.get(name) ?? ''; });
    state.page = Number(query.get('page') ?? 1) || 1;

    form.elements.q.addEventListener('input', debounce(() => load(1), 350));
    ['faculty_id', 'knowledge_block', 'requirement', 'status'].forEach((n) => form.elements[n].addEventListener('change', () => load(1)));
    root.querySelector('[data-action="create"]')?.addEventListener('click', () => edit(null));
    root.querySelector('[data-action="export"]')?.addEventListener('click', (e) => exportList(e.currentTarget));

    bootstrap();

    async function bootstrap() {
        try {
            const [faculties, departments] = await Promise.all([listAll(page.api.faculties), listAll(page.api.departments)]);
            state.departments = departments;
            fillSelect(form.elements.faculty_id, faculties, (f) => f.name);
            form.elements.faculty_id.value = query.get('faculty_id') ?? '';
        } catch (error) {
            showAlert(region('alert'), error);
        }

        await Promise.all([load(state.page), loadStats()]);
    }

    function filters() {
        return Object.fromEntries([...new FormData(form).entries()].filter(([, v]) => v !== ''));
    }

    async function load(target = state.page) {
        state.page = target;
        const params = filters();
        const url = new URLSearchParams({ ...params, ...(target > 1 ? { page: target } : {}) });
        window.history.replaceState(null, '', `${window.location.pathname}${url.toString() ? `?${url}` : ''}`);
        region('table').replaceChildren(loadingBlock(5));

        try {
            const data = await api.get(page.api.list, { ...params, page: target });
            const list = rows(data);
            root.querySelector('[data-field="range"]').textContent = data.total ? `Hiển thị ${data.from}–${data.to} trên tổng số ${data.total} học phần` : '';
            region('table').replaceChildren(list.length ? table(list) : emptyBlock('Không tìm thấy học phần nào', 'Không có học phần phù hợp với bộ lọc đã chọn.', 'library_books'));
            pager(region('pager'), data, load);
        } catch (error) {
            region('table').replaceChildren(errorBlock(error, () => load(target)));
        }
    }

    async function loadStats() {
        const count = async (params) => (await api.get(page.api.list, { ...params, per_page: 1 }))?.total ?? 0;

        try {
            const [total, active, general] = await Promise.all([count({}), count({ status: 'active' }), count({ knowledge_block: 'general' })]);
            stat('total').textContent = total.toLocaleString('vi-VN');
            stat('active').textContent = `${active} / ${total}`;
            stat('active-pct').textContent = total ? `Tỷ lệ áp dụng: ${((active * 100) / total).toFixed(1)}%` : '';
            stat('general').textContent = general.toLocaleString('vi-VN');
            stat('major').textContent = (total - general).toLocaleString('vi-VN');
        } catch {
            stat('total').textContent = '—';
        }
    }

    function table(list) {
        const th = (t, extra = '') => el('th', { scope: 'col', class: `py-3 px-4 font-semibold ${extra}` }, t);

        return el('table', { class: 'w-full text-sm min-w-[1000px]' },
            el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] text-left text-xs uppercase tracking-wider text-secondary border-b border-outline-variant/30' },
                th('Mã & Tên học phần'), th('Số tín chỉ', 'text-center'), th('Khoa quản lý'), th('Khối kiến thức'), th('Học phần tiên quyết'), th('Trạng thái', 'text-center'), th('Thao tác', 'text-right'))),
            el('tbody', { class: 'divide-y divide-outline-variant/20' }, list.map((s) => el('tr', { class: 'hover:bg-[#F8FAFC]' },
                el('td', { class: 'py-3 px-4' }, el('p', { class: 'font-mono text-xs font-bold text-primary' }, s.code), el('p', { class: 'font-semibold' }, s.name), s.name_en ? el('p', { class: 'text-[11px] text-secondary italic' }, s.name_en) : null),
                el('td', { class: 'py-3 px-4 text-center' }, el('p', { class: 'font-bold' }, `${s.credits} TC`), el('p', { class: 'text-[11px] text-secondary' }, `${s.theory_credits ?? 0} LT - ${s.practice_credits ?? 0} TH`)),
                el('td', { class: 'py-3 px-4' }, el('p', {}, s.faculty?.name ?? '—'), el('p', { class: 'text-[11px] text-secondary' }, s.department?.name ?? '')),
                el('td', { class: 'py-3 px-4' }, el('p', {}, s.knowledge_block_label), el('p', { class: 'text-[11px] text-secondary' }, s.requirement_label)),
                el('td', { class: 'py-3 px-4' }, (s.prerequisites ?? []).length
                    ? el('div', { class: 'flex flex-wrap gap-1' }, s.prerequisites.map((p) => el('span', { class: 'rounded bg-surface-container px-1.5 py-0.5 text-[11px] font-mono', title: `${p.type_label}: ${p.name}` }, p.code)))
                    : el('span', { class: 'text-xs text-secondary' }, 'Không có')),
                el('td', { class: 'py-3 px-4 text-center' }, el('span', { class: `inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold ${s.status === 'active' ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-slate-100 text-slate-600'}` }, s.status_label)),
                el('td', { class: 'py-3 px-4 text-right whitespace-nowrap' },
                    el('button', { type: 'button', class: 'p-1 text-secondary hover:text-primary', title: 'Xem chi tiết', 'aria-label': `Xem chi tiết ${s.code}`, onclick: () => open(s) }, icon('visibility')),
                    page.can.update ? el('button', { type: 'button', class: 'p-1 text-secondary hover:text-primary', title: 'Sửa', 'aria-label': `Sửa ${s.code}`, onclick: () => edit(s) }, icon('edit')) : null,
                    page.can.update ? el('button', { type: 'button', class: 'p-1 text-secondary hover:text-amber-700', title: s.status === 'active' ? 'Tạm ngừng' : 'Mở lại', 'aria-label': s.status === 'active' ? `Tạm ngừng ${s.code}` : `Mở lại ${s.code}`, onclick: (e) => toggle(e.currentTarget, s) }, icon(s.status === 'active' ? 'lock' : 'lock_open')) : null,
                    page.can.delete ? el('button', { type: 'button', class: 'p-1 text-secondary hover:text-red-700', title: 'Xóa', 'aria-label': `Xóa ${s.code}`, onclick: (e) => remove(e.currentTarget, s) }, icon('delete')) : null)))));
    }

    async function edit(s) {
        const saved = await formDialog({
            title: s ? `Sửa học phần ${s.code}` : 'Thêm học phần mới',
            description: 'Tín chỉ lý thuyết + thực hành phải bằng tổng tín chỉ. Học phần đã được dùng ở lớp / CTĐT thì không đổi mã và số tín chỉ.',
            fields: [
                { name: 'code', label: 'Mã học phần', required: true, value: s?.code },
                { name: 'name', label: 'Tên học phần', required: true, value: s?.name },
                { name: 'name_en', label: 'Tên tiếng Anh', value: s?.name_en },
                { name: 'department_id', label: 'Bộ môn quản lý', type: 'select', required: true, value: s?.department_id, placeholder: 'Chọn bộ môn', options: state.departments.map((d) => ({ value: d.id, label: `${d.name}${d.faculty?.name ? ` (${d.faculty.name})` : ''}` })) },
                { name: 'credits', label: 'Tổng tín chỉ', type: 'number', min: 0, required: true, value: s?.credits },
                { name: 'theory_credits', label: 'Tín chỉ lý thuyết', type: 'number', min: 0, value: s?.theory_credits },
                { name: 'practice_credits', label: 'Tín chỉ thực hành', type: 'number', min: 0, value: s?.practice_credits },
                { name: 'knowledge_block', label: 'Khối kiến thức', type: 'select', required: true, value: s?.knowledge_block, options: Object.entries(BLOCKS).map(([v, l]) => ({ value: v, label: l })) },
                { name: 'requirement', label: 'Loại', type: 'select', value: s?.requirement ?? 'compulsory', options: [{ value: 'compulsory', label: 'Bắt buộc' }, { value: 'elective', label: 'Tự chọn' }] },
                { name: 'teaching_language', label: 'Ngôn ngữ giảng dạy', type: 'select', value: s?.teaching_language ?? 'vi', options: [{ value: 'vi', label: 'Tiếng Việt' }, { value: 'en', label: 'Tiếng Anh' }, { value: 'vi_en', label: 'Song ngữ Việt – Anh' }] },
                { name: 'counts_toward_gpa', label: 'Tính vào GPA', type: 'checkbox', value: s ? s.counts_toward_gpa : true },
                { name: 'is_graduation_requirement', label: 'Điều kiện tốt nghiệp (Đạt / Không đạt)', type: 'checkbox', value: s?.is_graduation_requirement },
            ],
            onSubmit: (data) => {
                const body = formJson(data, ['name_en', 'theory_credits', 'practice_credits']);
                ['credits', 'theory_credits', 'practice_credits', 'department_id'].forEach((k) => { if (body[k] !== undefined && body[k] !== null) body[k] = Number(body[k]); });
                body.counts_toward_gpa = data.get('counts_toward_gpa') === '1';
                body.is_graduation_requirement = data.get('is_graduation_requirement') === '1';

                return s ? api.put(tpl(page.api.update, s.id), body) : api.post(page.api.store, body);
            },
        });

        if (saved) {
            toast(s ? 'Đã cập nhật học phần.' : 'Đã thêm học phần.', 'success');
            await Promise.all([load(), loadStats()]);
        }
    }

    async function toggle(button, s) {
        const ok = await confirmDialog({ title: s.status === 'active' ? `Tạm ngừng ${s.code}?` : `Mở lại ${s.code}?`, message: s.status === 'active' ? 'Học phần ngừng không mở lớp mới được; dữ liệu cũ giữ nguyên.' : '', confirmText: s.status === 'active' ? 'Tạm ngừng' : 'Mở lại' });

        if (!ok) {
            return;
        }

        try {
            await withBusy(button, () => api.put(tpl(page.api.update, s.id), { status: s.status === 'active' ? 'inactive' : 'active' }));
            toast('Đã cập nhật trạng thái học phần.', 'success');
            await Promise.all([load(), loadStats()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function remove(button, s) {
        const ok = await confirmDialog({ title: `Xóa học phần ${s.code}?`, message: 'Chỉ xóa được học phần chưa được dùng ở lớp học phần, CTĐT hay quan hệ; đã dùng thì chuyển sang "Tạm ngừng".', confirmText: 'Xóa', danger: true });

        if (!ok) {
            return;
        }

        try {
            await withBusy(button, () => api.del(tpl(page.api.destroy, s.id)));
            toast('Đã xóa học phần.', 'success');
            await Promise.all([load(), loadStats()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    // ---- Chi tiết: quan hệ, cơ cấu điểm -------------------------------------------------------------------------

    async function open(s) {
        const box = region('detail');
        box.hidden = false;
        box.replaceChildren(loadingBlock(3));
        box.scrollIntoView({ behavior: 'smooth', block: 'start' });

        try {
            const [relations, structure] = await Promise.all([api.get(tpl(page.api.relations, s.id)), api.get(tpl(page.api.components, s.id))]);
            renderDetail(s, relations, structure);
        } catch (error) {
            box.replaceChildren(errorBlock(error, () => open(s)));
        }
    }

    function renderDetail(s, relations, structure) {
        const relationList = (title, list, own) => el('div', { class: 'space-y-2' },
            el('h3', { class: 'text-sm font-semibold' }, title),
            list.length ? el('ul', { class: 'space-y-1.5' }, list.map((r) => el('li', { class: 'rounded-lg border border-outline-variant/60 px-3 py-2 text-sm flex items-center justify-between gap-2' },
                el('span', {}, el('span', { class: 'rounded bg-surface-container px-1.5 py-0.5 text-[11px] font-semibold mr-2' }, r.type_label), `${r.subject?.code ?? ''} — ${r.subject?.name ?? ''}`,
                    el('span', { class: 'block text-[11px] text-secondary' }, `Hiệu lực: ${r.effective_from ?? 'luôn'}${r.effective_to ? ` → ${r.effective_to}` : ''}${r.decision_no ? ` · QĐ ${r.decision_no}` : ''}`)),
                own && page.can.update ? el('span', { class: 'flex gap-2 shrink-0' },
                    el('button', { type: 'button', class: 'text-xs font-semibold text-amber-700 hover:underline', onclick: () => endRelation(s, r) }, 'Kết thúc'),
                    el('button', { type: 'button', class: 'text-xs font-semibold text-red-700 hover:underline', onclick: () => deleteRelation(s, r) }, 'Xóa')) : null)))
                : el('p', { class: 'text-xs text-secondary' }, 'Không có.'));
        const comps = structure.components ?? [];

        region('detail').replaceChildren(
            el('div', { class: 'flex items-start justify-between gap-3' },
                el('div', {}, el('h2', { class: 'font-headline-sm text-headline-sm font-semibold' }, `${s.code} — ${s.name}`), el('p', { class: 'text-xs text-secondary' }, `${s.credits} tín chỉ (${s.theory_credits ?? 0} LT, ${s.practice_credits ?? 0} TH) · ${s.knowledge_block_label} · ${s.faculty?.name ?? ''}`)),
                el('button', { type: 'button', class: 'text-xs text-secondary hover:underline', onclick: () => { region('detail').hidden = true; } }, 'Đóng')),
            el('div', { class: 'grid lg:grid-cols-2 gap-6' },
                el('div', { class: 'space-y-4' },
                    relationList('Điều kiện của học phần này', relations.requires ?? [], true),
                    relationList('Tương đương', relations.equivalents ?? [], true),
                    relationList('Là điều kiện của', relations.required_by ?? [], false),
                    page.can.update ? el('button', { type: 'button', class: 'rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white', onclick: () => addRelation(s) }, 'Thêm quan hệ') : null),
                el('div', { class: 'space-y-2' },
                    el('h3', { class: 'text-sm font-semibold' }, 'Cơ cấu điểm thành phần'),
                    el('p', { class: 'text-[11px] text-secondary' }, `Tối thiểu ${structure.rules?.min_components ?? 3} thành phần, tổng 100%, thi cuối kỳ ≥ ${structure.rules?.final_exam_min_weight ?? 50}%. Không đổi khi học phần đã có lớp mở (BR-SUB-05).`),
                    comps.length ? el('table', { class: 'w-full text-sm' },
                        el('thead', {}, el('tr', { class: 'text-left text-xs uppercase text-secondary' }, ['Thành phần', 'Loại', 'Trọng số', 'Điểm sàn'].map((t) => el('th', { class: 'py-1.5 pr-3 font-semibold' }, t)))),
                        el('tbody', {}, comps.map((c) => el('tr', { class: 'border-t border-outline-variant/30' },
                            el('td', { class: 'py-1.5 pr-3' }, c.name, c.is_required ? el('span', { class: 'text-red-600' }, ' *') : null),
                            el('td', { class: 'py-1.5 pr-3' }, c.kind_label ?? KINDS[c.kind]),
                            el('td', { class: 'py-1.5 pr-3' }, `${c.weight}%`),
                            el('td', { class: 'py-1.5 pr-3' }, c.min_score ?? '—'))))) : el('p', { class: 'text-xs text-secondary' }, 'Chưa khai báo cơ cấu điểm.'),
                    page.can.update ? el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold', onclick: () => editComponents(s, comps) }, 'Sửa cơ cấu điểm') : null)));
    }

    async function addRelation(s) {
        const others = await listAll(page.api.list, { status: 'active', per_page: 100 }).catch(() => []);
        const saved = await formDialog({
            title: `Thêm quan hệ cho ${s.code}`,
            description: 'Hệ thống chặn vòng lặp tiên quyết, kể cả gián tiếp (BR-SUB-03).',
            fields: [
                { name: 'type', label: 'Loại quan hệ', type: 'select', required: true, options: Object.entries(RELATIONS).map(([v, l]) => ({ value: v, label: l })) },
                { name: 'related_subject_id', label: 'Học phần liên quan', type: 'select', required: true, placeholder: 'Chọn học phần', options: others.filter((o) => o.id !== s.id).map((o) => ({ value: o.id, label: `${o.code} — ${o.name}` })) },
                { name: 'effective_from', label: 'Hiệu lực từ', type: 'date' },
                { name: 'decision_no', label: 'Số quyết định' },
                { name: 'note', label: 'Ghi chú', full: true },
            ],
            onSubmit: (data) => api.post(tpl(page.api.relationStore, s.id), formJson(data)),
        });

        if (saved) {
            toast('Đã thêm quan hệ học phần.', 'success');
            await Promise.all([open(s), load()]);
        }
    }

    async function endRelation(s, r) {
        const saved = await formDialog({
            title: `Kết thúc quan hệ ${r.type_label} với ${r.subject?.code}`,
            fields: [{ name: 'effective_to', label: 'Ngày kết thúc', type: 'date', required: true, value: new Date().toISOString().slice(0, 10) }],
            submitText: 'Kết thúc',
            onSubmit: (data) => api.post(tpl(page.api.relationEnd, r.id), formJson(data)),
        });

        if (saved) {
            toast('Đã kết thúc quan hệ.', 'success');
            await Promise.all([open(s), load()]);
        }
    }

    async function deleteRelation(s, r) {
        const ok = await confirmDialog({ title: `Xóa quan hệ ${r.type_label} với ${r.subject?.code}?`, message: 'Chỉ dùng khi khai báo nhầm; quan hệ đã áp dụng nên "Kết thúc" thay vì xóa.', confirmText: 'Xóa', danger: true });

        if (!ok) {
            return;
        }

        try {
            await api.del(tpl(page.api.relationDestroy, r.id));
            toast('Đã xóa quan hệ.', 'success');
            await Promise.all([open(s), load()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function editComponents(s, comps) {
        const rowsCount = Math.max(comps.length, 3);
        const fields = Array.from({ length: rowsCount + 1 }, (_, i) => {
            const c = comps[i] ?? {};

            return [
                { name: `name_${i}`, label: `Thành phần ${i + 1}`, value: c.name, hint: i === rowsCount ? 'Để trống nếu không dùng' : undefined },
                { name: `kind_${i}`, label: 'Loại', type: 'select', value: c.kind ?? (i === rowsCount - 1 ? 'final_exam' : 'assignment'), options: Object.entries(KINDS).map(([v, l]) => ({ value: v, label: l })) },
                { name: `weight_${i}`, label: 'Trọng số (%)', type: 'number', min: 0, value: c.weight },
                { name: `min_score_${i}`, label: 'Điểm sàn', type: 'number', min: 0, value: c.min_score },
            ];
        }).flat();
        const saved = await formDialog({
            title: `Cơ cấu điểm ${s.code}`,
            description: 'Tổng trọng số phải bằng 100%.',
            fields,
            onSubmit: (data) => {
                const components = Array.from({ length: rowsCount + 1 }, (_, i) => ({
                    name: String(data.get(`name_${i}`) ?? '').trim(),
                    kind: data.get(`kind_${i}`),
                    weight: Number(data.get(`weight_${i}`) || 0),
                    min_score: data.get(`min_score_${i}`) === '' ? null : Number(data.get(`min_score_${i}`)),
                    is_required: data.get(`kind_${i}`) === 'final_exam',
                })).filter((c) => c.name !== '');

                return api.put(tpl(page.api.componentsUpdate, s.id), { components });
            },
        });

        if (saved) {
            toast('Đã lưu cơ cấu điểm.', 'success');
            await open(s);
        }
    }

    async function exportList(button) {
        try {
            const result = await withBusy(button, () => api.download(page.api.export, { ...filters(), format: 'xlsx' }, 'hoc-phan.xlsx'), 'Đang xuất…');
            toast(result.queued ? 'Danh sách đang được xuất nền.' : `Đã tải ${result.filename}.`, 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }
}
