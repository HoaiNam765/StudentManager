{{--
    FE #25 — Lớp học phần đang dạy và danh sách sinh viên (mock teacher/lop-hoc-phan). Dữ liệu do
    resources/js/pages/teacher-sections.js gọi API CLS của giảng viên (chỉ lớp được phân công, phạm vi SECTION).
--}}
@extends('layouts.portal')

@section('title', 'Lớp học phần - Cổng Giảng viên')
@section('page-title', 'Lớp học phần')

@section('page')
<div data-page="teacher-sections" class="space-y-6">
    <div data-region="alert" hidden></div>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 print:hidden" aria-label="Thống kê lớp phụ trách">
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs">
            <div class="flex items-start justify-between"><p class="text-xs font-semibold uppercase text-secondary">Số lớp phụ trách</p><span class="material-symbols-outlined text-primary" aria-hidden="true">school</span></div>
            <p class="text-2xl font-bold mt-1"><span data-stat="sections">—</span> <span class="text-sm font-medium text-secondary">lớp học phần</span></p>
            <p data-stat="term" class="text-xs text-secondary mt-1"></p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs">
            <div class="flex items-start justify-between"><p class="text-xs font-semibold uppercase text-secondary">Tổng sinh viên</p><span class="material-symbols-outlined text-green-700" aria-hidden="true">groups</span></div>
            <p class="text-2xl font-bold mt-1"><span data-stat="students">—</span> <span class="text-sm font-medium text-secondary">sinh viên</span></p>
            <p data-stat="average" class="text-xs text-secondary mt-1"></p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs">
            <div class="flex items-start justify-between"><p class="text-xs font-semibold uppercase text-secondary">Tổng tín chỉ</p><span class="material-symbols-outlined text-amber-700" aria-hidden="true">hourglass_bottom</span></div>
            <p class="text-2xl font-bold mt-1"><span data-stat="credits">—</span> <span class="text-sm font-medium text-secondary">tín chỉ</span></p>
            <p data-stat="periods" class="text-xs text-secondary mt-1"></p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs">
            <div class="flex items-start justify-between"><p class="text-xs font-semibold uppercase text-secondary">Bảng điểm</p><span class="material-symbols-outlined text-tertiary" aria-hidden="true">fact_check</span></div>
            <p class="text-2xl font-bold mt-1"><span data-stat="pending">—</span> <span class="text-sm font-medium text-secondary">lớp cần nhập / nộp</span></p>
            <p data-stat="submitted" class="text-xs text-secondary mt-1"></p>
        </div>
    </section>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4 print:hidden">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-3">
            <div>
                <h1 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Lớp học phần đang phụ trách</h1>
                <p class="text-xs text-secondary">Chọn một lớp học phần để xem danh sách sinh viên.</p>
            </div>
            <label class="text-xs font-semibold text-on-surface-variant">Học kỳ
                <select data-input="term" class="mt-1 block min-w-[220px] h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"></select>
            </label>
        </div>
        <div data-region="sections" class="grid md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
    </section>

    <section data-region="roster-box" hidden class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-outline-variant/30 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div>
                <h2 data-field="roster-title" class="font-headline-sm text-headline-sm font-semibold"></h2>
                <p data-field="roster-meta" class="text-xs text-secondary"></p>
            </div>
            <div class="flex flex-wrap gap-2 print:hidden">
                <button type="button" onclick="window.print()" class="h-9 px-3 rounded-lg border border-outline-variant text-sm font-semibold flex items-center gap-1.5 hover:bg-surface-container-low"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">print</span> In danh sách lớp (A4)</button>
                <button type="button" data-action="csv" class="h-9 px-3 rounded-lg border border-outline-variant text-sm font-semibold flex items-center gap-1.5 hover:bg-surface-container-low"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span> Xuất CSV</button>
            </div>
        </div>
        <div class="px-5 py-3 border-b border-outline-variant/30 grid sm:grid-cols-3 gap-3 print:hidden">
            <label class="text-xs font-semibold text-on-surface-variant sm:col-span-2">Tìm sinh viên
                <input data-input="q" type="search" placeholder="MSSV hoặc họ tên" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Lớp hành chính
                <select data-input="class" class="mt-1 w-full h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"><option value="">Tất cả</option></select>
            </label>
        </div>
        <div data-region="roster" class="overflow-x-auto"></div>
    </section>
</div>
@endsection
