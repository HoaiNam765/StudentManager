@forelse ($blocks as $block)
<div class="p-3 rounded-lg border border-[#E2E8F0] bg-surface-container-low/30 space-y-1">
    <div class="flex justify-between text-xs font-semibold text-on-surface">
        <span>{{ $block['name'] }}</span><span class="text-primary">{{ $block['percent'] }}%</span>
    </div>
    <div class="w-full h-1.5 rounded-full bg-surface-container-high overflow-hidden">
        <div class="h-full bg-primary rounded-full" style="width: {{ $block['percent'] }}%"></div>
    </div>
    <div class="text-[11px] text-on-surface-variant">{{ $block['earned'] }} / {{ $block['required'] }} tín chỉ đã đạt theo bảng điểm công bố</div>
</div>
@empty
<p class="text-sm text-on-surface-variant">Chưa được gán chương trình đào tạo.</p>
@endforelse
