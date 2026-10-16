@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'id' => null])
@php $fieldId = $id ?? $name; $hasError = $errors->has($name); @endphp
<div class="space-y-1.5">
    <label for="{{ $fieldId }}" class="block text-sm font-semibold text-on-surface">{{ $label }}@if($required)<span class="ml-1 text-error" aria-hidden="true">*</span>@endif</label>
    <input id="{{ $fieldId }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" @required($required) @if($hasError) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif {{ $attributes->class(['w-full rounded-lg border bg-surface-container-lowest px-3 py-2 text-sm text-on-surface focus:border-primary focus:ring-primary', $hasError ? 'border-error' : 'border-outline-variant']) }}>
    @error($name)<p id="{{ $fieldId }}-error" class="text-xs text-error" role="alert">{{ $message }}</p>@enderror
</div>
