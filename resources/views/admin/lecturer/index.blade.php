{{--
    Quản lý giảng viên (mock admin/giang-vien) cho Phòng Đào tạo / Quản trị. Dữ liệu do resources/js/pages/admin-lecturers.js
    gọi API TCH: danh sách theo phạm vi xem, thẻ thống kê theo trạng thái, số lớp / số tiết trong học kỳ, hồ sơ, thêm / sửa,
    nghỉ việc / nghỉ hưu, cấp tài khoản, xuất Excel.
--}}
@extends('layouts.portal')

@section('title', 'Quản lý Giảng viên - Quản lý Đào tạo')
@section('page-title', 'Giảng viên')

@section('page')
<div data-page="admin-lecturers" class="space-y-6">
    {{-- Tiêu đề và thao tác chính --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-outline-variant/20">
        <div>
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">Quản lý Giảng viên</h1>
            <p class="font-body-md text-body-md text-secondary mt-0.5">Hồ sơ giảng viên, đơn vị công tác, chức danh – học vị và khối lượng giảng dạy theo học kỳ.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            @if ($pageData['can']['import'])
                <a href="{{ $pageData['links']['import'] }}" class="h-9 px-3.5 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low hover:border-primary/40 font-label-md text-label-md flex items-center gap-1.5 transition-colors shadow-2xs">
                    <span class="material-symbols-outlined text-[18px] text-secondary" aria-hidden="true">upload_file</span>
                    <span>Nhập file</span>
                </a>
            @endif
            @if ($pageData['can']['export'])
                <button type="button" data-action="export" class="h-9 px-3.5 rounded-lg border border-outline-variant bg-white text-on-surface hover:bg-surface-container-low hover:border-primary/40 font-label-md text-label-md flex items-center gap-1.5 transition-colors shadow-2xs">
                    <span class="material-symbols-outlined text-[18px] text-secondary" aria-hidden="true">file_download</span>
                    <span>Xuất Excel</span>
                </button>
            @endif
            @if ($pageData['can']['create'])
                <button type="button" data-action="create" class="h-9 px-4 rounded-lg bg-primary hover:bg-primary-container active:scale-[0.98] text-white font-label-md text-label-md flex items-center gap-1.5 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span>
                    <span>Thêm giảng viên mới</span>
                </button>
            @endif
        </div>
    </div>

    <div data-region="alert" hidden></div>

    {{-- 4 thẻ thống kê theo trạng thái công tác --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" aria-label="Thống kê giảng viên">
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Tổng số giảng viên</p>
                    <h3 data-stat="total" class="font-headline-xl text-headline-xl font-bold text-on-surface mt-1">—</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">groups</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-outline-variant/20 font-body-sm text-body-sm text-secondary">Trong phạm vi dữ liệu của bạn</div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Đang công tác</p>
                    <h3 data-stat="working" class="font-headline-xl text-headline-xl font-bold text-green-700 mt-1">—</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">cast_for_education</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-outline-variant/20 font-body-sm text-body-sm text-secondary">Được phân công giảng dạy</div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Vượt định mức tiết</p>
                    <h3 data-stat="overload" class="font-headline-xl text-headline-xl font-bold text-amber-700 mt-1">—</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-700">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">event_busy</span>
                </div>
            </div>
            <div data-stat="overload-note" class="mt-3 pt-3 border-t border-outline-variant/20 font-body-sm text-body-sm text-secondary">Trong học kỳ đang xem (trang hiện tại)</div>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-label-sm text-label-sm uppercase tracking-wide text-secondary font-semibold">Đã nghỉ việc / Nghỉ hưu</p>
                    <h3 data-stat="left" class="font-headline-xl text-headline-xl font-bold text-red-700 mt-1">—</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-700">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">person_off</span>
                </div>
            </div>
            <div data-stat="left-detail" class="mt-3 pt-3 border-t border-outline-variant/20 font-body-sm text-body-sm text-secondary">Hồ sơ lưu trữ, không phân công được</div>
        </div>
    </section>

    {{-- Bộ lọc và tìm kiếm --}}
    <section class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/50 shadow-xs space-y-4">
        <form data-form="filters" onsubmit="event.preventDefault()" class="grid grid-cols-2 md:grid-cols-6 xl:grid-cols-12 gap-3 items-end">
            <div class="col-span-2 md:col-span-6 xl:col-span-3">
                <label for="l-q" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Tìm kiếm giảng viên</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]" aria-hidden="true">search</span>
                    <input id="l-q" name="q" type="search" autocomplete="off" placeholder="Mã, họ tên, email…" class="w-full h-10 pl-9 pr-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                </div>
            </div>
            <div class="xl:col-span-2">
                <label for="l-faculty" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Khoa / Viện</label>
                <select id="l-faculty" name="faculty_id" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface"><option value="">Tất cả các khoa</option></select>
            </div>
            <div class="xl:col-span-2">
                <label for="l-department" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Bộ môn</label>
                <select id="l-department" name="department_id" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface"><option value="">Tất cả bộ môn</option></select>
            </div>
            <div class="xl:col-span-2">
                <label for="l-degree" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Học vị</label>
                <select id="l-degree" name="academic_degree" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface">
                    <option value="">Tất cả học vị</option>
                    <option value="doctor">Tiến sĩ</option>
                    <option value="master">Thạc sĩ</option>
                    <option value="engineer">Kỹ sư</option>
                    <option value="bachelor">Cử nhân</option>
                </select>
            </div>
            <div class="xl:col-span-1">
                <label for="l-status" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Trạng thái</label>
                <select id="l-status" name="status" class="w-full h-10 px-2 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface">
                    <option value="">Tất cả</option>
                    <option value="working">Đang công tác</option>
                    <option value="resigned">Nghỉ việc</option>
                    <option value="retired">Nghỉ hưu</option>
                </select>
            </div>
            <div class="xl:col-span-2">
                <label for="l-term" class="block font-label-sm text-label-sm font-semibold text-on-surface-variant mb-1">Khối lượng theo học kỳ</label>
                <select id="l-term" name="term_id" class="w-full h-10 px-3 text-body-md font-body-md bg-white border border-outline-variant rounded-lg text-on-surface"></select>
            </div>
        </form>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-3 border-t border-outline-variant/30 text-body-sm">
            <div data-region="tags" class="flex items-center gap-2 flex-wrap"></div>
            <div class="text-secondary text-body-sm">Tìm thấy <strong data-field="found" class="text-on-surface font-semibold">—</strong> giảng viên phù hợp</div>
        </div>
    </section>

    {{-- Bảng danh sách --}}
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-xs overflow-hidden flex flex-col">
        <div class="px-5 py-3 border-b border-outline-variant/30 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="font-headline-sm text-headline-sm text-on-surface font-semibold">Danh sách giảng viên</span>
                <span data-field="count" class="font-label-sm text-label-sm bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-bold"></span>
            </div>
            <button type="button" data-action="reload" class="p-1.5 rounded-lg border border-outline-variant/60 text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors" title="Làm mới dữ liệu" aria-label="Làm mới dữ liệu">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">refresh</span>
            </button>
        </div>
        <div data-region="table" class="overflow-x-auto custom-scrollbar"></div>
        <div class="px-5 py-3 border-t border-outline-variant/30 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-secondary">
                <label for="l-per-page">Hiển thị</label>
                <select id="l-per-page" data-filter="per_page" class="h-8 px-2 text-xs font-semibold bg-white border border-outline-variant rounded-md text-on-surface">
                    <option value="20">20</option><option value="50" selected>50</option><option value="100">100</option>
                </select>
                <span>dòng / trang</span>
            </div>
            <nav data-region="pager" class="flex items-center gap-1" aria-label="Phân trang"></nav>
        </div>
    </section>
</div>
@endsection
