{{--
    FE #10 (phần học phần) — Quản lý học phần (mock admin/hoc-phan). Dữ liệu do resources/js/pages/admin-subjects.js gọi
    API SUB: danh sách, thêm / sửa / ngừng / xóa, quan hệ tiên quyết, cơ cấu điểm thành phần, xuất Excel.
--}}
@extends('layouts.portal')

@section('title', 'Quản lý Học phần - Quản lý Đào tạo')
@section('page-title', 'Học phần')

@section('page')
<div data-page="admin-subjects" class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-outline-variant/20">
        <div>
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">Quản lý Học phần</h1>
            <p class="font-body-md text-body-md text-secondary mt-0.5">Quản lý danh mục học phần, quy chuẩn tín chỉ lý thuyết / thực hành và học phần tiên quyết.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            @if ($pageData['can']['export'])
                <button type="button" data-action="export" class="h-9 px-3.5 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low font-label-md text-label-md flex items-center gap-1.5 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px] text-secondary" aria-hidden="true">download</span> Xuất Excel
                </button>
            @endif
            @if ($pageData['can']['create'])
                <button type="button" data-action="create" class="h-9 px-4 rounded-lg bg-primary hover:bg-[#1647a8] text-white font-label-md text-label-md flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span> Thêm học phần mới
                </button>
            @endif
        </div>
    </div>

    <div data-region="alert" hidden></div>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" aria-label="Thống kê học phần">
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Tổng số học phần</p><p data-stat="total" class="text-2xl font-bold mt-1">—</p><p class="text-xs text-secondary mt-1">Trong phạm vi của bạn</p></div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Đang giảng dạy</p><p data-stat="active" class="text-2xl font-bold text-green-700 mt-1">—</p><p data-stat="active-pct" class="text-xs text-secondary mt-1"></p></div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Học phần đại cương</p><p data-stat="general" class="text-2xl font-bold text-primary mt-1">—</p><p class="text-xs text-secondary mt-1">Khối kiến thức chung</p></div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Cơ sở & chuyên ngành</p><p data-stat="major" class="text-2xl font-bold text-tertiary mt-1">—</p><p class="text-xs text-secondary mt-1">Cơ sở ngành, chuyên ngành, thực tập, đồ án</p></div>
    </section>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <form data-form="filters" onsubmit="event.preventDefault()" class="p-4 border-b border-outline-variant/30 grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
            <label class="col-span-2 md:col-span-1 text-xs font-semibold text-on-surface-variant">Tìm kiếm
                <input name="q" type="search" placeholder="Mã, tên học phần" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Khoa / Viện
                <select name="faculty_id" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả Khoa / Viện</option></select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Khối kiến thức
                <select name="knowledge_block" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
                    <option value="">Tất cả</option><option value="general">Giáo dục đại cương</option><option value="foundation">Cơ sở ngành</option><option value="specialized">Chuyên ngành</option><option value="internship">Thực tập</option><option value="thesis">Đồ án / khóa luận</option>
                </select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Loại
                <select name="requirement" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả</option><option value="compulsory">Bắt buộc</option><option value="elective">Tự chọn</option></select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Trạng thái
                <select name="status" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả</option><option value="active">Đang hoạt động</option><option value="inactive">Tạm ngừng</option></select>
            </label>
        </form>
        <div data-region="table" class="overflow-x-auto"></div>
        <div class="px-5 py-3 border-t border-outline-variant/30 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
            <span data-field="range" class="text-secondary"></span>
            <nav data-region="pager" class="flex items-center gap-1" aria-label="Phân trang"></nav>
        </div>
    </section>

    {{-- Chi tiết học phần: quan hệ, cơ cấu điểm --}}
    <section data-region="detail" hidden class="bg-surface-container-lowest rounded-xl border border-primary/30 shadow-xs p-5 space-y-5"></section>
</div>
@endsection
