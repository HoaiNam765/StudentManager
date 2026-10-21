{{--
    Dashboard quản trị theo vai trò (#118; FR-RPT-001, 002, 008; BR-RPT-03, 07; FE #34). Dữ liệu:
    resources/js/pages/admin-dashboard.js gọi GET /admin/dashboard/data, tự làm mới theo refresh_seconds.
    Mỗi widget có nút xem định nghĩa chỉ số.
--}}
@extends('layouts.portal')

@section('title', 'Tổng quan - Quản lý Đào tạo')
@section('page-title', 'Tổng quan')

@section('page')
<div data-page="admin-dashboard" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1 min-w-0">
            <h1 class="text-headline-lg font-bold text-on-surface">Tổng quan đào tạo</h1>
            <p class="text-body-md text-on-surface-variant" data-field="subtitle">Đang tải…</p>
        </div>
        <button type="button" data-action="reload" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold text-on-surface hover:bg-surface-container-low inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">refresh</span>
            Làm mới
        </button>
    </section>

    <div data-region="alert" hidden></div>
    <div data-region="widgets" class="grid grid-cols-1 lg:grid-cols-2 gap-6"></div>
</div>
@endsection
