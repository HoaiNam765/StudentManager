/**
 * Hồ sơ sinh viên (FE #16, mock admin/sinh-vien-chi-tiet): thông tin đã che (xem đầy đủ ghi nhật ký, BR-STU-11), liên
 * hệ, người thân (thêm / sửa / xóa), kết quả học tập theo học kỳ, trạng thái theo quyết định (FR-STU-009, 010),
 * xác nhận / hủy nhập học (FR-STU-002), CTĐT đang áp dụng, lịch sử thay đổi hồ sơ.
 */
import { api } from '../api.js';
import { dmy, formDialog, formJson, initials, listAll, rows, statusBadge, tpl } from '../lookups.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

export default function adminStudentDetail(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const field = (name) => root.querySelector(`[data-field="${name}"]`);
    const termSelect = root.querySelector('[data-input="term"]');
    const state = { student: null, revealed: null, transcript: null };

    root.querySelector('[data-action="reveal"]')?.addEventListener('click', (event) => reveal(event.currentTarget));
    root.querySelector('[data-action="add-contact"]')?.addEventListener('click', () => editContact(null));
    termSelect.addEventListener('change', renderResults);

    loadProfile();
    loadStatus();
    loadCurricula();
    loadLogs();

    if (page.can.grades) {
        loadResults();
    } else {
        region('results').replaceChildren(emptyBlock('Bạn không có quyền xem điểm', '', 'lock'));
        termSelect.hidden = true;
    }

    async function loadProfile() {
        region('basic').replaceChildren(loadingBlock(2));

        try {
            state.student = await api.get(page.api.show);
            renderProfile();
        } catch (error) {
            region('basic').replaceChildren(errorBlock(error, loadProfile));
        }
    }

    function renderProfile() {
        const s = state.student;
        const p = s.personal ?? {};
        const c = s.contact ?? {};
        const a = s.academic ?? {};
        const r = state.revealed;

        field('name').textContent = s.full_name;
        region('status-badge').replaceChildren(statusBadge(s.status, s.status_label));
        field('training-type').textContent = s.training_type?.name ?? '';
        field('meta').textContent = [
            `MSSV: ${s.student_code ?? 'chưa cấp'}`,
            a.faculty ? `Ngành: ${s.major?.name ?? '—'} (${a.faculty.name})` : `Ngành: ${s.major?.name ?? '—'}`,
            s.cohort ? `Khóa: K${s.cohort}` : null,
            a.admission_date ? `Nhập học: ${dmy(a.admission_date)}` : null,
        ].filter(Boolean).join(' • ');
        region('avatar').replaceChildren(s.photo_url ? el('img', { src: s.photo_url, alt: `Ảnh thẻ ${s.full_name}`, class: 'w-full h-full object-cover' }) : initials(s.full_name));

        region('basic').replaceChildren(...items([
            ['Họ và tên khai sinh', s.full_name],
            ['Ngày sinh', dmy(s.date_of_birth)],
            ['Giới tính', p.gender?.name],
            [p.id_type_label ?? 'Số giấy tờ', r ? `${r.national_id ?? '—'}${r.national_id_issued_on ? ` (cấp ${dmy(r.national_id_issued_on)}${r.national_id_issued_by ? ` tại ${r.national_id_issued_by}` : ''})` : ''}` : p.national_id_masked],
            ['Dân tộc', p.ethnicity?.name],
            ['Tôn giáo', p.religion?.name],
            ['Quốc tịch', p.nationality?.name],
            ['Nơi sinh', p.place_of_birth],
            ['Chuyên ngành', a.specialization?.name],
            ['Tài khoản đăng nhập', a.username],
        ]));
        region('contact').replaceChildren(...items([
            ['Email sinh viên (trường cấp)', s.school_email],
            ['Email cá nhân', c.personal_email],
            ['Số điện thoại', c.phone],
            ['Địa chỉ thường trú', [r ? r.permanent_address : c.permanent_address_masked, c.permanent_unit?.name].filter(Boolean).join(', ')],
            ['Địa chỉ tạm trú', [r ? r.temporary_address : c.temporary_address_masked, c.temporary_unit?.name].filter(Boolean).join(', ')],
            ['Ghi chú', s.note],
        ]));
        renderContacts();
        renderStatusActions();
    }

    function items(list) {
        return list.flatMap(([label, value]) => [el('div', {},
            el('dt', { class: 'text-xs text-secondary' }, label),
            el('dd', { class: 'font-semibold text-on-surface break-words' }, value === null || value === undefined || value === '' ? '—' : String(value)))]);
    }

    async function reveal(button) {
        const ok = await confirmDialog({ title: 'Xem đầy đủ thông tin nhạy cảm?', message: 'Lượt xem số giấy tờ và địa chỉ đầy đủ được ghi vào nhật ký hệ thống.', confirmText: 'Xem' });

        if (!ok) {
            return;
        }

        try {
            state.revealed = await withBusy(button, () => api.get(page.api.sensitive));
            renderProfile();
            button.hidden = true;
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    // ---- Người thân ---------------------------------------------------------------------------------------------

    function renderContacts() {
        const list = state.student.contacts ?? [];
        region('contacts').replaceChildren(list.length
            ? el('div', { class: 'grid sm:grid-cols-2 gap-3' }, list.map((c) => el('article', { class: 'rounded-xl border border-outline-variant p-4 space-y-1 text-sm' },
                el('div', { class: 'flex items-start justify-between gap-2' },
                    el('div', {}, el('p', { class: 'text-xs text-secondary' }, c.relationship), el('p', { class: 'font-semibold text-on-surface' }, c.full_name)),
                    c.is_emergency ? el('span', { class: 'rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-700' }, 'Khẩn cấp') : null),
                c.phone ? el('p', { class: 'flex items-center gap-1 text-on-surface-variant' }, icon('call', 'text-[16px]'), c.phone) : null,
                c.email ? el('p', { class: 'flex items-center gap-1 text-on-surface-variant' }, icon('mail', 'text-[16px]'), c.email) : null,
                c.receives_notifications ? el('p', { class: 'text-[11px] text-secondary' }, 'Nhận thông báo học vụ') : null,
                page.can.update ? el('div', { class: 'flex gap-3 pt-1 print:hidden' },
                    el('button', { type: 'button', class: 'text-xs font-semibold text-primary hover:underline', onclick: () => editContact(c) }, 'Sửa'),
                    el('button', { type: 'button', class: 'text-xs font-semibold text-red-700 hover:underline', onclick: (e) => removeContact(e.currentTarget, c) }, 'Xóa')) : null)))
            : emptyBlock('Chưa có thông tin người thân', '', 'family_restroom'));
    }

    async function editContact(contact) {
        const saved = await formDialog({
            title: contact ? 'Sửa người thân' : 'Thêm người thân',
            fields: [
                { name: 'full_name', label: 'Họ và tên', required: true, value: contact?.full_name },
                { name: 'relationship', label: 'Quan hệ', required: true, value: contact?.relationship, hint: 'Ví dụ: Cha, Mẹ, Anh, Người giám hộ' },
                { name: 'phone', label: 'Số điện thoại', value: contact?.phone },
                { name: 'email', label: 'Email', type: 'email', value: contact?.email },
                { name: 'is_emergency', label: 'Liên hệ khẩn cấp', type: 'checkbox', value: contact?.is_emergency },
                { name: 'receives_notifications', label: 'Nhận thông báo học vụ', type: 'checkbox', value: contact?.receives_notifications },
            ],
            onSubmit: (data) => {
                const body = formJson(data, ['phone', 'email']);
                body.is_emergency = data.get('is_emergency') === '1';
                body.receives_notifications = data.get('receives_notifications') === '1';

                return contact ? api.put(tpl(page.api.contact, contact.id), body) : api.post(page.api.contacts, body);
            },
        });

        if (saved) {
            toast(contact ? 'Đã cập nhật người thân.' : 'Đã thêm người thân.', 'success');
            await loadProfile();
        }
    }

    async function removeContact(button, contact) {
        const ok = await confirmDialog({ title: `Xóa ${contact.full_name}?`, confirmText: 'Xóa', danger: true });

        if (!ok) {
            return;
        }

        try {
            await withBusy(button, () => api.del(tpl(page.api.contact, contact.id)));
            toast('Đã xóa người thân.', 'success');
            await loadProfile();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    // ---- Kết quả học tập ----------------------------------------------------------------------------------------

    async function loadResults() {
        region('results').replaceChildren(loadingBlock(2));

        try {
            const [gpa, transcript] = await Promise.all([api.get(page.api.gpa), api.get(page.api.transcript)]);
            state.transcript = transcript;
            const cumulative = gpa.cumulative;
            field('gpa').textContent = cumulative?.gpa === null || cumulative?.gpa === undefined ? '—' : Number(cumulative.gpa).toFixed(2);
            field('classification').textContent = cumulative?.classification ?? 'Chưa xếp loại';
            field('credits').textContent = cumulative ? `${cumulative.credits_earned ?? 0}` : '—';
            field('terms').textContent = String((gpa.terms ?? []).length);
            const terms = transcript.terms ?? [];
            termSelect.replaceChildren(...terms.map((t) => el('option', { value: t.term_id }, t.name)).reverse());
            renderResults();
        } catch (error) {
            region('results').replaceChildren(errorBlock(error, loadResults));
        }
    }

    function renderResults() {
        const term = (state.transcript?.terms ?? []).find((t) => String(t.term_id) === termSelect.value);

        if (!term) {
            region('results').replaceChildren(emptyBlock('Chưa có điểm đã công bố', 'Điểm hiện ở đây sau khi Phòng Đào tạo công bố.', 'grading'));

            return;
        }

        const th = (text, extra = '') => el('th', { scope: 'col', class: `py-2 px-3 font-semibold ${extra}` }, text);
        const number = (value, digits = 1) => (value === null || value === undefined ? '—' : Number(value).toFixed(digits));
        const passed = term.rows.filter((r) => r.is_passed).length;

        region('results').replaceChildren(
            el('div', { class: 'grid grid-cols-3 gap-3 mb-3 text-center text-sm' },
                el('div', { class: 'rounded-lg bg-surface-container-low p-2' }, el('p', { class: 'text-xs text-secondary' }, 'GPA học kỳ'), el('p', { class: 'font-bold text-primary' }, number(term.gpa?.gpa, 2))),
                el('div', { class: 'rounded-lg bg-surface-container-low p-2' }, el('p', { class: 'text-xs text-secondary' }, 'Xếp loại'), el('p', { class: 'font-bold' }, term.gpa?.classification ?? '—')),
                el('div', { class: 'rounded-lg bg-surface-container-low p-2' }, el('p', { class: 'text-xs text-secondary' }, 'TC đạt'), el('p', { class: 'font-bold' }, term.gpa ? `${term.gpa.credits_earned}/${term.gpa.credits_attempted}` : '—'))),
            el('div', { class: 'overflow-x-auto' }, el('table', { class: 'w-full text-sm min-w-[560px]' },
                el('thead', {}, el('tr', { class: 'text-left text-xs uppercase text-secondary border-b border-outline-variant/40' }, th('Học phần'), th('TC', 'text-center'), th('Hệ 10', 'text-center'), th('Hệ 4', 'text-center'), th('Chữ', 'text-center'), th('Kết quả', 'text-center'))),
                el('tbody', { class: 'divide-y divide-outline-variant/20' }, term.rows.map((r) => el('tr', {},
                    el('td', { class: 'py-2 px-3' }, el('p', { class: 'font-semibold' }, r.subject_name), el('p', { class: 'text-[11px] text-secondary font-mono' }, r.subject_code)),
                    el('td', { class: 'py-2 px-3 text-center' }, r.credits),
                    el('td', { class: 'py-2 px-3 text-center' }, number(r.score10)),
                    el('td', { class: 'py-2 px-3 text-center' }, number(r.point4)),
                    el('td', { class: 'py-2 px-3 text-center font-bold' }, r.letter ?? '—'),
                    el('td', { class: 'py-2 px-3 text-center' }, r.special_label ?? (r.is_passed === null ? 'Chưa đủ dữ liệu' : el('span', { class: r.is_passed ? 'text-green-700 font-semibold' : 'text-red-700 font-semibold' }, r.is_passed ? 'Đạt' : 'Không đạt')))))))),
            el('p', { class: 'mt-2 text-xs text-secondary' }, `Tổng ${term.rows.reduce((sum, r) => sum + (r.credits ?? 0), 0)} TC (${term.rows.length} học phần) · Qua môn: ${passed}/${term.rows.length}`));
    }

    // ---- Trạng thái học vụ, nhập học ----------------------------------------------------------------------------

    async function loadStatus() {
        region('status').replaceChildren(loadingBlock(1));

        try {
            state.status = await api.get(page.api.status);
            renderStatus();
            renderStatusActions();
        } catch (error) {
            region('status').replaceChildren(errorBlock(error, loadStatus));
        }
    }

    function renderStatus() {
        const history = state.status?.history ?? [];
        region('status').replaceChildren(
            el('p', { class: 'text-sm' }, 'Hiện tại: ', statusBadge(state.status.status, state.status.status_label)),
            history.length
                ? el('ol', { class: 'mt-3 space-y-3 border-l border-outline-variant pl-4' }, history.map((h) => el('li', { class: 'text-sm' },
                    el('p', { class: 'font-semibold' }, `${h.from} → ${h.to}`, h.special ? el('span', { class: 'ml-2 text-[11px] text-violet-700' }, '(quyết định đặc biệt)') : null),
                    el('p', { class: 'text-xs text-secondary' }, `QĐ ${h.decision_no} · hiệu lực ${dmy(h.effective_date)} · ghi lúc ${h.created_at ?? ''}`),
                    el('p', { class: 'text-xs text-on-surface-variant' }, h.reason),
                    h.has_attachment ? el('a', { href: tpl(page.api.attachment, h.id), class: 'text-xs text-primary hover:underline' }, 'Tải tệp quyết định') : null)))
                : el('p', { class: 'mt-2 text-xs text-secondary' }, 'Chưa có quyết định thay đổi trạng thái.'));
    }

    function renderStatusActions() {
        const box = region('status-actions');
        box.replaceChildren();

        if (!state.status || !state.student) {
            return;
        }

        const button = (label, onclick, tone = 'border-outline-variant text-on-surface hover:bg-surface-container-low') => el('button', { type: 'button', class: `rounded-lg border px-3 py-1.5 text-xs font-semibold ${tone}`, onclick }, label);

        if (page.can.approve && state.student.status === 'pending') {
            box.append(
                button('Xác nhận nhập học', admit, 'border-primary bg-primary text-white hover:bg-primary-container'),
                button('Hủy nhập học', cancelAdmission, 'border-red-300 text-red-700 hover:bg-red-50'));
        }

        if (page.can.update && (state.status.next ?? []).length) {
            box.append(button('Đổi trạng thái', () => changeStatus(false), 'border-amber-300 text-amber-800 hover:bg-amber-50'));
        }

        if (page.can.restore && state.status.can_restore) {
            box.append(button('Khôi phục (QĐ đặc biệt)', () => changeStatus(true), 'border-violet-300 text-violet-800 hover:bg-violet-50'));
        }
    }

    async function changeStatus(special) {
        const options = special ? [{ value: 'studying', label: 'Đang học' }] : state.status.next.map((s) => ({ value: s.value, label: s.label }));
        const saved = await formDialog({
            title: special ? 'Khôi phục trạng thái bằng quyết định đặc biệt' : 'Đổi trạng thái học vụ',
            description: 'Thay đổi theo quyết định; sinh viên bảo lưu / thôi học sẽ được hủy đăng ký chưa chốt và cập nhật tài khoản theo quy định.',
            fields: [
                { name: 'status', label: 'Trạng thái mới', type: 'select', required: true, options },
                { name: 'decision_no', label: 'Số quyết định', required: true, maxlength: 100 },
                { name: 'effective_date', label: 'Ngày hiệu lực', type: 'date', required: true, value: new Date().toISOString().slice(0, 10) },
                { name: 'attachment', label: 'Tệp quyết định (PDF, ảnh)', type: 'file', accept: '.pdf,.jpg,.jpeg,.png' },
                { name: 'reason', label: 'Lý do', type: 'textarea', required: true, full: true },
            ],
            submitText: 'Lưu quyết định',
            onSubmit: (data) => {
                if (!(data.get('attachment') instanceof File) || data.get('attachment').size === 0) {
                    data.delete('attachment');
                }

                if (special) {
                    data.set('special', '1');
                }

                return api.post(page.api.changeStatus, data);
            },
        });

        if (saved) {
            toast('Đã ghi nhận quyết định thay đổi trạng thái.', 'success');
            await Promise.all([loadProfile(), loadStatus(), loadLogs()]);
        }
    }

    async function admit() {
        let classes = [];

        try {
            classes = (await listAll(page.api.adminClasses, { major_id: state.student.major?.id, status: 'active' }))
                .filter((c) => !state.student.cohort || c.cohort?.start_year === state.student.cohort);
        } catch {
            classes = [];
        }

        const saved = await formDialog({
            title: `Xác nhận nhập học: ${state.student.full_name}`,
            description: 'Hệ thống cấp MSSV, email trường, tài khoản, gán CTĐT và xếp lớp hành chính trong một lần.',
            fields: [
                { name: 'admin_class_id', label: 'Lớp hành chính', type: 'select', placeholder: 'Tự xếp lớp theo khóa – ngành', options: classes.map((c) => ({ value: c.id, label: `${c.code} (${c.current_students_count}/${c.max_size})` })) },
                { name: 'admission_date', label: 'Ngày nhập học', type: 'date', value: new Date().toISOString().slice(0, 10) },
            ],
            submitText: 'Xác nhận nhập học',
            onSubmit: (data) => api.post(page.api.admit, formJson(data)),
        });

        if (saved) {
            toast(`Đã nhập học. MSSV: ${saved.student_code}.`, 'success');
            await Promise.all([loadProfile(), loadStatus(), loadCurricula(), loadLogs()]);
        }
    }

    async function cancelAdmission() {
        const answer = await confirmDialog({ title: 'Hủy nhập học?', message: 'Hồ sơ chuyển sang trạng thái hủy, không cấp MSSV.', confirmText: 'Hủy nhập học', danger: true, input: { label: 'Lý do', required: true } });

        if (!answer) {
            return;
        }

        try {
            await api.post(page.api.cancelAdmission, { reason: answer.value });
            toast('Đã hủy nhập học.', 'success');
            await Promise.all([loadProfile(), loadStatus()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    // ---- CTĐT và lịch sử thay đổi -------------------------------------------------------------------------------

    async function loadCurricula() {
        region('curricula').replaceChildren(loadingBlock(1));

        try {
            const data = await api.get(page.api.curricula);
            const history = data.history ?? [];
            region('curricula').replaceChildren(el('div', {},
                el('p', { class: 'text-sm' }, 'Đang áp dụng: ', el('strong', {}, data.current ? `${data.current.code} — ${data.current.name}` : 'Chưa gán')),
                history.length ? el('ul', { class: 'mt-2 space-y-1 text-xs text-secondary' }, history.map((h) => el('li', {},
                    `${h.curriculum?.code ?? ''} · từ ${dmy(h.effective_from)}${h.effective_to ? ` đến ${dmy(h.effective_to)}` : ''}${h.reason ? ` · ${h.reason}` : ''}`))) : null));
        } catch (error) {
            region('curricula').replaceChildren(errorBlock(error, loadCurricula));
        }
    }

    async function loadLogs() {
        region('logs').replaceChildren(loadingBlock(1));

        try {
            const list = rows(await api.get(page.api.changeLogs));
            region('logs').replaceChildren(list.length
                ? el('ul', { class: 'space-y-2 text-sm max-h-96 overflow-y-auto pr-1' }, list.map((log) => el('li', { class: 'rounded-lg border border-outline-variant/50 p-2.5' },
                    el('p', { class: 'font-semibold' }, log.field_label, log.sensitive ? el('span', { class: 'ml-1 text-[11px] text-amber-700' }, '(nhạy cảm)') : null),
                    el('p', { class: 'text-xs text-on-surface-variant break-words' }, `${log.old_value ?? '—'} → ${log.new_value ?? '—'}`),
                    el('p', { class: 'text-[11px] text-secondary' }, `${log.changed_by?.name ?? 'Hệ thống'} · ${new Date(log.changed_at).toLocaleString('vi-VN')}${log.reason ? ` · ${log.reason}` : ''}`))))
                : emptyBlock('Chưa có thay đổi', '', 'history'));

            if (list[0]) {
                field('updated').textContent = `Lần cập nhật cuối: ${new Date(list[0].changed_at).toLocaleString('vi-VN')}`;
            }
        } catch (error) {
            region('logs').replaceChildren(errorBlock(error, loadLogs));
        }
    }
}
