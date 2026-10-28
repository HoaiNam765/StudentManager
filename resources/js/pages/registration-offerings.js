import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, loadingBlock, showAlert, toast, withBusy } from '../ui.js';

/**
 * Đợt đăng ký theo chương trình khung (cả học kỳ): bảng Khoa × Khóa chọn kỳ CTĐT từng ô, mỗi khung mở lớp + giảng viên
 * của khoa, xếp lịch từng lớp. Giữ kiểu thẻ / hộp thoại của trang ENR; mọi ràng buộc được kiểm tra lại ở API.
 */
export async function showOfferings(container, page, period, changed) {
    const url = (template, extra = {}) => Object.entries({ __ID__: period.id, ...extra }).reduce((u, [k, v]) => u.replace(k, v), template);
    const button = (text, action, primary = false) => el('button', {
        type: 'button',
        class: primary ? 'rounded-lg bg-primary text-white px-3 py-2 text-xs font-semibold' : 'rounded-lg border border-outline-variant px-3 py-2 text-xs font-semibold text-primary',
        onclick: action,
    }, text);
    const alert = el('div', { hidden: true });
    const inputClass = 'mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm';
    let data;
    container.replaceChildren(loadingBlock(3));
    try { data = await api.get(url(page.api.offerings)); }
    catch (error) { container.replaceChildren(errorBlock(error, () => showOfferings(container, page, period, changed))); return; }
    const reload = () => showOfferings(container, page, period, changed);
    const mutable = data.editable && page.can.offerings;
    const cellOf = (facultyId, cohortId) => data.cells.find((c) => c.faculty_id === facultyId && c.cohort_id === cohortId);
    const missing = data.cells.filter((c) => !c.framework_id);

    container.replaceChildren(
        el('h3', { class: 'font-bold text-base' }, period.name),
        el('p', { class: 'mt-1 text-xs text-on-surface-variant' }, `${data.term.name}. Chọn kỳ CTĐT cho mọi khoa – khóa đang có sinh viên (${data.cells.length - missing.length}/${data.cells.length} đã chọn). Đủ hết, mỗi khung có lớp, mọi lớp có giảng viên và lịch thì mới công bố được.`),
        alert,
        matrix(),
        el('div', { class: 'space-y-4 mt-4' }, data.frameworks.length
            ? data.frameworks.map(frameworkBlock)
            : emptyBlock('Chưa có khung nào', 'Chọn kỳ cho các ô trong bảng trên (hoặc "Điền theo gợi ý").', 'account_tree')));

    function matrix() {
        const th = (text) => el('th', { scope: 'col', class: 'px-3 py-2 text-left text-xs font-semibold text-on-surface-variant' }, text);
        const fill = mutable && missing.length
            ? button(`Điền theo gợi ý (${missing.length} ô)`, async (event) => {
                const target = event.currentTarget;
                try {
                    await withBusy(target, async () => {
                        for (const c of missing) {
                            if (c.suggested_term) await api.put(url(page.api.frameworkSetTerm), { faculty_id: c.faculty_id, cohort_id: c.cohort_id, planned_term: c.suggested_term });
                        }
                    });
                    toast('Đã chọn kỳ theo gợi ý cho các ô còn trống.', 'success'); await reload(); await changed();
                } catch (error) { showAlert(alert, error); await reload(); }
            }, true)
            : null;

        return el('div', { class: 'mt-3 space-y-2' },
            el('div', { class: 'flex items-center justify-between gap-2' }, el('p', { class: 'text-sm font-semibold' }, 'Bảng khung: Khoa × Khóa'), fill),
            el('div', { class: 'overflow-x-auto rounded-lg border border-outline-variant' },
                el('table', { class: 'w-full min-w-[520px] text-sm' },
                    el('thead', { class: 'bg-surface-container-low' }, el('tr', {}, th('Khoa'), data.cohorts.map((c) => th(`Khóa ${c.code}`)))),
                    el('tbody', {}, data.faculties.map((f) => el('tr', { class: 'border-t border-outline-variant align-top' },
                        el('td', { class: 'px-3 py-2 font-semibold' }, f.name),
                        data.cohorts.map((c) => el('td', { class: 'px-3 py-2' }, cell(cellOf(f.id, c.id))))))))));
    }

    function cell(c) {
        if (!c) return el('span', { class: 'text-xs text-on-surface-variant' }, '—');
        const select = el('select', { class: 'w-full rounded-lg border border-outline-variant bg-white px-2 py-1.5 text-sm', disabled: !mutable, 'aria-label': 'Kỳ CTĐT' },
            el('option', { value: '' }, 'Chọn kỳ…'),
            Array.from({ length: 8 }, (_, i) => el('option', { value: i + 1, selected: c.planned_term === i + 1 }, `Kỳ ${i + 1}`)));
        select.addEventListener('change', async () => {
            if (!select.value) return;
            try { await api.put(url(page.api.frameworkSetTerm), { faculty_id: c.faculty_id, cohort_id: c.cohort_id, planned_term: Number(select.value) }); await reload(); await changed(); }
            catch (error) { showAlert(alert, error); await reload(); }
        });

        return el('div', { class: 'space-y-1' }, select,
            el('p', { class: 'text-[11px] text-on-surface-variant' }, `Gợi ý: kỳ ${c.suggested_term ?? '—'} · ${c.students} SV`),
            c.framework_id
                ? el('p', { class: `text-[11px] ${c.sections === 0 || c.unscheduled ? 'text-amber-700 font-semibold' : 'text-emerald-700'}` },
                    `${c.items} môn · ${c.sections} lớp${c.unscheduled ? ` (${c.unscheduled} chưa xếp lịch)` : ''}`)
                : el('p', { class: 'text-[11px] text-amber-700 font-semibold' }, 'Chưa chọn kỳ'));
    }

    function frameworkBlock(fw) {
        const own = data.sections.filter((s) => s.framework_id === fw.id);
        const head = el('div', { class: 'flex flex-wrap items-center justify-between gap-2' },
            el('div', {},
                el('p', { class: 'font-bold text-sm' }, `${fw.faculty.name} – Khóa ${fw.cohort.code}`),
                el('p', { class: 'text-xs text-on-surface-variant' }, `Kỳ ${fw.planned_term ?? '—'} theo CTĐT · ${fw.items.length} môn: ${fw.items.map((i) => i.subject.code).join(', ')}`)),
            mutable ? el('div', { class: 'flex flex-wrap gap-2' },
                button('Lớp & giảng viên', (e) => addClasses(e.currentTarget, fw), true),
                button('Thêm học phần khác', () => edit(null, fw, true)),
                own.length ? null : button('Bỏ khỏi đợt', async () => {
                    if (!await confirmDialog({ title: `Bỏ khung ${fw.faculty.name} – ${fw.cohort.code} khỏi đợt?`, message: 'Khung vẫn giữ ở trang Chương trình khung.', confirmText: 'Bỏ khỏi đợt' })) return;
                    try { await api.del(url(page.api.frameworkDetach, { __FW__: fw.id })); await reload(); await changed(); }
                    catch (error) { showAlert(alert, error); }
                })) : null);

        return el('section', { class: 'rounded-xl border border-outline-variant p-3 space-y-3' }, head,
            own.length ? el('div', { class: 'space-y-2' }, own.map((s) => sectionCard(s, fw))) : el('p', { class: 'text-xs text-amber-700 font-semibold' }, 'Chưa có lớp — bấm "Lớp & giảng viên".'));
    }

    function sectionCard(section, fw) {
        return el('article', { class: 'rounded-lg border border-outline-variant p-3 space-y-1' },
            el('p', { class: 'font-semibold text-sm' }, `${section.code} · ${section.subject.name} (${section.subject.credits} TC)`),
            el('p', { class: 'text-xs text-on-surface-variant' }, `${section.lecturer?.full_name ?? 'Chưa phân công'} · Sĩ số ${section.min_size}–${section.max_size}`),
            section.schedules.length
                ? section.schedules.map((s) => el('p', { class: 'text-xs text-on-surface-variant' }, `${s.description} · ${s.room ?? '—'}`))
                : el('p', { class: 'text-xs font-semibold text-amber-700' }, 'Chưa xếp lịch — bấm "Sửa giảng viên / lịch" để chọn phòng, thứ, tiết.'),
            mutable ? el('div', { class: 'flex gap-2 pt-1' }, button('Sửa giảng viên / lịch', () => edit(section, fw)), button('Xóa khỏi đợt', async () => {
                if (!await confirmDialog({ title: `Bỏ lớp ${section.code}?`, message: 'Lớp dự kiến sẽ được hủy; chương trình khung giữ nguyên.', danger: true, confirmText: 'Xóa khỏi đợt' })) return;
                try { await api.del(url(page.api.offeringDelete, { __SECTION__: section.id })); await reload(); await changed(); }
                catch (error) { showAlert(alert, error); }
            })) : null);
    }

    /** Số lớp + sĩ số + giảng viên của khoa từng lớp cho các môn của khung (lịch xếp sau từng lớp). */
    async function addClasses(trigger, fw) {
        let lecturers;
        try { lecturers = await withBusy(trigger, () => api.get(url(page.api.frameworkLecturers, { __FW__: fw.id }))); }
        catch (error) { showAlert(alert, error); return; }
        if (!lecturers.length) { showAlert(alert, 'Khoa chưa có giảng viên đang công tác để phân công.', 'warning'); return; }
        const byLoad = [...lecturers].sort((a, b) => a.load - b.load);
        let turn = 0;
        const small = 'w-20 rounded-lg border border-outline-variant bg-white px-2 py-1.5 text-sm';
        const rowsOf = fw.items.map((item) => {
            const opened = data.sections.filter((s) => s.framework_id === fw.id && s.subject.id === item.subject.id).length;
            const count = el('input', { type: 'number', min: 0, max: 20, value: opened ? 0 : item.planned_sections, class: small, 'aria-label': `Số lớp ${item.subject.code}` });
            const size = el('input', { type: 'number', min: 1, value: item.max_size ?? data.default_max_size, class: small, 'aria-label': `Sĩ số ${item.subject.code}` });
            const picks = el('div', { class: 'space-y-1' });
            const render = () => {
                const keep = [...picks.querySelectorAll('select')].map((s) => s.value);
                picks.replaceChildren(...Array.from({ length: Math.max(0, Number(count.value) || 0) }, (_, i) => el('select', { class: 'w-full rounded-lg border border-outline-variant bg-white px-2 py-1.5 text-sm', 'aria-label': `Giảng viên lớp ${i + 1} ${item.subject.code}` },
                    lecturers.map((l) => el('option', { value: l.id, selected: String(l.id) === (keep[i] ?? String(byLoad[turn++ % byLoad.length].id)) }, `Lớp ${i + 1}: ${l.full_name} (${l.code}) — ${l.load}/${l.limit} tiết`)))));
                if (!picks.children.length) picks.append(el('span', { class: 'text-xs text-on-surface-variant' }, opened ? `Đã mở ${opened} lớp` : 'Không mở lớp'));
            };
            count.addEventListener('input', render);
            render();

            return { item, size, picks, row: el('tr', { class: 'border-t border-outline-variant align-top' },
                el('td', { class: 'px-2 py-2' }, el('p', { class: 'font-semibold' }, item.subject.code), el('p', { class: 'text-xs text-on-surface-variant' }, `${item.subject.name} · ${item.subject.credits} TC`)),
                el('td', { class: 'px-2 py-2' }, count), el('td', { class: 'px-2 py-2' }, size), el('td', { class: 'px-2 py-2 min-w-[260px]' }, picks)) };
        });
        const message = el('div', { hidden: true });
        const overload = el('input', { type: 'checkbox', class: 'mr-2' });
        const save = el('button', { type: 'submit', class: 'rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold' }, 'Mở lớp');
        const th = (text) => el('th', { scope: 'col', class: 'px-2 py-2 text-left text-xs font-semibold text-on-surface-variant' }, text);
        const form = el('form', { class: 'space-y-4' },
            el('h3', { class: 'font-bold text-base' }, `Lớp & giảng viên — ${fw.faculty.name} · Khóa ${fw.cohort.code}`),
            el('p', { class: 'text-xs text-on-surface-variant' }, 'Mỗi môn nhập số lớp, sĩ số và giảng viên của khoa cho từng lớp (gợi ý chia theo số tiết đang nhận, ít trước). Lịch học xếp sau ở từng lớp.'),
            message,
            el('div', { class: 'overflow-x-auto rounded-lg border border-outline-variant' },
                el('table', { class: 'w-full min-w-[640px] text-sm' },
                    el('thead', { class: 'bg-surface-container-low' }, el('tr', {}, th('Học phần'), th('Số lớp'), th('Sĩ số tối đa'), th('Giảng viên của khoa (đã nhận / định mức tiết)'))),
                    el('tbody', {}, rowsOf.map((r) => r.row)))),
            el('label', { class: 'flex items-center text-sm' }, overload, 'Đồng ý phân công dù giảng viên vượt định mức tiết'),
            el('div', { class: 'flex justify-end gap-2' }, el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold', onclick: () => dialog.close() }, 'Hủy'), save));
        const dialog = el('dialog', { class: 'm-auto w-[min(96vw,900px)] max-h-[92vh] overflow-y-auto rounded-xl border border-outline-variant bg-white p-5 shadow-xl backdrop:bg-black/40' }, form);
        dialog.addEventListener('close', () => dialog.remove());
        document.body.append(dialog); dialog.showModal();
        form.addEventListener('submit', async (event) => {
            event.preventDefault(); showAlert(message, null);
            const classes = rowsOf.flatMap((r) => [...r.picks.querySelectorAll('select')].map((s) => ({ item_id: r.item.id, lecturer_id: Number(s.value), max_size: Number(r.size.value) || null })));
            try {
                const result = await withBusy(save, () => api.post(url(page.api.frameworkClasses, { __FW__: fw.id }), { classes, confirm_overload: overload.checked }));
                dialog.close(); toast(`Đã mở ${result.opened.length} lớp. Xếp lịch từng lớp trước khi công bố.`, 'success'); await reload(); await changed();
            } catch (error) { showAlert(message, error); }
        });
    }

    /** Thêm / sửa một lớp kèm giảng viên của khoa và lịch học. */
    async function edit(section, fw, extra = false) {
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
        const subjectOptions = section ? [section.subject] : extra ? data.subjects : fw.items.map((i) => i.subject);
        const schedule = section?.schedules?.[0] ?? {};
        const message = el('div', { hidden: true });
        const lecturerHint = el('p', { class: 'text-xs text-on-surface-variant sm:col-span-2' }, 'Chọn lịch học để kiểm tra giảng viên.');
        const save = el('button', { type: 'submit', disabled: true, class: 'rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold disabled:opacity-50' }, 'Lưu lớp học phần');
        const scheduleBody = () => Object.fromEntries(['day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'week_parity', 'room_id'].map((k) => [k, k === 'week_parity' ? fields[k].value : Number(fields[k].value)]));
        const form = el('form', { class: 'space-y-4' },
            el('h3', { class: 'font-bold text-base' }, section ? `Sửa ${section.code}` : `${extra ? 'Thêm học phần khác' : 'Thêm lớp'} — ${fw.faculty.name} · Khóa ${fw.cohort.code}`),
            extra ? el('p', { class: 'text-xs text-on-surface-variant' }, 'Học phần chưa có trong khung sẽ được thêm vào khung của khoa – khóa này (loại tự chọn).') : null,
            message,
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
            el('div', { class: 'flex justify-end gap-2' }, button('Đóng', () => dialog.close()), save));
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
                const lecturers = await api.post(url(page.api.lecturerOptions), { schedule: scheduleBody(), framework_id: fw.id, ignore_section_id: section?.id ?? null });
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
            const payload = { framework_id: fw.id, subject_id: Number(fields.subject_id.value), lecturer_id: Number(fields.lecturer_id.value),
                min_size: Number(fields.min_size.value), max_size: Number(fields.max_size.value), section_type: fields.section_type.value, schedule: scheduleBody() };
            try {
                await withBusy(save, () => section ? api.put(url(page.api.offeringUpdate, { __SECTION__: section.id }), payload) : api.post(url(page.api.offeringStore), payload));
                dialog.close(); toast('Đã lưu lớp, giảng viên và lịch học.', 'success'); await reload(); await changed();
            } catch (error) { showAlert(message, error); }
        });
        if (schedule.room_id) refreshLecturers();
    }
}
