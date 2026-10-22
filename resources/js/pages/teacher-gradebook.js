/**
 * Trang sổ điểm lớp học phần của giảng viên (resources/views/teacher/grade/gradebook.blade.php). Bảng điểm dựng sẵn ở
 * máy chủ; trang này chỉ gửi thao tác ghi rồi tải lại trang.
 *
 * API: POST saveDraft {items: [{enrollment_id, component_id, score|null}]} (chỉ ô đã sửa), POST special/{id}
 * {status|null, reason} (trạng thái điểm đặc biệt), POST initialize (khởi tạo cơ cấu điểm), POST import (tệp .xlsx),
 * POST workflow {status: 'submitted'} (nộp bảng điểm). Máy chủ kiểm tra phân công, bước điểm, trạng thái bảng điểm.
 */
import { api } from '../api.js';
import { confirmDialog, pageData, showAlert, toast, withBusy } from '../ui.js';

export default function teacherGradebook(root) {
    const page = pageData();
    const alertBox = root.querySelector('[data-region="alert"]');
    const draftForm = root.querySelector('[data-form="draft"]');
    const importForm = root.querySelector('[data-form="import"]');

    draftForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        saveDraft(event.submitter ?? draftForm.querySelector('[type="submit"]'));
    });
    importForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        importFile(event.submitter ?? importForm.querySelector('[type="submit"]'));
    });
    root.querySelector('[data-action="submit"]')?.addEventListener('click', (event) => submitBook(event.currentTarget));
    root.querySelector('[data-action="initialize"]')?.addEventListener('click', (event) => run(event.currentTarget, () => api.post(page.api.initialize), 'Đã khởi tạo cơ cấu điểm.'));

    // Cảnh báo khi rời trang mà còn điểm chưa lưu
    window.addEventListener('beforeunload', (event) => {
        if (changedScores().length || changedSpecials().length) {
            event.preventDefault();
        }
    });

    function changedScores() {
        return [...root.querySelectorAll('input[data-enrollment]')]
            .filter((input) => input.value.trim() !== input.dataset.original)
            .map((input) => ({
                enrollment_id: Number(input.dataset.enrollment),
                component_id: Number(input.dataset.component),
                score: input.value.trim() === '' ? null : Number(input.value),
            }));
    }

    function changedSpecials() {
        return [...root.querySelectorAll('select[data-special]')].filter((select) => select.value !== select.dataset.original);
    }

    async function saveDraft(button) {
        showAlert(alertBox, null);
        const items = changedScores();
        const specials = changedSpecials();

        if (!items.length && !specials.length) {
            toast('Chưa có điểm nào thay đổi.', 'info');

            return;
        }

        let reason = '';

        if (specials.some((select) => select.value !== '')) {
            const answer = await confirmDialog({
                title: 'Ghi trạng thái điểm đặc biệt',
                message: 'Vắng thi không lý do / cấm thi: điểm thi cuối kỳ được ghi 0. Hoãn thi, miễn, công nhận: không tính điểm, không tính GPA.',
                confirmText: 'Lưu',
                input: { label: 'Lý do / số quyết định', required: true, maxlength: 500 },
            });

            if (!answer) {
                return;
            }

            reason = answer.value;
        }

        try {
            await withBusy(button, async () => {
                if (items.length) {
                    await api.post(page.api.saveDraft, { items });
                }

                for (const select of specials) {
                    await api.post(page.api.special.replace('__ID__', select.dataset.special), { status: select.value || null, reason: select.value ? reason : null });
                }
            }, 'Đang lưu…');
            markSaved();
            toast('Đã lưu nháp điểm.', 'success');
            window.location.reload();
        } catch (error) {
            showAlert(alertBox, error);
        }
    }

    async function submitBook(button) {
        if (changedScores().length || changedSpecials().length) {
            showAlert(alertBox, 'Còn điểm chưa lưu. Bấm "Lưu nháp điểm" trước khi nộp.', 'warning');

            return;
        }

        const ok = await confirmDialog({
            title: `Nộp bảng điểm lớp ${page.sectionCode}?`,
            message: 'Hệ thống tính điểm tổng kết cho từng sinh viên. Sau khi nộp, bạn không sửa trực tiếp được nữa (trừ khi bảng điểm bị trả lại).',
            confirmText: 'Nộp điểm',
        });

        if (ok) {
            await run(button, () => api.post(page.api.workflow, { status: 'submitted' }), 'Đã nộp bảng điểm.');
        }
    }

    async function importFile(button) {
        const file = importForm.querySelector('input[type="file"]').files[0];

        if (!file) {
            showAlert(alertBox, 'Chọn tệp .xlsx tải từ nút "Mẫu Excel".', 'warning');

            return;
        }

        const data = new FormData();
        data.append('file', file);
        await run(button, () => api.post(page.api.import, data), 'Đã nhập điểm từ tệp Excel.');
    }

    async function run(button, task, message) {
        showAlert(alertBox, null);

        try {
            await withBusy(button, task);
            markSaved();
            toast(message, 'success');
            window.location.reload();
        } catch (error) {
            document.getElementById('import-modal')?.classList.add('hidden');
            showAlert(alertBox, error);
        }
    }

    function markSaved() {
        root.querySelectorAll('input[data-enrollment]').forEach((input) => { input.dataset.original = input.value.trim(); });
        root.querySelectorAll('select[data-special]').forEach((select) => { select.dataset.original = select.value; });
    }
}
