{{-- Nút đăng xuất: POST tới route('logout') kèm CSRF. Truyền `class` và `icon` (cỡ biểu tượng) nếu cần. --}}
<form method="POST" action="{{ route('logout') }}" class="shrink-0">
    @csrf
    <button type="submit" title="Đăng xuất" aria-label="Đăng xuất" class="{{ $class ?? 'p-1 text-on-surface-variant hover:text-error transition-colors rounded-lg' }}">
        <span class="material-symbols-outlined {{ $icon ?? 'text-[20px]' }}" aria-hidden="true">logout</span>
    </button>
</form>
