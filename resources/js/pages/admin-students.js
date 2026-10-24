/**
 * Trang quản lý sinh viên (FE #16, mock admin/sinh-vien): danh sách theo phạm vi xem (API STU), bộ lọc khoa / ngành /
 * khóa / lớp / trạng thái, thẻ thống kê theo trạng thái, xuất Excel, duyệt yêu cầu sửa hồ sơ của sinh viên.
 */
import { api } from '../api.js';
import { debounce, dmy, fillSelect, initials, listAll, pager, rows, statusBadge, tpl } from '../lookups.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

export default function adminStudents(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const stat = (name) => root.querySelector(`[data-stat="${name}"]`);
    const form = root.querySelector('[data-form="filters"]');
    const perPage = root.querySelector('[data-filter="per_page"]');
    const state = { page: 1, majors: [], faculties: [], cohorts: [], classes: [] };

    form.addEventListener('submit', (event) => event.preventDefault());
    form.elements.q.addEventListener('input', debounce(() => reload(1), 350));
    ['faculty_id', 'major_id', 'cohort', 'admin_class_id', 'status'].forEach((name) => form.elements[name].addEventListener('change', () => {
        if (name === 'faculty_id') {
            fillMajors();
        }

        reload(1);
    }));
    perPage.addEventListener('change', () => reload(1));
    root.querySelector('[data-action="reload"]').addEventListener('click', () => reload(state.page));
    root.querySelector('[data-action="export"]')?.addEventListener('click', (event) => exportList(event.currentTarget));

    bootstrap();

    async function bootstrap() {
        region('table').replaceChildren(loadingBlock(5));

        try {
            const [faculties, majors, cohorts, classes] = await Promise.all([
                listAll(page.api.faculties), listAll(page.api.majors), api.get(page.api.cohorts), listAll(page.api.adminClasses),
            ]);
            state.faculties = faculties;
            state.majors = majors;
            state.cohorts = rows(cohorts);
            state.classes = classes;
            fillSelect(form.elements.faculty_id, faculties, (f) => f.name);
            fillMajors();
            fillSelect(form.elements.cohort, state.cohorts, (c) => `${c.code} (${c.start_year})`, 'start_year');
            fillSelect(form.elements.admin_class_id, classes, (c) => c.code);
        } catch (error) {
            showAlert(region('alert'), error);
        }

        await Promise.all([reload(1), loadStats(), loadRequests()]);
    }

    function fillMajors() {
        const facultyId = form.elements.faculty_id.value;
        const majors = facultyId ? state.majors.filter((m) => String(m.faculty_id ?? m.faculty?.id) === facultyId) : state.majors;
        fillSelect(form.elements.major_id, majors, (m) => m.name);
    }

    function filters() {
        const data = Object.fromEntries(new FormData(form).entries());

        return Object.fromEntries(Object.entries(data).filter(([, value]) => value !== ''));
    }

    async function reload(target = 1) {
        state.page = target;
        renderTags();
        region('table').replaceChildren(loadingBlock(5));

        try {
            const data = await api.get(page.api.list, { ...filters(), page: target, per_page: perPage.value });
            const list = rows(data);
            field('found').textContent = (data.total ?? list.length).toLocaleString('vi-VN');
            field('count').textContent = `${list.length} / ${(data.total ?? list.length).toLocaleString('vi-VN')} sinh viên`;
            region('table').replaceChildren(list.length ? table(list) : emptyBlock('Không có sinh viên phù hợp', 'Thử bỏ bớt bộ lọc hoặc đổi từ khóa tìm kiếm.', 'person_search'));
            pager(region('pager'), data, reload);
        } catch (error) {
            region('table').replaceChildren(errorBlock(error, () => reload(target)));
            region('pager').replaceChildren();
        }
    }

    /** Thẻ thống kê: đếm `total` theo trạng thái trong phạm vi xem (mỗi thẻ một lần gọi, per_page=1). */
    async function loadStats() {
        const count = async (status) => (await api.get(page.api.list, { per_page: 1, ...(status ? { status } : {}) }))?.total ?? 0;

        try {
            const [total, studying, suspended, pending] = await Promise.all([count(), count('studying'), count('suspended'), count('pending')]);
            const format = (n) => n.toLocaleString('vi-VN');
            stat('total').textContent = format(total);
            stat('studying').textContent = format(studying);
            stat('studying-pct').textContent = total ? `${((studying * 100) / total).toFixed(1)}%` : '0%';
            stat('paused').textContent = format(suspended + pending);
            stat('paused-detail').textContent = `${format(suspended)} bảo lưu · ${format(pending)} chờ nhập học`;
            stat('other').textContent = format(Math.max(0, total - studying - suspended - pending));
        } catch {
            ['total', 'studying', 'paused', 'other'].forEach((name) => { stat(name).textContent = '—'; });
        }
    }

    function renderTags() {
        const labels = { q: 'Từ khóa', faculty_id: 'Khoa', major_id: 'Ngành', cohort: 'Khóa', admin_class_id: 'Lớp', status: 'Trạng thái' };
        const active = Object.entries(filters());
        const tags = active.map(([name, value]) => {
            const select = form.elements[name];
            const text = select.tagName === 'SELECT' ? select.selectedOptions[0]?.textContent : value;

            return el('span', { class: 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-surface-container-low text-primary border border-primary/20' },
                `${labels[name]}: `, el('strong', {}, text),
                el('button', { type: 'button', class: 'hover:text-error flex items-center', 'aria-label': `Bỏ lọc ${labels[name]}`, onclick: () => {
                    select.value = '';

                    if (name === 'faculty_id') {
                        fillMajors();
                    }

                    reload(1);
                } }, icon('close', 'text-[14px]')));
        });

        region('tags').replaceChildren(...[
            el('span', { class: 'font-label-sm text-label-sm text-secondary font-semibold flex items-center gap-1' }, icon('filter_alt', 'text-[16px]'), active.length ? 'Đang lọc theo:' : 'Chưa áp dụng bộ lọc'),
            ...tags,
            active.length ? el('button', { type: 'button', class: 'text-primary font-label-md text-label-md font-semibold underline underline-offset-2 ml-1', onclick: () => {
                form.reset();
                fillMajors();
                reload(1);
            } }, 'Xóa tất cả bộ lọc') : null,
        ].filter(Boolean));
    }

    function table(list) {
        const th = (text, extra = '') => el('th', { scope: 'col', class: `py-3.5 px-4 font-semibold text-on-surface-variant ${extra}` }, text);

        return el('table', { class: 'w-full text-left border-collapse min-w-[1000px]' },
            el('thead', {}, el('tr', { class: 'bg-[#F8FAFC] border-b border-outline-variant/30 text-secondary font-label-md text-label-md uppercase tracking-wider' },
                th('MSSV', 'w-32'), th('Họ và tên sinh viên', 'w-64'), th('Lớp hành chính'), th('Ngành đào tạo'), th('Khóa học'),
                th('ĐTB tích lũy / TC', 'text-right'), th('Trạng thái', 'text-center'), th('Thao tác', 'text-center w-28'))),
            el('tbody', { class: 'divide-y divide-outline-variant/20 font-body-md text-body-md text-on-surface' }, list.map(row)));
    }

    function row(s) {
        const show = tpl(page.links.show, s.id);
        const gpa = s.gpa?.gpa;
        const action = (href, name, title, tone = 'hover:text-primary hover:bg-surface-container-low') => el('a', { href, class: `p-1 rounded text-secondary ${tone} transition-colors`, title, 'aria-label': title }, icon(name));

        return el('tr', { class: 'hover:bg-[#F8FAFC] transition-colors group' },
            el('td', { class: 'py-3 px-4 font-mono font-bold text-primary' }, s.student_code ?? el('span', { class: 'text-secondary font-sans font-normal text-xs' }, 'Chưa cấp')),
            el('td', { class: 'py-3 px-4' }, el('div', { class: 'flex items-center gap-3' },
                el('div', { class: 'w-8 h-8 rounded-full bg-blue-100 text-primary font-bold flex items-center justify-center text-xs ring-1 ring-blue-200 shrink-0' }, initials(s.full_name)),
                el('div', { class: 'min-w-0' },
                    el('a', { href: show, class: 'font-semibold text-on-surface group-hover:text-primary hover:underline' }, s.full_name),
                    el('p', { class: 'text-[11px] text-secondary truncate' }, s.school_email ?? `Ngày sinh ${dmy(s.date_of_birth)}`)))),
            el('td', { class: 'py-3 px-4 font-medium text-secondary' }, s.admin_class?.code ?? '—'),
            el('td', { class: 'py-3 px-4 font-medium' }, s.major?.name ?? '—'),
            el('td', { class: 'py-3 px-4 text-secondary' }, s.cohort ? `K${s.cohort}` : '—'),
            el('td', { class: 'py-3 px-4 text-right' }, gpa === undefined || gpa === null
                ? el('span', { class: 'text-secondary text-xs' }, 'Chưa có')
                : [el('span', { class: `font-bold ${gpa >= 2 ? 'text-green-700' : 'text-red-700'}` }, gpa.toFixed(2)), el('span', { class: 'text-secondary text-xs' }, ` / ${s.gpa.credits_earned} TC`)]),
            el('td', { class: 'py-3 px-4 text-center' }, statusBadge(s.status, s.status_label)),
            el('td', { class: 'py-3 px-4 text-center' }, el('div', { class: 'flex items-center justify-center gap-1' },
                action(show, 'visibility', 'Xem hồ sơ'),
                page.can.update ? action(tpl(page.links.edit, s.id), 'edit', 'Chỉnh sửa') : null,
                page.can.update ? action(`${show}#trang-thai`, 'swap_horiz', 'Đổi trạng thái', 'hover:text-amber-700 hover:bg-amber-50') : null)));
    }

    async function exportList(button) {
        try {
            const result = await withBusy(button, () => api.download(page.api.export, { ...filters(), format: 'xlsx' }, 'danh-sach-sinh-vien.xlsx'), 'Đang xuất…');
            toast(result.queued ? 'Danh sách lớn đang được xuất nền; tệp sẽ có trong mục tải về khi xong.' : `Đã tải ${result.filename}.`, 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function loadRequests() {
        region('requests').replaceChildren(loadingBlock(2));

        try {
            const list = rows(await api.get(page.api.changeRequests, { status: 'pending' }));
            field('requests-count').textContent = `${list.length} yêu cầu`;
            region('requests').replaceChildren(list.length
                ? el('div', { class: 'space-y-3' }, list.map(requestCard))
                : emptyBlock('Không có yêu cầu chờ duyệt', 'Yêu cầu sinh viên gửi ở trang Hồ sơ cá nhân sẽ hiện tại đây.', 'task_alt'));
        } catch (error) {
            region('requests').replaceChildren(errorBlock(error, loadRequests));
        }
    }

    function requestCard(r) {
        const labels = { full_name: 'Họ tên', date_of_birth: 'Ngày sinh', national_id: 'Số giấy tờ', major_id: 'Ngành' };
        const shown = (name, value) => {
            if (value === null || value === undefined || value === '') {
                return '—';
            }

            if (name === 'major_id') {
                return state.majors.find((m) => String(m.id) === String(value))?.name ?? value;
            }

            return name === 'date_of_birth' ? dmy(value) : String(value);
        };
        const changes = Object.entries(r.changes ?? {}).map(([name, change]) => el('li', {},
            el('span', { class: 'font-semibold' }, `${labels[name] ?? name}: `),
            el('span', { class: 'line-through text-secondary' }, shown(name, change.old)), ' → ', el('span', { class: 'text-on-surface font-semibold' }, shown(name, change.new))));
        const decide = async (button, approve) => {
            const answer = await confirmDialog({
                title: approve ? 'Duyệt yêu cầu sửa hồ sơ?' : 'Từ chối yêu cầu?',
                message: approve ? 'Thay đổi được áp dụng vào hồ sơ và ghi lịch sử.' : 'Sinh viên sẽ thấy lý do từ chối.',
                confirmText: approve ? 'Duyệt' : 'Từ chối',
                danger: !approve,
                input: { label: approve ? 'Ghi chú (không bắt buộc)' : 'Lý do từ chối', required: !approve, maxlength: 1000 },
            });

            if (!answer) {
                return;
            }

            try {
                await withBusy(button, () => api.post(tpl(approve ? page.api.approveRequest : page.api.rejectRequest, r.id), { note: answer.value || null }));
                toast(approve ? 'Đã duyệt yêu cầu.' : 'Đã từ chối yêu cầu.', 'success');
                await Promise.all([loadRequests(), reload(state.page)]);
            } catch (error) {
                showAlert(region('alert'), error);
            }
        };

        return el('article', { class: 'rounded-xl border border-outline-variant p-4 flex flex-col lg:flex-row lg:items-start justify-between gap-3' },
            el('div', { class: 'min-w-0 space-y-1' },
                el('p', { class: 'font-semibold text-on-surface' }, `${r.student?.full_name ?? ''} `, el('span', { class: 'font-mono text-primary text-xs' }, r.student?.student_code ?? '')),
                el('ul', { class: 'text-xs text-on-surface-variant space-y-0.5' }, changes),
                el('p', { class: 'text-xs text-secondary' }, `Lý do: ${r.reason ?? '—'} · Gửi lúc ${r.created_at ?? ''}`),
                r.evidence_name ? el('a', { href: tpl(page.api.evidence, r.id), class: 'inline-flex items-center gap-1 text-xs text-primary hover:underline', target: '_blank', rel: 'noopener' }, icon('attach_file', 'text-[14px]'), r.evidence_name) : null),
            page.can.approve ? el('div', { class: 'flex gap-2 shrink-0' },
                el('button', { type: 'button', class: 'rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-container', onclick: (e) => decide(e.currentTarget, true) }, 'Duyệt'),
                el('button', { type: 'button', class: 'rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50', onclick: (e) => decide(e.currentTarget, false) }, 'Từ chối')) : null);
    }
}
