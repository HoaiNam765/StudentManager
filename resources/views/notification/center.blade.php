{{--
    Trung tâm thông báo (FR-NOT-001; FE #29), dùng chung cho mọi cổng. Dữ liệu: resources/js/pages/notification-center.js
    gọi GET /notifications (lọc chưa đọc / nhóm / từ khóa / đã hết hạn), POST /notifications/{id}/read, /read-all.
--}}
@extends('layouts.portal')

@section('title', 'Thông báo')
@section('page-title', 'Thông báo')

@section('page')
<div data-page="notification-center" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1 min-w-0">
            <h1 class="text-headline-lg font-bold text-on-surface">Thông báo</h1>
            <p class="text-body-md text-on-surface-variant" data-field="subtitle">Đang tải…</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-action="read-all" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold text-on-surface hover:bg-surface-container-low inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">done_all</span>
                Đánh dấu đã đọc tất cả
            </button>
        </div>
    </section>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-4 flex flex-wrap items-end gap-3">
        <label class="text-label-md text-on-surface-variant flex flex-col gap-1">
            Hiển thị
            <select data-input="status" class="px-3 py-2 border border-outline-variant rounded-lg text-body-md text-on-surface bg-white">
                <option value="all">Tất cả</option>
                <option value="unread">Chưa đọc</option>
                <option value="archived">Đã hết hạn (lưu trữ)</option>
            </select>
        </label>
        <label class="text-label-md text-on-surface-variant flex flex-col gap-1">
            Nhóm
            <select data-input="category" class="px-3 py-2 border border-outline-variant rounded-lg text-body-md text-on-surface bg-white">
                <option value="">Tất cả nhóm</option>
            </select>
        </label>
        <label class="text-label-md text-on-surface-variant flex flex-col gap-1 flex-1 min-w-[200px]">
            Tìm kiếm
            <input type="search" data-input="q" maxlength="100" placeholder="Tiêu đề hoặc nội dung" class="px-3 py-2 border border-outline-variant rounded-lg text-body-md text-on-surface bg-white">
        </label>
    </section>

    <div data-region="alert" hidden></div>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
        <div data-region="list" class="divide-y divide-outline-variant"></div>
        <div data-region="pager" class="px-4 py-3 border-t border-outline-variant flex items-center justify-between gap-2 text-label-md text-on-surface-variant" hidden></div>
    </section>
</div>
@endsection
