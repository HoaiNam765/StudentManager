{{--
    Tổng quan cổng giảng viên (FR-RPT-010; FR-TCH-009; mock teacher/tong-quan). Dữ liệu thật từ
    App\Modules\Report\Services\PortalOverview::lecturer() ($overview); tài khoản chưa gắn hồ sơ giảng viên thì báo rõ.
--}}
@extends('layouts.portal')

@section('title', 'Cổng Giảng viên - ' . config('studentmanager.school.short_name'))
@section('page-title', 'Tổng quan Giảng viên')

@section('page')
@php $card = 'bg-white border border-slate-200 rounded-xl shadow-sm'; @endphp
<div class="space-y-7">
    @if ($overview === null)
        <div class="{{ $card }} p-8 text-center space-y-2">
            <span class="material-symbols-outlined text-outline text-[44px]" aria-hidden="true">person_off</span>
            <h1 class="text-lg font-bold text-on-surface">Tài khoản chưa gắn với hồ sơ giảng viên</h1>
            <p class="text-sm text-on-surface-variant">Liên hệ Phòng Đào tạo hoặc khoa để được gắn hồ sơ giảng viên.</p>
        </div>
    @else
        @php
            $p = $overview['profile'];
            $toSubmit = collect($overview['sections'])->where('grade_editable', true);
        @endphp
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-1 border-b border-slate-200/60">
            <div>
                <h1 class="text-[26px] sm:text-[28px] font-bold text-[#1E293B] tracking-tight">Xin chào, {{ $p['name'] }} <span>👋</span></h1>
                <div class="flex flex-wrap items-center gap-2 mt-1 text-sm text-slate-500">
                    <span class="material-symbols-outlined text-base text-[#4776E6]" aria-hidden="true">calendar_today</span>
                    <span>{{ now(config('studentmanager.display_timezone'))->translatedFormat('l, d/m/Y') }}</span>
                    <span class="text-slate-300">•</span>
                    <span class="font-semibold text-[#4776E6]">{{ count($overview['today_schedule']) }} buổi dạy hôm nay</span>
                    @if ($overview['term'])<span class="text-slate-300">•</span><span>{{ $overview['term'] }}</span>@endif
                    @if ($p['department'])<span class="text-slate-300">•</span><span>{{ $p['department'] }}</span>@endif
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if (Route::has('teacher.pages.gradebook'))
                    <a href="{{ route('teacher.pages.gradebook') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4776E6] hover:bg-[#3b63c4] text-white text-sm font-semibold rounded-lg shadow-sm">
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">edit_note</span><span>Nhập điểm học phần</span>
                    </a>
                @endif
                @if (Route::has('teacher.pages.announcements'))
                    <a href="{{ route('teacher.pages.announcements') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg shadow-sm">
                        <span class="material-symbols-outlined text-lg text-slate-500" aria-hidden="true">campaign</span><span>Gửi thông báo lớp</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-[#FFFBEB] border border-amber-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-2.5 pb-3 border-b border-amber-200/80 mb-3">
                <span class="material-symbols-outlined text-amber-700" aria-hidden="true">priority_high</span>
                <h2 class="text-base font-bold text-slate-800">Việc cần xử lý</h2>
                <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-0.5 rounded-full border border-amber-300/50">{{ $toSubmit->count() + count($overview['deadlines']) }} việc</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach ($toSubmit as $s)
                    <a href="{{ route('teacher.pages.gradebook', ['course_section_id' => $s['id']]) }}" class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm hover:bg-amber-50">
                        <span class="flex flex-col"><span class="text-xs font-semibold text-slate-800">Nhập và nộp điểm lớp {{ $s['code'] }}</span><span class="text-[11px] text-slate-500 mt-0.5">{{ $s['subject'] }} • Bảng điểm: {{ $s['grade_status'] }}</span></span>
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Điểm</span>
                    </a>
                @endforeach
                @foreach ($overview['deadlines'] as $d)
                    <div class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm">
                        <span class="flex flex-col"><span class="text-xs font-semibold text-slate-800">{{ $d['label'] }}</span><span class="text-[11px] text-slate-500 mt-0.5">{{ \Carbon\CarbonImmutable::parse($d['date'])->format('d/m/Y') }} • {{ $d['term'] }}</span></span>
                        <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Hạn chót</span>
                    </div>
                @endforeach
                @if ($toSubmit->isEmpty() && empty($overview['deadlines']))
                    <p class="text-sm text-slate-600">Không có việc cần xử lý.</p>
                @endif
            </div>
        </div>

        <section class="space-y-3.5">
            <h2 class="text-[20px] font-bold text-[#1E293B] flex items-center gap-2.5"><span class="material-symbols-outlined text-[#4776E6] text-xl" aria-hidden="true">calendar_today</span>Lịch giảng dạy hôm nay</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @forelse ($overview['today_schedule'] as $cls)
                    <div class="{{ $card }} p-5 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-[#4776E6] flex items-center gap-1"><span class="material-symbols-outlined text-sm" aria-hidden="true">schedule</span>{{ $cls['time'] }} (tiết {{ $cls['period_from'] }}–{{ $cls['period_to'] }})</span>
                            <span class="bg-blue-100 text-blue-800 text-[11px] font-bold px-2 py-0.5 rounded-full">{{ $cls['status_label'] }}</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">{{ $cls['subject'] }}</h3>
                        <p class="text-xs text-slate-500">Lớp {{ $cls['section'] }} • Phòng <strong class="text-slate-700">{{ $cls['room'] ?? 'trực tuyến' }}</strong></p>
                    </div>
                @empty
                    <p class="text-sm text-slate-600 md:col-span-3">Hôm nay không có buổi dạy (hoặc thời khóa biểu học kỳ chưa công bố).</p>
                @endforelse
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <section class="lg:col-span-8 {{ $card }} overflow-hidden">
                <h2 class="px-5 py-4 border-b border-slate-200 text-base font-bold text-slate-800">Lớp học phần học kỳ này</h2>
                @if (empty($overview['sections']))
                    <p class="p-5 text-sm text-slate-600">Chưa được phân công lớp nào trong học kỳ này.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs min-w-[640px]">
                            <thead class="bg-slate-50 text-slate-600"><tr><th class="py-2.5 px-4">Lớp</th><th class="py-2.5 px-4">Học phần</th><th class="py-2.5 px-4 text-center">Sĩ số</th><th class="py-2.5 px-4">Trạng thái lớp</th><th class="py-2.5 px-4">Bảng điểm</th><th class="py-2.5 px-4"></th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($overview['sections'] as $s)
                                    <tr>
                                        <td class="py-2.5 px-4 font-mono font-semibold text-[#4776E6]">{{ $s['code'] }}</td>
                                        <td class="py-2.5 px-4">{{ $s['subject'] }} <span class="text-slate-400">({{ $s['credits'] }} TC)</span></td>
                                        <td class="py-2.5 px-4 text-center">{{ $s['students'] }} / {{ $s['max'] }}</td>
                                        <td class="py-2.5 px-4">{{ $s['status'] }}</td>
                                        <td class="py-2.5 px-4">{{ $s['grade_status'] }}</td>
                                        <td class="py-2.5 px-4 text-right"><a href="{{ route('teacher.pages.gradebook', ['course_section_id' => $s['id']]) }}" class="text-[#4776E6] font-semibold hover:underline">Sổ điểm</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <div class="lg:col-span-4 space-y-6">
                <section class="{{ $card }} p-5 space-y-3">
                    <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3">Yêu cầu điều chỉnh điểm đã gửi</h3>
                    @forelse ($overview['change_requests'] as $r)
                        <div class="text-xs flex justify-between gap-2"><span>{{ $r['section'] }} • {{ $r['student'] }}</span><strong class="text-slate-700">{{ $r['status'] }}</strong></div>
                    @empty
                        <p class="text-xs text-slate-500">Chưa gửi yêu cầu nào.</p>
                    @endforelse
                </section>
                <section class="{{ $card }} p-5 space-y-3">
                    <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3">Thông báo chưa đọc ({{ $overview['notifications']['unread'] }})</h3>
                    @forelse ($overview['notifications']['latest'] as $n)
                        <a href="{{ route('notifications.page') }}" class="block text-xs font-semibold text-slate-800 hover:text-[#4776E6] line-clamp-2">{{ $n['title'] }}</a>
                    @empty
                        <p class="text-xs text-slate-500">Không có thông báo chưa đọc.</p>
                    @endforelse
                </section>
            </div>
        </div>
    @endif
</div>
@endsection
