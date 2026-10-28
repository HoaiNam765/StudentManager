@extends('layouts.portal')

@section('title', 'Cổng Giảng viên - ' . config('studentmanager.school.short_name'))
@section('page-title', 'Tổng quan Giảng viên')

@php
    // Dữ liệu thật từ PortalOverview::lecturer (null khi tài khoản chưa gắn hồ sơ giảng viên); giữ nguyên bố cục của nhóm FE
    $o = $overview ?? null;
    $day = $o['teaching_day'] ?? null;
    $daySessions = collect($day['sessions'] ?? []);
    $sections = collect($o['sections'] ?? []);
    $tasks = $sections->where('grade_editable', true)->values();
    $attendanceUrl = \Illuminate\Support\Facades\Route::has('teacher.pages.attendance') ? route('teacher.pages.attendance') : '#';
    $sectionsUrl = \Illuminate\Support\Facades\Route::has('teacher.pages.sections') ? route('teacher.pages.sections') : '#';
    $gradebookUrl = \Illuminate\Support\Facades\Route::has('teacher.pages.gradebook') ? route('teacher.pages.gradebook') : '#';
    $states = [
        'ongoing' => ['Đang diễn ra', 'bg-emerald-100 text-emerald-800 border-emerald-200', 'border-2 border-emerald-400'],
        'soon' => ['Sắp bắt đầu', 'bg-blue-100 text-[#4776E6] border-blue-200', 'border border-[#E2E8F0]'],
        'upcoming' => ['Chưa bắt đầu', 'bg-slate-100 text-slate-600 border-slate-200', 'border border-[#E2E8F0]'],
        'done' => ['Đã kết thúc', 'bg-slate-100 text-slate-500 border-slate-200', 'border border-[#E2E8F0]'],
    ];
    $dayLabel = $day && $day['date'] ? \Carbon\Carbon::parse($day['date'])->translatedFormat('l, d/m/Y') : null;
@endphp

@section('page')
<div class="space-y-7">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-1 border-b border-slate-200/60">
        <div>
            <h1 class="text-[26px] sm:text-[28px] font-bold text-[#1E293B] tracking-tight flex items-center gap-2">
                Xin chào, {{ $o['profile']['name'] ?? auth()->user()->name }} <span>👋</span>
            </h1>
            <div class="flex flex-wrap items-center gap-2 mt-1 text-sm text-slate-500 font-normal">
                <span class="material-symbols-outlined text-base text-[#4776E6]" aria-hidden="true">calendar_today</span>
                <span>{{ now(config('studentmanager.display_timezone'))->translatedFormat('l, d/m/Y') }}</span>
                @if ($day && $day['is_today'] && $daySessions->isNotEmpty())
                <span class="text-slate-300">•</span>
                <span class="inline-flex items-center gap-1.5 font-semibold text-[#4776E6]">
                    <span class="w-2 h-2 rounded-full bg-[#4776E6] animate-ping inline-block"></span>
                    {{ $daySessions->whereIn('state', ['ongoing', 'soon', 'upcoming'])->count() }} ca dạy đang chờ
                </span>
                @endif
                @if (! empty($o['term']))
                <span class="text-slate-300">•</span>
                <span>{{ $o['term'] }}</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 lg:shrink-0">
            @if (\Illuminate\Support\Facades\Route::has('teacher.pages.gradebook'))
                <a href="{{ route('teacher.pages.gradebook') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4776E6] hover:bg-[#3b63c4] text-white text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">edit_note</span>
                    <span>Nhập điểm học phần</span>
                </a>
            @endif
            <a href="{{ $attendanceUrl }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined text-lg text-slate-500" aria-hidden="true">how_to_reg</span>
                <span>Điểm danh nhanh</span>
            </a>
        </div>
    </div>

    @if ($o === null)
    <div class="bg-[#FFFBEB] border border-amber-200 rounded-xl p-5 shadow-sm text-sm text-slate-700">Tài khoản chưa gắn với hồ sơ giảng viên nên chưa có lịch dạy và lớp phụ trách.</div>
    @else
    <!-- Alert Panel -->
    <div class="bg-[#FFFBEB] border border-amber-200 rounded-xl p-5 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-amber-200/80 mb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                    <span class="material-symbols-outlined text-base" aria-hidden="true">priority_high</span>
                </div>
                <h2 class="text-base font-bold text-slate-800">Công việc học vụ cần xử lý</h2>
                <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-0.5 rounded-full border border-amber-300/50">{{ $tasks->count() }} việc cần xử lý</span>
            </div>
            <a href="{{ $gradebookUrl }}" class="text-xs font-semibold text-[#4776E6] hover:underline flex items-center gap-1">
                <span>Xử lý nhập điểm</span>
                <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @forelse ($tasks as $task)
            <a href="{{ $gradebookUrl }}" class="bg-white rounded-lg p-3 border border-amber-200/80 flex items-start justify-between gap-2 shadow-sm hover:border-amber-400">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-slate-800 line-clamp-1">Nhập / nộp điểm lớp {{ $task['code'] }}</span>
                    <span class="text-[11px] text-slate-500 mt-0.5">{{ $task['subject'] }} · {{ $task['grade_status'] }}</span>
                </div>
                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Cần xử lý</span>
            </a>
            @empty
            <p class="md:col-span-3 text-xs text-slate-500">Không có việc nào đang chờ.</p>
            @endforelse
        </div>
    </div>

    <!-- Lịch giảng dạy hôm nay -->
    <section class="space-y-3.5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#4776E6] text-xl" aria-hidden="true">calendar_today</span>
                <h2 class="text-[20px] font-bold text-[#1E293B]">{{ $day && ! $day['is_today'] && $daySessions->isNotEmpty() ? 'Lịch dạy gần nhất' : 'Lịch giảng dạy hôm nay' }}</h2>
                <span class="text-xs font-semibold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full">{{ $dayLabel ?? now(config('studentmanager.display_timezone'))->format('d/m/Y') }}</span>
            </div>
            @if (\Illuminate\Support\Facades\Route::has('teacher.pages.schedule'))
            <a href="{{ route('teacher.pages.schedule') }}" class="text-xs font-semibold text-[#4776E6] hover:underline">Xem thời khóa biểu tuần</a>
            @endif
        </div>
        @if ($daySessions->isEmpty())
        <div class="bg-white border border-dashed border-slate-300 rounded-xl p-6 text-center text-sm text-slate-500">
            {{ ($day['published'] ?? false) ? 'Không có buổi dạy trong 7 ngày tới.' : 'Thời khóa biểu học kỳ chưa công bố; lịch dạy hiện khi Phòng Đào tạo công bố.' }}
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach ($daySessions as $index => $session)
            @php [$stateLabel, $badgeClass, $cardClass] = $states[$session['state']] ?? $states['upcoming']; @endphp
            <div class="bg-white {{ $cardClass }} rounded-xl p-5 shadow-sm relative flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold {{ $session['state'] === 'ongoing' ? 'text-[#4776E6]' : 'text-slate-600' }} flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm {{ $session['state'] === 'ongoing' ? '' : 'text-slate-400' }}" aria-hidden="true">schedule</span>
                            Ca {{ $index + 1 }}: {{ $session['time'] }} (Tiết {{ $session['period_from'] }} - {{ $session['period_to'] }})
                        </span>
                        <span class="inline-flex items-center gap-1 {{ $badgeClass }} text-[11px] font-bold px-2 py-0.5 rounded-full border">
                            @if ($session['state'] === 'ongoing')<span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>@endif
                            {{ $stateLabel }}
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mt-1 truncate">{{ $session['subject'] }}</h3>
                    <div class="mt-3.5 space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Lớp học phần:</span>
                            <span class="font-medium text-slate-800">{{ $session['section'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Phòng:</span>
                            <span class="font-medium text-slate-800">{{ $session['room'] ?? 'Chưa có phòng' }}</span>
                        </div>
                        <div class="flex justify-between pt-1 border-t border-slate-200/60">
                            <span class="text-slate-400">Sĩ số:</span>
                            <span class="font-semibold text-slate-800">{{ $session['students'] }} SV</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500">{{ $session['recorded'] > 0 ? "Đã điểm danh {$session['recorded']}/{$session['students']}" : 'Chưa điểm danh' }}</span>
                    @if (in_array($session['state'], ['ongoing', 'done'], true))
                    <a href="{{ $attendanceUrl }}?section={{ $session['section_id'] }}&session={{ $session['id'] }}" class="px-3 py-1.5 bg-[#4776E6] hover:bg-[#3b63c4] text-white text-xs font-semibold rounded-lg shadow-sm transition-all active:scale-95 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">fact_check</span>
                        <span>Điểm danh</span>
                    </a>
                    @else
                    <a href="{{ $sectionsUrl }}" class="px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                        Xem danh sách
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </section>

    <!-- Grid 8-4 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Học phần phụ trách (8 cols) -->
        <section class="lg:col-span-8 bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#4776E6] text-xl" aria-hidden="true">school</span>
                    <h2 class="text-base font-bold text-[#1E293B]">Học phần & Lớp phụ trách{{ ! empty($o['term']) ? " ({$o['term']})" : '' }}</h2>
                </div>
                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">
                    {{ $sections->count() }} lớp học phần
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse ($sections as $section)
                @php $pct = $section['sessions_total'] > 0 ? round($section['sessions_taught'] * 100 / $section['sessions_total']) : 0; @endphp
                <div class="p-4 rounded-xl border border-slate-200 hover:border-[#4776E6]/60 transition-all bg-white shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[11px] font-semibold text-[#4776E6] bg-blue-50 border border-blue-200 px-2 py-0.5 rounded">{{ $section['code'] }} • {{ $section['credits'] }} Tín chỉ</span>
                            <span class="text-xs text-slate-500 font-medium">{{ $section['status'] }}</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mt-2">{{ $section['subject'] }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $section['students'] }} sinh viên · Điểm: {{ $section['grade_status'] }}</p>
                        <div class="mt-3">
                            <div class="flex justify-between text-[11px] mb-1 text-slate-500">
                                <span>Tiến độ giảng dạy</span>
                                <span class="font-semibold text-slate-800">{{ $section['sessions_taught'] }}/{{ $section['sessions_total'] }} buổi ({{ $pct }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#4776E6] h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <a href="{{ $sectionsUrl }}" class="text-slate-600 hover:text-[#4776E6] font-medium transition-colors">Xem danh sách</a>
                        <a href="{{ $gradebookUrl }}" class="text-[#4776E6] font-semibold hover:underline">Quản lý điểm</a>
                    </div>
                </div>
                @empty
                <p class="sm:col-span-2 text-sm text-slate-500">Chưa được phân công lớp học phần trong học kỳ này.</p>
                @endforelse
            </div>
        </section>

        <!-- Thông báo & Tài liệu (4 cols) -->
        <div class="lg:col-span-4 space-y-5">
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm space-y-3.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#4776E6] text-xl" aria-hidden="true">campaign</span>
                        <h2 class="text-sm font-bold text-[#1E293B]">Thông báo Học vụ mới nhất</h2>
                    </div>
                    <a href="{{ \Illuminate\Support\Facades\Route::has('notifications.page') ? route('notifications.page') : url('/thong-bao') }}" class="text-xs text-[#4776E6] font-semibold">Xem tất cả</a>
                </div>
                <div class="space-y-2.5">
                    @forelse (($o['notifications']['latest'] ?? []) as $item)
                    <a href="{{ $item['link'] ?: url('/thong-bao') }}" class="block p-2.5 rounded-lg border-l-4 border-l-blue-500 border border-slate-200 bg-blue-50/20 hover:bg-blue-50/50 transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Chưa đọc</span>
                            <span class="text-[11px] text-slate-400">{{ $item['sent_at'] ? \Carbon\Carbon::parse($item['sent_at'])->setTimezone(config('studentmanager.display_timezone'))->format('d/m/Y') : '' }}</span>
                        </div>
                        <h4 class="text-xs font-semibold text-slate-900 hover:text-[#4776E6] line-clamp-2">{{ $item['title'] }}</h4>
                    </a>
                    @empty
                    <p class="text-xs text-slate-500">Không có thông báo chưa đọc.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm">
                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-200 mb-3">
                    <span class="material-symbols-outlined text-slate-500 text-lg" aria-hidden="true">folder_open</span>
                    <h2 class="text-sm font-bold text-[#1E293B]">Biểu mẫu & Quy định đào tạo</h2>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors">
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="material-symbols-outlined text-base text-red-500" aria-hidden="true">picture_as_pdf</span>
                            <span class="font-medium">Mẫu đề cương chi tiết HP 2024</span>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">PDF</span>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors">
                        <div class="flex items-center gap-2 text-slate-700">
                            <span class="material-symbols-outlined text-base text-blue-500" aria-hidden="true">description</span>
                            <span class="font-medium">Quy chế đào tạo tín chỉ sửa đổi</span>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">DOCX</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
