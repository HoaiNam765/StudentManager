@forelse ($todayClasses as $class)
<div class="p-4 rounded-xl border border-primary/30 bg-[#EEF2FD]/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded text-xs font-bold bg-primary text-white">{{ $class['time'] }}</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">{{ $class['status_label'] }}</span>
        </div>
        <h3 class="text-sm font-bold text-on-surface">{{ $class['course_name'] }}</h3>
        <div class="flex flex-wrap items-center gap-x-3 text-xs text-on-surface-variant">
            <span>Phòng: <strong class="text-primary font-semibold">{{ $class['room'] }}</strong></span><span>•</span>
            <span>Giảng viên: <strong class="text-on-surface">{{ $class['lecturer'] }}</strong></span>
        </div>
    </div>
    <a href="{{ route('student.pages.timetable') }}" class="self-start sm:self-center px-3 py-1.5 rounded-lg border border-primary/30 bg-white text-primary text-xs font-semibold hover:bg-surface-container-low transition-colors shadow-xs">Xem lịch học</a>
</div>
@empty
<p class="text-sm text-on-surface-variant">Không có buổi học đã công bố trong hôm nay.</p>
@endforelse
