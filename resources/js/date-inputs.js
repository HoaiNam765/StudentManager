/**
 * Ô ngày / ngày giờ luôn hiển thị kiểu Việt Nam (dd/mm/yyyy, dd/mm/yyyy hh:mm) bất kể ngôn ngữ trình duyệt — ô
 * `type="date"` / `datetime-local` gốc hiện theo ngôn ngữ trình duyệt (ví dụ mm/dd/yyyy).
 *
 * Giữ nguyên ô gốc (tên, giá trị ISO cho form và mã JS của trang), làm nó trong suốt đè lên biểu tượng lịch để bấm vào
 * vẫn mở bộ chọn ngày; người dùng gõ / xem ở một ô chữ đặt trước nó. Đồng bộ hai chiều, kể cả khi mã gán `.value`,
 * `form.reset()` hoặc ô được thêm sau (hộp thoại).
 */
const nativeValue = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
const enhanced = new WeakSet();
const pad = (n) => String(n).padStart(2, '0');

function toDisplay(type, iso) {
    const m = /^(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2}))?/.exec(iso ?? '');
    if (!m) return '';

    return type === 'date' || !m[4] ? `${m[3]}/${m[2]}/${m[1]}` : `${m[3]}/${m[2]}/${m[1]} ${m[4]}:${m[5]}`;
}

/** Chữ người dùng gõ → ISO; '' khi để trống; null khi sai dạng / ngày không có thật. */
function toIso(type, text) {
    if (text === '') return '';
    const m = /^(\d{1,2})[/.-](\d{1,2})[/.-](\d{4})(?:\s+(\d{1,2})[:h](\d{2}))?$/.exec(text);
    if (!m) return null;
    const [day, month, year] = [Number(m[1]), Number(m[2]), Number(m[3])];
    const check = new Date(year, month - 1, day);
    if (check.getFullYear() !== year || check.getMonth() !== month - 1 || check.getDate() !== day) return null;
    const date = `${year}-${pad(month)}-${pad(day)}`;
    if (type === 'date') return date;
    const [hour, minute] = [Number(m[4] ?? 0), Number(m[5] ?? 0)];
    if (hour > 23 || minute > 59) return null;

    return `${date}T${pad(hour)}:${pad(minute)}`;
}

function enhance(input) {
    if (enhanced.has(input) || input.hasAttribute('data-native-date') || !input.parentNode) return;
    enhanced.add(input);

    const type = input.type;
    const classes = [...input.classList];
    const isMargin = (c) => /^-?m[tbyxlr]?-/.test(c);
    const wrapper = document.createElement('span');
    wrapper.className = ['relative block', ...classes.filter(isMargin)].join(' ');
    const text = document.createElement('input');
    text.type = 'text';
    text.className = [...classes.filter((c) => !isMargin(c)), 'pr-10'].join(' ');
    text.placeholder = type === 'date' ? 'dd/mm/yyyy' : 'dd/mm/yyyy hh:mm';
    text.autocomplete = 'off';
    if (input.getAttribute('aria-label')) text.setAttribute('aria-label', input.getAttribute('aria-label'));
    // Bắt buộc nhập: báo ở ô chữ (ô gốc trong suốt, báo lỗi ở đó người dùng không thấy rõ)
    if (input.required) {
        text.required = true;
        input.required = false;
    }
    const icon = document.createElement('span');
    icon.className = 'material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant';
    icon.setAttribute('aria-hidden', 'true');
    icon.textContent = type === 'date' ? 'calendar_today' : 'event';

    input.parentNode.insertBefore(wrapper, input);
    wrapper.append(text, icon, input);
    // Ô gốc trong suốt, nằm trên biểu tượng lịch: bấm vào là mở bộ chọn ngày của trình duyệt
    Object.assign(input.style, { position: 'absolute', right: '0', top: '0', width: '2.75rem', height: '100%', margin: '0', padding: '0', opacity: '0', cursor: 'pointer' });
    input.tabIndex = -1;

    const sync = () => {
        text.value = toDisplay(type, nativeValue.get.call(input));
        text.setCustomValidity('');
        text.disabled = input.disabled;
        text.readOnly = input.readOnly;
    };
    Object.defineProperty(input, 'value', {
        configurable: true,
        get() { return nativeValue.get.call(this); },
        set(v) { nativeValue.set.call(this, v); sync(); },
    });
    input.addEventListener('input', sync);
    input.addEventListener('change', sync);
    input.addEventListener('click', () => { try { input.showPicker?.(); } catch { /* trình duyệt cũ: bấm biểu tượng của ô gốc */ } });
    input.form?.addEventListener('reset', () => setTimeout(sync));
    new MutationObserver(sync).observe(input, { attributes: true, attributeFilter: ['disabled', 'readonly'] });

    text.addEventListener('change', () => {
        const iso = toIso(type, text.value.trim());
        if (iso === null) {
            text.setCustomValidity(type === 'date' ? 'Nhập ngày dạng ngày/tháng/năm, ví dụ 06/09/2027.' : 'Nhập dạng ngày/tháng/năm giờ:phút, ví dụ 06/09/2027 08:00.');
            text.reportValidity();

            return;
        }
        nativeValue.set.call(input, iso);
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
    sync();
}

function scan(node) {
    if (!(node instanceof Element)) return;
    if (node.matches('input[type="date"], input[type="datetime-local"]')) enhance(node);
    node.querySelectorAll?.('input[type="date"], input[type="datetime-local"]').forEach(enhance);
}

/** Gắn cho các ô có sẵn và ô thêm sau (hộp thoại, bảng dựng bằng JS). */
export function initDateInputs() {
    scan(document.body);
    new MutationObserver((mutations) => mutations.forEach((m) => m.addedNodes.forEach(scan)))
        .observe(document.body, { childList: true, subtree: true });
}
