@props(['title' => null])
<section {{ $attributes->class(['rounded-xl border border-outline-variant bg-surface-container-lowest p-4 md:p-6']) }}>
    @if ($title)<h2 class="mb-4 text-lg font-semibold text-on-surface">{{ $title }}</h2>@endif
    {{ $slot }}
</section>
