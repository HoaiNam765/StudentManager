{{--
    FE #42 — Điểm danh (mock teacher/diem-danh). Dữ liệu do resources/js/pages/teacher-attendance.js gọi API ATT:
    buổi học của lớp, phiếu điểm danh, lưu / sửa, đề nghị sửa quá hạn, sổ đầu bài, tổng hợp chuyên cần lớp.
--}}
@extends('layouts.portal')

@section('title', 'Điểm danh - Cổng Giảng viên')
@section('page-title', 'Điểm danh')

@section('page')
<div data-page="teacher-attendance" class="space-y-5">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-4 sm:p-5 grid sm:grid-cols-2 gap-3">
        <label class="text-xs font-semibold text-on-surface-variant">Lớp học phần
            <select data-input="section" class="mt-1 w-full h-11 rounded-lg border border-outline-variant bg-white px-3 text-sm"></select>
        </label>
        <label class="text-xs font-semibold text-on-surface-variant">Buổi điểm danh
            <select data-input="session" class="mt-1 w-full h-11 rounded-lg border border-outline-variant bg-white px-3 text-sm"></select>
        </label>
    </section>

    <div data-region="alert" hidden></div>

    <nav class="flex flex-wrap gap-2" aria-label="Chức năng">
        <button type="button" data-tab="sheet" class="rounded-lg px-4 py-2 text-sm font-semibold">Điểm danh buổi</button>
        <button type="button" data-tab="log" class="rounded-lg px-4 py-2 text-sm font-semibold">Sổ đầu bài</button>
        <button type="button" data-tab="summary" class="rounded-lg px-4 py-2 text-sm font-semibold">Tổng hợp chuyên cần lớp</button>
    </nav>

    <section data-panel="sheet" class="space-y-3">
        <div class="sticky top-14 z-20 bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div data-region="counts" class="flex flex-wrap gap-2 text-xs font-semibold"></div>
            <div class="flex gap-2">
                <button type="button" data-action="all-present" class="h-10 px-3 rounded-lg border border-emerald-300 text-emerald-800 hover:bg-emerald-50 text-sm font-semibold flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">done_all</span> Tất cả có mặt</button>
                <button type="button" data-action="save" class="h-10 px-4 rounded-lg bg-primary text-white hover:bg-primary-container text-sm font-semibold flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">save</span> Lưu điểm danh</button>
            </div>
        </div>
        <p data-field="window" class="text-xs text-secondary"></p>
        <div data-region="rows" class="space-y-2"></div>
    </section>

    <section data-panel="log" hidden class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5">
        <form data-form="log" onsubmit="event.preventDefault()" class="space-y-3 max-w-2xl">
            <p data-field="log-session" class="text-sm font-semibold"></p>
            <label class="block text-sm font-medium">Số tiết thực dạy
                <input name="periods" type="number" min="1" max="20" required class="mt-1 w-32 h-10 rounded-lg border border-outline-variant px-3 text-sm">
            </label>
            <label class="block text-sm font-medium">Nội dung buổi dạy
                <textarea name="content" rows="4" maxlength="2000" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm" placeholder="Chương, bài, hoạt động…"></textarea>
            </label>
            <div data-region="log-alert" hidden></div>
            <button type="submit" class="h-10 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Xác nhận đã dạy</button>
        </form>
    </section>

    <section data-panel="summary" hidden class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <div class="px-5 py-3 border-b border-outline-variant/30 flex flex-wrap items-center justify-between gap-2">
            <p data-field="summary-meta" class="text-sm text-secondary"></p>
            @if ($pageData['can']['grades'])
                <button type="button" data-action="push-grades" class="h-9 px-3 rounded-lg border border-outline-variant text-sm font-semibold hover:bg-surface-container-low">Chuyển điểm chuyên cần vào sổ điểm</button>
            @endif
        </div>
        <div data-region="summary" class="overflow-x-auto"></div>
    </section>
</div>
@endsection
