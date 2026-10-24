{{--
    FE #16 — Nhập hồ sơ sinh viên từ Excel (mock admin/sinh-vien-import). Dữ liệu do resources/js/pages/admin-student-import.js
    gọi trung tâm import (importer `students`): tải lên → kiểm tra → xem trước lỗi theo dòng → lưu → hoàn tác.
--}}
@extends('layouts.portal')

@section('title', 'Nhập sinh viên từ Excel - Quản lý Đào tạo')
@section('page-title', 'Nhập sinh viên từ file')

@section('page')
<div data-page="admin-student-import" class="space-y-6">
    <a href="{{ $pageData['links']['list'] }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:underline">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span> Quay lại danh sách sinh viên
    </a>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Nhập dữ liệu Sinh viên từ file Excel</h1>
            <p class="text-sm text-secondary mt-1">Tải lên danh sách hồ sơ thí sinh trúng tuyển (trạng thái "Chờ nhập học"). Mỗi dòng được kiểm tra riêng, báo lỗi theo cột kèm cách sửa.</p>
        </div>
        <a data-field="template" href="#" hidden class="h-10 px-4 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low text-sm font-semibold flex items-center gap-1.5 shrink-0">
            <span class="material-symbols-outlined text-[18px] text-primary" aria-hidden="true">download_for_offline</span> Tải file mẫu chuẩn (.xlsx)
        </a>
    </section>

    <div data-region="alert" hidden></div>

    @if (! $pageData['available'])
        <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">Hệ thống chưa đăng ký trình nhập hồ sơ sinh viên.</div>
    @elseif (! $pageData['can']['create'])
        <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">Tài khoản của bạn chỉ được xem các lô đã nhập. Tải lên và lưu dữ liệu cần quyền SYS.create (mặc định: Quản trị hệ thống).</div>
    @endif

    {{-- Các bước --}}
    <ol data-region="steps" class="grid sm:grid-cols-3 gap-3 text-sm">
        <li data-step="1" class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4"><p class="font-bold">1. Tải lên tệp dữ liệu</p><p data-step-note="1" class="text-xs text-secondary mt-1">Chọn tệp .xlsx, .xls hoặc .csv</p></li>
        <li data-step="2" class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4"><p class="font-bold">2. Xem trước & Xác thực</p><p data-step-note="2" class="text-xs text-secondary mt-1">Kiểm tra hợp lệ từng dòng</p></li>
        <li data-step="3" class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4"><p class="font-bold">3. Hoàn tất & Kết quả</p><p data-step-note="3" class="text-xs text-secondary mt-1">Ghi cơ sở dữ liệu học vụ</p></li>
    </ol>

    @if ($pageData['available'] && $pageData['can']['create'])
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5">
            <form data-form="upload" onsubmit="event.preventDefault()" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <label class="block flex-1 text-sm font-medium text-on-surface">Tệp dữ liệu sinh viên
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="mt-1 block w-full text-sm text-on-surface border border-outline-variant rounded-lg cursor-pointer bg-surface-container-low file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white">
                </label>
                <button type="submit" class="h-10 px-4 rounded-lg bg-primary text-white hover:bg-primary-container text-sm font-semibold flex items-center gap-1.5 justify-center">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">upload_file</span> Tải lên & kiểm tra
                </button>
            </form>
        </section>
    @endif

    {{-- Kết quả kiểm tra lô đang xử lý --}}
    <section data-region="batch" hidden class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs p-5 space-y-4"></section>

    {{-- Lịch sử lô import sinh viên --}}
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <div class="px-5 py-3 border-b border-outline-variant/30 flex items-center justify-between">
            <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Các lần nhập trước</h2>
            <button type="button" data-action="reload" class="p-1.5 rounded-lg border border-outline-variant/60 text-secondary hover:bg-surface-container-low" aria-label="Làm mới"><span class="material-symbols-outlined text-[18px]" aria-hidden="true">refresh</span></button>
        </div>
        <div data-region="history" class="p-4"></div>
    </section>
</div>
@endsection
