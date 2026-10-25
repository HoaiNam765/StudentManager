{{--
    FE #38 — Chuyên cần (mock student/chuyen-can). Dữ liệu do resources/js/pages/student-attendance.js gọi API chuyên cần
    của chính sinh viên (ATT, phạm vi OWN): tỷ lệ vắng từng học phần, cảnh báo / cấm thi, chi tiết từng buổi.
--}}
@extends('layouts.portal')

@section('title', 'Chuyên cần - Cổng Sinh viên')
@section('page-title', 'Chuyên cần')

@section('page')
<div data-page="student-attendance" class="space-y-6">
    <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-amber-700" aria-hidden="true">warning</span>
        <div class="text-sm text-amber-900">
            <p class="font-semibold">Quy định về ngưỡng cấm thi chuyên cần</p>
            <p data-field="rule">Đang tải quy định…</p>
        </div>
    </div>

    <div data-region="alert" hidden></div>

    <section class="grid grid-cols-2 xl:grid-cols-4 gap-4" aria-label="Tổng quan chuyên cần">
        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Tổng học phần</p><p data-stat="total" class="text-2xl font-bold mt-1">—</p><p class="text-xs text-secondary">Đang theo học</p></div>
        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Chuyên cần tốt</p><p data-stat="good" class="text-2xl font-bold text-green-700 mt-1">—</p><p data-stat="good-note" class="text-xs text-secondary"></p></div>
        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Nguy cơ cấm thi</p><p data-stat="warning" class="text-2xl font-bold text-amber-700 mt-1">—</p><p class="text-xs text-secondary">Đã chạm ngưỡng cảnh báo</p></div>
        <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/50 shadow-xs"><p class="text-xs font-semibold uppercase text-secondary">Bị cấm thi</p><p data-stat="banned" class="text-2xl font-bold text-red-700 mt-1">—</p><p class="text-xs text-secondary">Không đủ điều kiện dự thi</p></div>
    </section>

    <div data-region="sections" class="space-y-4"></div>
</div>
@endsection
