@props(['id', 'title' => 'Xác nhận thao tác', 'confirmLabel' => 'Xác nhận'])
<dialog id="{{ $id }}" {{ $attributes->class(['w-[min(92vw,28rem)] rounded-xl border border-outline-variant bg-surface-container-lowest p-6 text-on-surface shadow-xl backdrop:bg-black/40']) }} aria-labelledby="{{ $id }}-title">
    <h2 id="{{ $id }}-title" class="mb-3 text-lg font-semibold">{{ $title }}</h2>
    <div class="mb-6 text-sm">{{ $slot }}</div>
    <form method="dialog" class="flex justify-end gap-2"><x-sm.button variant="secondary" type="submit">Hủy</x-sm.button></form>
    {{-- Real dangerous action belongs in a SEPARATE POST/DELETE form with CSRF and backend authorization. --}}
    @isset($action)<div class="mt-2 flex justify-end">{{ $action }}</div>@endisset
</dialog>
