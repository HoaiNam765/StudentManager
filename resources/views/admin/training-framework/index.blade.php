@extends('layouts.portal')

@section('title', 'Chương trình khung - Quản lý Đào tạo')
@section('page-title', 'Chương trình khung')

@section('page')
<div data-page="admin-training-frameworks" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col xl:flex-row xl:items-end justify-between gap-4">
        <div>
            <h1 class="text-headline-lg font-bold text-on-surface">Chương trình khung theo khoa – khóa – học kỳ</h1>
            <p class="mt-1 text-sm text-on-surface-variant">Chọn <strong>học kỳ</strong> và <strong>khoa</strong> → danh sách hiện các khóa của khoa; bấm một khóa là có chương trình khung (môn lấy từ CTĐT). Mở lớp, phân công giảng viên, xếp lịch và công bố ở <strong>Đăng ký học phần</strong> (đợt cả học kỳ, bảng Khoa × Khóa).</p>
        </div>
        <div class="grid sm:grid-cols-4 gap-2 w-full xl:w-auto items-end">
            <label class="text-xs font-semibold text-on-surface-variant">Học kỳ
                <select data-filter="term" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"><option value="">Tất cả học kỳ</option></select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Khoa
                <select data-filter="faculty" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"><option value="">Tất cả khoa</option></select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Khóa
                <select data-filter="cohort" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"><option value="">Tất cả khóa</option></select>
            </label>
            @if ($pageData['can']['create'] ?? false)
            <button type="button" data-action="create" class="inline-flex items-center justify-center gap-1 rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold hover:bg-primary-container">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span>Lập chương trình khung
            </button>
            @endif
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(300px,.6fr)_minmax(0,1.4fr)] gap-6 items-start">
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
            <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-3">
                <h2 class="text-headline-sm font-bold">Danh sách chương trình khung</h2>
                <button type="button" data-action="reload" class="rounded-lg border border-outline-variant px-3 py-2 text-sm font-semibold hover:bg-surface-container-low">Làm mới</button>
            </div>
            <div data-region="list" class="p-4"></div>
        </section>

        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
            <div class="px-5 py-4 border-b border-outline-variant"><h2 class="text-headline-sm font-bold">Chi tiết</h2></div>
            <div data-region="detail" class="p-5"></div>
        </section>
    </div>
</div>
@endsection
