@extends('layouts.portal')

@section('title', 'Quản lý đăng ký học phần - Quản lý Đào tạo')
@section('page-title', 'Đăng ký học phần')

@section('page')
<div data-page="admin-enrollment" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col xl:flex-row xl:items-end justify-between gap-4">
        <div>
            <h1 class="text-headline-lg font-bold">Quản lý đăng ký học phần</h1>
            <p class="mt-1 text-sm text-on-surface-variant">Tạo đợt cho học kỳ, chọn kỳ CTĐT cho mọi khoa – khóa (bảng khung), mở lớp + giảng viên, xếp lịch, công bố rồi mở đăng ký.</p>
        </div>
        <label class="text-xs font-semibold text-on-surface-variant min-w-[280px]">Học kỳ
            <select data-filter="term" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select>
        </label>
    </section>

    <div data-region="alert" hidden></div>
    <section data-region="stats"></section>

    <div class="grid xl:grid-cols-[minmax(0,.85fr)_minmax(0,1.15fr)] gap-6 items-start">
        <div class="space-y-6">
            @if (($pageData['can']['create'] ?? false) || ($pageData['can']['update'] ?? false))
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div><h2 class="text-headline-sm font-bold" data-form-title>Tạo đợt đăng ký</h2><p class="text-xs text-on-surface-variant mt-1">Đợt theo chương trình khung áp dụng cho mọi khoa – khóa có trong bảng khung của đợt.</p></div>
                    <button type="button" data-action="cancel-edit" hidden class="text-xs text-primary hover:underline">Hủy sửa</button>
                </div>
                <form data-form="period" class="space-y-4">
                    <label class="block text-sm">Loại đợt
                        <select name="mode" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2">
                            <option value="frameworks">Theo chương trình khung — cả học kỳ, mọi khoa – khóa</option>
                            <option value="targets">Theo đối tượng (khóa / ngành / lớp)</option>
                        </select>
                    </label>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <label class="text-sm">Tên đợt<input name="name" required maxlength="255" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                        <label class="text-sm">Vòng<select name="round" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"><option value="round_1">Vòng 1</option><option value="round_2">Vòng 2</option><option value="supplementary">Bổ sung</option></select></label>
                        <label class="text-sm">Mở lúc<input name="opens_at" type="datetime-local" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                        <label class="text-sm">Đóng lúc<input name="closes_at" type="datetime-local" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                        <label class="text-sm">Tín chỉ tối thiểu<input name="min_credits" type="number" min="0" max="60" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                        <label class="text-sm">Tín chỉ tối đa<input name="max_credits" type="number" min="1" max="60" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                    </div>
                    <div data-region="targets-box" class="rounded-xl bg-surface-container-low p-3 space-y-3">
                        <div class="flex items-center justify-between"><div><p class="text-sm font-semibold">Đối tượng áp dụng</p><p class="text-xs text-on-surface-variant">Chỉ dùng cho đợt theo đối tượng. Để trống = mọi sinh viên.</p></div><button type="button" data-action="add-target" class="rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold">+ Dòng đối tượng</button></div>
                        <div data-region="targets" class="space-y-2"></div>
                    </div>
                    <div class="flex justify-end"><button type="submit" class="rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold">Lưu đợt đăng ký</button></div>
                </form>
            </section>
            @endif

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
                <div class="px-5 py-4 border-b border-outline-variant"><h2 class="text-headline-sm font-bold">Chốt đăng ký học kỳ</h2><p class="text-xs text-on-surface-variant mt-1">Xem trước các vấn đề bắt buộc trước khi finalize.</p></div>
                <div data-region="finalize" class="p-4"></div>
            </section>
        </div>

        <div class="space-y-6">
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
                <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between"><h2 class="text-headline-sm font-bold">Các đợt đăng ký</h2><button type="button" data-action="reload" class="rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold">Làm mới</button></div>
                <div data-region="periods" class="p-4"></div>
            </section>

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
                <div class="px-5 py-4 border-b border-outline-variant"><h2 class="text-headline-sm font-bold">Khung & lớp của đợt</h2></div>
                <div data-region="offerings" class="p-4"><p class="text-sm text-on-surface-variant">Bấm “Khung & lớp” trên một đợt theo chương trình khung để chọn kỳ cho từng khoa – khóa, mở lớp, phân công giảng viên và xếp lịch.</p></div>
            </section>

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
                <div class="px-5 py-4 border-b border-outline-variant"><h2 class="text-headline-sm font-bold">Lớp thiếu sĩ số</h2></div>
                <div data-region="under-min" class="p-4"></div>
            </section>
        </div>
    </div>
</div>
@endsection
