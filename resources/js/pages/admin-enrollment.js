import { showOfferings } from './registration-offerings.js';
import { listAll } from '../lookups.js';
import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const BADGE = { draft: 'bg-slate-100 text-slate-800', published: 'bg-blue-50 text-blue-800', finalized: 'bg-emerald-50 text-emerald-800' };

export default function adminEnrollment(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const termSelect = root.querySelector('[data-filter="term"]');
    const form = root.querySelector('[data-form="period"]');
    const state = { terms: [], cohorts: [], majors: [], classes: [], editId: null, frameworks: [], periods: [], selectedPeriod: null };
    const url = (template, id, status = '') => template.replace('__ID__', encodeURIComponent(id)).replace('__TERM__', encodeURIComponent(id)).replace('__STATUS__', encodeURIComponent(status));

    termSelect.addEventListener('change', () => { resetForm(); state.selectedPeriod = null; region('offerings').replaceChildren(); loadAll(); });
    root.querySelector('[data-action="reload"]').addEventListener('click', loadAll);
    root.querySelector('[data-action="add-target"]')?.addEventListener('click', () => addTargetRow());
    root.querySelector('[data-action="cancel-edit"]')?.addEventListener('click', resetForm);
    form?.addEventListener('submit', savePeriod);

    bootstrap();

    async function bootstrap() {
        region('periods').replaceChildren(loadingBlock(3));
        try {
            const years = await api.get(page.api.terms);
            // Lookup endpoints are protected by other modules. Read-only ENR users can still view periods/stats.
            const [cohorts, majors, classes] = await Promise.all([
                api.get(page.api.cohorts).catch(() => []),
                api.get(page.api.majors).catch(() => ({ data: [] })),
                api.get(page.api.adminClasses).catch(() => ({ data: [] })),
            ]);
            state.terms = (years?.data ?? []).flatMap((year) => (year.terms ?? []).map((term) => ({ ...term, academic_year: year.name })));
            state.cohorts = Array.isArray(cohorts) ? cohorts : (cohorts?.data ?? []);
            state.majors = majors?.data ?? [];
            state.classes = classes?.data ?? [];
            fill(termSelect, state.terms, (x) => `${x.name} · ${x.academic_year}`);
            // Đến từ trang Chương trình khung (?term_id=&period_id=): chọn đúng học kỳ và mở danh sách lớp của đợt
            const params = new URLSearchParams(window.location.search);
            if (params.get('term_id') && state.terms.some((t) => String(t.id) === params.get('term_id'))) termSelect.value = params.get('term_id');
            resetForm();
            await loadAll();
            const fromFramework = state.periods.find((p) => String(p.id) === params.get('period_id'));
            if (fromFramework?.training_framework_id) {
                state.selectedPeriod = fromFramework;
                await showOfferings(region('offerings'), page, fromFramework, loadPeriods);
                region('offerings').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } catch (error) { region('periods').replaceChildren(errorBlock(error, bootstrap)); }
    }

    async function loadAll() {
        if (!termSelect.value) {
            region('periods').replaceChildren(emptyBlock('Chưa có học kỳ', 'Tạo năm học và học kỳ trước khi cấu hình đợt đăng ký.'));
            region('stats').replaceChildren(); region('finalize').replaceChildren(); region('under-min').replaceChildren();
            return;
        }
        await Promise.all([loadFrameworks(), loadPeriods(), loadStats(), loadFinalizePreview(), loadUnderMin()]);
    }

    async function loadPeriods() {
        region('periods').replaceChildren(loadingBlock(3));
        try {
            const data = await api.get(page.api.periods, { term_id: termSelect.value });
            const rows = Array.isArray(data) ? data : (data?.data ?? []);
            state.periods = rows;
            region('periods').replaceChildren(rows.length ? el('div', { class: 'space-y-3' }, rows.map(periodCard)) : emptyBlock('Chưa có đợt đăng ký', 'Tạo đợt đầu tiên cho học kỳ này.', 'event_available'));
        } catch (error) { region('periods').replaceChildren(errorBlock(error, loadPeriods)); }
    }

    function periodCard(item) {
        const actions = [];
        if (page.can.update && item.status === 'draft') {
            actions.push(el('button', { type: 'button', class: 'text-xs text-primary font-semibold hover:underline', onclick: () => editPeriod(item) }, 'Sửa'));
            actions.push(el('button', { type: 'button', class: 'text-xs text-blue-700 font-semibold hover:underline', onclick: (e) => publishPeriod(e.currentTarget, item) }, 'Công bố'));
        }
        if (item.training_framework_id) {
            actions.push(el('button', { type: 'button', class: 'text-xs text-primary font-semibold hover:underline', onclick: () => {
                state.selectedPeriod = item; showOfferings(region('offerings'), page, item, loadPeriods);
                region('offerings').scrollIntoView({ behavior: 'smooth', block: 'start' });
            } }, 'Học phần / giảng viên / lịch'));
            if (page.can.update && page.can.notify && item.status === 'draft') actions.push(el('button', { type: 'button', class: 'text-xs text-primary font-semibold hover:underline', onclick: (e) => announce(e.currentTarget, item) }, item.preview_at ? 'Gửi lại thông báo xem trước' : 'Thông báo xem trước'));
        }
        if (page.can.update && item.status === 'published' && !item.is_open && new Date(item.opens_at.replace(' ', 'T') + '+07:00') > new Date()) {
            actions.push(el('button', { type: 'button', class: 'text-xs text-primary font-semibold hover:underline', onclick: (e) => openEarly(e.currentTarget, item) }, 'Mở ngay (sớm hơn lịch)'));
        }
        if (page.can.delete && item.status === 'draft') actions.push(el('button', { type: 'button', class: 'text-xs text-red-700 font-semibold hover:underline', onclick: (e) => deletePeriod(e.currentTarget, item) }, 'Xóa'));
        return el('article', { class: 'rounded-xl border border-outline-variant p-4 space-y-2' },
            el('div', { class: 'flex items-start justify-between gap-3' },
                el('div', {}, el('p', { class: 'font-bold' }, item.name), el('p', { class: 'text-xs text-on-surface-variant' }, `${item.round_label} · ${item.opens_at ?? '—'} → ${item.closes_at ?? '—'}`)),
                el('span', { class: `rounded-full px-2 py-1 text-[11px] font-semibold ${BADGE[item.status] ?? ''}` }, item.status_label)),
            item.framework_label ? el('p', { class: 'text-xs text-on-surface-variant' }, item.framework_label) : null,
            el('p', { class: 'text-xs text-on-surface-variant' }, `Khung tín chỉ: ${item.min_credits ?? '—'} – ${item.max_credits ?? '—'} · ${item.targets?.length ?? 0} nhóm đối tượng`),
            item.targets?.length ? el('div', { class: 'text-xs text-on-surface-variant space-y-0.5' }, item.targets.map((x) => el('p', {}, `• ${x.description}`))) : null,
            el('div', { class: 'flex flex-wrap gap-3 pt-1' }, actions));
    }

    async function loadStats() {
        try {
            const data = await api.get(page.api.stats, { term_id: termSelect.value });
            const s = data.summary ?? {};
            region('stats').replaceChildren(el('div', { class: 'grid sm:grid-cols-2 lg:grid-cols-4 gap-3' },
                metric('Sinh viên đã đăng ký', s.students_registered ?? '—'),
                metric('Chưa đăng ký', s.students_not_registered ?? '—'),
                metric('Thiếu tín chỉ', s.students_under_min_credits ?? '—'),
                metric('Lớp dưới ngưỡng', data.under_min_sections?.length ?? s.under_min_sections ?? '—')));
        } catch { region('stats').replaceChildren(); }
    }

    async function loadFinalizePreview() {
        region('finalize').replaceChildren(loadingBlock(2));
        try {
            const p = await api.get(url(page.api.preview, termSelect.value));
            const issues = [];
            if (p.open_periods?.length) issues.push(`Còn ${p.open_periods.length} đợt đang mở: ${p.open_periods.join(', ')}`);
            if (p.under_min_sections?.length) issues.push(`Còn ${p.under_min_sections.length} lớp dưới sĩ số tối thiểu.`);
            if (p.students_under_min_credits?.length) issues.push(`Còn ${p.students_under_min_credits.length} sinh viên dưới tín chỉ tối thiểu.`);
            if (p.students_not_registered) issues.push(`Còn ${p.students_not_registered} sinh viên chưa đăng ký.`);
            const box = el('div', { class: 'space-y-3' },
                el('div', { class: `rounded-lg border p-3 text-sm ${issues.length ? 'border-amber-300 bg-amber-50 text-amber-900' : 'border-emerald-200 bg-emerald-50 text-emerald-900'}` },
                    el('p', { class: 'font-semibold' }, p.already_finalized ? 'Học kỳ đã được chốt' : issues.length ? 'Cần kiểm tra trước khi chốt' : 'Có thể chốt đăng ký'),
                    issues.map((x) => el('p', { class: 'mt-1' }, `• ${x}`)),
                    el('p', { class: 'mt-2 text-xs' }, `Số đăng ký sẽ xác nhận: ${p.to_confirm ?? 0}`)),
            );
            const blocked = (p.open_periods?.length ?? 0) > 0 || (p.under_min_sections?.length ?? 0) > 0;
            if (page.can.approve && !p.already_finalized && !blocked) box.append(el('button', { type: 'button', class: 'rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold', onclick: finalizeTerm }, 'Chốt đăng ký'));
            region('finalize').replaceChildren(box);
        } catch (error) { region('finalize').replaceChildren(errorBlock(error, loadFinalizePreview)); }
    }

    async function finalizeTerm(event) {
        const button = event.currentTarget;
        // Re-read the backend preview immediately before a consequential action.
        let preview;
        try { preview = await api.get(url(page.api.preview, termSelect.value)); }
        catch (error) { showAlert(region('alert'), error); return; }
        if (preview.already_finalized || (preview.open_periods?.length ?? 0) || (preview.under_min_sections?.length ?? 0)) {
            showAlert(region('alert'), 'Không thể chốt: còn đợt đang mở, lớp thiếu sĩ số hoặc học kỳ đã được chốt. Hãy xử lý và xem trước lại.');
            await loadFinalizePreview();
            return;
        }
        const needsAcknowledgement = (preview.students_under_min_credits?.length ?? 0) > 0;
        const message = needsAcknowledgement
            ? `Có ${preview.students_under_min_credits.length} sinh viên dưới tín chỉ tối thiểu. Chỉ xác nhận nếu đã xem danh sách và chấp nhận ngoại lệ này; kết quả sẽ được ghi nhận.`
            : 'Đăng ký hiện tại sẽ được xác nhận; sinh viên không thể tự hủy theo luồng thông thường.';
        const ok = await confirmDialog({ title: needsAcknowledgement ? 'Xác nhận ngoại lệ thiếu tín chỉ?' : 'Chốt đăng ký học kỳ?', message, danger: needsAcknowledgement, confirmText: needsAcknowledgement ? 'Tôi đã kiểm tra, chốt đăng ký' : 'Chốt đăng ký' });
        if (!ok) return;
        try {
            await withBusy(button, () => api.post(url(page.api.finalize, termSelect.value), { acknowledge_under_min: needsAcknowledgement }));
            toast('Đã chốt đăng ký học kỳ.', 'success'); await loadAll();
        } catch (error) { showAlert(region('alert'), error); }
    }

    async function loadUnderMin() {
        region('under-min').replaceChildren(loadingBlock(2));
        try {
            const result = await api.get(url(page.api.underMin, termSelect.value));
            const rows = result.data ?? [];
            region('under-min').replaceChildren(rows.length ? el('div', { class: 'space-y-3' }, rows.map(underMinCard)) : emptyBlock('Không có lớp thiếu sĩ số', 'Không còn lớp cần xử lý trước khi chốt.', 'task_alt'));
        } catch (error) { region('under-min').replaceChildren(errorBlock(error, loadUnderMin)); }
    }

    function underMinCard(row) {
        const actions = [];
        if (page.can.classUpdate && row.merge_targets?.length) actions.push(el('button', { type: 'button', class: 'text-xs text-primary font-semibold hover:underline', onclick: (e) => mergeSection(e.currentTarget, row) }, 'Gộp'));
        if (page.can.classUpdate) actions.push(el('button', { type: 'button', class: 'text-xs text-red-700 font-semibold hover:underline', onclick: (e) => cancelSection(e.currentTarget, row) }, 'Hủy lớp'));
        return el('article', { class: 'rounded-xl border border-outline-variant p-3' },
            el('div', { class: 'flex justify-between gap-3' }, el('div', {}, el('p', { class: 'font-semibold' }, `${row.code} · ${row.subject?.code ?? ''}`), el('p', { class: 'text-xs text-on-surface-variant' }, row.subject?.name ?? '')), el('strong', { class: 'text-red-700' }, `${row.enrolled}/${row.min_size}`)),
            el('div', { class: 'flex gap-3 mt-2' }, actions));
    }

    async function mergeSection(button, row) {
        const targets = row.merge_targets.map((x) => `${x.id}: ${x.code} (${x.enrolled}/${x.max_size})`).join('\n');
        const ok = await confirmDialog({ title: `Gộp lớp ${row.code}`, message: `Lớp đích:\n${targets}\n\nNhập ID lớp đích | lý do`, input: { label: 'Ví dụ: 12 | Thiếu sĩ số', required: true, maxlength: 600 }, confirmText: 'Gộp' });
        if (!ok) return;
        const [id, ...rest] = ok.value.split('|'); const targetId = Number(id.trim()); const reason = rest.join('|').trim();
        if (!targetId || !reason) { toast('Nhập đúng định dạng ID | lý do.', 'error'); return; }
        try { await withBusy(button, () => api.post(url(page.api.merge, row.id), { target_id: targetId, reason })); toast('Đã gộp lớp.', 'success'); await loadAll(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function cancelSection(button, row) {
        const ok = await confirmDialog({ title: `Hủy lớp ${row.code}?`, danger: true, input: { label: 'Lý do hủy', required: true, maxlength: 500 }, confirmText: 'Hủy lớp' });
        if (!ok) return;
        try { await withBusy(button, () => api.post(url(page.api.sectionStatus, row.id, 'cancelled'), { reason: ok.value })); toast('Đã hủy lớp.', 'success'); await loadAll(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    function resetForm() {
        if (!form) return;
        state.editId = null; form.reset(); form.elements.training_framework_id.disabled = false; form.elements.training_framework_id.required = true; region('targets').replaceChildren(); addTargetRow();
        root.querySelector('[data-form-title]').textContent = 'Tạo đợt đăng ký'; root.querySelector('[data-action="cancel-edit"]').hidden = true;
        form.querySelector('button[type="submit"]').textContent = 'Lưu đợt đăng ký';
    }

    function editPeriod(item) {
        state.editId = item.id; form.elements.training_framework_id.value = item.training_framework_id ?? ''; form.elements.training_framework_id.disabled = true; form.elements.training_framework_id.required = false;
        form.elements.name.value = item.name ?? ''; form.elements.round.value = item.round ?? 'round_1';
        form.elements.opens_at.value = toLocalInput(item.opens_at); form.elements.closes_at.value = toLocalInput(item.closes_at);
        form.elements.min_credits.value = item.min_credits ?? ''; form.elements.max_credits.value = item.max_credits ?? '';
        region('targets').replaceChildren(); (item.targets?.length ? item.targets : [{}]).forEach(addTargetRow);
        root.querySelector('[data-form-title]').textContent = `Sửa ${item.name}`; root.querySelector('[data-action="cancel-edit"]').hidden = false;
        form.querySelector('button[type="submit"]').textContent = 'Lưu thay đổi'; form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function addTargetRow(target = {}) {
        const row = el('div', { class: 'grid sm:grid-cols-[1fr_1fr_1fr_auto] gap-2 items-end', dataset: { targetRow: '1' } },
            targetSelect('Khóa', 'cohort_id', state.cohorts, (x) => `${x.code} · ${x.start_year}`, target.cohort_id),
            targetSelect('Ngành', 'major_id', state.majors, (x) => `${x.code} · ${x.name}`, target.major_id),
            targetSelect('Lớp HC', 'admin_class_id', state.classes, (x) => `${x.code}`, target.admin_class_id),
            el('button', { type: 'button', class: 'rounded-lg border border-red-200 text-red-700 px-3 py-2 text-xs', onclick: () => row.remove() }, 'Xóa'));
        region('targets').append(row);
    }

    function targetSelect(label, name, items, text, current) {
        const select = el('select', { name, class: 'mt-1 w-full rounded-lg border border-outline-variant px-2 py-2 text-sm' }, el('option', { value: '' }, 'Tất cả / không chọn'), items.map((x) => el('option', { value: x.id, selected: Number(current) === Number(x.id) }, text(x))));
        return el('label', { class: 'text-xs' }, label, select);
    }

    async function savePeriod(event) {
        event.preventDefault(); const button = form.querySelector('button[type="submit"]');
        const body = { name: form.elements.name.value.trim(), round: form.elements.round.value, opens_at: fromLocalInput(form.elements.opens_at.value), closes_at: fromLocalInput(form.elements.closes_at.value), min_credits: numberOrNull(form.elements.min_credits.value), max_credits: numberOrNull(form.elements.max_credits.value), targets: collectTargets() };
        if (!state.editId) { body.term_id = Number(termSelect.value); body.training_framework_id = Number(form.elements.training_framework_id.value); }
        Object.keys(body).forEach((k) => { if (body[k] === null || body[k] === '') delete body[k]; });
        try { await withBusy(button, () => state.editId ? api.put(url(page.api.periodUpdate, state.editId), body) : api.post(page.api.periodStore, body)); toast(state.editId ? 'Đã cập nhật đợt đăng ký.' : 'Đã tạo đợt đăng ký.', 'success'); resetForm(); await loadAll(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function publishPeriod(button, item) {
        const ok = await confirmDialog({ title: `Công bố ${item.name}?`, message: 'Sinh viên thuộc đối tượng áp dụng có thể đăng ký trong khung thời gian đã cấu hình.', confirmText: 'Công bố' }); if (!ok) return;
        try { await withBusy(button, () => api.post(url(page.api.periodPublish, item.id), {})); toast('Đã công bố đợt đăng ký.', 'success'); if (state.selectedPeriod?.id === item.id) { state.selectedPeriod = { ...item, status: 'published' }; await showOfferings(region('offerings'), page, state.selectedPeriod, loadPeriods); } await loadAll(); } catch (error) { showAlert(region('alert'), error); }
    }

    async function deletePeriod(button, item) {
        const ok = await confirmDialog({ title: `Xóa ${item.name}?`, danger: true, confirmText: 'Xóa' }); if (!ok) return;
        try { await withBusy(button, () => api.del(url(page.api.periodDelete, item.id))); toast('Đã xóa đợt đăng ký.', 'success'); await loadAll(); } catch (error) { showAlert(region('alert'), error); }
    }

    async function loadFrameworks() {
        if (!form || state.editId) return;
        const select = form.elements.training_framework_id;
        const current = select.value;
        try {
            state.frameworks = await listAll(page.api.frameworks, { term_id: termSelect.value });
            select.replaceChildren(el('option', { value: '' }, 'Chọn chương trình khung…'), ...state.frameworks.map((f) => el('option', { value: f.id, selected: String(f.id) === current }, `${f.faculty?.name} · ${f.cohort?.code} (${f.items_count} môn)`)));
        } catch (error) { showAlert(region('alert'), error); }
    }

    async function announce(button, item) {
        if (!await confirmDialog({ title: 'Thông báo cho sinh viên xem trước?', message: 'Gửi thông báo tới sinh viên đúng khoa và khóa. Danh sách lớp, giảng viên và lịch sẽ hiện để xem; chưa được đăng ký.', confirmText: 'Thông báo xem trước' })) return;
        try { await withBusy(button, () => api.post(url(page.api.announce, item.id), {})); toast('Đã thông báo danh sách để xem trước.', 'success'); await loadPeriods(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    async function openEarly(button, item) {
        if (!await confirmDialog({ title: 'Mở đăng ký ngay?', message: `Đợt ${item.name} sẽ bắt đầu ngay theo giờ máy chủ, giữ nguyên hạn đóng. Sinh viên sẽ nhận thông báo.`, confirmText: 'Mở ngay' })) return;
        try { await withBusy(button, () => api.post(url(page.api.periodOpenEarly, item.id), {})); toast('Đã mở đăng ký sớm.', 'success'); await loadAll(); }
        catch (error) { showAlert(region('alert'), error); }
    }

    function collectTargets() {
        return [...region('targets').querySelectorAll('[data-target-row]')].map((row) => {
            const out = {}; ['cohort_id', 'major_id', 'admin_class_id'].forEach((key) => { const value = row.querySelector(`[name="${key}"]`).value; if (value) out[key] = Number(value); }); return out;
        }).filter((x) => Object.keys(x).length);
    }

    function metric(label, value) { return el('div', { class: 'rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm' }, el('p', { class: 'text-xs text-on-surface-variant' }, label), el('p', { class: 'mt-1 text-2xl font-bold' }, value)); }
    function fill(select, items, label) { select.replaceChildren(); items.forEach((x) => select.append(el('option', { value: x.id }, label(x)))); }
    function numberOrNull(v) { return v === '' ? null : Number(v); }
    function toLocalInput(value) { return value ? value.replace(' ', 'T') : ''; }
    function fromLocalInput(value) { return value ? value.replace('T', ' ') : null; }
}
