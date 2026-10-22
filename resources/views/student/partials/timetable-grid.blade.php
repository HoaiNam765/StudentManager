@if ($schedule['message'])<p role="status" class="p-5 text-secondary">{{ $schedule['message'] }}</p>@endif
@foreach ($weeks as $week)
@php
    $sessions = collect($schedule['sessions'])->filter(fn ($s) => $s['date'] >= $week->toDateString() && $s['date'] <= $week->addDays(6)->toDateString());
    $slots = $sessions->map(fn ($s) => ['from' => $s['period_from'], 'to' => $s['period_to'], 'time' => $s['time']])->unique(fn ($s) => $s['from'].'-'.$s['to'])->sortBy('from');
@endphp
<table class="w-full border-collapse table-fixed text-left min-w-[1180px]">
    <thead><tr class="bg-surface-container-low/60 border-b border-outline-variant divide-x divide-outline-variant/60 text-center font-label-md text-label-md text-secondary">
        <th class="w-32 py-3 px-2 font-semibold text-on-surface-variant bg-surface-container-low">Tiết / Giờ</th>
        @foreach (range(0, 6) as $day)
        <th class="py-3 px-2 font-medium"><div class="text-on-surface font-semibold text-sm">{{ $week->addDays($day)->translatedFormat('l') }}</div><div class="text-xs text-secondary">{{ $week->addDays($day)->format('d/m/Y') }}</div></th>
        @endforeach
    </tr></thead>
    <tbody class="divide-y divide-outline-variant/60 font-body-sm text-body-sm">
        @forelse ($slots as $slot)
        <tr class="divide-x divide-outline-variant/60 h-36">
            <td class="p-2.5 bg-surface-container-lowest align-top"><div class="font-semibold text-on-surface">Tiết {{ $slot['from'] }} – {{ $slot['to'] }}</div><div class="text-secondary text-xs mt-0.5">{{ $slot['time'] }}</div></td>
            @foreach (range(0, 6) as $day)
            <td class="p-2 align-top bg-surface-container-lowest hover:bg-slate-50 transition-colors">
                @foreach ($sessions->filter(fn ($s) => $s['date'] === $week->addDays($day)->toDateString() && $s['period_from'] === $slot['from'] && $s['period_to'] === $slot['to']) as $session)
                <div class="h-full bg-blue-50/90 border-l-4 border-l-blue-600 border border-blue-200 rounded-lg p-2.5 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">{{ $session['status_label'] }}</span><div class="font-headline-sm text-sm font-bold text-blue-900 leading-snug">{{ $session['subject'] }}</div><div class="text-[11px] text-blue-700 font-mono mt-0.5">{{ $session['section'] }}</div></div>
                    <div class="pt-2 border-t border-blue-200/60 space-y-0.5 text-xs text-slate-700"><div>{{ $session['time'] }} • {{ $session['room'] ?? 'Chưa có phòng' }}</div><div>{{ $session['lecturer'] ?? 'Chưa phân công' }}</div>@if ($session['note'])<p>{{ $session['note'] }}</p>@endif</div>
                </div>
                @endforeach
            </td>
            @endforeach
        </tr>
        @empty
        <tr><td colspan="8" class="p-8 text-center text-secondary">Không có buổi học đã công bố trong tuần này.</td></tr>
        @endforelse
    </tbody>
</table>
@endforeach
