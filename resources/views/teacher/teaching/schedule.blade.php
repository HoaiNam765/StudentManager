{{--
    FE #24 — Lịch giảng dạy (mock teacher/lich-giang-day). Dữ liệu do resources/js/pages/teacher-schedule.js gọi API
    thời khóa biểu của giảng viên (TTB), lớp phụ trách (CLS) và dữ liệu tổng quan (RPT: việc cần xử lý, hạn chót, thông báo).
--}}
@extends('layouts.portal')

@section('title', 'Lịch giảng dạy - Cổng Giảng viên')
@section('page-title', 'Lịch giảng dạy')

@section('page')
<div data-page="teacher-schedule" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Xin chào, {{ $pageData['user'] }} 👋</h1>
            <p class="text-sm text-secondary mt-1 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">calendar_today</span>
                <span data-field="today"></span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ $pageData['links']['sections'] }}" class="h-10 px-3.5 rounded-lg border border-outline-variant bg-white hover:bg-surface-container-low text-sm font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px] text-primary" aria-hidden="true">groups</span> Danh sách lớp
            </a>
            <a href="{{ $pageData['links']['gradebook'] }}" class="h-10 px-4 rounded-lg bg-primary text-white hover:bg-primary-container text-sm font-semibold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">edit_note</span> Nhập điểm học phần
            </a>
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <div class="grid xl:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)] gap-6 items-start">
        <div class="space-y-6">
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">calendar_today</span> Lịch giảng dạy hôm nay</h2>
                </div>
                <div data-region="today"></div>
            </section>

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">date_range</span> Thời khóa biểu tuần</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" data-week="-1" class="h-8 w-8 rounded-lg border border-outline-variant hover:bg-surface-container-low" aria-label="Tuần trước"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">chevron_left</span></button>
                        <span data-field="range" class="text-sm font-semibold min-w-[170px] text-center"></span>
                        <button type="button" data-week="1" class="h-8 w-8 rounded-lg border border-outline-variant hover:bg-surface-container-low" aria-label="Tuần sau"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">chevron_right</span></button>
                        <button type="button" data-week="0" class="h-8 px-3 rounded-lg border border-outline-variant text-xs font-semibold hover:bg-surface-container-low">Tuần này</button>
                    </div>
                </div>
                <div data-region="week" class="overflow-x-auto"></div>
            </section>

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">school</span> Học phần & Lớp phụ trách <span data-field="term" class="text-sm font-normal text-secondary"></span></h2>
                    <span data-field="section-count" class="text-xs font-semibold text-primary bg-primary/10 rounded-full px-2.5 py-0.5"></span>
                </div>
                <div data-region="sections" class="grid md:grid-cols-2 gap-3"></div>
            </section>
        </div>

        <div class="space-y-6">
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold flex items-center gap-2"><span class="material-symbols-outlined text-red-700" aria-hidden="true">priority_high</span> Công việc học vụ cần xử lý</h2>
                    <span data-field="task-count" class="text-xs font-semibold text-red-700 bg-red-50 rounded-full px-2.5 py-0.5"></span>
                </div>
                <div data-region="tasks"></div>
            </section>

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-headline-sm text-headline-sm font-semibold flex items-center gap-2"><span class="material-symbols-outlined text-primary" aria-hidden="true">campaign</span> Thông báo học vụ mới nhất</h2>
                    <a href="{{ route('notifications.page') }}" class="text-xs font-semibold text-primary hover:underline">Xem tất cả</a>
                </div>
                <div data-region="notifications"></div>
            </section>
        </div>
    </div>
</div>
@endsection
