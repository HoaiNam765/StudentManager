/**
 * Tiện ích dùng chung cho các trang quản trị sinh viên / lớp hành chính: đọc danh sách (có hoặc không phân trang),
 * đổ ô chọn, thay tham số trong URL mẫu của $pageData, nhãn và màu trạng thái sinh viên.
 */
import { api } from './api.js';
import { applyFieldErrors, clearFieldErrors, el, showAlert, withBusy } from './ui.js';

/** Thay `__ID__` (hoặc khóa khác) trong URL mẫu do controller trả về. */
export function tpl(url, id, key = '__ID__') {
    return url.replace(key, encodeURIComponent(id));
}

/** Mảng bản ghi từ phản hồi API: mảng thuần hoặc trang phân trang của Laravel ({data: […]}). */
export function rows(response) {
    return Array.isArray(response) ? response : (response?.data ?? []);
}

/** Đọc hết các trang (tối đa `maxPages`) của một API phân trang, dùng cho ô chọn danh mục. */
export async function listAll(url, params = {}, maxPages = 10) {
    const first = await api.get(url, { ...params, page: 1 });

    if (Array.isArray(first) || !first?.last_page || first.last_page <= 1) {
        return rows(first);
    }

    const rest = await Promise.all(
        Array.from({ length: Math.min(first.last_page, maxPages) - 1 }, (_, index) => api.get(url, { ...params, page: index + 2 })),
    );

    return [first, ...rest].flatMap(rows);
}

/** Đổ các lựa chọn vào <select>, giữ lại lựa chọn đầu (ví dụ "Tất cả") nếu `keepFirst`. */
export function fillSelect(select, items, label, value = 'id', keepFirst = true) {
    if (!select) {
        return;
    }

    const current = select.value;
    const first = keepFirst ? select.querySelector('option') : null;
    select.replaceChildren(...(first ? [first] : []), ...items.map((item) => el('option', { value: item[value] }, label(item))));

    if ([...select.options].some((option) => option.value === current)) {
        select.value = current;
    }
}

export const STUDENT_STATUS = {
    pending: ['Chờ nhập học', 'bg-slate-100 text-slate-700'],
    studying: ['Đang học', 'bg-[#DCFCE7] text-[#15803D]'],
    suspended: ['Bảo lưu', 'bg-amber-50 text-amber-800'],
    withdrawn: ['Thôi học', 'bg-red-50 text-red-700'],
    expelled: ['Buộc thôi học', 'bg-red-100 text-red-800'],
    transferred: ['Chuyển trường', 'bg-violet-50 text-violet-800'],
    graduated: ['Đã tốt nghiệp', 'bg-sky-50 text-sky-800'],
};

/** Nhãn trạng thái dạng huy hiệu. */
export function statusBadge(status, label = null) {
    const [text, tone] = STUDENT_STATUS[status] ?? [label ?? status, 'bg-slate-100 text-slate-700'];

    return el('span', { class: `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${tone}` }, label ?? text);
}

/** Hai chữ cái đầu của hai từ cuối trong họ tên (ảnh đại diện chữ). */
export function initials(name) {
    return String(name ?? '').trim().split(/\s+/).slice(-2).map((word) => word.charAt(0).toUpperCase()).join('');
}

/** Định dạng ngày Y-m-d thành d/m/Y. */
export function dmy(value) {
    if (!value) {
        return '—';
    }

    const [y, m, d] = String(value).slice(0, 10).split('-');

    return d ? `${d}/${m}/${y}` : value;
}

/** Chạy hàm sau khi người dùng ngừng gõ. */
export function debounce(fn, wait = 300) {
    let timer;

    return (...args) => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => fn(...args), wait);
    };
}

/**
 * Hộp thoại biểu mẫu. `fields`: [{name, label, type = text|date|number|email|select|textarea|checkbox|file, options:
 * [{value, label}], required, value, hint, accept, full}]. `onSubmit(formData)` gọi API; lỗi 422 gắn vào từng ô và hộp
 * vẫn mở; lỗi khác hiện ở đầu hộp. Trả về kết quả của onSubmit, hoặc false nếu người dùng hủy.
 */
export function formDialog({ title, description = '', fields, submitText = 'Lưu', danger = false, onSubmit }) {
    return new Promise((resolve) => {
        const alert = el('div', { hidden: true });
        const input = (f) => {
            const base = 'mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20';

            if (f.type === 'select') {
                return el('select', { name: f.name, class: base, required: f.required },
                    f.placeholder !== undefined ? el('option', { value: '' }, f.placeholder) : null,
                    (f.options ?? []).map((o) => el('option', { value: o.value, selected: String(o.value) === String(f.value ?? '') }, o.label)));
            }

            if (f.type === 'textarea') {
                return el('textarea', { name: f.name, class: base, rows: 3, maxlength: f.maxlength ?? 1000, required: f.required }, f.value ?? '');
            }

            if (f.type === 'checkbox') {
                return el('input', { type: 'checkbox', name: f.name, value: '1', checked: Boolean(f.value), class: 'h-4 w-4 rounded border-outline-variant text-primary' });
            }

            return el('input', { type: f.type ?? 'text', name: f.name, value: f.value ?? '', class: base, required: f.required, accept: f.accept, maxlength: f.maxlength, min: f.min });
        };
        const field = (f) => f.type === 'checkbox'
            ? el('label', { class: 'flex items-center gap-2 text-sm text-on-surface sm:col-span-2' }, input(f), f.label)
            : el('label', { class: `block text-sm font-medium text-on-surface ${f.full ? 'sm:col-span-2' : ''}` },
                f.label, f.required ? el('span', { class: 'text-red-600' }, ' *') : null, input(f),
                f.hint ? el('span', { class: 'mt-1 block text-xs text-on-surface-variant' }, f.hint) : null);
        const cancel = el('button', { type: 'button', class: 'rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface hover:bg-surface-container-low' }, 'Hủy bỏ');
        const ok = el('button', { type: 'submit', class: `inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white ${danger ? 'bg-error hover:opacity-90' : 'bg-primary hover:bg-primary-container'}` }, submitText);
        const form = el('form', { class: 'space-y-4', novalidate: true },
            el('h2', { class: 'text-base font-bold text-on-surface' }, title),
            description ? el('p', { class: 'text-sm text-on-surface-variant whitespace-pre-line' }, description) : null,
            alert,
            el('div', { class: 'grid gap-3 sm:grid-cols-2' }, fields.map(field)),
            el('div', { class: 'flex justify-end gap-2 pt-1' }, cancel, ok));
        const dialog = el('dialog', { class: 'm-auto w-[min(94vw,640px)] max-h-[90vh] overflow-y-auto rounded-xl border border-outline-variant p-5 shadow-xl backdrop:bg-black/40' }, form);
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
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearFieldErrors(form);
            showAlert(alert, null);
            const data = new FormData(form);
            fields.filter((f) => f.type === 'checkbox').forEach((f) => data.set(f.name, form.elements[f.name].checked ? '1' : '0'));

            try {
                const result = await withBusy(ok, () => onSubmit(data, form));
                finish(result ?? true);
            } catch (error) {
                const unmatched = error?.isValidation ? applyFieldErrors(form, error.errors) : [];

                if (!error?.isValidation || unmatched.length) {
                    showAlert(alert, unmatched.length ? unmatched.join(' ') : error);
                }
            }
        });

        document.body.append(dialog);
        dialog.showModal();
        form.querySelector('input:not([type=hidden]), select, textarea')?.focus();
    });
}

/** FormData thành đối tượng JSON, bỏ ô rỗng (gửi null cho các ô trong `nullable`). */
export function formJson(data, nullable = []) {
    const result = {};

    for (const [key, value] of data.entries()) {
        if (value instanceof File) {
            continue;
        }

        if (value === '') {
            if (nullable.includes(key)) {
                result[key] = null;
            }

            continue;
        }

        result[key] = value;
    }

    return result;
}

/** Nút phân trang cho trang phân trang của Laravel. */
export function pager(container, page, onGo) {
    container.replaceChildren();

    if (!page || !page.last_page || page.last_page <= 1) {
        return;
    }

    const button = (label, target, active = false, disabled = false) => el('button', {
        type: 'button',
        disabled,
        'aria-current': active ? 'page' : null,
        class: active
            ? 'min-w-8 h-8 px-2 rounded-lg bg-primary text-white font-semibold text-xs'
            : 'min-w-8 h-8 px-2 rounded-lg border border-outline-variant/60 text-on-surface hover:bg-surface-container-low font-medium text-xs disabled:opacity-40',
        onclick: () => onGo(target),
    }, label);
    const current = page.current_page;
    const pages = [...new Set([1, current - 1, current, current + 1, page.last_page])].filter((n) => n >= 1 && n <= page.last_page).sort((a, b) => a - b);
    const items = [button('‹ Trước', current - 1, false, current <= 1)];
    let last = 0;

    for (const n of pages) {
        if (n - last > 1) {
            items.push(el('span', { class: 'px-1 text-secondary' }, '…'));
        }

        items.push(button(String(n), n, n === current));
        last = n;
    }

    items.push(button('Sau ›', current + 1, false, current >= page.last_page));
    container.append(...items);
}
