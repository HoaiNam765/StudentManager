{{--
    FE #16 — Thêm / sửa hồ sơ sinh viên (mock admin/sinh-vien-doi-trang-thai). Dữ liệu do
    resources/js/pages/admin-student-form.js gọi API STU (POST / PUT /admin/students) và danh mục dùng chung.
--}}
@extends('layouts.portal')

@php
    $editing = $student !== null;
    $input = 'mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20';
    $label = 'block text-sm font-medium text-on-surface';
@endphp

@section('title', ($editing ? 'Sửa hồ sơ sinh viên' : 'Thêm sinh viên').' - Quản lý Đào tạo')
@section('page-title', $editing ? 'Sửa hồ sơ sinh viên' : 'Thêm sinh viên')

@section('page')
<div data-page="admin-student-form" class="space-y-6">
    <a href="{{ $editing ? str_replace('__ID__', $student->id, $pageData['links']['show']) : $pageData['links']['list'] }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span> {{ $editing ? 'Quay lại hồ sơ' : 'Quay lại danh sách sinh viên' }}
    </a>

    <form data-form="student" novalidate onsubmit="event.preventDefault()" class="space-y-6">
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">
                    @if ($editing)
                        Chỉnh sửa hồ sơ sinh viên: {{ $student->full_name }}@if ($student->student_code) (MSSV: {{ $student->student_code }})@endif
                    @else
                        Thêm hồ sơ sinh viên mới
                    @endif
                </h1>
                <p class="text-sm text-secondary mt-1">
                    @if ($editing)
                        Trạng thái: <strong>{{ $student->status_label }}</strong>. Đổi trạng thái học vụ thực hiện ở trang hồ sơ theo quyết định.
                    @else
                        Hồ sơ mới ở trạng thái "Chờ nhập học"; MSSV, email trường, tài khoản được cấp khi xác nhận nhập học.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($editing)
                    <a href="{{ str_replace('__ID__', $student->id, $pageData['links']['show']) }}#trang-thai" class="h-10 px-3.5 rounded-lg border border-amber-300 text-amber-800 hover:bg-amber-50 text-sm font-semibold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">warning</span> Đổi trạng thái học vụ
                    </a>
                @endif
                <a href="{{ $editing ? str_replace('__ID__', $student->id, $pageData['links']['show']) : $pageData['links']['list'] }}" class="h-10 px-4 rounded-lg border border-outline-variant text-sm font-semibold flex items-center hover:bg-surface-container-low">Hủy bỏ</a>
                <button type="submit" data-action="save" disabled class="disabled:opacity-60 h-10 px-4 rounded-lg bg-primary text-white hover:bg-primary-container text-sm font-semibold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">save</span> {{ $editing ? 'Lưu thay đổi hồ sơ' : 'Tạo hồ sơ' }}
                </button>
            </div>
        </section>

        <div data-region="alert" hidden></div>

        {{-- 1. Học vụ & đào tạo --}}
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">school</span> 1. Thông tin học vụ & Đào tạo</h2>
            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <label class="{{ $label }}">Mã sinh viên (MSSV)
                    <input value="{{ $student?->student_code ?? 'Cấp khi nhập học' }}" disabled class="{{ $input }} bg-surface-container-low text-secondary">
                </label>
                <label class="{{ $label }}">Khóa <span class="text-red-600">*</span>
                    <select name="cohort" required class="{{ $input }}"><option value="">Chọn khóa</option></select>
                </label>
                <label class="{{ $label }}">Ngành đào tạo <span class="text-red-600">*</span>
                    <select name="major_id" required class="{{ $input }}"><option value="">Chọn ngành</option></select>
                </label>
                <label class="{{ $label }}">Chuyên ngành
                    <select name="specialization_id" class="{{ $input }}"><option value="">Không có</option></select>
                </label>
                <label class="{{ $label }}">Hệ đào tạo <span class="text-red-600">*</span>
                    <select name="training_type_id" required class="{{ $input }}"><option value="">Chọn hệ</option></select>
                </label>
                <label class="{{ $label }}">Ngày nhập học
                    <input name="admission_date" type="date" class="{{ $input }}">
                </label>
                <label class="{{ $label }}">Đối tượng ưu tiên
                    <select name="priority_group_id" data-lookup="PRIORITY_GROUP" class="{{ $input }}"><option value="">Không</option></select>
                </label>
                <label class="{{ $label }}">Khu vực ưu tiên
                    <select name="priority_area_id" data-lookup="PRIORITY_AREA" class="{{ $input }}"><option value="">Không</option></select>
                </label>
            </div>
        </section>

        {{-- 2. Cá nhân & nhận dạng --}}
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">person</span> 2. Thông tin cá nhân & Nhận dạng</h2>
            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <label class="{{ $label }} sm:col-span-2">Họ và tên khai sinh <span class="text-red-600">*</span>
                    <input name="full_name" required maxlength="255" class="{{ $input }}">
                </label>
                <label class="{{ $label }}">Ngày sinh <span class="text-red-600">*</span>
                    <input name="date_of_birth" type="date" required class="{{ $input }}">
                </label>
                <label class="{{ $label }}">Giới tính
                    <select name="gender_id" data-lookup="GENDER" class="{{ $input }}"><option value="">Chọn</option></select>
                </label>
                <label class="{{ $label }}">Loại giấy tờ
                    <select name="id_type" class="{{ $input }}">
                        <option value="national_id">Căn cước công dân</option>
                        <option value="passport">Hộ chiếu</option>
                    </select>
                </label>
                <label class="{{ $label }}">Số giấy tờ
                    <input name="national_id" maxlength="20" inputmode="numeric" class="{{ $input }}" placeholder="{{ $editing ? 'Để trống nếu giữ nguyên' : '12 chữ số' }}">
                    @if ($editing)<span data-field="national-id-masked" class="mt-1 block text-xs text-secondary"></span>@endif
                </label>
                <label class="{{ $label }}">Ngày cấp
                    <input name="national_id_issued_on" type="date" class="{{ $input }}">
                </label>
                <label class="{{ $label }}">Nơi cấp
                    <input name="national_id_issued_by" maxlength="255" class="{{ $input }}">
                </label>
                <label class="{{ $label }}">Dân tộc
                    <select name="ethnicity_id" data-lookup="ETHNICITY" class="{{ $input }}"><option value="">Chọn</option></select>
                </label>
                <label class="{{ $label }}">Tôn giáo
                    <select name="religion_id" data-lookup="RELIGION" class="{{ $input }}"><option value="">Chọn</option></select>
                </label>
                <label class="{{ $label }}">Quốc tịch
                    <select name="nationality_id" data-lookup="NATIONALITY" class="{{ $input }}"><option value="">Chọn</option></select>
                </label>
                <label class="{{ $label }}">Nơi sinh (Tỉnh/TP)
                    <input name="place_of_birth" maxlength="255" class="{{ $input }}">
                </label>
            </div>
        </section>

        {{-- 3. Liên lạc & cư trú --}}
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">contact_mail</span> 3. Thông tin liên lạc & Cư trú</h2>
            <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <label class="{{ $label }}">Email trường
                    <input name="school_email" type="email" maxlength="255" class="{{ $input }}" placeholder="Cấp tự động khi nhập học">
                </label>
                <label class="{{ $label }}">Email cá nhân
                    <input name="personal_email" type="email" maxlength="255" class="{{ $input }}">
                </label>
                <label class="{{ $label }}">Số điện thoại
                    <input name="phone" maxlength="15" inputmode="tel" class="{{ $input }}" placeholder="0912345678">
                </label>
            </div>
            <div class="grid lg:grid-cols-2 gap-4">
                <fieldset class="rounded-xl border border-outline-variant/60 p-4 space-y-3">
                    <legend class="px-1 text-sm font-semibold text-on-surface">Địa chỉ thường trú</legend>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <label class="{{ $label }}">Tỉnh / Thành phố
                            <select data-unit-province="permanent" class="{{ $input }}"><option value="">Chọn tỉnh</option></select>
                        </label>
                        <label class="{{ $label }}">Xã / Phường
                            <select name="permanent_unit_id" data-unit-ward="permanent" class="{{ $input }}"><option value="">Chọn xã / phường</option></select>
                        </label>
                    </div>
                    <label class="{{ $label }}">Số nhà, đường
                        <input name="permanent_address" maxlength="500" class="{{ $input }}" placeholder="{{ $editing ? 'Để trống nếu giữ nguyên' : '' }}">
                    </label>
                    <p data-field="permanent-current" class="text-xs text-secondary"></p>
                </fieldset>
                <fieldset class="rounded-xl border border-outline-variant/60 p-4 space-y-3">
                    <legend class="px-1 text-sm font-semibold text-on-surface">Địa chỉ tạm trú / Nơi ở hiện tại</legend>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <label class="{{ $label }}">Tỉnh / Thành phố
                            <select data-unit-province="temporary" class="{{ $input }}"><option value="">Chọn tỉnh</option></select>
                        </label>
                        <label class="{{ $label }}">Xã / Phường
                            <select name="temporary_unit_id" data-unit-ward="temporary" class="{{ $input }}"><option value="">Chọn xã / phường</option></select>
                        </label>
                    </div>
                    <label class="{{ $label }}">Số nhà, đường
                        <input name="temporary_address" maxlength="500" class="{{ $input }}" placeholder="{{ $editing ? 'Để trống nếu giữ nguyên' : '' }}">
                    </label>
                    <p data-field="temporary-current" class="text-xs text-secondary"></p>
                </fieldset>
            </div>
            <label class="{{ $label }}">Ghi chú
                <textarea name="note" rows="2" maxlength="1000" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></textarea>
            </label>
        </section>

        {{-- 4. Gia đình & khẩn cấp --}}
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-3">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-red-700" aria-hidden="true">emergency</span> 4. Thông tin gia đình & Khẩn cấp</h2>
            <p class="text-sm text-on-surface-variant">
                @if ($editing)
                    Người thân và liên hệ khẩn cấp được thêm / sửa ở <a class="text-primary font-semibold hover:underline" href="{{ str_replace('__ID__', $student->id, $pageData['links']['show']) }}">trang hồ sơ</a>.
                @else
                    Sau khi tạo hồ sơ, thêm người thân và liên hệ khẩn cấp ở trang hồ sơ sinh viên.
                @endif
            </p>
        </section>

        @if ($editing)
            <section class="bg-amber-50 rounded-xl border border-amber-300 p-5 space-y-2">
                <label class="{{ $label }}">Lý do thay đổi
                    <textarea name="reason" rows="2" maxlength="500" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm" placeholder="Bắt buộc khi đổi họ tên, ngày sinh, giấy tờ, ngành, chuyên ngành"></textarea>
                </label>
                <p class="text-xs text-amber-900">Thay đổi trường nhạy cảm được ghi lịch sử kèm lý do (BR-STU-06).</p>
            </section>
        @endif
    </form>
</div>
@endsection
