@props(['variant' => 'primary', 'type' => 'button', 'size' => 'md'])
@php
    $variants = [
        'primary' => 'bg-primary text-on-primary hover:opacity-90 focus-visible:ring-primary',
        'secondary' => 'bg-surface-container-low text-on-surface hover:bg-surface-container focus-visible:ring-primary',
        'danger' => 'bg-error text-on-error hover:opacity-90 focus-visible:ring-error',
        'ghost' => 'bg-transparent text-primary hover:bg-surface-container-low focus-visible:ring-primary',
    ];
    $sizes = ['sm' => 'px-3 py-1.5 text-xs', 'md' => 'px-4 py-2 text-sm', 'lg' => 'px-5 py-3 text-base'];
@endphp
<button type="{{ $type }}" {{ $attributes->class(['inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50', $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md']]) }}>{{ $slot }}</button>
