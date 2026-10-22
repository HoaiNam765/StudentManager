@forelse ($recentGrades as $grade)
<div class="p-2.5 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 flex items-center justify-between">
    <div>
        <div class="font-semibold text-on-surface">{{ $grade['course_name'] }}</div>
        <div class="text-[11px] text-on-surface-variant">{{ $grade['credits'] }} tín chỉ • {{ $grade['semester'] }}</div>
    </div>
    <div class="text-right"><span class="px-2 py-0.5 rounded font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">{{ $grade['score'] === null ? '—' : number_format($grade['score'], 1) }} ({{ $grade['letter'] }})</span></div>
</div>
@empty
<p class="text-sm text-on-surface-variant">Chưa có điểm được công bố.</p>
@endforelse
