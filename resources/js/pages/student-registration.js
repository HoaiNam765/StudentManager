/**
 * Trang đăng ký học phần của sinh viên (resources/views/student/enrollment/register.blade.php).
 *
 * API (routes/student.php): GET options (đợt đang mở, khung tín chỉ, lớp được chọn kèm lý do), GET enrollments
 * (đăng ký của tôi), POST enroll / cancel / change, GET result (tổng tín chỉ, thời khóa biểu tuần).
 * Máy chủ quyết định mọi điều kiện (thời gian đợt, sĩ số, tiên quyết, trùng lịch, tín chỉ); trang chỉ hiển thị lý do.
 */
import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

/** Trạng thái đăng ký còn hiệu lực (EnrollmentStatus::activeValues). */
const ACTIVE = ['registered', 'confirmed'];

/** Bỏ dấu tiếng Việt để tìm không dấu. */
const fold = (text) => String(text ?? '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/đ/g, 'd');

export default function studentRegistration(root) {
    const page = pageData();
    const state = { options: null, enrollments: [], result: null, periodId: null, filter: 'all', search: '', day: '', clockOffset: 0, timer: null };
    const field = (name) => root.querySelectorAll(`[data-field="${name}"]`);
    const setText = (name, value) => field(name).forEach((node) => { node.textContent = value; });
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const url = (template, id) => template.replace('__ID__', encodeURIComponent(id));

    // ---- Sự kiện lọc / tìm ---------------------------------------------------------------------------------
    root.querySelectorAll('[data-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            state.filter = button.dataset.filter;
            renderSections();
        });
    });
    root.querySelector('[data-input="search"]').addEventListener('input', (event) => {
        state.search = fold(event.target.value.trim());
        renderSections();
    });
    root.querySelector('[data-input="day"]').addEventListener('change', (event) => {
        state.day = event.target.value;
        renderSections();
    });
    root.querySelector('[data-action="reload"]').addEventListener('click', () => load());

    load();

    // ---- Tải dữ liệu --------------------------------------------------------------------------------------
    async function load() {
        region('sections').replaceChildren(loadingBlock(3, 'Đang tải danh sách lớp…'));
        region('timetable').replaceChildren(loadingBlock(1, 'Đang tải thời khóa biểu…'));
        setText('shown-count', 'Đang tải…');
        showAlert(region('alert'), null);

        try {
            const [options, mine] = await Promise.all([
                api.get(page.api.options, { period_id: state.periodId }),
                api.get(page.api.enrollments),
            ]);
            state.options = options;
            state.enrollments = mine?.data ?? [];
            state.result = options.period
                ? await api.get(page.api.result, { term_id: options.period.term_id }).catch((error) => ({ error }))
                : null;
            render();
        } catch (error) {
            renderLoadError(error);
        }
    }

    function renderLoadError(error) {
        stopCountdown();
        setText('period-state', 'Không tải được');
        setText('period-name', 'Đăng ký học phần');
        setText('period-term', '');
        setText('shown-count', '');
        region('sections').replaceChildren(errorBlock(error, () => load()));
        region('timetable').replaceChildren(emptyBlock('Chưa có dữ liệu', '', 'calendar_month'));
    }

    // ---- Hiển thị -----------------------------------------------------------------------------------------
    function render() {
        const { options } = state;
        setText('student-code', options.student?.student_code ?? '—');
        setText('student-name', options.student?.full_name ?? '—');
        renderPeriod();
        renderCredits();
        renderSections();
        renderTimetable();
    }

    function renderPeriod() {
        const { period, problems = [], open_periods: openPeriods = [] } = state.options;
        const deadlineBox = root.querySelector('[data-field="deadline-box"]');
        root.querySelector('[data-period-select]')?.remove();

        if (!period) {
            stopCountdown();
            setText('period-state', 'Chưa có đợt mở');
            setText('period-name', 'Hiện không có đợt đăng ký học phần nào đang mở');
            setText('period-term', 'Khi Phòng Đào tạo mở đợt cho khóa / ngành / lớp của bạn, danh sách lớp sẽ hiện ở đây.');
            deadlineBox.hidden = true;
            showAlert(region('alert'), problems.length ? { message: problems.join(' '), reasons: problems } : null, 'info');

            return;
        }

        setText('period-state', 'Cổng đăng ký trực tuyến');
        setText('period-name', period.name);
        setText('period-term', `${period.term} · Mở ${formatVn(period.opens_at)} – đóng ${formatVn(period.closes_at)}`);
        setText('closes-at', `Đóng lúc ${formatVn(period.closes_at)} (giờ máy chủ)`);
        deadlineBox.hidden = false;

        if (openPeriods.length > 1) {
            const select = el('select', { class: 'mt-1 rounded-lg border border-white/40 bg-white/15 px-3 py-1.5 text-sm text-white', 'aria-label': 'Chọn đợt đăng ký', dataset: { periodSelect: '' } },
                openPeriods.map((p) => el('option', { value: p.id, class: 'text-on-surface', selected: p.id === period.id }, p.name)));
            select.addEventListener('change', () => {
                state.periodId = Number(select.value);
                load();
            });
            field('period-term')[0].after(select);
        }

        // Đồng hồ theo giờ máy chủ (BR-ENR-11): lệch giữa máy chủ và trình duyệt tính một lần khi tải
        state.clockOffset = toDate(state.options.server_time) - Date.now();
        startCountdown(toDate(`${period.closes_at}:00`));

        showAlert(region('alert'), problems.length ? { message: problems.join(' '), reasons: problems } : null, 'warning');
    }

    function renderCredits() {
        const limits = state.options.credit_limits ?? null;
        const total = Number.isFinite(state.result?.total_credits) ? state.result.total_credits : activeEnrollments().reduce((sum, e) => sum + (e.subject?.credits ?? 0), 0);
        setText('credits-total', total);
        setText('credits-total-mobile', total);
        setText('credits-min', limits?.min ?? '—');
        setText('credits-max', limits?.max ?? '—');
        setText('credits-max-mobile', limits?.max ?? '—');
        setText('credits-basis', limits?.basis ? `Khung tín chỉ: ${limits.basis}.` : '');

        const fill = root.querySelector('[data-field="credits-fill"]');
        const bar = root.querySelector('[data-field="credits-bar"]');
        const badge = root.querySelector('[data-field="credits-badge"]');

        if (limits?.max) {
            const percent = Math.min(100, Math.round((total / limits.max) * 100));
            fill.style.width = `${percent}%`;
            fill.className = `h-3 rounded-full transition-all duration-500 ${total < limits.min ? 'bg-amber-500' : 'bg-primary'}`;
            bar.setAttribute('aria-valuemax', limits.max);
            bar.setAttribute('aria-valuenow', total);
            badge.textContent = `${percent}% mức tối đa`;
            badge.hidden = false;
            setText('credits-left', total < limits.min
                ? `Còn thiếu ${limits.min - total} TC để đạt tối thiểu`
                : `Còn được đăng ký tối đa ${Math.max(0, limits.max - total)} TC`);
        } else {
            fill.style.width = '0%';
            badge.hidden = true;
            setText('credits-left', '');
        }
    }

    function activeEnrollments() {
        const sectionIds = new Set((state.options?.sections ?? []).map((s) => s.id));

        return state.enrollments.filter((e) => ACTIVE.includes(e.status) && sectionIds.has(e.section?.id));
    }

    function renderSections() {
        const container = region('sections');

        if (!state.options) {
            return;
        }

        if (!state.options.period) {
            container.replaceChildren(emptyBlock('Chưa có lớp để đăng ký', 'Danh sách lớp chỉ hiện khi có đợt đăng ký đang mở.', 'event_busy'));
            setText('shown-count', '');
            updateTabs({ all: 0, eligible: 0, mine: 0 });

            return;
        }

        const mineBySection = new Map(activeEnrollments().map((e) => [e.section.id, e]));
        const mineBySubject = new Map(activeEnrollments().map((e) => [e.subject.id, e]));
        const sections = state.options.sections ?? [];
        const counts = {
            all: sections.length,
            eligible: sections.filter((s) => s.eligible && !mineBySection.has(s.id)).length,
            mine: sections.filter((s) => mineBySection.has(s.id)).length,
        };
        updateTabs(counts);

        const shown = sections.filter((s) => {
            if (state.filter === 'eligible' && !(s.eligible && !mineBySection.has(s.id))) return false;
            if (state.filter === 'mine' && !mineBySection.has(s.id)) return false;
            if (state.day && !s.schedule.some((r) => r.description.startsWith(state.day))) return false;

            if (state.search) {
                const haystack = fold([s.code, s.subject.code, s.subject.name, ...s.instructors.map((i) => i.name)].join(' '));

                if (!haystack.includes(state.search)) return false;
            }

            return true;
        });

        setText('shown-count', `Hiển thị ${shown.length} / ${sections.length} lớp học phần`);

        if (sections.length === 0) {
            container.replaceChildren(emptyBlock('Không có lớp nào để chọn', 'Học kỳ của đợt này chưa có lớp thuộc chương trình đào tạo của bạn, hoặc bạn chưa được gán CTĐT. Liên hệ Phòng Đào tạo nếu thấy sai.', 'search_off'));

            return;
        }

        if (shown.length === 0) {
            container.replaceChildren(emptyBlock('Không có lớp phù hợp bộ lọc', 'Đổi từ khóa tìm, thứ trong tuần hoặc chọn "Tất cả lớp trong CTĐT".', 'filter_alt_off'));

            return;
        }

        container.replaceChildren(...shown.map((s) => sectionCard(s, mineBySection.get(s.id), mineBySubject.get(s.subject.id))));
    }

    function updateTabs(counts) {
        root.querySelectorAll('[data-filter]').forEach((button) => {
            const active = button.dataset.filter === state.filter;
            button.setAttribute('aria-selected', active ? 'true' : 'false');
            button.className = `flex-1 min-w-[120px] py-2 px-3 rounded-lg text-label-md text-center transition-colors ${active ? 'bg-primary text-white shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-low'}`;
        });
        Object.entries(counts).forEach(([key, value]) => {
            root.querySelectorAll(`[data-count="${key}"]`).forEach((node) => { node.textContent = `(${value})`; });
        });
    }

    function sectionCard(section, mine, sibling) {
        const alertBox = el('div', { class: 'mt-3', hidden: true });
        const otherClass = !mine && sibling && sibling.section.id !== section.id ? sibling : null;
        const reasons = (section.reasons ?? []).filter((reason) => !(otherClass && reason.includes('đổi lớp')));
        const enrolled = Math.max(0, section.max_size - section.remaining);
        const percent = section.max_size > 0 ? Math.min(100, Math.round((enrolled / section.max_size) * 100)) : 0;
        const full = section.remaining <= 0;

        const border = mine ? 'border-2 border-primary/50' : !section.eligible && !otherClass ? 'border border-amber-300 bg-amber-50/30' : 'border border-outline-variant hover:border-primary/50';
        const instructors = [...section.instructors].sort((a, b) => (a.role === 'main' ? -1 : b.role === 'main' ? 1 : 0));

        return el(
            'article',
            { class: `bg-surface-container-lowest p-4 rounded-xl shadow-sm transition-all ${border}` },
            el(
                'div',
                { class: 'flex flex-col sm:flex-row sm:items-start justify-between gap-3' },
                el(
                    'div',
                    { class: 'space-y-1 min-w-0' },
                    el(
                        'div',
                        { class: 'flex flex-wrap items-center gap-1.5' },
                        el('span', { class: 'px-2 py-0.5 rounded bg-primary-fixed text-on-primary-fixed text-label-sm font-bold' }, section.code),
                        el('span', { class: 'px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-label-sm font-semibold' }, `${section.subject.credits} tín chỉ`),
                        el('span', { class: 'px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant text-label-sm' }, section.compulsory ? 'Bắt buộc' : 'Tự chọn'),
                        section.planned_term ? el('span', { class: 'px-2 py-0.5 rounded-full bg-blue-50 text-blue-800 text-label-sm' }, `HK ${section.planned_term} theo CTĐT`) : null,
                        mine ? el('span', { class: 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 text-primary border border-primary/30 text-label-sm font-semibold' }, icon('check_circle', 'text-[14px]'), mine.status_label) : null,
                        section.status !== 'registration_open' ? el('span', { class: 'px-2 py-0.5 rounded-full bg-slate-100 text-secondary text-label-sm' }, section.status_label) : null,
                    ),
                    el('h3', { class: 'text-headline-sm font-bold text-on-surface' }, `${section.subject.name} (${section.subject.code})`),
                    el(
                        'div',
                        { class: 'text-body-sm text-secondary space-y-0.5' },
                        el('p', { class: 'flex items-center gap-1' }, icon('person_outline', 'text-[16px]'), instructors.length ? instructors.map((i) => i.name + (i.role === 'main' ? '' : ' (trợ giảng)')).join(', ') : 'Chưa phân công giảng viên'),
                        section.schedule.length
                            ? section.schedule.map((rule) => el('p', { class: 'flex items-center gap-1 text-on-surface' }, icon('schedule', 'text-[16px] text-primary'), rule.description, rule.room ? el('span', { class: 'text-secondary' }, ` · phòng ${rule.room}`) : null))
                            : el('p', { class: 'flex items-center gap-1' }, icon('schedule', 'text-[16px]'), 'Chưa có lịch học'),
                        section.starts_on
                            ? el('p', { class: 'flex items-center gap-1 text-on-surface font-semibold' }, icon('event', 'text-[16px] text-primary'), `Bắt đầu học: ${section.starts_on.split('-').reverse().join('/')}`)
                            : null,
                    ),
                ),
                actionArea(section, mine, otherClass, alertBox),
            ),
            !mine && reasons.length
                ? el('div', { class: 'mt-3 p-2.5 bg-amber-100/70 border border-amber-300 rounded-lg text-amber-900 text-label-sm space-y-1' },
                    reasons.map((reason) => el('p', { class: 'flex items-start gap-2' }, icon('info', 'text-[16px] text-amber-700 shrink-0'), reason)))
                : null,
            otherClass
                ? el('p', { class: 'mt-3 p-2.5 bg-blue-50 border border-primary/20 rounded-lg text-label-sm text-on-surface' }, `Bạn đang học lớp ${otherClass.section.code} của học phần này. Có thể đổi sang lớp này (giữ nguyên chỗ cũ nếu lớp mới không nhận).`)
                : null,
            alertBox,
            el(
                'div',
                { class: 'mt-3 pt-3 border-t border-surface-container flex flex-wrap items-center gap-2 text-label-sm text-secondary' },
                el('span', {}, 'Sĩ số: ', el('strong', { class: full ? 'text-red-700' : 'text-on-surface' }, `${enrolled}/${section.max_size}`)),
                el('div', { class: 'w-24 h-2 bg-slate-200 rounded-full overflow-hidden', role: 'img', 'aria-label': `Đã có ${enrolled} trên ${section.max_size} chỗ` },
                    el('div', { class: `h-full rounded-full ${full ? 'bg-red-500' : percent >= 80 ? 'bg-amber-500' : 'bg-emerald-600'}`, style: `width: ${percent}%` })),
                el('span', { class: full ? 'text-red-700 font-medium' : 'text-emerald-700 font-medium' }, full ? 'Hết chỗ' : `Còn ${section.remaining} chỗ`),
            ),
        );
    }

    function actionArea(section, mine, otherClass, alertBox) {
        const base = 'px-3.5 py-2 rounded-lg text-label-md font-semibold transition-colors flex items-center justify-center gap-1.5 shrink-0';

        if (mine) {
            if (mine.status !== 'registered' || !page.can.cancel) {
                return el('a', { href: page.links.result, class: `${base} border border-outline-variant text-on-surface hover:bg-surface-container-low` }, icon('fact_check'), 'Xem ở trang kết quả');
            }

            const button = el('button', { type: 'button', class: `${base} border border-red-300 bg-red-50 text-red-700 hover:bg-red-100` }, icon('delete_outline'), 'Hủy đăng ký');
            button.addEventListener('click', () => cancel(mine, section, button, alertBox));

            return button;
        }

        if (otherClass && otherClass.status === 'registered' && page.can.enroll) {
            const button = el('button', { type: 'button', class: `${base} bg-amber-700 hover:bg-amber-800 text-white shadow-sm` }, icon('swap_horiz'), 'Đổi sang lớp này');
            button.addEventListener('click', () => change(otherClass, section, button, alertBox));

            return button;
        }

        if (section.eligible && page.can.enroll) {
            const button = el('button', { type: 'button', class: `${base} bg-primary hover:bg-primary-container text-white shadow-sm active:scale-95` }, icon('add_circle'), 'Đăng ký lớp này');
            button.addEventListener('click', () => enroll(section, button, alertBox));

            return button;
        }

        return el('button', { type: 'button', disabled: true, class: `${base} bg-slate-200 text-slate-500 cursor-not-allowed` }, icon(section.remaining <= 0 ? 'block' : 'lock'), section.remaining <= 0 ? 'Đã hết chỗ' : 'Không đăng ký được');
    }

    // ---- Thao tác ghi ---------------------------------------------------------------------------------------
    async function enroll(section, button, alertBox) {
        await act(button, alertBox, () => api.post(page.api.enroll, { course_section_id: section.id }), `Đã đăng ký lớp ${section.code} – ${section.subject.name}.`);
    }

    async function cancel(enrollment, section, button, alertBox) {
        const ok = await confirmDialog({
            title: 'Hủy đăng ký lớp này?',
            message: `${section.code} – ${section.subject.name}\nChỗ của bạn sẽ được trả lại cho lớp; muốn học lại phải đăng ký lại nếu còn chỗ.`,
            confirmText: 'Hủy đăng ký',
            danger: true,
        });

        if (ok) {
            await act(button, alertBox, () => api.post(url(page.api.cancel, enrollment.id)), `Đã hủy đăng ký lớp ${section.code}.`);
        }
    }

    async function change(enrollment, section, button, alertBox) {
        const ok = await confirmDialog({
            title: 'Đổi lớp?',
            message: `Từ lớp ${enrollment.section.code} sang lớp ${section.code} (${section.subject.name}).\nNếu lớp mới không nhận, bạn vẫn giữ lớp cũ.`,
            confirmText: 'Đổi lớp',
        });

        if (ok) {
            await act(button, alertBox, () => api.post(url(page.api.change, enrollment.id), { course_section_id: section.id }), `Đã đổi sang lớp ${section.code}.`);
        }
    }

    /** Gửi một thao tác: thành công thì báo và tải lại danh sách; lỗi (422 nghiệp vụ, 403…) thì hiện ngay trên thẻ lớp. */
    async function act(button, alertBox, task, successMessage) {
        showAlert(alertBox, null);

        try {
            await withBusy(button, task);
            toast(successMessage, 'success');
            await load();
        } catch (error) {
            showAlert(alertBox, error);
            toast(error.message ?? 'Không thực hiện được thao tác.', 'error', 7000);
        }
    }

    // ---- Thời khóa biểu tuần ----------------------------------------------------------------------------------
    function renderTimetable() {
        const container = region('timetable');
        const result = state.result;

        if (!state.options?.period) {
            container.replaceChildren(emptyBlock('Chưa có thời khóa biểu', '', 'calendar_month'));

            return;
        }

        if (result?.error) {
            container.replaceChildren(errorBlock(result.error, () => load()));

            return;
        }

        const rows = result?.timetable ?? [];

        if (rows.length === 0) {
            container.replaceChildren(emptyBlock('Chưa có lớp nào trong thời khóa biểu', 'Đăng ký lớp để xem lịch tuần ở đây.', 'calendar_month'));

            return;
        }

        const days = [1, 2, 3, 4, 5, 6, ...(rows.some((r) => r.day_of_week === 7) ? [7] : [])];
        const dayLabel = (day) => rows.find((r) => r.day_of_week === day)?.day_label ?? ['', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ nhật'][day];
        const shifts = [
            { key: 'morning', label: 'Sáng' },
            { key: 'afternoon', label: 'Chiều' },
            { key: 'evening', label: 'Tối' },
        ].filter((shift) => rows.some((r) => shiftOf(r) === shift.key));

        const table = el(
            'table',
            { class: 'w-full min-w-[560px] border-collapse text-xs' },
            el('caption', { class: 'sr-only' }, 'Thời khóa biểu tuần của các lớp đã đăng ký'),
            el('thead', {}, el('tr', { class: 'bg-surface-container text-on-surface' },
                el('th', { scope: 'col', class: 'p-2 text-left font-medium text-secondary' }, 'Buổi'),
                days.map((day) => el('th', { scope: 'col', class: 'p-2 font-bold' }, dayLabel(day))))),
            el('tbody', {}, shifts.map((shift) => el('tr', { class: 'border-t border-outline-variant align-top' },
                el('th', { scope: 'row', class: 'p-2 text-left font-bold text-primary bg-surface-container-low' }, shift.label),
                days.map((day) => el('td', { class: 'p-1 border-l border-outline-variant min-w-[80px]' },
                    rows.filter((r) => r.day_of_week === day && shiftOf(r) === shift.key).map((r) =>
                        el('div', { class: 'mb-1 bg-primary/10 border-l-4 border-primary p-1.5 rounded text-[11px] space-y-0.5' },
                            el('p', { class: 'font-bold text-primary leading-tight' }, r.subject),
                            el('p', { class: 'text-secondary' }, `Tiết ${r.periods}${r.room ? ` · ${r.room}` : ''}`),
                            el('p', { class: 'text-outline' }, `${r.time} · tuần ${r.weeks}`)))))))),
        );

        container.replaceChildren(el('div', { class: 'overflow-x-auto border border-outline-variant rounded-lg', tabindex: '0', role: 'region', 'aria-label': 'Thời khóa biểu tuần' }, table));
    }

    /** Buổi theo giờ bắt đầu của khung tiết (dữ liệu "07:00–09:30" từ API). */
    function shiftOf(row) {
        const hour = Number.parseInt(String(row.time).slice(0, 2), 10);

        if (Number.isNaN(hour) || hour < 12) return 'morning';

        return hour < 17 ? 'afternoon' : 'evening';
    }

    // ---- Đồng hồ đếm ngược --------------------------------------------------------------------------------
    function toDate(vnDateTime) {
        return new Date(`${String(vnDateTime).replace(' ', 'T')}${page.tzOffset ?? '+07:00'}`);
    }

    function formatVn(value) {
        // "2026-11-18 11:00" → "11:00 18/11/2026"
        const [date, time] = String(value).split(' ');
        const [y, m, d] = date.split('-');

        return `${time} ${d}/${m}/${y}`;
    }

    function startCountdown(closesAt) {
        stopCountdown();
        const tick = () => {
            const left = closesAt - (Date.now() + state.clockOffset);

            if (left <= 0) {
                setText('countdown', 'Đã hết thời gian đăng ký');
                stopCountdown();

                return;
            }

            const minutes = Math.floor(left / 60000);
            setText('countdown', `Còn ${Math.floor(minutes / 1440)} ngày ${Math.floor((minutes % 1440) / 60)} giờ ${minutes % 60} phút`);
        };
        tick();
        state.timer = window.setInterval(tick, 30_000);
    }

    function stopCountdown() {
        if (state.timer) {
            window.clearInterval(state.timer);
            state.timer = null;
        }
    }
}
