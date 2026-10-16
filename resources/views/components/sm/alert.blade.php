@props(['type' => 'info'])
@php $colors = ['info'=>'border-blue-200 bg-blue-50 text-blue-900','success'=>'border-green-200 bg-green-50 text-green-900','warning'=>'border-amber-200 bg-amber-50 text-amber-900','error'=>'border-red-200 bg-red-50 text-red-900']; @endphp
<div role="{{ $type === 'error' ? 'alert' : 'status' }}" {{ $attributes->class(['rounded-lg border px-4 py-3 text-sm', $colors[$type] ?? $colors['info']]) }}>{{ $slot }}</div>
