{{--
    FE #16 — Hồ sơ sinh viên (mock admin/sinh-vien-chi-tiet). Dữ liệu do resources/js/pages/admin-student-detail.js gọi
    API STU (hồ sơ đã che, người thân, trạng thái theo quyết định, lịch sử thay đổi, nhập học) và GRD (GPA, bảng điểm).
--}}
@extends('layouts.portal')

@section('title', 'Hồ sơ sinh viên - Quản lý Đào tạo')
@section('page-title', 'Hồ sơ sinh viên')

@section('page')
<div data-page="admin-student-detail" class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
        <a href="{{ $pageData['links']['list'] }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span> Quay lại danh sách sinh viên
        </a>
        <span data-field="updated" class="text-xs text-secondary"></span>
    </div>

    <div data-region="alert" hidden></div>

    {{-- Thẻ tiêu đề hồ sơ --}}
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 flex flex-col xl:flex-row xl:items-center justify-between gap-5">
        <div class="flex items-start gap-4 min-w-0">
            <div data-region="avatar" class="w-20 h-24 rounded-xl bg-blue-100 text-primary font-bold text-2xl flex items-center justify-center ring-1 ring-blue-200 shrink-0 overflow-hidden"></div>
            <div class="min-w-0 space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 data-field="name" class="font-headline-lg text-headline-lg font-bold text-on-surface">Đang tải…</h1>
                    <span data-region="status-badge"></span>
                    <span data-field="training-type" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-surface-container text-on-surface-variant"></span>
                </div>
                <p data-field="meta" class="text-sm text-secondary"></p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3 text-center shrink-0">
            <div class="rounded-xl border border-outline-variant/50 px-4 py-2">
                <p class="text-[11px] font-semibold uppercase text-secondary">GPA tích lũy</p>
                <p class="text-xl font-bold text-primary"><span data-field="gpa">—</span><span class="text-xs text-secondary font-medium"> / 4.0</span></p>
                <p data-field="classification" class="text-[11px] text-secondary">—</p>
            </div>
            <div class="rounded-xl border border-outline-variant/50 px-4 py-2">
                <p class="text-[11px] font-semibold uppercase text-secondary">Tín chỉ đạt</p>
                <p data-field="credits" class="text-xl font-bold text-on-surface">—</p>
                <p class="text-[11px] text-secondary">tích lũy</p>
            </div>
            <div class="rounded-xl border border-outline-variant/50 px-4 py-2">
                <p class="text-[11px] font-semibold uppercase text-secondary">Học kỳ đã học</p>
                <p data-field="terms" class="text-xl font-bold text-on-surface">—</p>
                <p class="text-[11px] text-secondary">có GPA</p>
            </div>
        </div>
        <div data-region="actions" class="flex flex-wrap gap-2 print:hidden">
            <button type="button" onclick="window.print()" class="h-9 px-3.5 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low text-sm font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">print</span> In hồ sơ
            </button>
            @if ($pageData['can']['update'])
                <a href="{{ $pageData['links']['edit'] }}" class="h-9 px-3.5 rounded-lg bg-primary text-white hover:bg-primary-container text-sm font-semibold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">edit</span> Chỉnh sửa hồ sơ
                </a>
            @endif
        </div>
    </section>

    <div class="grid xl:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)] gap-6 items-start">
        <div class="space-y-6">
            {{-- Thông tin cơ bản --}}
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">badge</span> Thông tin cơ bản sinh viên</h2>
                    @if ($pageData['can']['update'])
                        <button type="button" data-action="reveal" class="text-xs font-semibold text-primary hover:underline print:hidden">Xem đầy đủ số giấy tờ, địa chỉ</button>
                    @endif
                </div>
                <dl data-region="basic" class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm"></dl>
            </section>

            {{-- Liên lạc và địa chỉ --}}
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">contacts</span> Thông tin liên lạc & Địa chỉ cư trú</h2>
                <dl data-region="contact" class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm"></dl>
            </section>

            {{-- Gia đình và liên hệ khẩn cấp --}}
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">family_restroom</span> Thông tin gia đình & Liên hệ khẩn cấp</h2>
                    @if ($pageData['can']['update'])
                        <button type="button" data-action="add-contact" class="inline-flex items-center gap-1 rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold hover:bg-surface-container-low print:hidden"><span class="material-symbols-outlined text-[16px]" aria-hidden="true">add</span> Thêm người thân</button>
                    @endif
                </div>
                <div data-region="contacts"></div>
            </section>

            {{-- Kết quả học tập --}}
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">analytics</span> Tóm tắt Kết quả Học tập</h2>
                    <select data-input="term" aria-label="Chọn học kỳ" class="rounded-lg border border-outline-variant bg-white px-3 py-1.5 text-sm print:hidden"></select>
                </div>
                <div data-region="results"></div>
            </section>
        </div>

        <div class="space-y-6">
            {{-- Trạng thái học vụ --}}
            <section id="trang-thai" class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-amber-700" aria-hidden="true">sync_alt</span> Trạng thái học vụ</h2>
                    <div data-region="status-actions" class="flex flex-wrap gap-2 print:hidden"></div>
                </div>
                <div data-region="status"></div>
            </section>

            {{-- Chương trình đào tạo --}}
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-3">
                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">account_tree</span> Chương trình đào tạo</h2>
                <div data-region="curricula"></div>
            </section>

            {{-- Lịch sử thay đổi hồ sơ --}}
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-3">
                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">history</span> Lịch sử thay đổi hồ sơ</h2>
                <div data-region="logs"></div>
            </section>
        </div>
    </div>
</div>
@endsection
