@props(['title', 'description' => null])
<header {{ $attributes->class(['mb-6 flex flex-wrap items-start justify-between gap-4']) }}>
    <div><h1 class="text-2xl font-bold tracking-tight text-on-surface md:text-3xl">{{ $title }}</h1>@if($description)<p class="mt-2 text-sm text-on-surface-variant">{{ $description }}</p>@endif</div>
    @isset($actions)<div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>@endisset
</header>
