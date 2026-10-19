/**
 * Gọi API JSON của hệ thống, dùng chung cho mọi trang (docs/fe-be-mapping.md mục 2.1).
 *
 * Xác thực bằng phiên (cookie) nên chỉ gọi cùng nguồn. Yêu cầu ghi gửi kèm X-CSRF-TOKEN đọc từ
 * <meta name="csrf-token">. Lỗi được ném thành ApiError để trang hiển thị đúng thông điệp của máy chủ:
 * 422 có `errors` theo từng trường (kiểm tra dữ liệu) hoặc `reasons` (lỗi nghiệp vụ kèm cách khắc phục).
 * Ẩn nút theo quyền chỉ để tiện dùng; máy chủ luôn kiểm tra lại.
 */

/** Thông điệp mặc định của Laravel (tiếng Anh) thay bằng câu tiếng Việt cho người dùng. */
const ENGLISH_DEFAULTS = [
    /^This action is unauthorized\.?$/i,
    /^Unauthenticated\.?$/i,
    /^Forbidden$/i,
    /^Not Found$/i,
    /^No query results/i,
    /^Server Error$/i,
    /^Too Many (Attempts|Requests)/i,
    /^CSRF token mismatch\.?$/i,
    /^Page Expired$/i,
];

const FALLBACK = {
    0: 'Không kết nối được máy chủ. Kiểm tra mạng rồi thử lại.',
    401: 'Phiên đăng nhập đã hết hạn. Đang chuyển về trang đăng nhập…',
    403: 'Bạn không có quyền thực hiện thao tác này.',
    404: 'Không tìm thấy dữ liệu, hoặc dữ liệu không thuộc phạm vi của bạn.',
    419: 'Phiên làm việc đã hết hạn. Tải lại trang rồi thử lại.',
    422: 'Dữ liệu chưa hợp lệ. Xem lỗi bên dưới.',
    428: 'Bạn cần đổi mật khẩu trước khi tiếp tục. Đang chuyển trang…',
    429: 'Bạn thao tác quá nhanh. Đợi một lát rồi thử lại.',
    500: 'Máy chủ gặp lỗi. Thử lại sau ít phút; nếu vẫn lỗi, báo cho quản trị hệ thống.',
};

export class ApiError extends Error {
    /**
     * @param {number} status mã HTTP (0 = lỗi mạng)
     * @param {string} message thông điệp hiển thị cho người dùng
     * @param {{errors?: Record<string, string[]>, reasons?: string[], data?: any}} [details]
     */
    constructor(status, message, { errors = {}, reasons = [], data = null } = {}) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.errors = errors;
        this.reasons = reasons;
        this.data = data;
    }

    /** 422 do kiểm tra dữ liệu (lỗi gắn vào từng ô của biểu mẫu). */
    get isValidation() {
        return this.status === 422 && Object.keys(this.errors).length > 0;
    }

    /** 422 do quy tắc nghiệp vụ (BusinessRuleException): thông điệp đã gồm lý do và cách khắc phục. */
    get isBusinessRule() {
        return this.status === 422 && !this.isValidation;
    }
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function buildUrl(url, params) {
    const target = new URL(url, window.location.origin);

    for (const [key, value] of Object.entries(params ?? {})) {
        if (value === undefined || value === null || value === '') {
            continue;
        }

        if (Array.isArray(value)) {
            value.forEach((item) => target.searchParams.append(`${key}[]`, item));
        } else if (typeof value === 'boolean') {
            target.searchParams.set(key, value ? '1' : '0');
        } else {
            target.searchParams.set(key, value);
        }
    }

    return target;
}

function serverMessage(data) {
    const message = typeof data?.message === 'string' ? data.message.trim() : '';

    return message !== '' && !ENGLISH_DEFAULTS.some((pattern) => pattern.test(message)) ? message : '';
}

function toError(status, data) {
    const fallback = FALLBACK[status] ?? (status >= 500 ? FALLBACK[500] : `Yêu cầu không thành công (mã ${status}).`);

    if (status === 422) {
        return new ApiError(422, serverMessage(data) || fallback, {
            errors: data?.errors ?? {},
            reasons: Array.isArray(data?.reasons) ? data.reasons : [],
            data,
        });
    }

    // Không lộ chi tiết lỗi máy chủ (5xx) ra giao diện
    const message = status >= 500 ? fallback : serverMessage(data) || fallback;

    return new ApiError(status, message, { data });
}

/** Chuyển trang khi phiên hết hạn (401) hoặc phải đổi mật khẩu tạm (428, middleware password.changed). */
function redirectIfNeeded(status) {
    if (status === 401) {
        window.setTimeout(() => window.location.assign('/login'), 1200);
    } else if (status === 428) {
        window.setTimeout(() => window.location.assign('/password/change'), 1200);
    }
}

async function send(method, url, { params, body, accept = 'application/json' } = {}) {
    const headers = { Accept: accept, 'X-Requested-With': 'XMLHttpRequest' };
    let payload;

    if (method !== 'GET') {
        headers['X-CSRF-TOKEN'] = csrfToken();

        if (body instanceof FormData) {
            payload = body;
        } else if (body !== undefined) {
            headers['Content-Type'] = 'application/json';
            payload = JSON.stringify(body);
        }
    }

    try {
        return await fetch(buildUrl(url, params), { method, headers, body: payload, credentials: 'same-origin' });
    } catch {
        throw new ApiError(0, FALLBACK[0]);
    }
}

async function readJson(response) {
    const type = response.headers.get('Content-Type') ?? '';

    return type.includes('application/json') ? response.json().catch(() => null) : null;
}

async function request(method, url, options = {}) {
    const response = await send(method, url, options);

    if (response.status === 204) {
        return null;
    }

    const data = await readJson(response);

    if (response.ok) {
        return data;
    }

    redirectIfNeeded(response.status);

    throw toError(response.status, data);
}

/** Tên tệp trong Content-Disposition (ưu tiên filename*=UTF-8''…). */
function filenameFrom(response, fallback) {
    const header = response.headers.get('Content-Disposition') ?? '';
    const encoded = header.match(/filename\*=UTF-8''([^;]+)/i);

    if (encoded) {
        try {
            return decodeURIComponent(encoded[1].trim());
        } catch {
            // dùng tên thường bên dưới
        }
    }

    return header.match(/filename="?([^";]+)"?/i)?.[1] ?? fallback;
}

/**
 * Tải tệp (PDF, Excel…). Máy chủ trả 202 khi xuất chạy nền: trả về {queued: true, ...} để trang báo "đang xử lý".
 *
 * @returns {Promise<{queued: false, filename: string} | {queued: true, data: any}>}
 */
async function download(url, params, fallbackName = 'tai-ve') {
    const response = await send('GET', url, { params, accept: '*/*' });

    if (!response.ok) {
        const data = await readJson(response);
        redirectIfNeeded(response.status);

        throw toError(response.status, data);
    }

    if (response.status === 202) {
        return { queued: true, data: await readJson(response) };
    }

    const blob = await response.blob();
    const link = document.createElement('a');
    const filename = filenameFrom(response, fallbackName);
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.append(link);
    link.click();
    link.remove();
    window.setTimeout(() => URL.revokeObjectURL(link.href), 10_000);

    return { queued: false, filename };
}

export const api = {
    get: (url, params) => request('GET', url, { params }),
    post: (url, body) => request('POST', url, { body }),
    put: (url, body) => request('PUT', url, { body }),
    del: (url, body) => request('DELETE', url, { body }),
    download,
};
