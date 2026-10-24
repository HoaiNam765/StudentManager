{{--
    FE #16 — Quản lý sinh viên (mock admin/sinh-vien). Dữ liệu do resources/js/pages/admin-students.js gọi API STU
    (danh sách, thẻ thống kê theo trạng thái, xuất Excel, yêu cầu sửa hồ sơ chờ duyệt).
--}}
@extends('layouts.portal')

@section('title', 'Quản lý Sinh viên - Quản lý Đào tạo')
@section('page-title', 'Sinh viên')

@section('page')
<div data-page="admin-students" class="space-y-6">
    {{-- Tiêu đề và thao tác chính --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-outline-variant/20">
        <div>
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">Quản lý Sinh viên</h1>
            <p class="font-body-md text-body-md text-secondary mt-0.5">Quản lý hồ sơ học vụ, trạng thái sinh viên và theo dõi tiến độ đào tạo.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            @if ($pageData['can']['import'])
                <a href="{{ $pageData['links']['import'] }}" class="h-9 px-3.5 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low hover:border-primary/40 font-label-md text-label-md flex items-center gap-1.5 transition-colors shadow-2xs">
                    <span class="material-symbols-outlined text-[18px] text-secondary" aria-hidden="true">upload_file</span>
                    <span>Nhập từ file (Excel/CSV)</span>
                </a>
            @endif
            @if ($pageData['can']['export'])
                <button type="button" data-action="export" class="h-9 px-3.5 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low hover:border-primary/40 font-label-md text-label-md flex items-center gap-1.5 transition-colors shadow-2xs">
                    <span class="material-symbols-outlined text-[18px] text-secondary" aria-hidden="true">download</span>
                    <span>Xuất Excel</span>
                </button>
            @endif
            @if ($pageData['can']['create'])
                <a href="{{ $pageData['links']['create'] }}" class="h-9 px-4 rounded-lg bg-primary hover:bg-[#1647a8] active:scale-[0.98] text-white font-label-md text-label-md flex items-center gap-1.5 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span>
                    <span>Thêm sinh viên</span>
                </a>
            @endif
        </div>
    </div>

    <div data-region="alert" hidden></div>

    {{-- 4 thẻ thống kê: đếm theo trạng thái trong phạm vi xem --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" aria-label="Thống kê sinh viên">
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Tổng số sinh viên</p>
                    <h3 data-stat="total" class="font-headline-xl text-headline-xl font-bold text-on-surface mt-1">—</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">groups</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 pt-3 border-t border-outline-variant/20">
                <span class="font-body-sm text-body-sm text-secondary">Trong phạm vi dữ liệu của bạn</span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Đang học</p>
                    <h3 data-stat="studying" class="font-headline-xl text-headline-xl font-bold text-green-700 mt-1">—</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">check_circle</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 pt-3 border-t border-outline-variant/20">
                <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Chiếm <strong data-stat="studying-pct" class="text-green-700 font-bold">—</strong> tổng số hồ sơ</span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Bảo lưu / Chờ nhập học</p>
                    <h3 data-stat="paused" class="font-headline-xl text-headline-xl font-bold text-amber-700 mt-1">—</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-700">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">pause_circle</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 pt-3 border-t border-outline-variant/20">
                <span class="font-label-sm text-label-sm text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded font-semibold">Theo dõi học vụ</span>
                <span data-stat="paused-detail" class="font-body-sm text-body-sm text-secondary"></span>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Đã tốt nghiệp / Khác</p>
                    <h3 data-stat="other" class="font-headline-xl text-headline-xl font-bold text-tertiary mt-1">—</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center text-tertiary">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">school</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 pt-3 border-t border-outline-variant/20">
                <span data-stat="other-detail" class="font-body-sm text-body-sm text-secondary">Tốt nghiệp, thôi học, chuyển trường</span>
            </div>
        </div>
    </section>

    {{-- Bộ lọc và tìm kiếm --}}
    <section class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs space-y-4">
        <form data-form="filters" onsubmit="event.preventDefault()" class="grid grid-cols-2 md:grid-cols-6 xl:grid-cols-12 gap-3 items-end">
            <div class="col-span-2 md:col-span-6 xl:col-span-3">
                <label for="f-q" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Tìm kiếm sinh viên</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]" aria-hidden="true">search</span>
                    <input id="f-q" name="q" type="search" autocomplete="off" placeholder="Nhập MSSV, họ tên, số CCCD…" class="w-full h-10 pl-9 pr-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                </div>
            </div>
            <div class="xl:col-span-2">
                <label for="f-faculty" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Khoa / Viện</label>
                <select id="f-faculty" name="faculty_id" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><option value="">Tất cả các khoa</option></select>
            </div>
            <div class="xl:col-span-2">
                <label for="f-major" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Ngành đào tạo</label>
                <select id="f-major" name="major_id" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><option value="">Tất cả các ngành</option></select>
            </div>
            <div class="xl:col-span-2">
                <label for="f-cohort" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Khóa học</label>
                <select id="f-cohort" name="cohort" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><option value="">Tất cả khóa</option></select>
            </div>
            <div class="xl:col-span-1">
                <label for="f-class" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Lớp</label>
                <select id="f-class" name="admin_class_id" class="w-full h-10 px-2 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><option value="">Tất cả</option></select>
            </div>
            <div class="xl:col-span-2">
                <label for="f-status" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Trạng thái SV</label>
                <select id="f-status" name="status" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending">Chờ nhập học</option>
                    <option value="studying">Đang học</option>
                    <option value="suspended">Bảo lưu</option>
                    <option value="withdrawn">Thôi học</option>
                    <option value="expelled">Buộc thôi học</option>
                    <option value="transferred">Chuyển trường</option>
                    <option value="graduated">Đã tốt nghiệp</option>
                </select>
            </div>
        </form>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-3 border-t border-outline-variant/30 text-body-sm">
            <div data-region="tags" class="flex items-center gap-2 flex-wrap"></div>
            <div class="text-secondary text-body-sm">Tìm thấy <strong data-field="found" class="text-on-surface font-semibold">—</strong> sinh viên phù hợp</div>
        </div>
    </section>

    {{-- Bảng danh sách --}}
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden flex flex-col">
        <div class="px-5 py-3 border-b border-outline-variant/30 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="font-headline-sm text-headline-sm text-on-surface font-semibold">Danh sách sinh viên</span>
                <span data-field="count" class="font-label-sm text-label-sm bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-bold"></span>
            </div>
            <button type="button" data-action="reload" class="p-1.5 rounded-lg border border-outline-variant/60 text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Làm mới dữ liệu" aria-label="Làm mới dữ liệu">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">refresh</span>
            </button>
        </div>
        <div data-region="table" class="overflow-x-auto custom-scrollbar"></div>
        <div class="px-5 py-3 border-t border-outline-variant/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-secondary">
                <label for="f-per-page">Hiển thị</label>
                <select id="f-per-page" data-filter="per_page" class="h-8 px-2 text-xs font-semibold bg-white border border-outline-variant rounded-md text-on-surface">
                    <option value="20">20</option><option value="50" selected>50</option><option value="100">100</option>
                </select>
                <span>dòng / trang</span>
            </div>
            <nav data-region="pager" class="flex items-center gap-1" aria-label="Phân trang"></nav>
        </div>
    </section>

    {{-- Yêu cầu sửa hồ sơ do sinh viên gửi (BR-STU-06) --}}
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden">
        <div class="px-5 py-3 border-b border-outline-variant/30 flex items-center justify-between gap-3">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Yêu cầu sửa hồ sơ chờ duyệt</h2>
                <p class="text-xs text-secondary">Họ tên, ngày sinh, số giấy tờ, ngành do sinh viên đề nghị kèm minh chứng.</p>
            </div>
            <span data-field="requests-count" class="font-label-sm text-label-sm bg-amber-50 text-amber-800 px-2.5 py-0.5 rounded-full font-bold"></span>
        </div>
        <div data-region="requests" class="p-4"></div>
    </section>
</div>
@endsection
