/**
 * Soạn thông báo thủ công và lịch sử (resources/views/notification/compose.blade.php), dùng cho cổng quản trị và cổng
 * giảng viên.
 *
 * API (theo cổng): GET options (đối tượng được chọn, nhóm đã lưu), POST preview {audience}, POST store (FormData có
 * audience dạng JSON), GET history, POST revoke {reason} (chỉ ADMIN), POST / DELETE recipient-groups.
 */
import { api } from '../api.js';
import { applyFieldErrors, clearFieldErrors, confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

export default function announcementCompose(root) {
    const page = pageData();
    const form = root.querySelector('[data-form="compose"]');
    const input = (name) => root.querySelector(`[data-input="${name}"]`);
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const state = { types: {}, options: {}, groups: [], targets: [] };

    input('type').addEventListener('change', fillTargets);
    root.querySelector('[data-action="add-target"]').addEventListener('click', addTarget);
    root.querySelector('[data-action="preview"]').addEventListener('click', (e) => preview(e.currentTarget));
    root.querySelector('[data-action="save-group"]').addEventListener('click', (e) => saveGroup(e.currentTarget));
    root.querySelector('[data-action="delete-group"]').addEventListener('click', (e) => deleteGroup(e.currentTarget));
    root.querySelector('[data-action="reload-history"]').addEventListener('click', () => loadHistory());
    input('group').addEventListener('change', useGroup);
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        submit(form.querySelector('[type="submit"]'));
    });

    loadOptions();
    loadHistory();

    async function loadOptions() {
        try {
            const result = await api.get(page.api.options);
            state.types = result.types;
            state.options = result.options;
            state.groups = result.groups;
            const usable = Object.keys(result.types).filter((type) => (result.options[type] ?? []).length > 0);
            input('type').replaceChildren(...usable.map((type) => el('option', { value: type }, result.types[type])));
            fillTargets();
            fillGroups();

            if (usable.length === 0) {
                showAlert(region('alert'), 'Bạn chưa có lớp hay đối tượng nào trong phạm vi để gửi thông báo; vẫn có thể nhập danh sách MSSV sinh viên bạn quản lý.', 'warning');
            }
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    function fillTargets() {
        const items = state.options[input('type').value] ?? [];
        input('target').replaceChildren(...items.map((item) => el('option', { value: item.id }, item.label)));
    }

    function fillGroups() {
        input('group').replaceChildren(el('option', { value: '' }, '— Dùng nhóm đã lưu —'), ...state.groups.map((g) => el('option', { value: g.id }, g.name)));
    }

    function addTarget() {
        const type = input('type').value;
        const id = Number(input('target').value);

        if (!type || Number.isNaN(id) || state.targets.some((t) => t.type === type && t.id === id)) {
            return;
        }

        const label = (state.options[type] ?? []).find((o) => o.id === id)?.label ?? '';
        state.targets.push({ type, id, label: `${state.types[type]}: ${label}` });
        renderChips();
    }

    function renderChips() {
        region('chips').replaceChildren(...state.targets.map((target, index) => el(
            'span',
            { class: 'inline-flex items-center gap-1 rounded-full border border-primary/30 bg-[#EEF2FD] px-3 py-1 text-xs font-semibold text-on-surface' },
            target.label,
            el('button', { type: 'button', class: 'text-outline hover:text-error', 'aria-label': `Bỏ ${target.label}`, onclick: () => { state.targets.splice(index, 1); renderChips(); } }, icon('close', 'text-[14px]')),
        )));
    }

    function useGroup() {
        const group = state.groups.find((g) => String(g.id) === input('group').value);

        if (!group) {
            return;
        }

        state.targets = (group.audience.targets ?? []).map((t) => ({
            type: t.type,
            id: t.id,
            label: `${state.types[t.type] ?? t.type}: ${(state.options[t.type] ?? []).find((o) => o.id === t.id)?.label ?? `#${t.id}`}`,
        }));
        input('codes').value = (group.audience.student_codes ?? []).join(', ');
        renderChips();
    }

    function audience() {
        return {
            targets: state.targets.map(({ type, id }) => ({ type, id })),
            student_codes: input('codes').value.split(/[\s,;]+/).map((c) => c.trim()).filter(Boolean),
        };
    }

    async function preview(button) {
        showAlert(region('alert'), null);
        const out = root.querySelector('[data-field="preview"]');

        try {
            const result = await withBusy(button, () => api.post(page.api.preview, { audience: audience() }), 'Đang tính…');
            out.textContent = `${result.students} sinh viên sẽ nhận (${result.labels.join('; ')}).`;
        } catch (error) {
            out.textContent = '';
            showAlert(region('alert'), error);
        }
    }

    async function saveGroup(button) {
        const answer = await confirmDialog({ title: 'Lưu nhóm người nhận', message: 'Lưu các đối tượng đang chọn để dùng lại lần sau.', confirmText: 'Lưu', input: { label: 'Tên nhóm', required: true, maxlength: 150 } });

        if (!answer) {
            return;
        }

        try {
            const group = await withBusy(button, () => api.post(page.api.groupStore, { name: answer.value, audience: audience() }));
            state.groups.push(group);
            fillGroups();
            input('group').value = String(group.id);
            toast(`Đã lưu nhóm "${group.name}".`, 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function deleteGroup(button) {
        const group = state.groups.find((g) => String(g.id) === input('group').value);

        if (!group || !(await confirmDialog({ title: `Xóa nhóm "${group.name}"?`, confirmText: 'Xóa', danger: true }))) {
            return;
        }

        try {
            await withBusy(button, () => api.del(page.api.groupDelete.replace('__ID__', group.id)));
            state.groups = state.groups.filter((g) => g.id !== group.id);
            fillGroups();
            toast('Đã xóa nhóm.', 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }

    async function submit(button) {
        clearFieldErrors(form);
        showAlert(region('alert'), null);
        const data = new FormData();
        const value = (name) => form.elements[name].value.trim();
        const localTime = (name) => (value(name) ? value(name).replace('T', ' ') : '');

        data.append('title', value('title'));
        data.append('body', value('body'));
        data.append('audience', JSON.stringify(audience()));
        data.append('pinned', form.elements.pinned.checked ? '1' : '0');
        data.append('urgent', form.elements.urgent.checked ? '1' : '0');

        if (localTime('scheduled_at')) {
            data.append('scheduled_at', localTime('scheduled_at'));
        }

        if (localTime('expires_at')) {
            data.append('expires_at', localTime('expires_at'));
        }

        if (form.elements.attachment.files[0]) {
            data.append('attachment', form.elements.attachment.files[0]);
        }

        if (!(await confirmDialog({ title: 'Gửi thông báo?', message: localTime('scheduled_at') ? `Thông báo sẽ được gửi lúc ${localTime('scheduled_at')}.` : 'Thông báo được gửi ngay cho các sinh viên đã chọn.', confirmText: 'Gửi' }))) {
            return;
        }

        try {
            const result = await withBusy(button, () => api.post(page.api.store, data), 'Đang gửi…');
            toast(result.status === 'scheduled' ? 'Đã hẹn giờ gửi thông báo.' : `Đã gửi cho ${result.recipient_count} sinh viên.`, 'success');
            form.reset();
            state.targets = [];
            renderChips();
            root.querySelector('[data-field="preview"]').textContent = '';
            loadHistory();
        } catch (error) {
            const rest = applyFieldErrors(form, error.errors ?? {});
            showAlert(region('alert'), rest.length ? rest.join(' ') : error);
        }
    }

    async function loadHistory() {
        region('history').replaceChildren(loadingBlock(3));

        try {
            const { data } = await api.get(page.api.history);
            region('history').replaceChildren(...(data.length === 0 ? [emptyBlock('Chưa gửi thông báo nào', '', 'campaign')] : data.map(historyItem)));
        } catch (error) {
            region('history').replaceChildren(errorBlock(error, () => loadHistory()));
        }
    }

    function historyItem(n) {
        const time = n.sent_at ?? n.scheduled_at;
        const rate = n.recipient_count ? Math.round((n.read_count * 100) / n.recipient_count) : 0;

        return el(
            'article',
            { class: 'p-4 space-y-1' },
            el('div', { class: 'flex items-start justify-between gap-2' },
                el('h3', { class: 'text-sm font-semibold text-on-surface' }, n.title),
                el('span', { class: 'shrink-0 rounded-full border border-outline-variant px-2 text-[11px] font-semibold' }, n.status_label)),
            el('p', { class: 'text-xs text-on-surface-variant' }, n.audience.join('; ')),
            el('p', { class: 'text-[11px] text-outline' }, [
                time ? new Date(time).toLocaleString('vi-VN') : null,
                n.status === 'sent' ? `Đã đọc ${n.read_count}/${n.recipient_count} (${rate}%)` : null,
                n.sender ? `Người gửi: ${n.sender}` : null,
                n.revoke_reason ? `Lý do thu hồi: ${n.revoke_reason}` : null,
            ].filter(Boolean).join(' · ')),
            page.can.revoke && n.status !== 'revoked'
                ? el('button', { type: 'button', class: 'text-xs font-semibold text-red-800 hover:underline', onclick: (e) => revoke(e.currentTarget, n) }, 'Thu hồi')
                : null,
        );
    }

    async function revoke(button, n) {
        const answer = await confirmDialog({ title: `Thu hồi "${n.title}"?`, message: 'Thông báo sẽ biến mất khỏi trung tâm thông báo của người nhận.', confirmText: 'Thu hồi', danger: true, input: { label: 'Lý do thu hồi', required: true, maxlength: 500 } });

        if (!answer) {
            return;
        }

        try {
            await withBusy(button, () => api.post(page.api.revoke.replace('__ID__', n.id), { reason: answer.value }));
            toast('Đã thu hồi thông báo.', 'success');
            loadHistory();
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }
}
