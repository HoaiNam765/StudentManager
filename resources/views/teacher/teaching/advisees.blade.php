{{--
    Lớp cố vấn học tập (mock teacher/lop-co-van-hoc-tap). Dữ liệu do resources/js/pages/teacher-advisees.js gọi API CLS
    của giảng viên (chỉ lớp đang cố vấn, phạm vi ADVISEE): sinh viên, GPA, tín chỉ tích lũy, cảnh báo học vụ.
--}}
@extends('layouts.portal')

@section('title', 'Lớp cố vấn học tập - Cổng Giảng viên')
@section('page-title', 'Lớp cố vấn học tập')

@section('page')
<div data-page="teacher-advisees" class="space-y-6">
    <div data-region="alert" hidden></div>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase text-secondary">Lớp chủ nhiệm</p>
            <h1 data-field="title" class="font-headline-lg text-headline-lg font-bold text-on-surface">Đang tải…</h1>
            <p data-field="meta" class="text-sm text-secondary mt-1"></p>
        </div>
        <div class="flex flex-wrap items-end gap-3">
            <label data-region="picker" hidden class="text-xs font-semibold text-on-surface-variant">Chọn lớp
                <select data-input="class" class="mt-1 block min-w-[200px] h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm"></select>
            </label>
            <div class="rounded-xl border border-outline-variant/50 px-4 py-2 text-center">
                <p class="text-[11px] font-semibold uppercase text-secondary">GPA tích lũy lớp</p>
                <p class="text-xl font-bold text-primary"><span data-field="avg">—</span><span class="text-xs text-secondary font-medium"> / 4.0</span></p>
            </div>
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-center">
                <p class="text-[11px] font-semibold uppercase text-red-800">Cảnh báo học vụ</p>
                <p class="text-xl font-bold text-red-700"><span data-field="warnings">—</span> <span class="text-xs font-medium">SV</span></p>
            </div>
        </div>
    </section>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <div class="px-5 py-3 border-b border-outline-variant/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="font-headline-sm text-headline-sm font-semibold">Danh sách sinh viên lớp cố vấn</h2>
            <input data-input="q" type="search" placeholder="Tìm MSSV, họ tên" class="h-9 w-full sm:w-64 rounded-lg border border-outline-variant bg-white px-3 text-sm">
        </div>
        <div data-region="students" class="overflow-x-auto"></div>
        <p class="px-5 py-3 text-xs text-secondary border-t border-outline-variant/30">Cảnh báo học vụ: GPA tích lũy dưới 2.0 (theo dữ liệu GPA đã tính sau khi điểm được công bố). Ghi chú cố vấn chưa có trên hệ thống.</p>
    </section>
</div>
@endsection
