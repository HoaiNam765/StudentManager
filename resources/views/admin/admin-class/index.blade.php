{{--
    FE #15 — Khóa tuyển sinh & lớp hành chính (mock admin/khoa-va-lop-hanh-chinh). Dữ liệu do resources/js/pages/admin-classes.js
    gọi API CLS: khóa, lớp, sĩ số, cố vấn học tập (phân công / kết thúc), thành viên lớp (thêm / gỡ có xem trước, xuất).
--}}
@extends('layouts.portal')

@section('title', 'Khóa & Lớp hành chính - Quản lý Đào tạo')
@section('page-title', 'Khóa & Lớp hành chính')

@section('page')
<div data-page="admin-classes" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-secondary">Đào tạo • Lớp hành chính</p>
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Quản lý Khóa tuyển sinh & Lớp hành chính</h1>
            <p class="text-sm text-secondary mt-1">Quản lý các khóa tuyển sinh, cơ cấu lớp sinh viên và phân công Cố vấn học tập (CVHT).</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($pageData['can']['createCohort'])
                <button type="button" data-action="add-cohort" class="h-10 px-3.5 rounded-lg border border-outline-variant bg-white hover:bg-surface-container-low text-sm font-semibold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px] text-primary" aria-hidden="true">event_note</span> Thêm khóa
                </button>
            @endif
            @if ($pageData['can']['createClass'])
                <button type="button" data-action="add-class" class="h-10 px-4 rounded-lg bg-primary text-white hover:bg-primary-container text-sm font-semibold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add_circle</span> Thêm lớp hành chính
                </button>
            @endif
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" aria-label="Thống kê lớp">
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs">
            <div class="flex items-start justify-between"><p class="text-xs font-semibold uppercase text-secondary">Tổng số lớp</p><span class="material-symbols-outlined text-primary" aria-hidden="true">meeting_room</span></div>
            <p data-stat="classes" class="text-2xl font-bold text-on-surface mt-1">—</p>
            <p data-stat="active" class="text-xs text-secondary mt-1"></p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs">
            <div class="flex items-start justify-between"><p class="text-xs font-semibold uppercase text-secondary">Tổng sinh viên</p><span class="material-symbols-outlined text-green-700" aria-hidden="true">groups</span></div>
            <p data-stat="students" class="text-2xl font-bold text-on-surface mt-1">—</p>
            <p class="text-xs text-secondary mt-1">Số sinh viên có phân lớp hiệu lực</p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs">
            <div class="flex items-start justify-between"><p class="text-xs font-semibold uppercase text-secondary">Sĩ số trung bình</p><span class="material-symbols-outlined text-amber-700" aria-hidden="true">analytics</span></div>
            <p data-stat="average" class="text-2xl font-bold text-on-surface mt-1">—</p>
            <p class="text-xs text-secondary mt-1">SV / lớp đang hoạt động</p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs">
            <div class="flex items-start justify-between"><p class="text-xs font-semibold uppercase text-secondary">Tỷ lệ có CVHT</p><span class="material-symbols-outlined text-tertiary" aria-hidden="true">supervisor_account</span></div>
            <p data-stat="coverage" class="text-2xl font-bold text-on-surface mt-1">—</p>
            <p data-stat="missing" class="text-xs text-secondary mt-1"></p>
        </div>
    </section>

    <nav class="flex flex-wrap gap-2" aria-label="Nhóm dữ liệu">
        <button type="button" data-tab="classes" class="rounded-lg px-4 py-2 text-sm font-semibold">Lớp hành chính <span data-count="classes" class="ml-1 text-xs opacity-80"></span></button>
        <button type="button" data-tab="cohorts" class="rounded-lg px-4 py-2 text-sm font-semibold">Khóa tuyển sinh <span data-count="cohorts" class="ml-1 text-xs opacity-80"></span></button>
    </nav>

    <section data-panel="classes" class="space-y-6">
        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
            <form data-form="filters" onsubmit="event.preventDefault()" class="p-4 border-b border-outline-variant/30 grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                <label class="col-span-2 md:col-span-1 text-xs font-semibold text-on-surface-variant">Tìm kiếm
                    <input name="q" type="search" placeholder="Mã / tên lớp" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
                </label>
                <label class="text-xs font-semibold text-on-surface-variant">Khoa
                    <select name="faculty_id" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả khoa</option></select>
                </label>
                <label class="text-xs font-semibold text-on-surface-variant">Khóa
                    <select name="cohort_id" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả khóa</option></select>
                </label>
                <label class="text-xs font-semibold text-on-surface-variant">Ngành
                    <select name="major_id" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả ngành</option></select>
                </label>
                <label class="text-xs font-semibold text-on-surface-variant">Trạng thái
                    <select name="status" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
                        <option value="">Tất cả trạng thái</option><option value="active">Đang hoạt động</option><option value="inactive">Đã đóng</option>
                    </select>
                </label>
            </form>
            <div data-region="classes" class="overflow-x-auto"></div>
            <div class="px-5 py-3 border-t border-outline-variant/30 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs">
                <span data-field="range" class="text-secondary"></span>
                <nav data-region="pager" class="flex items-center gap-1" aria-label="Phân trang"></nav>
            </div>
        </div>

        {{-- Chi tiết lớp đang chọn: thành viên, cố vấn học tập --}}
        <section data-region="detail" hidden class="bg-surface-container-lowest rounded-xl border border-primary/30 shadow-xs p-5 space-y-5"></section>
    </section>

    <section data-panel="cohorts" hidden class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <div data-region="cohorts" class="overflow-x-auto"></div>
    </section>
</div>
@endsection
