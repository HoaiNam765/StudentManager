{{--
    Đăng ký học phần (cổng sinh viên) — gộp mock student/dang-ky-hoc-phan và -mobile thành một trang đáp ứng.
    Dữ liệu: resources/js/pages/student-registration.js gọi API trong $pageData['api'] (docs/fe-be-mapping.md, nhóm a).
    Khác mock: đăng ký TỪNG LỚP ngay khi bấm (API giữ chỗ theo từng lớp, chống tranh chấp chỗ cuối), không có giỏ tạm.
--}}
@extends('layouts.portal')

@section('title', 'Đăng ký học phần - Cổng Sinh viên')
@section('page-title', 'Đăng ký học phần')

@section('page')
<div data-page="student-registration" class="space-y-6 pb-20 xl:pb-0">
    {{-- Đợt đăng ký đang mở --}}
    <section class="rounded-2xl p-5 sm:p-6 bg-gradient-to-r from-primary via-primary-container to-tertiary text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none" aria-hidden="true">
            <span class="material-symbols-outlined text-[200px]">school</span>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="space-y-2 min-w-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-[11px] font-semibold tracking-wider uppercase">
                    <span class="w-2 h-2 rounded-full bg-emerald-300" data-field="open-dot"></span>
                    <span data-field="period-state">Đang tải đợt đăng ký…</span>
                </div>
                <h1 class="text-headline-xl-mobile sm:text-headline-xl font-bold tracking-tight break-words" data-field="period-name">Đăng ký học phần</h1>
                <p class="text-body-md text-white/90" data-field="period-term"></p>
                <div class="flex flex-wrap items-center gap-2 pt-1 text-label-md">
                    <span class="flex items-center gap-1.5 bg-black/15 px-3 py-1 rounded-lg">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">badge</span>
                        MSSV: <strong data-field="student-code">—</strong>
                    </span>
                    <span class="flex items-center gap-1.5 bg-black/15 px-3 py-1 rounded-lg">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">person</span>
                        <strong data-field="student-name">—</strong>
                    </span>
                </div>
            </div>
            <div class="shrink-0 bg-white/10 p-4 rounded-xl border border-white/20 text-center min-w-[230px]" data-field="deadline-box" hidden>
                <span class="text-xs font-semibold uppercase tracking-wider block mb-1">Hạn đóng đợt</span>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-error text-white text-label-lg font-bold shadow-sm">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">timer</span>
                    <span data-field="countdown">—</span>
                </div>
                <p class="text-[11px] mt-2" data-field="closes-at"></p>
            </div>
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <div class="grid grid-cols-12 gap-6 items-start">
        {{-- Danh sách lớp được chọn --}}
        <section class="col-span-12 xl:col-span-7 space-y-4" aria-labelledby="sections-heading">
            <h2 id="sections-heading" class="sr-only">Danh sách lớp học phần được chọn</h2>
            <div class="bg-surface-container-lowest p-2 rounded-xl border border-outline-variant shadow-sm flex flex-wrap items-center gap-2" role="tablist" aria-label="Lọc lớp học phần">
                <button type="button" role="tab" data-filter="all" class="flex-1 min-w-[120px] py-2 px-3 rounded-lg text-label-md text-center transition-colors">Tất cả học phần của đợt <span data-count="all"></span></button>
                <button type="button" role="tab" data-filter="eligible" class="flex-1 min-w-[120px] py-2 px-3 rounded-lg text-label-md text-center transition-colors">Đăng ký được <span data-count="eligible"></span></button>
                <button type="button" role="tab" data-filter="mine" class="flex-1 min-w-[120px] py-2 px-3 rounded-lg text-label-md text-center transition-colors">Đã đăng ký <span data-count="mine"></span></button>
                <button type="button" disabled title="Học cải thiện sẽ được triển khai sau" class="flex-1 min-w-[120px] py-2 px-3 rounded-lg text-label-md text-center text-outline cursor-not-allowed">Học cải thiện (chưa triển khai)</button>
            </div>

            <div class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shadow-sm space-y-3">
                <div class="flex flex-col sm:flex-row gap-3">
                    <label class="relative flex-1">
                        <span class="sr-only">Tìm theo mã lớp, tên học phần hoặc giảng viên</span>
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-secondary text-[20px]" aria-hidden="true">search</span>
                        <input type="search" data-input="search" placeholder="Mã lớp, tên học phần hoặc tên giảng viên…" class="w-full pl-10 pr-4 py-2 border border-outline-variant rounded-lg text-body-md text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </label>
                    <label>
                        <span class="sr-only">Lọc theo thứ</span>
                        <select data-input="day" class="w-full sm:w-auto px-3 py-2 border border-outline-variant rounded-lg text-body-md text-on-surface bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="">Mọi thứ trong tuần</option>
                            @foreach (\App\Modules\Timetable\Models\ScheduleRule::DAYS as $day)
                                <option value="{{ $day }}">{{ $day }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="flex items-center justify-between gap-2 text-label-sm text-secondary pt-1 border-t border-surface-container">
                    <span data-field="shown-count">Đang tải…</span>
                    <button type="button" data-action="reload" class="text-primary hover:underline inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">refresh</span>
                        Làm mới danh sách
                    </button>
                </div>
            </div>

            <div data-region="sections" class="space-y-3.5" aria-live="polite"></div>
        </section>

        {{-- Tín chỉ và thời khóa biểu tuần --}}
        <aside class="col-span-12 xl:col-span-5 space-y-4" aria-label="Tóm tắt đăng ký">
            <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm space-y-4" data-region="credits">
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-surface-container">
                    <div>
                        <span class="text-label-sm text-secondary uppercase tracking-wider block">Khối lượng học kỳ này</span>
                        <h3 class="text-headline-sm font-bold text-on-surface">Đã đăng ký: <span data-field="credits-total">—</span> tín chỉ</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-primary/10 text-primary text-label-md font-bold" data-field="credits-badge" hidden></span>
                </div>
                <div>
                    <div class="w-full bg-surface-container rounded-full h-3 overflow-hidden" role="progressbar" aria-label="Tín chỉ đã đăng ký so với tối đa" aria-valuemin="0" data-field="credits-bar">
                        <div class="bg-primary h-3 rounded-full transition-all duration-500" style="width: 0%" data-field="credits-fill"></div>
                    </div>
                    <div class="flex flex-wrap justify-between items-center gap-1 mt-2 text-label-sm text-secondary">
                        <span>Tối thiểu: <strong data-field="credits-min">—</strong> TC</span>
                        <span class="font-semibold text-primary" data-field="credits-left"></span>
                        <span>Tối đa: <strong data-field="credits-max">—</strong> TC</span>
                    </div>
                    <p class="mt-1 text-[11px] text-outline" data-field="credits-basis"></p>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant shadow-sm space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[22px]" aria-hidden="true">calendar_view_week</span>
                        <h3 class="text-headline-sm font-bold text-on-surface">Thời khóa biểu tuần</h3>
                    </div>
                    <span class="text-xs text-secondary bg-surface-container px-2 py-0.5 rounded">Các lớp đã đăng ký</span>
                </div>
                <div data-region="timetable"></div>
            </div>

            <a href="{{ route('student.pages.registration-result') }}" class="w-full py-3 px-5 rounded-xl bg-primary hover:bg-primary-container text-white text-label-lg font-bold shadow-sm transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[22px]" aria-hidden="true">fact_check</span>
                Xem kết quả và tải phiếu đăng ký
            </a>
        </aside>
    </div>

    {{-- Thanh tóm tắt cố định ở cuối màn hình nhỏ (thay khung bên phải của bản mobile) --}}
    <div class="xl:hidden fixed bottom-0 inset-x-0 md:left-64 z-20 bg-surface-container-lowest border-t border-outline-variant px-4 py-2.5 flex items-center justify-between gap-3 shadow-[0_-2px_8px_rgba(0,0,0,0.06)] print:hidden">
        <span class="text-sm text-on-surface">Đã đăng ký <strong data-field="credits-total-mobile">—</strong> / <span data-field="credits-max-mobile">—</span> TC</span>
        <a href="{{ route('student.pages.registration-result') }}" class="px-3 py-2 rounded-lg bg-primary text-white text-label-md font-semibold">Kết quả</a>
    </div>
</div>
@endsection
