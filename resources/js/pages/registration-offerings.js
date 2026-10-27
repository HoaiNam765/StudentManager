import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, loadingBlock, showAlert, toast, withBusy } from '../ui.js';

/** Giữ kiểu thẻ / hộp thoại của trang ENR; mọi ràng buộc được kiểm tra lại ở API. */
export async function showOfferings(container, page, period, changed) {
    const url = (template, section = '') => template.replace('__ID__', period.id).replace('__SECTION__', section);
    const button = (text, action) => el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-3 py-2 text-xs font-semibold text-primary', onclick: action }, text);
    const alert = el('div', { hidden: true });
    let data;
    container.replaceChildren(loadingBlock(2));
    try { data = await api.get(url(page.api.offerings)); }
    catch (error) { container.replaceChildren(errorBlock(error, () => showOfferings(container, page, period, changed))); return; }
    const reload = () => showOfferings(container, page, period, changed);
    const mutable = data.editable && page.can.offerings;
    const list = data.sections.map((section) => el('article', { class: 'rounded-xl border border-outline-variant p-3 space-y-2' },
        el('p', { class: 'font-semibold text-sm' }, `${section.code} · ${section.subject.name} (${section.subject.credits} TC)`),
        el('p', { class: 'text-xs text-on-surface-variant' }, `${section.lecturer?.full_name ?? 'Chưa phân công'} · Sĩ số ${section.min_size}–${section.max_size}`),
        section.schedules.length
            ? section.schedules.map((s) => el('p', { class: 'text-xs text-on-surface-variant' }, `${s.description} · ${s.room ?? '—'}`))
            : el('p', { class: 'text-xs font-semibold text-amber-700' }, 'Chưa xếp lịch — bấm "Sửa giảng viên / lịch" để chọn phòng, thứ, tiết.'),
        mutable ? el('div', { class: 'flex gap-2' }, button('Sửa giảng viên / lịch', () => edit(section)), button('Xóa khỏi đợt', async () => {
            if (!await confirmDialog({ title: `Bỏ lớp ${section.code}?`, message: 'Lớp dự kiến sẽ được hủy; chương trình khung giữ nguyên.', danger: true, confirmText: 'Xóa khỏi đợt' })) return;
            try { await api.del(url(page.api.offeringDelete, section.id)); await reload(); await changed(); }
            catch (error) { showAlert(alert, error); }
        })) : null));
    container.replaceChildren(
        el('h3', { class: 'font-bold text-base' }, period.name),
        el('p', { class: 'mt-1 text-xs text-on-surface-variant' }, `${data.framework.faculty} · ${data.framework.cohort}. Thêm lớp từ các môn trong khung; có thể bổ sung học phần khác. Việc sửa ở đây không sửa chương trình khung.`),
        mutable ? el('div', { class: 'flex flex-wrap gap-2 my-3' }, button('Thêm học phần từ khung', () => edit()), button('Thêm học phần khác', () => edit(null, true))) : null,
        alert, el('div', { class: 'space-y-3 mt-3' }, list.length ? list : emptyBlock('Chưa có lớp học phần', 'Thêm học phần, chọn giảng viên của khoa và xếp lịch trước khi thông báo.')));

    async function edit(section = null, extra = false) {
        const inputClass = 'mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm';
        const fields = {};
        const input = (name, label, value, type = 'number') => {
            fields[name] = el('input', { name, type, value: value ?? '', min: 1, required: true, class: inputClass });
            return el('label', { class: 'text-sm' }, label, fields[name]);
        };
        const select = (name, label, options, value, placeholder = false) => {
            fields[name] = el('select', { name, class: inputClass, required: true },
                placeholder ? el('option', { value: '' }, 'Chọn…') : null,
                options.map((o) => el('option', { value: o.value, selected: String(o.value) === String(value) }, o.label)));
            return el('label', { class: 'text-sm' }, label, fields[name]);
        };
        const subjectOptions = section ? [section.subject] : extra ? data.subjects : data.framework.items.map((i) => i.subject);
        if (!subjectOptions.length) { toast('Chương trình khung chưa có học phần. Thêm môn vào khung trước.', 'info'); return; }
        const schedule = section?.schedules?.[0] ?? {};
        const message = el('div', { hidden: true });
        const lecturerHint = el('p', { class: 'text-xs text-on-surface-variant sm:col-span-2' }, 'Chọn lịch học để kiểm tra giảng viên.');
        const save = el('button', { type: 'submit', disabled: true, class: 'rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold disabled:opacity-50' }, 'Lưu lớp học phần');
        const cancel = button('Đóng', () => dialog.close());
        const scheduleBody = () => Object.fromEntries(['day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'week_parity', 'room_id'].map((k) => [k, k === 'week_parity' ? fields[k].value : Number(fields[k].value)]));
        const form = el('form', { class: 'space-y-4' },
            el('h3', { class: 'font-bold text-base' }, section ? `Sửa ${section.code}` : 'Thêm lớp vào đợt đăng ký'), message,
            el('div', { class: 'grid sm:grid-cols-2 gap-3' },
                select('subject_id', 'Học phần', subjectOptions.map((s) => ({ value: s.id, label: `${s.code} – ${s.name}` })), section?.subject.id),
                select('section_type', 'Loại lớp', [{ value: 'theory', label: 'Lý thuyết' }, { value: 'practice', label: 'Thực hành' }, { value: 'mixed', label: 'Kết hợp' }], section?.section_type ?? 'theory'),
                input('min_size', 'Sĩ số tối thiểu', section?.min_size ?? 15), input('max_size', 'Sĩ số tối đa', section?.max_size ?? 40),
                select('day_of_week', 'Thứ', [1, 2, 3, 4, 5, 6, 7].map((d) => ({ value: d, label: d === 7 ? 'Chủ nhật' : `Thứ ${d + 1}` })), schedule.day_of_week ?? 1),
                select('room_id', 'Phòng học', data.rooms.map((r) => ({ value: r.id, label: `${r.code} (${r.capacity} chỗ)` })), schedule.room_id, true),
                input('period_from', 'Từ tiết', schedule.period_from ?? 1), input('period_to', 'Đến tiết', schedule.period_to ?? 3),
                input('week_from', 'Từ tuần của học kỳ', schedule.week_from ?? 1), input('week_to', 'Đến tuần của học kỳ', schedule.week_to ?? Math.min(15, data.term.weeks)),
                select('week_parity', 'Tuần học', [{ value: 'all', label: 'Tất cả' }, { value: 'odd', label: 'Tuần lẻ' }, { value: 'even', label: 'Tuần chẵn' }], schedule.week_parity ?? 'all'),
                select('lecturer_id', 'Giảng viên trong khoa', [], null, true), lecturerHint),
            el('div', { class: 'flex justify-end gap-2' }, cancel, save));
        const dialog = el('dialog', { class: 'm-auto w-[min(94vw,720px)] max-h-[90vh] overflow-y-auto rounded-xl border border-outline-variant bg-white p-5 shadow-xl backdrop:bg-black/40' }, form);
        dialog.addEventListener('close', () => dialog.remove());
        document.body.append(dialog); dialog.showModal();
        let requestNumber = 0;
        async function refreshLecturers() {
            const version = ++requestNumber;
            const current = fields.lecturer_id.value || section?.lecturer?.id;
            save.disabled = true; fields.lecturer_id.disabled = true;
            lecturerHint.textContent = 'Đang kiểm tra lịch giảng viên…';
            try {
                const lecturers = await api.post(url(page.api.lecturerOptions), { schedule: scheduleBody(), ignore_section_id: section?.id ?? null });
                if (version !== requestNumber || !dialog.open) return;
                fields.lecturer_id.replaceChildren(el('option', { value: '' }, 'Chọn giảng viên còn trống lịch'), ...lecturers.map((l) => el('option', {
                    value: l.id, disabled: !l.available, selected: l.available && Number(current) === l.id,
                }, `${l.full_name} (${l.code})${l.available ? '' : ' — TRÙNG LỊCH'}`)));
                lecturerHint.textContent = lecturers.filter((l) => !l.available).map((l) => l.reason).join(' ') || 'Chỉ hiển thị giảng viên đang công tác trong khoa. Máy chủ kiểm tra lại lịch khi lưu.';
                fields.lecturer_id.disabled = false; save.disabled = !fields.lecturer_id.value;
            } catch (error) {
                if (version !== requestNumber) return;
                lecturerHint.textContent = error.message ?? 'Chưa kiểm tra được lịch; chọn đủ phòng, thứ, tiết và tuần học.';
            }
        }
        ['day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'week_parity', 'room_id'].forEach((key) => fields[key].addEventListener('change', refreshLecturers));
        fields.lecturer_id.addEventListener('change', () => { save.disabled = !fields.lecturer_id.value; });
        form.addEventListener('submit', async (event) => {
            event.preventDefault(); showAlert(message, null);
            const payload = { subject_id: Number(fields.subject_id.value), lecturer_id: Number(fields.lecturer_id.value),
                min_size: Number(fields.min_size.value), max_size: Number(fields.max_size.value), section_type: fields.section_type.value, schedule: scheduleBody() };
            try {
                await withBusy(save, () => section ? api.put(url(page.api.offeringUpdate, section.id), payload) : api.post(url(page.api.offeringStore), payload));
                dialog.close(); toast('Đã lưu lớp, giảng viên và lịch học.', 'success'); await reload(); await changed();
            } catch (error) { showAlert(message, error); }
        });
        if (schedule.room_id) refreshLecturers();
    }
}
