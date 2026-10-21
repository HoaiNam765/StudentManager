/**
 * Trang kết quả học tập của sinh viên (resources/views/student/grade/results.blade.php).
 *
 * API: GET gpa (GPA học kỳ / tích lũy đã lưu), GET transcript (học phần đã công bố theo học kỳ), GET grades (điểm thành
 * phần), GET transcript?format=pdf&lang=vi|en (bảng điểm PDF). Máy chủ chỉ trả lớp đã công bố điểm.
 */
import { api } from '../api.js';
import { el, emptyBlock, errorBlock, icon, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const number = (value, digits = 2) => (value === null || value === undefined || value === '' ? '—' : Number(value).toLocaleString('vi-VN', { minimumFractionDigits: digits, maximumFractionDigits: digits }));

export default function studentGrades(root) {
    const page = pageData();
    const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const setText = (name, value) => root.querySelectorAll(`[data-field="${name}"]`).forEach((node) => { node.textContent = value; });

    root.querySelector('[data-action="reload"]').addEventListener('click', () => load());
    root.querySelectorAll('[data-action="pdf"]').forEach((button) => button.addEventListener('click', () => downloadPdf(button)));

    load();

    async function load() {
        region('terms').replaceChildren(loadingBlock(3));
        region('summary').replaceChildren();
        showAlert(region('alert'), null);

        try {
            const [gpa, transcript, grades] = await Promise.all([api.get(page.api.gpa), api.get(page.api.transcript), api.get(page.api.grades)]);
            const components = new Map((grades.grades ?? []).map((item) => [item.enrollment_id, item.components ?? []]));
            render(gpa, transcript, components);
        } catch (error) {
            setText('subtitle', 'Không tải được kết quả học tập.');
            region('terms').replaceChildren(errorBlock(error, () => load()));
        }
    }

    function render(gpa, transcript, components) {
        const { student } = transcript;
        setText('subtitle', `${student.full_name} · MSSV ${student.student_code} · Khóa ${student.cohort ?? '—'}`);
        renderSummary(gpa, transcript);

        if (transcript.terms.length === 0) {
            region('terms').replaceChildren(emptyBlock('Chưa có điểm đã công bố', 'Điểm hiện ở đây khi Phòng Đào tạo công bố bảng điểm lớp học phần.', 'school'));

            return;
        }

        region('terms').replaceChildren(...[...transcript.terms].reverse().map((term) => termCard(term, components)));
    }

    function renderSummary(gpa, transcript) {
        const cumulative = gpa.cumulative ?? {};
        const card = (label, value, hint = '') => el('div', { class: 'bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-4' },
            el('p', { class: 'text-label-sm text-secondary uppercase tracking-wider' }, label),
            el('p', { class: 'text-headline-lg font-bold text-on-surface mt-1' }, value),
            hint ? el('p', { class: 'text-[11px] text-outline mt-0.5' }, hint) : null);

        region('summary').replaceChildren(
            card('GPA tích lũy', number(cumulative.gpa), 'Thang 4'),
            card('Xếp loại học lực', cumulative.classification ?? '—', 'Theo quy chế của khóa'),
            card('Tín chỉ tích lũy', cumulative.credits_earned ?? 0, 'Học phần đạt, mỗi học phần tính một lần'),
            card('Số học kỳ có điểm', transcript.terms.length),
        );
    }

    function termCard(term, components) {
        const th = (text, extra = '') => el('th', { scope: 'col', class: `px-3 py-2.5 font-semibold text-on-surface-variant ${extra}` }, text);
        const td = (content, extra = '') => el('td', { class: `px-3 py-2.5 ${extra}` }, content);
        const result = (row) => {
            if (row.special_label) {
                return el('span', { class: 'inline-flex rounded-full border border-amber-300 bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-900' }, row.special_label);
            }

            if (row.is_passed === null) {
                return el('span', { class: 'text-outline' }, '—');
            }

            return row.is_passed
                ? el('span', { class: 'inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-800' }, 'Đạt')
                : el('span', { class: 'inline-flex rounded-full border border-red-300 bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-800' }, 'Không đạt');
        };
        const detail = (row) => {
            const parts = components.get(row.enrollment_id) ?? [];

            return parts.length === 0
                ? null
                : el('p', { class: 'mt-0.5 text-[11px] text-outline' }, parts.map((part) => `${part.name} (${Number(part.weight)}%): ${part.score === null ? '—' : Number(part.score).toLocaleString('vi-VN')}`).join(' · '));
        };

        const summary = term.gpa
            ? el('p', { class: 'text-label-md text-on-surface-variant' }, `GPA học kỳ ${number(term.gpa.gpa)} · ${term.gpa.credits_earned}/${term.gpa.credits_attempted} tín chỉ đạt · ${term.gpa.classification ?? 'chưa xếp loại'}`)
            : null;

        return el(
            'section',
            { class: 'bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm' },
            el('div', { class: 'px-5 py-4 border-b border-outline-variant flex flex-wrap items-center justify-between gap-2' },
                el('h2', { class: 'text-headline-sm font-bold text-on-surface flex items-center gap-2' }, icon('event_note', 'text-[20px] text-primary'), term.name || 'Học kỳ'),
                summary),
            el('div', { class: 'overflow-x-auto p-4' }, el(
                'table',
                { class: 'w-full min-w-[760px] border-collapse text-left text-sm' },
                el('caption', { class: 'sr-only' }, `Điểm học phần ${term.name}`),
                el('thead', { class: 'bg-surface-container-low' }, el('tr', {}, th('Mã HP'), th('Học phần'), th('TC', 'text-center'), th('Hệ 10', 'text-center'), th('Chữ', 'text-center'), th('Hệ 4', 'text-center'), th('Kết quả'))),
                el('tbody', {}, term.rows.map((row) => el(
                    'tr',
                    { class: 'border-t border-outline-variant align-top' },
                    td(row.subject_code, 'font-mono text-xs'),
                    td([el('span', { class: 'font-medium text-on-surface' }, row.subject_name), row.counts_toward_gpa ? null : el('span', { class: 'ml-1 text-[11px] text-outline' }, '(không tính GPA)'), detail(row)]),
                    td(row.credits, 'text-center'),
                    td(number(row.score10, 1), 'text-center'),
                    td(row.letter ?? '—', 'text-center font-semibold'),
                    td(number(row.point4, 1), 'text-center'),
                    td(result(row)),
                ))),
            )),
        );
    }

    async function downloadPdf(button) {
        try {
            await withBusy(button, () => api.download(page.api.transcript, { format: 'pdf', lang: button.dataset.lang }, 'bang-diem.pdf'), 'Đang tạo PDF…');
            toast('Đã tải bảng điểm.', 'success');
        } catch (error) {
            showAlert(region('alert'), error);
        }
    }
}
