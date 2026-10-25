{{--
    FE #43 — Điểm danh và chuyên cần (mock admin/diem-danh-chuyen-can). Dữ liệu do resources/js/pages/admin-attendance.js
    gọi API ATT: tổng hợp theo phạm vi, lọc, điều chỉnh bản ghi có lý do, miễn cấm thi, yêu cầu sửa, khóa sổ, xuất Excel.
--}}
@extends('layouts.portal')

@section('title', 'Điểm danh & Chuyên cần - Quản lý Đào tạo')
@section('page-title', 'Điểm danh & Chuyên cần')

@section('page')
<div data-page="admin-attendance" class="space-y-6">
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" aria-label="Thống kê chuyên cần">
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Lượt đăng ký theo dõi</p><p data-stat="total" class="text-2xl font-bold mt-1">—</p><p data-stat="term" class="text-xs text-secondary mt-1"></p></div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Tỷ lệ chuyên cần TB</p><p data-stat="avg" class="text-2xl font-bold text-primary mt-1">—</p><p class="text-xs text-secondary mt-1">Theo phạm vi và bộ lọc</p></div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-amber-200 shadow-xs"><p class="text-xs font-semibold uppercase text-amber-800">Chạm ngưỡng cảnh báo</p><p data-stat="warning" class="text-2xl font-bold text-amber-700 mt-1">—</p><p class="text-xs text-secondary mt-1">Chưa tới ngưỡng cấm thi</p></div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-red-200 shadow-xs"><p class="text-xs font-semibold uppercase text-red-800">Cấm thi chuyên cần</p><p data-stat="banned" class="text-2xl font-bold text-red-700 mt-1">—</p><p class="text-xs text-secondary mt-1">Không đủ điều kiện dự thi</p></div>
    </section>

    <div data-region="alert" hidden></div>

    <nav class="flex flex-wrap gap-2" aria-label="Nhóm chức năng">
        <button type="button" data-tab="list" class="rounded-lg px-4 py-2 text-sm font-semibold">Danh sách chuyên cần</button>
        <button type="button" data-tab="requests" class="rounded-lg px-4 py-2 text-sm font-semibold">Yêu cầu sửa điểm danh <span data-field="request-count" class="ml-1 text-xs"></span></button>
    </nav>

    <section data-panel="list" class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <form data-form="filters" onsubmit="event.preventDefault()" class="p-4 border-b border-outline-variant/30 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 items-end">
            <label class="text-xs font-semibold text-on-surface-variant">Học kỳ
                <select name="term_id" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"></select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Khoa / Đơn vị
                <select name="faculty_id" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả các khoa</option></select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant xl:col-span-2">Lớp học phần
                <select name="course_section_id" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả lớp học phần</option></select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Phân loại
                <select name="flag" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
                    <option value="">Tất cả</option><option value="warning">Cảnh báo</option><option value="banned">Cấm thi</option>
                </select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Tìm sinh viên
                <input name="q" type="search" placeholder="MSSV, họ tên" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
            </label>
        </form>
        <div class="px-5 py-3 border-b border-outline-variant/30 flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold">Danh sách sinh viên theo tỷ lệ vắng <span class="text-xs font-normal text-secondary">(quy chế đào tạo, BA mục 6.8)</span></h2>
            <div class="flex flex-wrap gap-2">
                <span data-region="lock"></span>
                @if ($pageData['can']['export'])
                    <button type="button" data-action="export" class="h-9 px-3 rounded-lg border border-outline-variant text-sm font-semibold hover:bg-surface-container-low flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span> Xuất Excel</button>
                @endif
            </div>
        </div>
        <div data-region="list" class="overflow-x-auto"></div>
        <div class="px-5 py-3 border-t border-outline-variant/30 flex justify-end"><nav data-region="pager" class="flex items-center gap-1" aria-label="Phân trang"></nav></div>
    </section>

    <section data-panel="requests" hidden class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-4">
        <div data-region="requests"></div>
    </section>
</div>
@endsection
