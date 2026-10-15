@props(['caption' => null])
<div class="w-full overflow-x-auto rounded-xl border border-outline-variant bg-surface-container-lowest" tabindex="0" role="region" aria-label="{{ $caption ?: 'Bảng dữ liệu' }}">
    <table {{ $attributes->class(['w-full min-w-[640px] border-collapse text-left text-sm']) }}>
        @if($caption)<caption class="sr-only">{{ $caption }}</caption>@endif
        {{ $slot }}
    </table>
</div>
