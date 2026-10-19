{{--
    Kết quả đăng ký học phần, thời khóa biểu tuần và phiếu đăng ký (mock student/ket-qua-dang-ky).
    Dữ liệu: resources/js/pages/student-registration-result.js gọi GET /student/registration/result (và /slip cho PDF).
    In trang: chỉ in bản xem trước phiếu (các phần khác có print:hidden).
--}}
@extends('layouts.portal')

@section('title', 'Kết quả đăng ký học phần - Cổng Sinh viên')
@section('page-title', 'Kết quả đăng ký')

@section('page')
<div data-page="student-registration-result" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 print:hidden">
        <div class="space-y-1 min-w-0">
            <h1 class="text-headline-lg font-bold text-on-surface">Kết quả đăng ký học phần</h1>
            <p class="text-body-md text-on-surface-variant" data-field="subtitle">Đang tải…</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <label class="text-label-md text-on-surface-variant flex items-center gap-2" data-field="term-picker" hidden>
                Học kỳ
                <select data-input="term" class="px-3 py-2 border border-outline-variant rounded-lg text-body-md text-on-surface bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"></select>
            </label>
            <button type="button" data-action="reload" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold text-on-surface hover:bg-surface-container-low inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">refresh</span>
                Làm mới
            </button>
            <button type="button" data-action="slip" class="px-3 py-2 rounded-lg bg-primary hover:bg-primary-container text-white text-label-md font-semibold inline-flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">picture_as_pdf</span>
                Tải phiếu PDF
            </button>
            <a href="{{ route('student.pages.registration') }}" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold text-on-surface hover:bg-surface-container-low inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">how_to_reg</span>
                Đăng ký thêm
            </a>
        </div>
    </section>

    <div data-region="alert" hidden class="print:hidden"></div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 print:hidden" data-region="summary"></div>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm print:hidden" aria-labelledby="items-heading">
        <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-2">
            <h2 id="items-heading" class="text-headline-sm font-bold text-on-surface">Danh sách học phần đã đăng ký</h2>
            <span class="text-label-sm text-secondary" data-field="items-count"></span>
        </div>
        <div data-region="items" class="p-4"></div>
        <p class="px-5 pb-4 text-[11px] text-outline">Hủy: trả chỗ, không ghi vào kết quả (trong đợt / trước hạn hủy). Rút: sau hạn hủy, tới hạn rút; học phần ghi "Đã rút", không tính GPA. Sau khi chốt đăng ký, liên hệ Phòng Đào tạo.</p>
    </section>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm print:hidden" aria-labelledby="timetable-heading">
        <div class="px-5 py-4 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">calendar_view_week</span>
            <h2 id="timetable-heading" class="text-headline-sm font-bold text-on-surface">Thời khóa biểu tuần</h2>
        </div>
        <div data-region="timetable" class="p-4"></div>
    </section>

    <section aria-labelledby="slip-heading" class="space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2 print:hidden">
            <h2 id="slip-heading" class="text-headline-md font-bold text-on-surface">Bản xem trước phiếu đăng ký (khổ A4)</h2>
            <button type="button" onclick="window.print()" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold text-on-surface hover:bg-surface-container-low inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">print</span>
                In phiếu
            </button>
        </div>
        <div class="overflow-x-auto">
            <div data-region="slip" class="bg-white text-slate-900 w-full max-w-[850px] min-w-[640px] mx-auto p-8 md:p-12 border border-slate-300 rounded-sm print:border-0 print:p-0 print:min-w-0"></div>
        </div>
        <p class="text-[11px] text-outline print:hidden">Bản in chính thức là tệp PDF do hệ thống tạo (nút "Tải phiếu PDF"); mỗi lần tải được ghi nhật ký.</p>
    </section>
</div>
@endsection
