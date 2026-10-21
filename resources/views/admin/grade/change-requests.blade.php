{{--
    Duyệt yêu cầu điều chỉnh điểm sau công bố (FR-GRD-006): lãnh đạo khoa duyệt cấp khoa, Phòng Đào tạo / Khảo thí xác
    nhận rồi hệ thống áp dụng (tính lại kết quả, lưu phiên bản, tính lại GPA). Dữ liệu: resources/js/pages/admin-grade-changes.js
    gọi /admin/grade-change-requests (danh sách, duyệt, từ chối có lý do, tải minh chứng).
--}}
@extends('layouts.portal')

@section('title', 'Duyệt điều chỉnh điểm - Quản lý Đào tạo')
@section('page-title', 'Duyệt điều chỉnh điểm')

@section('page')
<div data-page="admin-grade-changes" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1 min-w-0">
            <h1 class="text-headline-lg font-bold text-on-surface">Yêu cầu điều chỉnh điểm</h1>
            <p class="text-body-md text-on-surface-variant">Giảng viên gửi sau khi điểm đã công bố, kèm lý do và minh chứng. Duyệt xong hệ thống tự tính lại điểm học phần và GPA, lưu lịch sử trước – sau.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <label class="text-label-md text-on-surface-variant flex items-center gap-2">
                Trạng thái
                <select data-input="status" class="px-3 py-2 border border-outline-variant rounded-lg text-body-md text-on-surface bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <option value="pending">Đang chờ duyệt</option>
                    <option value="approved">Đã áp dụng</option>
                    <option value="rejected">Bị từ chối</option>
                    <option value="all">Tất cả</option>
                </select>
            </label>
            <button type="button" data-action="reload" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold text-on-surface hover:bg-surface-container-low inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">refresh</span>
                Làm mới
            </button>
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
        <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-2">
            <h2 class="text-headline-sm font-bold text-on-surface">Danh sách yêu cầu</h2>
            <span class="text-label-sm text-secondary" data-field="count"></span>
        </div>
        <div data-region="list" class="p-4"></div>
    </section>
</div>
@endsection
