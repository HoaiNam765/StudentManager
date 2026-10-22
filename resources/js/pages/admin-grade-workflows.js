/**
 * Trang duyệt và công bố điểm (resources/views/admin/grade/index.blade.php).
 *
 * API: POST change {status, reason?, scheduled_at?} cho từng lớp. Trả lại bắt buộc lý do; công bố hỏi gửi ngay hay hẹn
 * giờ (giờ Việt Nam, "YYYY-MM-DD HH:mm"). Máy chủ kiểm tra quyền từng bước; thành công thì tải lại trang.
 */
import { api } from '../api.js';
import { confirmDialog, pageData, showAlert, toast, withBusy } from '../ui.js';

const MESSAGES = {
    faculty_approved: 'Bảng điểm chuyển sang Phòng Đào tạo / Khảo thí xác nhận.',
    confirmed: 'Bảng điểm đã kiểm tra, chờ công bố.',
    published: 'Sinh viên sẽ xem được điểm và nhận thông báo. Để trống giờ hẹn để công bố ngay, hoặc nhập giờ hẹn dạng 2026-11-30 08:00.',
    locked: 'Sau khi khóa, điểm chỉ thay đổi được qua yêu cầu điều chỉnh có duyệt.',
    returned: 'Giảng viên sẽ sửa và nộp lại.',
};

export default function adminGradeWorkflows(root) {
    const page = pageData();
    const alertBox = root.querySelector('[data-region="alert"]');

    root.querySelectorAll('[data-action="step"]').forEach((button) => button.addEventListener('click', () => step(button)));

    async function step(button) {
        const { section, code, status, label } = button.dataset;
        const input = status === 'returned'
            ? { label: 'Lý do trả lại', required: true, maxlength: 1000 }
            : status === 'published' ? { label: 'Giờ hẹn công bố (không bắt buộc)', required: false, maxlength: 16 } : null;
        const answer = await confirmDialog({
            title: `${label} bảng điểm lớp ${code}?`,
            message: MESSAGES[status] ?? '',
            confirmText: label,
            danger: status === 'returned',
            input,
        });

        if (!answer) {
            return;
        }

        const body = { status };

        if (status === 'returned') {
            body.reason = answer.value;
        } else if (status === 'published' && answer.value) {
            body.scheduled_at = answer.value;
        }

        showAlert(alertBox, null);

        try {
            await withBusy(button, () => api.post(page.api.change.replace('__ID__', section), body));
            toast(body.scheduled_at ? `Đã hẹn công bố lớp ${code}.` : `Đã cập nhật bảng điểm lớp ${code}.`, 'success');
            window.location.reload();
        } catch (error) {
            showAlert(alertBox, error);
        }
    }
}
