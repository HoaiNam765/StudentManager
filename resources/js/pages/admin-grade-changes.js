/**
 * Trang duyệt yêu cầu điều chỉnh điểm (resources/views/admin/grade/change-requests.blade.php).
 *
 * API: GET list?status=pending|approved|rejected|all (máy chủ chỉ trả yêu cầu người xem được duyệt), POST approve,
 * POST reject {reason}, GET evidence (tệp minh chứng). Quyền từng bước (khoa / Phòng Đào tạo) kiểm tra ở máy chủ.
 */
import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const STATUS_STYLE = {
    pending_faculty: 'bg-amber-50 text-amber-900 border-amber-300',
    pending_confirmation: 'bg-blue-50 text-primary border-primary/30',
    approved: 'bg-emerald-50 text-emerald-800 border-emerald-200',
    rejected: 'bg-red-50 text-red-800 border-red-300',
};

export default function adminGradeChanges(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const url = (template, id) => template.replace('__ID__', encodeURIComponent(id));
    const statusInput = root.querySelector('[data-input="status"]');

    root.querySelector('[data-action="reload"]').addEventListener('click', () => load());
    statusInput.addEventListener('change', () => load());

    load();

    async function load() {
        region('list').replaceChildren(loadingBlock(3));
        showAlert(region('alert'), null);

        try {
            const { data } = await api.get(page.api.list, { status: statusInput.value });
            root.querySelector('[data-field="count"]').textContent = `${data.length} yêu cầu`;
            region('list').replaceChildren(data.length === 0
                ? emptyBlock('Không có yêu cầu nào', 'Yêu cầu chờ khoa chỉ hiện với lãnh đạo khoa phụ trách lớp và Phòng Đào tạo / Khảo thí.', 'task_alt')
                : el('div', { class: 'space-y-3' }, data.map(card)));
        } catch (error) {
            region('list').replaceChildren(errorBlock(error, () => load()));
        }
    }

    function card(item) {
        const alertBox = el('div', { hidden: true });
        // Chỉ hiện nút cho bước người xem được duyệt (máy chủ vẫn kiểm tra lại)
        const pending = item.can_act;
        const scores = el('table', { class: 'mt-2 w-full max-w-md border-collapse text-sm' },
            el('thead', {}, el('tr', { class: 'text-left text-on-surface-variant' }, el('th', { class: 'py-1 pr-3 font-semibold' }, 'Thành phần'), el('th', { class: 'py-1 pr-3 font-semibold text-center' }, 'Điểm cũ'), el('th', { class: 'py-1 font-semibold text-center' }, 'Điểm mới'))),
            el('tbody', {}, (item.proposed_scores ?? []).map((s) => el('tr', { class: 'border-t border-outline-variant' },
                el('td', { class: 'py-1 pr-3' }, s.name ?? `#${s.component_id}`),
                el('td', { class: 'py-1 pr-3 text-center text-outline' }, s.old ?? '—'),
                el('td', { class: 'py-1 text-center font-semibold text-on-surface' }, s.score)))));

        const actions = el('div', { class: 'flex flex-wrap gap-2' },
            item.has_evidence
                ? el('button', { type: 'button', class: 'px-3 py-1.5 rounded-lg border border-outline-variant text-label-md font-semibold hover:bg-surface-container-low inline-flex items-center gap-1', onclick: (e) => evidence(e.currentTarget, item, alertBox) }, icon('attach_file', 'text-[16px]'), 'Minh chứng')
                : null,
            pending
                ? el('button', { type: 'button', class: 'px-3 py-1.5 rounded-lg border border-red-300 text-red-800 text-label-md font-semibold hover:bg-red-50 inline-flex items-center gap-1', onclick: (e) => reject(e.currentTarget, item, alertBox) }, icon('close', 'text-[16px]'), 'Từ chối')
                : null,
            pending
                ? el('button', { type: 'button', class: 'px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-white text-label-md font-semibold inline-flex items-center gap-1', onclick: (e) => approve(e.currentTarget, item, alertBox) }, icon('check', 'text-[16px]'), item.status === 'pending_faculty' ? 'Khoa duyệt' : 'Xác nhận và áp dụng')
                : null);

        return el('article', { class: 'rounded-xl border border-outline-variant p-4 space-y-2' },
            el('div', { class: 'flex flex-wrap items-start justify-between gap-2' },
                el('div', {},
                    el('p', { class: 'font-semibold text-on-surface' }, `${item.student?.student_code ?? ''} · ${item.student?.full_name ?? ''}`),
                    el('p', { class: 'text-label-md text-on-surface-variant' }, `${item.subject?.code ?? ''} ${item.subject?.name ?? ''} · Lớp ${item.section?.code ?? ''} · Gửi lúc ${item.created_at ? new Date(item.created_at).toLocaleString('vi-VN') : '—'}`)),
                el('span', { class: `inline-flex rounded-full border px-2 py-0.5 text-[11px] font-semibold ${STATUS_STYLE[item.status] ?? ''}` }, item.status_label)),
            el('p', { class: 'text-sm text-on-surface whitespace-pre-line' }, el('span', { class: 'font-semibold' }, 'Lý do: '), item.reason),
            scores,
            item.rejection_reason ? el('p', { class: 'text-sm text-red-800' }, el('span', { class: 'font-semibold' }, 'Lý do từ chối: '), item.rejection_reason) : null,
            alertBox,
            actions);
    }

    async function approve(button, item, alertBox) {
        const confirmed = await confirmDialog({
            title: item.status === 'pending_faculty' ? 'Khoa duyệt yêu cầu?' : 'Xác nhận và áp dụng điểm mới?',
            message: item.status === 'pending_faculty'
                ? 'Yêu cầu chuyển sang Phòng Đào tạo / Khảo thí xác nhận.'
                : 'Điểm mới được ghi, kết quả học phần và GPA được tính lại, sinh viên nhận thông báo. Lịch sử trước – sau được lưu và không sửa được.',
            confirmText: 'Duyệt',
        });

        if (!confirmed) {
            return;
        }

        await act(button, alertBox, () => api.post(url(page.api.approve, item.id)), 'Đã duyệt yêu cầu.');
    }

    async function reject(button, item, alertBox) {
        const confirmed = await confirmDialog({ title: 'Từ chối yêu cầu điều chỉnh?', confirmText: 'Từ chối', danger: true, input: { label: 'Lý do từ chối', required: true, maxlength: 2000 } });

        if (!confirmed) {
            return;
        }

        await act(button, alertBox, () => api.post(url(page.api.reject, item.id), { reason: confirmed.value }), 'Đã từ chối yêu cầu.');
    }

    async function evidence(button, item, alertBox) {
        try {
            await withBusy(button, () => api.download(url(page.api.evidence, item.id), {}, `minh-chung-${item.id}`), 'Đang tải…');
        } catch (error) {
            showAlert(alertBox, error);
        }
    }

    async function act(button, alertBox, task, message) {
        try {
            await withBusy(button, task);
            toast(message, 'success');
            load();
        } catch (error) {
            showAlert(alertBox, error);
        }
    }
}
