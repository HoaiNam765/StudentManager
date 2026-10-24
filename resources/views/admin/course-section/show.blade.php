{{--
    FE #18 — Chi tiết lớp học phần (mock admin/lop-hoc-phan-chi-tiet). Dữ liệu do resources/js/pages/admin-course-section-detail.js
    gọi API CLS (thông tin lớp, giảng viên), TTB (lịch học, buổi học), ENR (sinh viên đăng ký, xuất danh sách, hủy thay).
--}}
@extends('layouts.portal')

@section('title', 'Lớp '.$section->code.' - Quản lý Đào tạo')
@section('page-title', 'Chi tiết lớp học phần')

@section('page')
<div data-page="admin-course-section-detail" class="space-y-6">
    <a href="{{ $pageData['links']['list'] }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline print:hidden">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span> Quay lại danh sách lớp học phần
    </a>

    <div data-region="alert" hidden></div>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
        <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono font-bold text-primary">{{ $section->code }}</span>
                    <span data-field="term" class="rounded-full bg-surface-container px-2.5 py-0.5 text-xs font-semibold"></span>
                    <span data-field="status" class="rounded-full px-2.5 py-0.5 text-xs font-semibold"></span>
                </div>
                <h1 data-field="subject" class="font-headline-lg text-headline-lg font-bold text-on-surface">Đang tải…</h1>
                <p data-field="subject-meta" class="text-sm text-secondary"></p>
            </div>
            <div class="flex flex-wrap gap-2 print:hidden">
                @if ($pageData['can']['grades'])
                    <a href="{{ $pageData['links']['grades'] }}" class="h-9 px-3 rounded-lg border border-outline-variant text-sm font-semibold flex items-center gap-1.5 hover:bg-surface-container-low"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">grade</span> Bảng điểm</a>
                @endif
                <button type="button" onclick="window.print()" class="h-9 px-3 rounded-lg border border-outline-variant text-sm font-semibold flex items-center gap-1.5 hover:bg-surface-container-low"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">print</span> In danh sách</button>
                @if ($pageData['can']['export'])
                    <button type="button" data-action="export" class="h-9 px-3 rounded-lg bg-primary text-white text-sm font-semibold flex items-center gap-1.5 hover:bg-primary-container"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span> Xuất Excel</button>
                @endif
            </div>
        </div>
        <div>
            <div class="flex items-center justify-between text-sm"><span>Sĩ số lớp học phần: <strong data-field="size">—</strong></span><span data-field="remaining" class="text-xs text-secondary"></span></div>
            <div class="mt-1 h-2 rounded-full bg-surface-container overflow-hidden"><div data-field="bar" class="h-full bg-primary rounded-full" style="width:0%"></div></div>
        </div>
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 text-sm">
            <div class="rounded-xl border border-outline-variant/60 p-3"><p class="text-xs text-secondary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]" aria-hidden="true">calendar_month</span> Thời khóa biểu & Phòng</p><div data-region="schedule" class="mt-1 font-semibold"></div></div>
            <div class="rounded-xl border border-outline-variant/60 p-3"><p class="text-xs text-secondary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]" aria-hidden="true">auto_stories</span> Cấu trúc tín chỉ</p><p data-field="credits" class="mt-1 font-semibold">—</p><p data-field="periods" class="text-xs text-secondary"></p></div>
            <div class="rounded-xl border border-outline-variant/60 p-3"><p class="text-xs text-secondary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]" aria-hidden="true">badge</span> Giảng viên phụ trách</p><div data-region="instructors" class="mt-1 font-semibold"></div></div>
            <div class="rounded-xl border border-outline-variant/60 p-3"><p class="text-xs text-secondary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]" aria-hidden="true">info</span> Hình thức</p><p data-field="mode" class="mt-1 font-semibold">—</p><p data-field="note" class="text-xs text-secondary"></p></div>
        </div>
    </section>

    <nav class="flex flex-wrap gap-2 print:hidden" aria-label="Nhóm thông tin">
        <button type="button" data-tab="students" class="rounded-lg px-4 py-2 text-sm font-semibold">Sinh viên đã đăng ký <span data-field="count" class="ml-1 text-xs opacity-80"></span></button>
        <button type="button" data-tab="sessions" class="rounded-lg px-4 py-2 text-sm font-semibold">Buổi học</button>
    </nav>

    <section data-panel="students" class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <div class="px-5 py-3 border-b border-outline-variant/30 grid sm:grid-cols-3 gap-3 print:hidden">
            <label class="text-xs font-semibold text-on-surface-variant">Tìm sinh viên
                <input data-input="q" type="search" placeholder="MSSV hoặc họ tên" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Lớp hành chính
                <select data-input="class" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả lớp hành chính</option></select>
            </label>
            <label class="flex items-end gap-2 text-sm pb-2">
                <input data-input="all" type="checkbox" class="h-4 w-4 rounded border-outline-variant"> Hiện cả đăng ký đã hủy / rút
            </label>
        </div>
        <div data-region="students" class="overflow-x-auto"></div>
    </section>

    <section data-panel="sessions" hidden class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5">
        <div data-region="sessions"></div>
    </section>
</div>
@endsection
