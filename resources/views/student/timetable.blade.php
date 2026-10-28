@extends('layouts.portal')
@section('title', 'Thời khóa biểu - Cổng Sinh viên')
@section('page-title', 'Thời khóa biểu')
@section('page')

<!-- ============================================================== -->
<!-- 3. ACTION CONTROLS & TIMETABLE TOOLBAR BANNER                 -->
<!-- ============================================================== -->
<div class="bg-surface-container-lowest rounded-xl p-5 border border-outline-variant shadow-sm flex flex-wrap items-center justify-between gap-4">
<!-- Left: View Mode Toggler -->
<div class="flex items-center gap-4">
<div class="bg-surface-container-low p-1 rounded-lg border border-outline-variant flex items-center gap-1">
<a href="{{ route('student.pages.timetable', ['view' => 'week', 'date' => request('date'), 'term_id' => $schedule['term']['id']]) }}" class="px-3.5 py-1.5 rounded-md bg-surface-container-lowest text-primary font-semibold font-label-md text-label-md shadow-xs flex items-center gap-1.5">
<span class="material-symbols-outlined text-sm" data-icon="calendar_view_week">calendar_view_week</span>
              Xem theo Tuần
            </a>
<a href="{{ route('student.pages.timetable', ['view' => 'term', 'term_id' => $schedule['term']['id']]) }}" class="px-3.5 py-1.5 rounded-md text-secondary hover:text-on-surface font-label-md text-label-md transition-colors flex items-center gap-1.5">
<span class="material-symbols-outlined text-sm" data-icon="calendar_month">calendar_month</span>
              Xem theo Học kỳ
            </a>
</div>
<form method="get" action="{{ route('student.pages.timetable') }}" class="flex items-center gap-2">
@if (request('view') === 'term')<input type="hidden" name="view" value="term">@endif
<label for="timetable-term" class="font-label-md text-label-md text-secondary">Học kỳ</label>
<select id="timetable-term" name="term_id" onchange="this.form.submit()" class="px-3 py-1.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md">
@foreach ($terms as $option)
<option value="{{ $option['id'] }}" @selected($option['id'] === $schedule['term']['id'])>{{ $option['label'] }}</option>
@endforeach
</select>
<noscript><button type="submit" class="px-3 py-1.5 border border-outline-variant rounded-lg font-label-md text-label-md">Xem</button></noscript>
</form>
</div>
<!-- Center: Week Navigator Controls -->
<div class="flex items-center gap-2">
<a href="{{ route('student.pages.timetable', ['date' => $previous, 'term_id' => $schedule['term']['id']]) }}" class="px-3 py-1.5 border border-outline-variant rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors flex items-center gap-1 font-label-md text-label-md" title="Tuần trước">
<span class="material-symbols-outlined text-base" data-icon="chevron_left">chevron_left</span>
<span>Tuần trước</span>
</a>
<div class="px-4 py-1.5 bg-surface-container-lowest text-center min-w-[280px]">
<div class="font-headline-sm text-headline-sm font-bold text-on-surface tracking-tight">{{ \Carbon\Carbon::parse($schedule['from'])->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($schedule['to'])->format('d/m/Y') }}</div>
<div class="font-label-sm text-label-sm text-primary font-medium">{{ $schedule['term']['name'] }}</div>
</div>
<a href="{{ route('student.pages.timetable', ['date' => $next, 'term_id' => $schedule['term']['id']]) }}" class="px-3 py-1.5 border border-outline-variant rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container-low transition-colors flex items-center gap-1 font-label-md text-label-md" title="Tuần sau">
<span>Tuần sau</span>
<span class="material-symbols-outlined text-base" data-icon="chevron_right">chevron_right</span>
</a>
<a href="{{ route('student.pages.timetable') }}" class="ml-1 px-3 py-1.5 bg-primary-fixed text-primary font-semibold border border-primary/20 rounded-lg font-label-md text-label-md hover:bg-primary-container hover:text-white transition-colors flex items-center gap-1">
<span class="material-symbols-outlined text-sm" data-icon="today">today</span>
<span>Hôm nay</span>
</a>
</div>
<!-- Right: Export & Print Action Buttons -->
<div class="flex items-center gap-2">
<button type="button" disabled title="Xuất lịch ICS chưa được triển khai" class="px-3 py-1.5 border border-outline-variant bg-surface-container-lowest rounded-lg font-label-md text-label-md text-on-surface font-medium hover:bg-surface-container-low transition-colors flex items-center gap-1.5 shadow-xs">
<span class="material-symbols-outlined text-base text-secondary" data-icon="calendar_add_on">calendar_add_on</span>
<span>Xuất lịch Google (.ics)</span>
</button>
<button type="button" onclick="window.print()" class="px-4 py-1.5 bg-primary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors flex items-center gap-1.5 shadow-sm">
<span class="material-symbols-outlined text-base" data-icon="print">print</span>
<span>In thời khóa biểu (A4)</span>
</button>
</div>
</div>
<!-- ============================================================== -->
<!-- 4. TIMETABLE GRID (Weekly Schedule Matrix D11 #23 Desktop)    -->
<!-- ============================================================== -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<!-- Grid Table Container -->
<div class="overflow-x-auto">
@include('student.partials.timetable-grid')
</div>
<!-- Table Legend Bar -->
<div class="bg-surface-container-low/50 px-6 py-3 border-t border-outline-variant flex flex-wrap items-center justify-between text-xs text-secondary gap-3">
<div class="flex items-center gap-6">
<span class="font-semibold text-on-surface">Ghi chú phân loại:</span>
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded bg-blue-100 border border-blue-400"></span>
<span>Lớp học phần lý thuyết</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded bg-orange-100 border border-orange-400"></span>
<span>Lớp học phần thực hành (Lab)</span>
</div>
<div class="flex items-center gap-1.5">
<span class="w-3 h-3 rounded bg-emerald-100 border border-emerald-500"></span>
<span>Đang diễn ra (Hôm nay)</span>
</div>
</div>
<div class="flex items-center gap-2 text-slate-500">
<span class="material-symbols-outlined text-sm" data-icon="info">info</span>
<span>Sinh viên có mặt trước giờ học 10 phút để ổn định vị trí phòng học.</span>
</div>
</div>
</div>
<!-- ============================================================== -->

@endsection
