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
    const unscheduled = data.sections.filter((s) => !s.schedules.length).length;
    // Chọn nhiều lớp rồi bỏ khỏi đợt một lần (một yêu cầu, tải lại một lần)
    const selected = new Set();
    const checkboxes = new Map();
    const frameworkChecks = [];
    const selectionCount = el('span', { class: 'text-sm font-semibold' });
    const deleteSelected = el('button', { type: 'button', class: 'rounded-lg bg-error text-white px-3 py-2 text-xs font-semibold', onclick: removeSelected });
    const selectionBar = el('div', { hidden: true, class: 'sticky top-16 z-10 mt-4 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-error/40 bg-error-container px-3 py-2 text-on-error-container shadow-sm' },
        selectionCount,
        el('div', { class: 'flex gap-2' }, button('Bỏ chọn', () => { selected.clear(); syncSelection(); }), deleteSelected));

    container.replaceChildren(
        el('h3', { class: 'font-bold text-base' }, period.name),
        el('p', { class: 'mt-1 text-xs text-on-surface-variant' }, `${data.term.name}. Chọn kỳ CTĐT cho mọi khoa – khóa đang có sinh viên (${data.cells.length - missing.length}/${data.cells.length} đã chọn). Đủ hết, mỗi khung có lớp, mọi lớp có giảng viên và lịch thì mới công bố được.`),
        alert,
        matrix(),
        mutable && unscheduled ? el('div', { class: 'mt-4 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2' },
            el('p', { class: 'text-xs text-on-surface-variant' }, `${unscheduled} lớp chưa có lịch. Hệ thống chọn thứ 2–7 (ban ngày trước), phòng trống đủ chỗ, không trùng lớp khác cùng khoa – khóa, và giảng viên của khoa trống lịch, ít tiết nhất (lớp đã có giảng viên thì giữ).`),
            button(`Tự xếp lịch & giảng viên (${unscheduled} lớp)`, (e) => autoSchedule(e), true)) : null,
        selectionBar,
        el('div', { class: 'space-y-4 mt-4' }, data.frameworks.length
            ? data.frameworks.map(frameworkBlock)
            : emptyBlock('Chưa có khung nào', 'Chọn kỳ cho các ô trong bảng trên (hoặc "Điền theo gợi ý").', 'account_tree')));

    function syncSelection() {
        checkboxes.forEach((box, id) => { box.checked = selected.has(id); });
        frameworkChecks.forEach(({ box, ids }) => {
            const count = ids.filter((id) => selected.has(id)).length;
            box.checked = count > 0 && count === ids.length;
            box.indeterminate = count > 0 && count < ids.length;
        });
        selectionBar.hidden = selected.size === 0;
        selectionCount.textContent = `Đã chọn ${selected.size} lớp`;
        deleteSelected.textContent = `Xóa ${selected.size} lớp khỏi đợt`;
    }

    /** Tự xếp thứ, tiết, phòng trống (và giảng viên nếu lớp chưa có) cho các lớp chưa có lịch; báo lớp không xếp được. */
    async function autoSchedule(event, fw = null) {
        const target = event.currentTarget;
        try {
            const result = await withBusy(target, () => api.post(url(page.api.offeringAutoSchedule), { framework_id: fw?.id ?? null }));
            await reload(); await changed();
            if (result.skipped.length) {
                showAlert(alert, `Đã xếp ${result.scheduled.length} lớp. Chưa xếp được ${result.skipped.length} lớp: ${result.skipped.map((s) => `${s.code} — ${s.reason}`).join(' ')} Xếp tay các lớp này bằng "Sửa giảng viên / lịch".`, 'warning');
            } else {
                toast(`Đã xếp lịch và giảng viên cho ${result.scheduled.length} lớp.`, 'success');
            }
        } catch (error) { showAlert(alert, error); }
    }

    async function removeSelected(event) {
        const target = event.currentTarget;
        const ids = [...selected];
        const codes = data.sections.filter((s) => selected.has(s.id)).map((s) => s.code);
        const listed = codes.slice(0, 12).join(', ') + (codes.length > 12 ? ` và ${codes.length - 12} lớp khác` : '');
        if (!await confirmDialog({ title: `Bỏ ${ids.length} lớp khỏi đợt?`, message: `${listed}. Các lớp dự kiến sẽ được hủy; chương trình khung giữ nguyên.`, danger: true, confirmText: `Xóa ${ids.length} lớp` })) return;
        try {
            const result = await withBusy(target, () => api.post(url(page.api.offeringDeleteMany), { section_ids: ids }));
            toast(`Đã bỏ ${result.deleted} lớp khỏi đợt.`, 'success'); await reload(); await changed();
        } catch (error) { showAlert(alert, error); }
    }

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
        let selectAll = null;
        if (mutable && own.length) {
            const box = el('input', { type: 'checkbox', class: 'rounded border-outline-variant' });
            const ids = own.map((s) => s.id);
            box.addEventListener('change', () => { ids.forEach((id) => (box.checked ? selected.add(id) : selected.delete(id))); syncSelection(); });
            frameworkChecks.push({ box, ids });
            selectAll = el('label', { class: 'inline-flex items-center gap-2 text-xs font-semibold text-on-surface-variant' }, box, `Chọn cả ${own.length} lớp của khung`);
        }
        const head = el('div', { class: 'flex flex-wrap items-center justify-between gap-2' },
            el('div', {},
                el('p', { class: 'font-bold text-sm' }, `${fw.faculty.name} – Khóa ${fw.cohort.code}`),
                el('p', { class: 'text-xs text-on-surface-variant' }, `Kỳ ${fw.planned_term ?? '—'} theo CTĐT · ${fw.items.length} môn: ${fw.items.map((i) => i.subject.code).join(', ')}`)),
            mutable ? el('div', { class: 'flex flex-wrap gap-2' },
                button('Lớp & giảng viên', (e) => addClasses(e.currentTarget, fw), true),
                button('Thêm học phần khác', () => edit(null, fw, true)),
                own.some((s) => !s.schedules.length) ? button(`Tự xếp lịch (${own.filter((s) => !s.schedules.length).length} lớp)`, (e) => autoSchedule(e, fw)) : null,
                own.length ? null : button('Bỏ khỏi đợt', async () => {
                    if (!await confirmDialog({ title: `Bỏ khung ${fw.faculty.name} – ${fw.cohort.code} khỏi đợt?`, message: 'Khung vẫn giữ ở trang Chương trình khung.', confirmText: 'Bỏ khỏi đợt' })) return;
                    try { await api.del(url(page.api.frameworkDetach, { __FW__: fw.id })); await reload(); await changed(); }
                    catch (error) { showAlert(alert, error); }
                })) : null);

        return el('section', { class: 'rounded-xl border border-outline-variant p-3 space-y-3' }, head, selectAll,
            own.length ? el('div', { class: 'space-y-2' }, own.map((s) => sectionCard(s, fw))) : el('p', { class: 'text-xs text-amber-700 font-semibold' }, 'Chưa có lớp — bấm "Lớp & giảng viên".'));
    }

    function sectionCard(section, fw) {
        const title = `${section.code} · ${section.subject.name} (${section.subject.credits} TC)`;
        let heading = el('p', { class: 'font-semibold text-sm' }, title);
        if (mutable) {
            const box = el('input', { type: 'checkbox', class: 'rounded border-outline-variant', 'aria-label': `Chọn lớp ${section.code}` });
            box.addEventListener('change', () => { box.checked ? selected.add(section.id) : selected.delete(section.id); syncSelection(); });
            checkboxes.set(section.id, box);
            heading = el('label', { class: 'flex items-start gap-2 font-semibold text-sm cursor-pointer' }, box, el('span', {}, title));
        }

        return el('article', { class: 'rounded-lg border border-outline-variant p-3 space-y-1' },
            heading,
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
        // Gợi ý: mỗi lớp giao cho người đang ít tiết nhất, cộng cả số tiết của lớp vừa chia trong hộp thoại này
        const planned = new Map(lecturers.map((l) => [l.id, l.load]));
        const suggest = (subject) => {
            const pick = [...lecturers].sort((a, b) => planned.get(a.id) - planned.get(b.id) || a.code.localeCompare(b.code))[0];
            planned.set(pick.id, planned.get(pick.id) + (Number(subject.theory_periods ?? 0) + Number(subject.practice_periods ?? 0) || 45));
            return String(pick.id);
        };
        const small = 'w-20 rounded-lg border border-outline-variant bg-white px-2 py-1.5 text-sm';
        const rowsOf = fw.items.map((item) => {
            const opened = data.sections.filter((s) => s.framework_id === fw.id && s.subject.id === item.subject.id).length;
            const count = el('input', { type: 'number', min: 0, max: 20, value: opened ? 0 : item.planned_sections, class: small, 'aria-label': `Số lớp ${item.subject.code}` });
            const size = el('input', { type: 'number', min: 1, value: item.max_size ?? data.default_max_size, class: small, 'aria-label': `Sĩ số ${item.subject.code}` });
            const picks = el('div', { class: 'space-y-1' });
            const render = () => {
                const keep = [...picks.querySelectorAll('select')].map((s) => s.value);
                picks.replaceChildren(...Array.from({ length: Math.max(0, Number(count.value) || 0) }, (_, i) => {
                    const chosen = keep[i] ?? suggest(item.subject);
                    return el('select', { class: 'w-full rounded-lg border border-outline-variant bg-white px-2 py-1.5 text-sm', 'aria-label': `Giảng viên lớp ${i + 1} ${item.subject.code}` },
                        lecturers.map((l) => el('option', { value: l.id, selected: String(l.id) === chosen }, `Lớp ${i + 1}: ${l.full_name} (${l.code}) — ${l.load}/${l.limit} tiết`)));
                }));
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
            el('p', { class: 'text-xs text-on-surface-variant' }, 'Mỗi môn nhập số lớp, sĩ số và giảng viên của khoa cho từng lớp (gợi ý chia đều theo số tiết đang nhận, ít trước). Mở lớp xong bấm "Tự xếp lịch" để chọn thứ, phòng trống, hoặc xếp tay từng lớp.'),
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
        const weekHint = el('p', { class: 'text-xs sm:col-span-2' });
        let preferLecturer = null;
        const suggestButton = el('button', { type: 'button', class: 'rounded-lg border border-primary px-3 py-2 text-xs font-semibold text-primary sm:col-span-2 justify-self-start', onclick: suggestSlot },
            'Tự chọn thứ, phòng & giảng viên còn trống');
        let planError = false;
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
                weekHint,
                suggestButton,
                select('lecturer_id', 'Giảng viên trong khoa', [], null, true), lecturerHint),
            el('div', { class: 'flex justify-end gap-2' }, button('Đóng', () => dialog.close()), save));
        const dialog = el('dialog', { class: 'm-auto w-[min(94vw,720px)] max-h-[90vh] overflow-y-auto rounded-xl border border-outline-variant bg-white p-5 shadow-xl backdrop:bg-black/40' }, form);
        dialog.addEventListener('close', () => dialog.remove());
        document.body.append(dialog); dialog.showModal();
        /**
         * Số buổi theo tín chỉ: tổng tiết của loại lớp (lý thuyết 15 tiết / TC, thực hành 30 tiết / TC — số tiết lưu ở học
         * phần) chia số tiết mỗi buổi, rồi tính tuần kết thúc từ tuần bắt đầu (tuần lẻ / chẵn thì cách một tuần). Không
         * đủ tuần của học kỳ hoặc buổi thực hành ít hơn số tiết tối thiểu thì báo và không cho lưu.
         */
        function planWeeks(setWeekTo) {
            const subject = subjectOptions.find((s) => String(s.id) === fields.subject_id.value);
            const type = fields.section_type.value;
            const theory = Number(subject?.theory_periods ?? 0);
            const practice = Number(subject?.practice_periods ?? 0);
            const byType = { theory: [theory, subject?.theory_credits, 'lý thuyết'], practice: [practice, subject?.practice_credits, 'thực hành'] };
            let [total, credits, kind] = byType[type] ?? [theory + practice, subject?.credits, 'lý thuyết + thực hành'];
            if (!total) [total, credits, kind] = [theory + practice, subject?.credits, 'cả học phần'];
            const perSession = Number(fields.period_to.value) - Number(fields.period_from.value) + 1;
            const fail = (text) => { planError = true; weekHint.className = 'text-xs sm:col-span-2 font-semibold text-error'; weekHint.textContent = text; save.disabled = true; };
            planError = false;
            if (!subject || !total || !(perSession > 0)) {
                weekHint.className = 'text-xs sm:col-span-2 text-on-surface-variant';
                weekHint.textContent = 'Chọn học phần và tiết học để tự tính số buổi, tuần học.';
                return;
            }
            if (type === 'practice' && perSession < data.practice_min_periods) {
                fail(`Lớp thực hành cần tối thiểu ${data.practice_min_periods} tiết mỗi buổi (đang chọn ${perSession} tiết).`);
                return;
            }
            const sessions = Math.ceil(total / perSession);
            const parity = fields.week_parity.value;
            const step = parity === 'all' ? 1 : 2;
            let start = Number(fields.week_from.value) || 1;
            if ((parity === 'odd' && start % 2 === 0) || (parity === 'even' && start % 2 === 1)) start += 1;
            const end = start + (sessions - 1) * step;
            if (setWeekTo) fields.week_to.value = end;
            const spare = sessions * perSession - total;
            const plan = `${subject.code}: ${credits} TC ${kind} = ${total} tiết; ${perSession} tiết/buổi → ${sessions} buổi → tuần ${start}–${end}`
                + (spare ? ` (buổi cuối dư ${spare} tiết)` : '') + '.';
            if (end > data.term.weeks) {
                fail(`${plan} Học kỳ chỉ có ${data.term.weeks} tuần: tăng số tiết mỗi buổi, bắt đầu từ tuần sớm hơn hoặc học mọi tuần.`);
                return;
            }
            weekHint.className = 'text-xs sm:col-span-2 text-on-surface-variant';
            weekHint.textContent = plan;
            save.disabled = !fields.lecturer_id.value;
        }
        /** Giữ số tiết mỗi buổi, tuần và giảng viên đang chọn; hệ thống chọn thứ, tiết bắt đầu, phòng (và giảng viên nếu chưa chọn). */
        async function suggestSlot() {
            showAlert(message, null);
            const length = Number(fields.period_to.value) - Number(fields.period_from.value) + 1;
            const body = {
                framework_id: fw.id, section_type: fields.section_type.value, max_size: Number(fields.max_size.value),
                length: length > 0 ? length : 3, week_from: Number(fields.week_from.value), week_to: Number(fields.week_to.value),
                lecturer_id: fields.lecturer_id.value ? Number(fields.lecturer_id.value) : null, ignore_section_id: section?.id ?? null,
            };
            try {
                const slot = await withBusy(suggestButton, () => api.post(url(page.api.offeringSuggestSlot), body));
                ['day_of_week', 'period_from', 'period_to', 'room_id', 'week_from', 'week_to'].forEach((k) => { fields[k].value = slot[k]; });
                fields.week_parity.value = slot.week_parity;
                preferLecturer = slot.lecturer_id;
                planWeeks(false);
                await refreshLecturers();
            } catch (error) { showAlert(message, error); }
        }
        let requestNumber = 0;
        async function refreshLecturers() {
            const version = ++requestNumber;
            const current = preferLecturer ?? (fields.lecturer_id.value || section?.lecturer?.id);
            preferLecturer = null;
            save.disabled = true; fields.lecturer_id.disabled = true;
            lecturerHint.textContent = 'Đang kiểm tra lịch giảng viên…';
            try {
                const lecturers = await api.post(url(page.api.lecturerOptions), { schedule: scheduleBody(), framework_id: fw.id, ignore_section_id: section?.id ?? null });
                if (version !== requestNumber || !dialog.open) return;
                fields.lecturer_id.replaceChildren(el('option', { value: '' }, 'Chọn giảng viên còn trống lịch'), ...lecturers.map((l) => el('option', {
                    value: l.id, disabled: !l.available, selected: l.available && Number(current) === l.id,
                }, `${l.full_name} (${l.code})${l.available ? '' : ' — TRÙNG LỊCH'}`)));
                lecturerHint.textContent = lecturers.filter((l) => !l.available).map((l) => l.reason).join(' ') || 'Chỉ hiển thị giảng viên đang công tác trong khoa. Máy chủ kiểm tra lại lịch khi lưu.';
                fields.lecturer_id.disabled = false; save.disabled = !fields.lecturer_id.value || planError;
            } catch (error) {
                if (version !== requestNumber) return;
                lecturerHint.textContent = error.message ?? 'Chưa kiểm tra được lịch; chọn đủ phòng, thứ, tiết và tuần học.';
            }
        }
        // Học phần chỉ có tiết lý thuyết / chỉ có tiết thực hành thì chọn sẵn loại lớp tương ứng
        fields.subject_id.addEventListener('change', () => {
            const subject = subjectOptions.find((s) => String(s.id) === fields.subject_id.value);
            if (subject && !Number(subject.theory_periods) && Number(subject.practice_periods)) fields.section_type.value = 'practice';
            if (subject && Number(subject.theory_periods) && !Number(subject.practice_periods)) fields.section_type.value = 'theory';
        });
        // Tính lại tuần kết thúc trước khi kiểm tra lịch giảng viên (tuần kết thúc là một phần của lịch)
        ['subject_id', 'section_type', 'period_from', 'period_to', 'week_from', 'week_parity'].forEach((key) => fields[key].addEventListener('change', () => planWeeks(true)));
        ['subject_id', 'section_type'].forEach((key) => fields[key].addEventListener('change', refreshLecturers));
        ['day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'week_parity', 'room_id'].forEach((key) => fields[key].addEventListener('change', refreshLecturers));
        fields.lecturer_id.addEventListener('change', () => { save.disabled = !fields.lecturer_id.value || planError; });
        form.addEventListener('submit', async (event) => {
            event.preventDefault(); showAlert(message, null);
            const payload = { framework_id: fw.id, subject_id: Number(fields.subject_id.value), lecturer_id: Number(fields.lecturer_id.value),
                min_size: Number(fields.min_size.value), max_size: Number(fields.max_size.value), section_type: fields.section_type.value, schedule: scheduleBody() };
            try {
                await withBusy(save, () => section ? api.put(url(page.api.offeringUpdate, { __SECTION__: section.id }), payload) : api.post(url(page.api.offeringStore), payload));
                dialog.close(); toast('Đã lưu lớp, giảng viên và lịch học.', 'success'); await reload(); await changed();
            } catch (error) { showAlert(message, error); }
        });
        // Lớp mới: điền sẵn tuần kết thúc; lớp đã có lịch giữ tuần đã lưu, chỉ hiện số buổi theo tín chỉ
        planWeeks(!section?.schedules?.length);
        if (schedule.room_id) refreshLecturers();
    }
}
