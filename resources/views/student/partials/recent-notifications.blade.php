@forelse ($notifications['latest'] as $notification)
<a href="{{ route('notifications.page') }}" class="block p-2.5 rounded-lg border border-[#E2E8F0] hover:bg-surface-container-low transition-colors space-y-1">
    <div class="flex items-center justify-between text-[10px]">
        <span class="px-1.5 py-0.5 rounded font-bold bg-[#EEF2FD] text-primary">CHƯA ĐỌC</span>
        <span class="text-on-surface-variant">{{ $notification['sent_at'] ? \Carbon\Carbon::parse($notification['sent_at'])->setTimezone(config('studentmanager.display_timezone'))->format('d/m/Y H:i') : '' }}</span>
    </div>
    <h4 class="text-xs font-semibold text-on-surface hover:text-primary leading-snug line-clamp-2">{{ $notification['title'] }}</h4>
</a>
@empty
<p class="text-sm text-on-surface-variant">Không có thông báo chưa đọc.</p>
@endforelse
