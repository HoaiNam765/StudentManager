@props(['status' => 'neutral'])
@php
    $variants = [
        'success' => 'bg-green-50 text-green-800 border-green-200',
        'warning' => 'bg-amber-50 text-amber-900 border-amber-200',
        'danger' => 'bg-red-50 text-red-800 border-red-200',
        'info' => 'bg-blue-50 text-blue-800 border-blue-200',
        'neutral' => 'bg-surface-container text-on-surface border-outline-variant',
    ];
@endphp
<span {{ $attributes->class(['inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-semibold', $variants[$status] ?? $variants['neutral']]) }}>{{ $slot }}</span>
