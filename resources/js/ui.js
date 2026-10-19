/**
 * Thành phần giao diện dùng chung cho các trang gọi API: dựng phần tử an toàn (chỉ textContent, không innerHTML với
 * dữ liệu từ máy chủ), thông báo ngắn, khung cảnh báo, hộp xác nhận, trạng thái đang tải / rỗng / lỗi, lỗi theo ô.
 */

/**
 * Tạo phần tử: el('span', {class: 'x', dataset: {id: 1}, onclick: fn}, 'chữ', con…).
 * Chuỗi luôn được gắn bằng textContent.
 */
export function el(tag, attrs = {}, ...children) {
    const node = document.createElement(tag);

    for (const [key, value] of Object.entries(attrs ?? {})) {
        if (value === undefined || value === null || value === false) {
            continue;
        }

        if (key === 'class') {
            node.className = value;
        } else if (key === 'dataset') {
            Object.assign(node.dataset, value);
        } else if (key.startsWith('on') && typeof value === 'function') {
            node.addEventListener(key.slice(2), value);
        } else if (value === true) {
            node.setAttribute(key, '');
        } else {
            node.setAttribute(key, String(value));
        }
    }

    for (const child of children.flat(Infinity)) {
        if (child === undefined || child === null || child === false) {
            continue;
        }

        node.append(child instanceof Node ? child : document.createTextNode(String(child)));
    }

    return node;
}

/** Biểu tượng Material Symbols (trang trí, ẩn với trình đọc màn hình). */
export function icon(name, extraClass = 'text-[18px]') {
    return el('span', { class: `material-symbols-outlined ${extraClass}`, 'aria-hidden': 'true' }, name);
}

/** Dữ liệu trang do controller truyền qua <script type="application/json" id="page-data">. */
export function pageData() {
    try {
        return JSON.parse(document.getElementById('page-data')?.textContent ?? '{}');
    } catch {
        return {};
    }
}

const TONES = {
    info: { box: 'bg-[#EEF2FD] border-primary/30 text-on-surface', icon: 'info', iconClass: 'text-primary' },
    success: { box: 'bg-emerald-50 border-emerald-200 text-emerald-900', icon: 'check_circle', iconClass: 'text-emerald-700' },
    warning: { box: 'bg-amber-50 border-amber-300 text-amber-900', icon: 'warning', iconClass: 'text-amber-700' },
    error: { box: 'bg-red-50 border-red-300 text-red-900', icon: 'error', iconClass: 'text-red-700' },
};

/** Thông báo ngắn góc màn hình, tự ẩn. Vùng #toast-region nằm trong layouts/portal. */
export function toast(message, tone = 'info', timeout = 5000) {
    const region = document.getElementById('toast-region');

    if (!region) {
        return;
    }

    const style = TONES[tone] ?? TONES.info;
    const close = el('button', { type: 'button', class: 'ml-2 p-0.5 rounded hover:bg-black/5', 'aria-label': 'Đóng thông báo' }, icon('close', 'text-[16px]'));
    const item = el(
        'div',
        { class: `pointer-events-auto flex items-start gap-2 rounded-lg border px-3.5 py-2.5 text-sm shadow-md ${style.box}`, role: tone === 'error' ? 'alert' : 'status' },
        icon(style.icon, `text-[20px] shrink-0 ${style.iconClass}`),
        el('p', { class: 'flex-1 break-words' }, message),
        close,
    );
    close.addEventListener('click', () => item.remove());
    region.append(item);

    if (timeout > 0) {
        window.setTimeout(() => item.remove(), timeout);
    }
}

/**
 * Khung cảnh báo trong trang. Nhận chuỗi hoặc ApiError: lỗi nghiệp vụ liệt kê từng lý do (đã kèm cách khắc phục).
 * Truyền null để xóa.
 */
export function showAlert(container, messageOrError, tone = 'error') {
    if (!container) {
        return;
    }

    container.replaceChildren();

    if (!messageOrError) {
        container.hidden = true;

        return;
    }

    const isError = typeof messageOrError === 'object';
    const reasons = isError ? messageOrError.reasons ?? [] : [];
    const style = TONES[tone] ?? TONES.error;
    const body = el('div', { class: 'space-y-1 min-w-0' });

    if (reasons.length > 1) {
        body.append(
            el('p', { class: 'font-semibold' }, 'Không thực hiện được vì:'),
            el('ul', { class: 'list-disc pl-5 space-y-0.5' }, reasons.map((reason) => el('li', {}, reason))),
        );
    } else {
        body.append(el('p', { class: 'break-words' }, isError ? messageOrError.message : messageOrError));
    }

    container.append(el('div', { class: `flex items-start gap-2 rounded-lg border px-4 py-3 text-sm ${style.box}`, role: tone === 'error' ? 'alert' : 'status' }, icon(style.icon, `text-[20px] shrink-0 ${style.iconClass}`), body));
    container.hidden = false;
}

/** Thông điệp hiển thị của một lỗi bất kỳ (ApiError hoặc lỗi JavaScript). */
export function errorMessage(error) {
    return error?.name === 'ApiError' ? error.message : 'Có lỗi khi hiển thị dữ liệu. Tải lại trang rồi thử lại.';
}

/** Khối "đang tải" dạng khung xương. */
export function loadingBlock(rows = 3, label = 'Đang tải dữ liệu…') {
    return el(
        'div',
        { class: 'space-y-3', role: 'status', 'aria-live': 'polite' },
        el('span', { class: 'sr-only' }, label),
        Array.from({ length: rows }, () =>
            el('div', { class: 'animate-pulse rounded-xl border border-outline-variant bg-surface-container-lowest p-4 space-y-2.5' },
                el('div', { class: 'h-3 w-1/3 rounded bg-surface-container' }),
                el('div', { class: 'h-4 w-2/3 rounded bg-surface-container' }),
                el('div', { class: 'h-3 w-1/2 rounded bg-surface-container' }),
            ),
        ),
    );
}

/** Khối "rỗng". */
export function emptyBlock(title, description = '', iconName = 'inbox') {
    return el(
        'div',
        { class: 'rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest p-8 text-center' },
        icon(iconName, 'text-[40px] text-outline'),
        el('p', { class: 'mt-2 text-sm font-semibold text-on-surface' }, title),
        description ? el('p', { class: 'mt-1 text-xs text-on-surface-variant' }, description) : null,
    );
}

/** Khối "lỗi" có nút thử lại. */
export function errorBlock(error, onRetry) {
    return el(
        'div',
        { class: 'rounded-xl border border-red-200 bg-red-50 p-6 text-center', role: 'alert' },
        icon('cloud_off', 'text-[36px] text-red-700'),
        el('p', { class: 'mt-2 text-sm font-semibold text-red-900' }, 'Không tải được dữ liệu'),
        el('p', { class: 'mt-1 text-xs text-red-800 break-words' }, errorMessage(error)),
        onRetry
            ? el('button', { type: 'button', class: 'mt-3 inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold text-red-800 hover:bg-red-100', onclick: onRetry }, icon('refresh', 'text-[16px]'), 'Thử lại')
            : null,
    );
}

/**
 * Hộp xác nhận cho thao tác ghi. Có thể kèm ô nhập (lý do…).
 *
 * @param {{title: string, message?: string, confirmText?: string, danger?: boolean, input?: {label: string, required?: boolean, maxlength?: number}}} options
 * @returns {Promise<false | {value: string}>}
 */
export function confirmDialog({ title, message = '', confirmText = 'Xác nhận', danger = false, input = null }) {
    return new Promise((resolve) => {
        const field = input ? el('textarea', { class: 'mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20', rows: 3, maxlength: input.maxlength ?? 500, required: input.required ?? false }) : null;
        const fieldError = el('p', { class: 'mt-1 text-xs text-red-700', hidden: true }, 'Vui lòng nhập nội dung này.');
        const cancel = el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface hover:bg-surface-container-low' }, 'Hủy bỏ');
        const ok = el('button', { type: 'submit', class: `rounded-lg px-4 py-2 text-sm font-semibold text-white ${danger ? 'bg-error hover:opacity-90' : 'bg-primary hover:bg-primary-container'}` }, confirmText);
        const form = el(
            'form',
            { method: 'dialog', class: 'space-y-3' },
            el('h2', { class: 'text-base font-bold text-on-surface' }, title),
            message ? el('p', { class: 'text-sm text-on-surface-variant whitespace-pre-line' }, message) : null,
            field ? el('label', { class: 'block text-sm font-medium text-on-surface' }, input.label, field, fieldError) : null,
            el('div', { class: 'flex justify-end gap-2 pt-1' }, cancel, ok),
        );
        // m-auto: preflight của Tailwind xóa margin mặc định nên <dialog> không tự căn giữa
        const dialog = el('dialog', { class: 'm-auto w-[min(92vw,440px)] rounded-xl border border-outline-variant p-5 shadow-xl backdrop:bg-black/40' }, form);

        const finish = (result) => {
            dialog.close();
            dialog.remove();
            resolve(result);
        };

        cancel.addEventListener('click', () => finish(false));
        dialog.addEventListener('cancel', (event) => {
            event.preventDefault();
            finish(false);
        });
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const value = field ? field.value.trim() : '';

            if (field && input.required && value === '') {
                fieldError.hidden = false;
                field.focus();

                return;
            }

            finish({ value });
        });

        document.body.append(dialog);
        dialog.showModal();
        (field ?? ok).focus();
    });
}

/** Gắn lỗi 422 vào các ô của biểu mẫu (ô có name trùng khóa lỗi); trả về các lỗi không có ô tương ứng. */
export function applyFieldErrors(form, errors = {}) {
    clearFieldErrors(form);
    const unmatched = [];

    for (const [name, messages] of Object.entries(errors)) {
        const field = form.querySelector(`[name="${CSS.escape(name)}"]`);

        if (!field) {
            unmatched.push(...messages);
            continue;
        }

        field.setAttribute('aria-invalid', 'true');
        field.classList.add('border-red-500');
        field.after(el('p', { class: 'mt-1 text-xs text-red-700', dataset: { fieldError: name } }, messages[0]));
    }

    return unmatched;
}

export function clearFieldErrors(form) {
    form.querySelectorAll('[data-field-error]').forEach((node) => node.remove());
    form.querySelectorAll('[aria-invalid="true"]').forEach((node) => {
        node.removeAttribute('aria-invalid');
        node.classList.remove('border-red-500');
    });
}

/** Khóa nút trong lúc gửi yêu cầu, trả lại trạng thái cũ khi xong. */
export async function withBusy(button, task, busyText = 'Đang xử lý…') {
    const original = [...button.childNodes];
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');
    button.replaceChildren(icon('progress_activity', 'text-[18px] animate-spin'), busyText);

    try {
        return await task();
    } finally {
        if (button.isConnected) {
            button.disabled = false;
            button.removeAttribute('aria-busy');
            button.replaceChildren(...original);
        }
    }
}
