/**
 * Trang quản lý giảng viên (mock admin/giang-vien) cho Phòng Đào tạo / Quản trị: danh sách theo phạm vi xem (API TCH),
 * lọc khoa / bộ môn / học vị / trạng thái, số lớp và số tiết theo học kỳ (so với định mức), thẻ thống kê, hồ sơ, thêm / sửa,
 * nghỉ việc / nghỉ hưu (xem trước việc phải bàn giao), cấp tài khoản, xuất Excel.
 */
import { api } from '../api.js';
import { debounce, dmy, fillSelect, formDialog, formJson, initials, listAll, pager, rows, tpl } from '../lookups.js';
import { el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const TITLES = { lecturer: 'Giảng viên', senior_lecturer: 'Giảng viên chính', principal_lecturer: 'Giảng viên cao cấp', associate_professor: 'Phó giáo sư', professor: 'Giáo sư' };
const DEGREES = { doctor: 'Tiến sĩ', master: 'Thạc sĩ', engineer: 'Kỹ sư', bachelor: 'Cử nhân' };
const STATUS = {
    working: ['Đang công tác', 'bg-[#DCFCE7] text-[#15803D]'],
    resigned: ['Nghỉ việc', 'bg-red-50 text-red-700'],
    retired: ['Nghỉ hưu', 'bg-slate-100 text-slate-700'],
};
const NULLABLE = ['date_of_birth', 'gender_id', 'national_id', 'email', 'phone', 'academic_degree', 'expertise', 'hired_on'];

/** Học hàm / học vị viết tắt trước tên: GS., PGS., TS., ThS. */
function prefix(l) {
    const title = { professor: 'GS.', associate_professor: 'PGS.' }[l.academic_title] ?? '';
    const degree = { doctor: 'TS.', master: 'ThS.' }[l.academic_degree] ?? '';

    return `${title}${degree}`;
}

export default function adminLecturers(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const stat = (name) => root.querySelector(`[data-stat="${name}"]`);
    const form = root.querySelector('[data-form="filters"]');
    const perPage = root.querySelector('[data-filter="per_page"]');
    const state = { page: 1, faculties: [], departments: [], genders: [] };

    form.elements.q.addEventListener('input', debounce(() => reload(1), 350));
    ['faculty_id', 'department_id', 'academic_degree', 'status'].forEach((name) => form.elements[name].addEventListener('change', () => {
        if (name === 'faculty_id') fillDepartments();
        reload(1);
    }));
    form.elements.term_id.addEventListener('change', () => Promise.all([reload(state.page), loadStats()]));
    perPage.addEventListener('change', () => reload(1));
    root.querySelector('[data-action="reload"]').addEventListener('click', () => Promise.all([reload(state.page), loadStats()]));
    root.querySelector('[data-action="export"]')?.addEventListener('click', (event) => exportList(event.currentTarget));
    root.querySelector('[data-action="create"]')?.addEventListener('click', () => editLecturer(null));

    bootstrap();

    async function bootstrap() {
        region('table').replaceChildren(loadingBlock(5));

        try {
            const [faculties, departments, years, current, genders] = await Promise.all([
                listAll(page.api.faculties), listAll(page.api.departments), api.get(page.api.academicYears),
                api.get(page.api.currentTerm).catch(() => null), api.get(tpl(page.api.lookup, 'GENDER', '__CODE__')).catch(() => []),
            ]);
            state.faculties = faculties;
            state.departments = departments;
            state.genders = rows(genders);
            fillSelect(form.elements.faculty_id, faculties, (f) => f.name);
            fillDepartments();
            const terms = rows(years).flatMap((y) => (y.terms ?? []).map((t) => ({ id: t.id, label: `${t.name} · ${y.name}` })));
            fillSelect(form.elements.term_id, terms, (t) => t.label, 'id', false);
            if (current?.id) form.elements.term_id.value = String(current.id);
        } catch (error) {
            showAlert(region('alert'), error);
        }

        // Bộ lọc và trang trên URL: tải lại / quay lại không mất từ khóa
        const query = new URLSearchParams(window.location.search);
        ['q', 'faculty_id', 'academic_degree', 'status'].forEach((name) => { form.elements[name].value = query.get(name) ?? ''; });
        fillDepartments();
        form.elements.department_id.value = query.get('department_id') ?? '';
        if (query.get('term_id')) form.elements.term_id.value = query.get('term_id');
        perPage.value = query.get('per_page') ?? perPage.value;

        await Promise.all([reload(Number(query.get('page') ?? 1) || 1), loadStats()]);
    }

    function fillDepartments() {
        const facultyId = form.elements.faculty_id.value;
        const list = facultyId ? state.departments.filter((d) => String(d.faculty_id ?? d.faculty?.id) === facultyId) : state.departments;
        fillSelect(form.elements.department_id, list, (d) => d.name);
    }

    function filters() {
        const data = Object.fromEntries(new FormData(form).entries());

        return Object.fromEntries(Object.entries(data).filter(([, value]) => value !== ''));
    }

    async function reload(target = 1) {
        state.page = target;
        const url = new URLSearchParams({ ...filters(), ...(target > 1 ? { page: target } : {}), ...(perPage.value !== '50' ? { per_page: perPage.value } : {}) });
        window.history.replaceState(null, '', `${window.location.pathname}${url.toString() ? `?${url}` : ''}`);
        renderTags();
        region('table').replaceChildren(loadingBlock(5));

        try {
            const data = await api.get(page.api.list, { ...filters(), page: target, per_page: perPage.value });
            const list = rows(data);
            field('found').textContent = (data.total ?? list.length).toLocaleString('vi-VN');
            field('count').textContent = `${list.length} / ${(data.total ?? list.length).toLocaleString('vi-VN')} giảng viên`;
            region('table').replaceChildren(list.length ? table(list) : emptyBlock('Không tìm thấy giảng viên nào', 'Thử bỏ bớt bộ lọc hoặc đổi từ khóa tìm kiếm.', 'person_search'));
            pager(region('pager'), data, reload);
        } catch (error) {
            region('table').replaceChildren(errorBlock(error, () => reload(target)));
            region('pager').replaceChildren();
        }
    }

    /** Thẻ thống kê trong phạm vi xem: đếm theo trạng thái; vượt định mức tính trên giảng viên đang công tác của học kỳ đang chọn. */
    async function loadStats() {
        const count = async (status) => (await api.get(page.api.list, { per_page: 1, ...(status ? { status } : {}) }))?.total ?? 0;
        const format = (n) => n.toLocaleString('vi-VN');

        try {
            const termId = form.elements.term_id.value;
            const [total, working, resigned, retired, loads] = await Promise.all([
                count(), count('working'), count('resigned'), count('retired'),
                termId ? listAll(page.api.list, { status: 'working', term_id: termId, per_page: 100 }) : Promise.resolve([]),
            ]);
            stat('total').textContent = format(total);
            stat('working').textContent = format(working);
            stat('left').textContent = format(resigned + retired);
            stat('left-detail').textContent = `${format(resigned)} nghỉ việc · ${format(retired)} nghỉ hưu`;
            stat('overload').textContent = format(loads.filter((l) => l.load && l.load.periods > l.load.limit).length);
            stat('overload-note').textContent = termId ? (form.elements.term_id.selectedOptions[0]?.textContent ?? '') : 'Chọn học kỳ để xem';
        } catch {
            ['total', 'working', 'overload', 'left'].forEach((name) => { stat(name).textContent = '—'; });
        }
    }

    function renderTags() {
        const labels = { q: 'Từ khóa', faculty_id: 'Khoa', department_id: 'Bộ môn', academic_degree: 'Học vị', status: 'Trạng thái' };
        const active = Object.entries(filters()).filter(([name]) => labels[name]);
        const tags = active.map(([name, value]) => {
            const input = form.elements[name];
            const text = input.tagName === 'SELECT' ? input.selectedOptions[0]?.textContent : value;

            return el('span', { class: 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-surface-container-low text-primary border border-primary/20' },
                `${labels[name]}: `, el('strong', {}, text),
                el('button', { type: 'button', class: 'hover:text-error flex items-center', 'aria-label': `Bỏ lọc ${labels[name]}`, onclick: () => {
                    input.value = '';
                    if (name === 'faculty_id') fillDepartments();
                    reload(1);
                } }, icon('close', 'text-[14px]')));
        });

        region('tags').replaceChildren(...[
            el('span', { class: 'font-label-sm text-label-sm text-secondary font-semibold flex items-center gap-1' }, icon('filter_alt', 'text-[16px]'), active.length ? 'Đang lọc theo:' : 'Chưa áp dụng bộ lọc'),
            ...tags,
            active.length ? el('button', { type: 'button', class: 'text-primary font-label-md text-label-md font-semibold underline underline-offset-2 ml-1', onclick: () => {
                const term = form.elements.term_id.value;
                form.reset();
                form.elements.term_id.value = term;
                fillDepartments();
                reload(1);
            } }, 'Xóa tất cả bộ lọc') : null,
        ].filter(Boolean));
    }

    function table(list) {
        const th = (text, extra = '') => el('th', { scope: 'col', class: `py-3.5 px-4 font-semibold text-on-surface-variant ${extra}` }, text);

        return el('table', { class: 'w-full text-left border-collapse min-w-[1050px]' },
            el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] border-b border-outline-variant/30 text-secondary font-label-md text-label-md uppercase tracking-wider' },
                th('Giảng viên', 'w-72'), th('Đơn vị / Khoa'), th('Bộ môn'), th('Học vị / Chức danh'),
                th('Lớp HP', 'text-center'), th('Giờ thực / Định mức', 'text-right'), th('Trạng thái', 'text-center'), th('Thao tác', 'text-center w-32'))),
            el('tbody', { class: 'divide-y divide-outline-variant/20 font-body-md text-body-md text-on-surface' }, list.map(row)));
    }

    function row(l) {
        const [statusText, tone] = STATUS[l.status] ?? [l.status_label, 'bg-slate-100 text-slate-700'];
        const over = l.load && l.load.periods > l.load.limit;
        const action = (name, title, onclick, toneClass = 'hover:text-primary hover:bg-surface-container-low') => el('button', { type: 'button', class: `p-1 rounded text-secondary ${toneClass} transition-colors`, title, 'aria-label': `${title} ${l.full_name}`, onclick }, icon(name));

        return el('tr', { class: 'hover:bg-[#F8FAFC] transition-colors group' },
            el('td', { class: 'py-3 px-4' }, el('div', { class: 'flex items-center gap-3' },
                el('div', { class: 'w-9 h-9 rounded-full bg-blue-100 text-primary font-bold flex items-center justify-center text-xs ring-1 ring-blue-200 shrink-0' }, initials(l.full_name)),
                el('div', { class: 'min-w-0' },
                    el('button', { type: 'button', class: 'font-semibold text-on-surface group-hover:text-primary hover:underline text-left', onclick: () => showProfile(l) }, `${prefix(l) ? `${prefix(l)} ` : ''}${l.full_name}`),
                    el('p', { class: 'text-[11px] text-secondary truncate' }, el('span', { class: 'font-mono font-bold text-primary' }, l.code), l.email ? ` · ${l.email}` : '')))),
            el('td', { class: 'py-3 px-4 font-medium' }, l.faculty?.name ?? '—'),
            el('td', { class: 'py-3 px-4 text-secondary' }, l.department?.name ?? '—'),
            el('td', { class: 'py-3 px-4' }, el('p', { class: 'font-medium' }, l.academic_degree_label ?? '—'), el('p', { class: 'text-[11px] text-secondary' }, l.academic_title_label ?? '')),
            el('td', { class: 'py-3 px-4 text-center font-semibold' }, l.load ? String(l.load.sections) : '—'),
            el('td', { class: 'py-3 px-4 text-right' }, l.load
                ? [el('span', { class: `font-bold ${over ? 'text-red-700' : 'text-on-surface'}` }, String(l.load.periods)), el('span', { class: 'text-secondary text-xs' }, ` / ${l.load.limit} tiết`), over ? el('span', { class: 'ml-1 text-[11px] font-semibold text-red-700' }, '(Vượt)') : null]
                : '—'),
            el('td', { class: 'py-3 px-4 text-center' }, el('span', { class: `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${tone}` }, statusText)),
            el('td', { class: 'py-3 px-4 text-center' }, el('div', { class: 'flex items-center justify-center gap-1' },
                action('visibility', 'Xem hồ sơ', () => showProfile(l)),
                page.can.update ? action('edit', 'Chỉnh sửa', () => editLecturer(l)) : null,
                page.can.update ? action('sync_alt', 'Đổi trạng thái công tác', () => changeStatus(l), 'hover:text-amber-700 hover:bg-amber-50') : null)));
    }

    function departmentOptions() {
        const facultyName = (d) => state.faculties.find((f) => String(f.id) === String(d.faculty_id ?? d.faculty?.id))?.name;

        return state.departments.map((d) => ({ value: d.id, label: facultyName(d) ? `${facultyName(d)} – ${d.name}` : d.name }));
    }

    /** Thêm (lecturer = null) hoặc sửa hồ sơ. Mã giảng viên là tên đăng nhập, không đổi sau khi tạo. */
    async function editLecturer(lecturer) {
        let full = lecturer;

        if (lecturer) {
            try { full = await api.get(tpl(page.api.show, lecturer.id)); } catch (error) { showAlert(region('alert'), error); return; }
        }

        const saved = await formDialog({
            title: lecturer ? `Sửa hồ sơ ${full.code} – ${full.full_name}` : 'Thêm giảng viên mới',
            description: lecturer ? '' : 'Mã giảng viên dùng làm tên đăng nhập. Chọn "Tạo tài khoản" để hệ thống cấp tài khoản và gửi mật khẩu tạm qua email.',
            fields: [
                lecturer ? null : { name: 'code', label: 'Mã giảng viên', required: true, maxlength: 20, placeholder: 'GV0101', hint: 'Chữ in hoa và số, ví dụ GV0101' },
                { name: 'full_name', label: 'Họ và tên', required: true, value: full?.full_name, maxlength: 255, full: !lecturer ? false : true },
                { name: 'department_id', label: 'Bộ môn chính', type: 'select', required: true, placeholder: 'Chọn bộ môn', options: departmentOptions(), value: full?.department_id, full: true },
                { name: 'academic_title', label: 'Chức danh', type: 'select', options: Object.entries(TITLES).map(([value, label]) => ({ value, label })), value: full?.academic_title ?? 'lecturer' },
                { name: 'academic_degree', label: 'Học vị', type: 'select', placeholder: 'Chưa nhập', options: Object.entries(DEGREES).map(([value, label]) => ({ value, label })), value: full?.academic_degree },
                { name: 'gender_id', label: 'Giới tính', type: 'select', placeholder: 'Chưa nhập', options: state.genders.map((g) => ({ value: g.id, label: g.name })), value: full?.gender_id },
                { name: 'date_of_birth', label: 'Ngày sinh', type: 'date', value: full?.date_of_birth },
                { name: 'email', label: 'Email', type: 'email', value: full?.email },
                { name: 'phone', label: 'Điện thoại', value: full?.phone, maxlength: 30 },
                { name: 'national_id', label: 'Số CCCD', value: full?.national_id, maxlength: 12, hint: '12 chữ số' },
                { name: 'hired_on', label: 'Ngày vào làm', type: 'date', value: full?.hired_on },
                { name: 'expertise', label: 'Chuyên môn', type: 'textarea', value: full?.expertise, maxlength: 500, full: true },
                lecturer ? null : { name: 'account', label: 'Tài khoản đăng nhập', type: 'select', options: [{ value: 'new', label: 'Tạo tài khoản (cần email)' }, { value: 'none', label: 'Chưa cấp, cấp sau' }], value: 'new', full: true },
            ].filter(Boolean),
            submitText: lecturer ? 'Lưu thay đổi' : 'Thêm giảng viên',
            onSubmit: (data) => {
                const body = formJson(data, lecturer ? NULLABLE : []);
                if (body.code) body.code = body.code.trim().toUpperCase();

                return lecturer ? api.put(tpl(page.api.update, lecturer.id), body) : api.post(page.api.store, body);
            },
        });

        if (saved) {
            toast(lecturer ? 'Đã lưu hồ sơ giảng viên.' : `Đã thêm giảng viên ${saved.code ?? ''}.`, 'success');
            await Promise.all([reload(lecturer ? state.page : 1), loadStats()]);
        }
    }

    /** Nghỉ việc / nghỉ hưu / làm việc trở lại: xem trước lớp đang dạy, chức vụ phải bàn giao (BR-TCH-02). */
    async function changeStatus(l) {
        const today = new Date().toISOString().slice(0, 10);
        let handover = [];

        try { handover = (await api.get(tpl(page.api.handover, l.id), { on: today })).items ?? []; } catch { /* vẫn cho chọn, máy chủ kiểm tra khi lưu */ }

        const saved = await formDialog({
            title: `Tình trạng công tác — ${l.full_name}`,
            description: handover.length
                ? `Việc cần bàn giao trước khi nghỉ:\n• ${handover.map((item) => (typeof item === 'string' ? item : item.description ?? JSON.stringify(item))).join('\n• ')}`
                : 'Không còn lớp đang dạy hoặc chức vụ cần bàn giao.',
            fields: [
                { name: 'status', label: 'Tình trạng', type: 'select', required: true, options: Object.entries(STATUS).map(([value, [label]]) => ({ value, label })), value: l.status === 'working' ? 'resigned' : 'working' },
                { name: 'on', label: 'Ngày hiệu lực', type: 'date', value: today },
                { name: 'reason', label: 'Lý do', type: 'textarea', maxlength: 500, full: true, hint: 'Bắt buộc khi còn lớp đang dạy (ghi cách bàn giao).' },
            ],
            submitText: 'Ghi nhận',
            onSubmit: (data) => api.post(tpl(page.api.status, l.id), formJson(data)),
        });

        if (saved) {
            toast('Đã cập nhật tình trạng công tác.', 'success');
            await Promise.all([reload(state.page), loadStats()]);
        }
    }

    async function showProfile(l) {
        const body = el('div', { class: 'space-y-4' }, loadingBlock(3));
        const close = el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold hover:bg-surface-container-low' }, 'Đóng');
        const dialog = el('dialog', { class: 'm-auto w-[min(94vw,680px)] max-h-[90vh] overflow-y-auto rounded-xl border border-outline-variant p-5 shadow-xl backdrop:bg-black/40' },
            el('div', { class: 'space-y-4' }, el('h2', { class: 'text-base font-bold' }, `Hồ sơ giảng viên ${l.code}`), body, el('div', { class: 'flex justify-end gap-2' }, close)));
        close.addEventListener('click', () => dialog.close());
        dialog.addEventListener('close', () => dialog.remove());
        document.body.append(dialog);
        dialog.showModal();

        try {
            const p = await api.get(tpl(page.api.show, l.id));
            const item = (label, value) => el('div', {}, el('dt', { class: 'text-xs text-secondary' }, label), el('dd', { class: 'text-sm font-medium text-on-surface' }, value || '—'));
            const account = p.has_account === false && page.can.update
                ? el('button', { type: 'button', class: 'rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white', onclick: async (event) => {
                    const button = event.currentTarget;
                    try {
                        await withBusy(button, () => api.post(tpl(page.api.account, p.id), { email: p.email || null }));
                        toast('Đã cấp tài khoản, mật khẩu tạm gửi qua email.', 'success');
                        dialog.close();
                        reload(state.page);
                    } catch (error) { showAlert(region('alert'), error); dialog.close(); }
                } }, 'Cấp tài khoản đăng nhập')
                : null;

            body.replaceChildren(
                el('div', { class: 'flex items-center gap-3' },
                    p.photo_url ? el('img', { src: p.photo_url, alt: '', class: 'w-14 h-14 rounded-full object-cover' }) : el('div', { class: 'w-14 h-14 rounded-full bg-blue-100 text-primary font-bold flex items-center justify-center' }, initials(p.full_name)),
                    el('div', {}, el('p', { class: 'font-bold text-on-surface' }, `${prefix(p) ? `${prefix(p)} ` : ''}${p.full_name}`), el('p', { class: 'text-xs text-secondary' }, `${p.academic_title_label ?? ''} · ${p.status_label ?? ''}`))),
                el('dl', { class: 'grid grid-cols-2 gap-3' },
                    item('Mã / tên đăng nhập', p.username ? `${p.code} (tài khoản: ${p.username})` : `${p.code} (chưa có tài khoản)`),
                    item('Khoa', p.faculty?.name), item('Bộ môn chính', p.department?.name),
                    item('Học vị', p.academic_degree_label), item('Email', p.email), item('Điện thoại', p.phone),
                    item('Ngày sinh', p.date_of_birth ? dmy(p.date_of_birth) : ''), item('Giới tính', p.gender),
                    item('Ngày vào làm', p.hired_on ? dmy(p.hired_on) : ''),
                    p.left_on ? item('Ngày nghỉ', `${dmy(p.left_on)}${p.left_reason ? ` — ${p.left_reason}` : ''}`) : null,
                    el('div', { class: 'col-span-2' }, item('Chuyên môn', p.expertise))),
                el('div', {}, el('p', { class: 'text-sm font-semibold mb-1' }, 'Kiêm nhiệm'),
                    (p.units ?? []).length
                        ? el('ul', { class: 'text-sm space-y-1' }, p.units.map((u) => el('li', {}, `${u.unit_type === 'faculty' ? 'Khoa' : 'Bộ môn'} ${u.unit?.name ?? ''} — từ ${dmy(u.starts_on)}${u.ends_on ? ` đến ${dmy(u.ends_on)}` : ''}`)))
                        : el('p', { class: 'text-xs text-secondary' }, 'Không kiêm nhiệm đơn vị khác.')),
                account);
        } catch (error) {
            body.replaceChildren(errorBlock(error, () => { dialog.close(); showProfile(l); }));
        }
    }

    async function exportList(button) {
        try {
            const params = Object.fromEntries(Object.entries(filters()).filter(([name]) => name !== 'term_id'));
            const result = await withBusy(button, () => api.download(page.api.export, { ...params, format: 'xlsx' }, 'danh-sach-giang-vien.xlsx'), 'Đang xuất…');
            toast(result.queued ? 'Danh sách lớn đang được xuất nền; tệp sẽ có trong mục tải về khi xong.' : `Đã tải ${result.filename}.`, 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }
}
