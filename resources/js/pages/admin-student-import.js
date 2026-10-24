/**
 * Nhập hồ sơ sinh viên từ Excel (FE #16, mock admin/sinh-vien-import) qua trung tâm import (FR-SYS-007, importer
 * `students`): tải lên → kiểm tra (chạy nền thì theo dõi tiến độ) → xem trước dòng lỗi theo cột → lưu dòng hợp lệ →
 * hoàn tác lô khi dữ liệu chưa phát sinh liên quan.
 */
import { api } from '../api.js';
import { rows, tpl } from '../lookups.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const RUNNING = ['validating', 'saving'];

export default function adminStudentImport(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const form = root.querySelector('[data-form="upload"]');

    form?.addEventListener('submit', upload);
    root.querySelector('[data-action="reload"]').addEventListener('click', loadHistory);

    loadTemplate();
    loadHistory();

    async function loadTemplate() {
        try {
            const importer = rows(await api.get(page.api.importers)).find((i) => i.key === page.importer);
            const link = root.querySelector('[data-field="template"]');

            if (importer?.template_url) {
                link.href = importer.template_url;
                link.hidden = false;
            }
        } catch {
            // Không có mẫu thì vẫn nhập được
        }
    }

    function step(active, notes = {}) {
        root.querySelectorAll('[data-step]').forEach((node) => {
            const n = Number(node.dataset.step);
            node.className = `rounded-xl border p-4 ${n < active ? 'border-emerald-300 bg-emerald-50' : n === active ? 'border-primary bg-[#EEF2FD]' : 'border-outline-variant bg-surface-container-lowest'}`;
        });
        Object.entries(notes).forEach(([n, text]) => { root.querySelector(`[data-step-note="${n}"]`).textContent = text; });
    }

    async function upload(event) {
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        const body = new FormData(form);
        body.set('importer', page.importer);
        showAlert(region('alert'), null);

        try {
            const batch = await withBusy(button, async () => {
                const uploaded = await api.post(page.api.upload, body);
                step(2, { 1: `Đã tải lên ${uploaded.original_filename}` });

                return wait(await api.post(tpl(page.api.validate, uploaded.id), {}));
            }, 'Đang kiểm tra…');
            form.reset();
            await showBatch(batch);
            await loadHistory();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    /** Lô chạy nền (validating / saving): hỏi lại trạng thái tới khi xong. */
    async function wait(batch) {
        let current = batch;

        while (RUNNING.includes(current.status)) {
            region('batch').hidden = false;
            region('batch').replaceChildren(el('p', { class: 'text-sm' }, `${current.status_label}… ${current.progress ?? 0}%`));
            await new Promise((resolve) => window.setTimeout(resolve, 1500));
            current = (await api.get(tpl(page.api.show, current.id))).batch;
        }

        return current;
    }

    async function showBatch(batch) {
        const box = region('batch');
        box.hidden = false;
        box.replaceChildren(loadingBlock(2));

        try {
            const preview = await api.get(tpl(page.api.preview, batch.id));
            const b = preview.batch;
            const errors = preview.error_rows ?? [];
            const total = b.total_rows ?? 0;
            const pct = (n) => (total ? `${((n * 100) / total).toFixed(1)}%` : '0%');
            const saved = b.status === 'saved';
            step(saved ? 4 : 2, { 2: `${b.valid_rows ?? 0} hợp lệ / ${b.invalid_rows ?? 0} lỗi`, 3: saved ? `Đã lưu ${b.saved_rows ?? 0} hồ sơ` : 'Chờ ghi cơ sở dữ liệu' });

            box.replaceChildren(
                el('div', { class: 'flex flex-wrap items-start justify-between gap-3' },
                    el('div', {}, el('h2', { class: 'font-headline-sm text-headline-sm font-semibold' }, `Lô ${b.code} · ${b.original_filename}`), el('p', { class: 'text-xs text-secondary' }, b.status_label)),
                    actions(b)),
                el('div', { class: 'grid sm:grid-cols-3 gap-3' },
                    card('Tổng số dòng', total, 'format_list_bulleted', 'text-on-surface'),
                    card(`Hợp lệ (${pct(b.valid_rows ?? 0)})`, b.valid_rows ?? 0, 'check_circle', 'text-emerald-700'),
                    card(`Có lỗi (${pct(b.invalid_rows ?? 0)})`, b.invalid_rows ?? 0, 'error', 'text-red-700')),
                errors.length
                    ? el('div', { class: 'space-y-2' },
                        el('div', { class: 'rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900' }, `Phát hiện ${b.invalid_rows} dòng không hợp lệ. Có thể lưu trước các dòng hợp lệ, sửa các dòng lỗi trong tệp rồi nhập lại.`),
                        errorTable(errors))
                    : el('p', { class: 'text-sm text-emerald-800' }, (b.invalid_rows ?? 0) > 0
                        ? `${b.invalid_rows} dòng lỗi không được lưu; sửa trong tệp rồi nhập lại.`
                        : 'Không có dòng lỗi.'));
        } catch (error) {
            box.replaceChildren(errorBlock(error, () => showBatch(batch)));
        }
    }

    function card(label, value, iconName, tone) {
        return el('div', { class: 'rounded-xl border border-outline-variant/60 p-4 flex items-center justify-between' },
            el('div', {}, el('p', { class: 'text-xs font-semibold text-secondary uppercase' }, label), el('p', { class: `text-2xl font-bold ${tone}` }, Number(value).toLocaleString('vi-VN'))),
            icon(iconName, `text-[28px] ${tone}`));
    }

    function errorTable(errors) {
        const messages = (row) => (Array.isArray(row.errors) ? row.errors : Object.values(row.errors ?? {}))
            .map((e) => (typeof e === 'string' ? e : `Cột ${e.column ?? '?'}: ${e.message ?? ''}`));
        const raw = (row, keys) => keys.map((k) => row.raw_data?.[k]).find((v) => v !== undefined && v !== null && v !== '') ?? '—';

        return el('div', { class: 'overflow-x-auto' }, el('table', { class: 'w-full text-sm min-w-[720px]' },
            el('thead', {}, el('tr', { class: 'text-left text-xs uppercase text-secondary border-b border-outline-variant/40' },
                ['Dòng', 'Họ và tên', 'Ngày sinh', 'Ngành', 'Chi tiết lỗi & cách sửa'].map((t) => el('th', { class: 'py-2 px-3 font-semibold' }, t)))),
            el('tbody', { class: 'divide-y divide-outline-variant/20' }, errors.map((row) => el('tr', { class: 'align-top' },
                el('td', { class: 'py-2 px-3 font-mono' }, row.row_number),
                el('td', { class: 'py-2 px-3' }, raw(row, ['full_name'])),
                el('td', { class: 'py-2 px-3' }, raw(row, ['date_of_birth'])),
                el('td', { class: 'py-2 px-3' }, raw(row, ['major_code'])),
                el('td', { class: 'py-2 px-3' }, el('ul', { class: 'space-y-0.5 text-xs text-red-800' }, messages(row).map((m) => el('li', { class: 'flex gap-1' }, icon('error', 'text-[14px] shrink-0'), m)))))))));
    }

    function actions(b) {
        const box = el('div', { class: 'flex flex-wrap gap-2' });

        if (page.can.create && b.status === 'validated' && (b.valid_rows ?? 0) > 0) {
            box.append(el('button', { type: 'button', class: 'rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-container', onclick: (e) => save(e.currentTarget, b) }, `Lưu ${b.valid_rows} dòng hợp lệ`));
        }

        if (page.can.delete && b.can_rollback) {
            box.append(el('button', { type: 'button', class: 'rounded-lg border border-amber-300 px-3 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-50', onclick: (e) => rollback(e.currentTarget, b) }, 'Hoàn tác lô'));
        }

        return box;
    }

    async function save(button, b) {
        const ok = await confirmDialog({ title: `Lưu lô ${b.code}?`, message: `Ghi ${b.valid_rows} hồ sơ hợp lệ (trạng thái "Chờ nhập học"). Các dòng lỗi giữ lại để xem.`, confirmText: 'Lưu' });

        if (!ok) {
            return;
        }

        try {
            const result = await withBusy(button, async () => wait(await api.post(tpl(page.api.save, b.id), { mode: 'all_valid' })));
            toast(`Đã lưu ${result.saved_rows ?? 0} hồ sơ sinh viên.`, 'success');
            await Promise.all([showBatch(result), loadHistory()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function rollback(button, b) {
        const ok = await confirmDialog({ title: `Hoàn tác lô ${b.code}?`, message: 'Xóa các hồ sơ đã nhập từ lô này. Chỉ thực hiện được khi hồ sơ chưa nhập học / chưa phát sinh dữ liệu.', confirmText: 'Hoàn tác', danger: true });

        if (!ok) {
            return;
        }

        try {
            await withBusy(button, () => api.post(tpl(page.api.rollback, b.id), {}));
            toast('Đã hoàn tác lô import.', 'success');
            await Promise.all([showBatch(b), loadHistory()]);
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function loadHistory() {
        region('history').replaceChildren(loadingBlock(2));

        try {
            const list = rows(await api.get(page.api.list, { importer: page.importer }));
            region('history').replaceChildren(list.length
                ? el('ul', { class: 'divide-y divide-outline-variant/30' }, list.map((b) => el('li', { class: 'py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2' },
                    el('div', {}, el('p', { class: 'font-semibold text-sm' }, `${b.code} · ${b.original_filename}`),
                        el('p', { class: 'text-xs text-secondary' }, `${b.status_label} · ${b.total_rows ?? 0} dòng · hợp lệ ${b.valid_rows ?? 0} · lỗi ${b.invalid_rows ?? 0} · đã lưu ${b.saved_rows ?? 0} · ${b.created_at ? new Date(b.created_at).toLocaleString('vi-VN') : ''}`)),
                    el('button', { type: 'button', class: 'text-xs font-semibold text-primary hover:underline self-start sm:self-auto', onclick: () => showBatch(b) }, 'Xem chi tiết'))))
                : emptyBlock('Chưa có lần nhập nào', 'Tải tệp lên để bắt đầu.', 'upload_file'));
        } catch (error) {
            region('history').replaceChildren(errorBlock(error, loadHistory));
        }
    }
}
