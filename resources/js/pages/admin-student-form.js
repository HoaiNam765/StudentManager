/**
 * Thêm / sửa hồ sơ sinh viên (FE #16, mock admin/sinh-vien-doi-trang-thai): danh mục từ API (khóa, ngành, chuyên
 * ngành, hệ, danh mục dùng chung, đơn vị hành chính). Khi sửa chỉ gửi các ô đã đổi; số giấy tờ và địa chỉ hiển thị dạng
 * che nên để trống nghĩa là giữ nguyên. Lỗi 422 gắn vào từng ô.
 */
import { api } from '../api.js';
import { fillSelect, listAll, rows, tpl } from '../lookups.js';
import { applyFieldErrors, clearFieldErrors, pageData, showAlert, toast, withBusy } from '../ui.js';

const NULLABLE = ['gender_id', 'place_of_birth', 'ethnicity_id', 'religion_id', 'nationality_id', 'national_id_issued_on', 'national_id_issued_by',
    'school_email', 'personal_email', 'phone', 'specialization_id', 'admission_date', 'priority_group_id', 'priority_area_id', 'note'];

export default function adminStudentForm(root) {
    const page = pageData();
    const form = root.querySelector('[data-form="student"]');
    const alert = root.querySelector('[data-region="alert"]');
    const editing = page.mode === 'edit';
    const state = { specializations: [], initial: {} };

    form.addEventListener('submit', save);
    form.elements.major_id.addEventListener('change', fillSpecializations);
    root.querySelectorAll('[data-unit-province]').forEach((select) => select.addEventListener('change', () => loadWards(select.dataset.unitProvince)));

    bootstrap();

    async function bootstrap() {
        const button = form.querySelector('[data-action="save"]');
        // Chưa nạp xong danh mục / hồ sơ thì chưa cho lưu (tránh ghi đè bằng ô trống)
        button.disabled = true;

        try {
            const [cohorts, majors, specializations, types, provinces] = await Promise.all([
                api.get(page.api.cohorts), listAll(page.api.majors), listAll(page.api.specializations), api.get(page.api.trainingTypes),
                api.get(page.api.units, { level: 'province' }),
            ]);
            state.specializations = specializations;
            fillSelect(form.elements.cohort, rows(cohorts), (c) => `${c.code} (${c.start_year})`, 'start_year');
            fillSelect(form.elements.major_id, majors, (m) => `${m.code} — ${m.name}`);
            fillSelect(form.elements.training_type_id, rows(types), (t) => t.name);
            root.querySelectorAll('[data-unit-province]').forEach((select) => fillSelect(select, rows(provinces), (u) => u.name));
            await Promise.all([...form.querySelectorAll('[data-lookup]')].map(async (select) => {
                fillSelect(select, rows(await api.get(tpl(page.api.lookup, select.dataset.lookup, '__CODE__'))), (v) => v.name);
            }));

            if (editing) {
                await loadStudent();
            } else {
                fillSpecializations();
            }
        } catch (error) {
            showAlert(alert, error);
        }

        state.initial = values();
        button.disabled = false;
    }

    async function loadStudent() {
        const s = await api.get(page.api.show);
        const p = s.personal ?? {};
        const c = s.contact ?? {};
        const set = (name, value) => {
            if (form.elements[name] && value !== undefined && value !== null) {
                form.elements[name].value = value;
            }
        };

        set('cohort', s.cohort);
        set('major_id', s.major?.id);
        fillSpecializations();
        set('specialization_id', s.academic?.specialization?.id);
        set('training_type_id', s.training_type?.id);
        set('admission_date', s.academic?.admission_date);
        set('priority_group_id', p.priority_group_id);
        set('priority_area_id', p.priority_area_id);
        set('full_name', s.full_name);
        set('date_of_birth', s.date_of_birth);
        set('gender_id', p.gender?.id);
        set('id_type', p.id_type);
        set('ethnicity_id', p.ethnicity?.id);
        set('religion_id', p.religion?.id);
        set('nationality_id', p.nationality?.id);
        set('place_of_birth', p.place_of_birth);
        set('school_email', s.school_email);
        set('personal_email', c.personal_email);
        set('phone', c.phone);
        set('note', s.note);
        root.querySelector('[data-field="national-id-masked"]').textContent = p.national_id_masked ? `Hiện tại: ${p.national_id_masked}` : 'Chưa có số giấy tờ';
        root.querySelector('[data-field="permanent-current"]').textContent = `Hiện tại: ${[c.permanent_address_masked, c.permanent_unit?.name].filter(Boolean).join(', ') || 'chưa có'}`;
        root.querySelector('[data-field="temporary-current"]').textContent = `Hiện tại: ${[c.temporary_address_masked, c.temporary_unit?.name].filter(Boolean).join(', ') || 'chưa có'}`;
    }

    function fillSpecializations() {
        const majorId = form.elements.major_id.value;
        fillSelect(form.elements.specialization_id, state.specializations.filter((s) => String(s.major_id ?? s.major?.id) === majorId), (s) => s.name);
    }

    async function loadWards(kind) {
        const province = root.querySelector(`[data-unit-province="${kind}"]`).value;
        const ward = root.querySelector(`[data-unit-ward="${kind}"]`);
        fillSelect(ward, [], () => '');

        if (!province) {
            return;
        }

        try {
            fillSelect(ward, rows(await api.get(page.api.units, { parent_id: province })), (u) => u.name);
        } catch (error) {
            showAlert(alert, error);
        }
    }

    function values() {
        return Object.fromEntries([...new FormData(form).entries()].filter(([, value]) => !(value instanceof File)));
    }

    async function save(event) {
        event.preventDefault();
        clearFieldErrors(form);
        showAlert(alert, null);
        const current = values();
        const body = {};

        for (const [name, value] of Object.entries(current)) {
            if (name === 'reason') {
                continue;
            }

            if (editing && value === (state.initial[name] ?? '')) {
                continue;
            }

            if (value === '') {
                // Ô số giấy tờ / địa chỉ để trống khi sửa = giữ nguyên
                if (NULLABLE.includes(name) && editing) {
                    body[name] = null;
                }

                continue;
            }

            body[name] = value;
        }

        if (editing && Object.keys(body).length === 0) {
            showAlert(alert, 'Chưa có thay đổi nào để lưu.', 'info');

            return;
        }

        if (editing && current.reason) {
            body.reason = current.reason;
        }

        try {
            const saved = await withBusy(form.querySelector('[data-action="save"]'), () => (editing ? api.put(page.api.update, body) : api.post(page.api.store, body)));
            toast(editing ? 'Đã lưu thay đổi hồ sơ.' : 'Đã tạo hồ sơ sinh viên.', 'success');
            window.setTimeout(() => window.location.assign(tpl(page.links.show, saved.id)), 600);
        } catch (error) {
            const unmatched = error?.isValidation ? applyFieldErrors(form, error.errors) : [];
            showAlert(alert, error?.isValidation && !unmatched.length ? 'Dữ liệu chưa hợp lệ, xem lỗi ở từng ô.' : (unmatched.length ? unmatched.join(' ') : error));
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}
