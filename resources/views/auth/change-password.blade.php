{{--
    Đổi mật khẩu (FR-AUTH-004), giao diện của nhóm FE (issue #6).
    Giữ nguyên với BE: PUT route('password.update'), các trường `current_password`, `password`,
    `password_confirmation`, @csrf, @method('PUT'). Chính sách mật khẩu lấy từ config('studentmanager.auth.password').
--}}
@extends('layouts.account')

@section('title', 'Đổi mật khẩu - Hệ thống Quản lý Học vụ - HUIT')
@section('page-title', 'Đổi mật khẩu')

@php
    $policy = config('studentmanager.auth.password');
    $rules = array_values(array_filter([
        ['key' => 'length', 'text' => "Tối thiểu {$policy['min_length']} ký tự"],
        $policy['mixed_case'] ? ['key' => 'mixed', 'text' => 'Ít nhất 1 chữ hoa (A-Z) và 1 chữ thường (a-z)'] : null,
        $policy['numbers'] ? ['key' => 'number', 'text' => 'Ít nhất 1 chữ số (0-9)'] : null,
        $policy['symbols'] ? ['key' => 'symbol', 'text' => 'Ít nhất 1 ký tự đặc biệt (!@#$%^&*...)'] : null,
    ]));
    $fieldClass = 'w-full h-10 px-3 pr-10 bg-surface-container-lowest border rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all';
@endphp

@section('account')
<!-- Page Title -->
<div class="w-full max-w-[540px] text-center mb-6">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-surface-container text-primary mb-3 shadow-sm border border-outline-variant">
        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">security</span>
    </div>
    <h1 class="text-2xl text-on-surface font-bold tracking-tight">Đổi mật khẩu</h1>
    <p class="text-sm text-on-surface-variant mt-1.5">
        Cập nhật mật khẩu định kỳ để bảo vệ tài khoản và dữ liệu học vụ cá nhân.
    </p>
</div>

<!-- Card -->
<div class="w-full max-w-[540px] bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 sm:p-8">
    <div class="flex items-center gap-3 pb-5 mb-6 border-b border-surface-container">
        <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-primary border border-outline-variant shrink-0">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">lock_clock</span>
        </div>
        <div>
            <h2 class="text-base text-on-surface font-semibold">Thiết lập mật khẩu mới</h2>
            <p class="text-xs text-on-surface-variant">Vui lòng điền đầy đủ các thông tin xác thực bảo mật bên dưới</p>
        </div>
    </div>

    <form action="{{ route('password.update') }}" method="POST" class="space-y-5" id="password-form">
        @csrf
        @method('PUT')

        <!-- Current Password -->
        <div>
            <label for="current-password" class="block text-xs text-on-surface font-semibold mb-1.5">
                Mật khẩu hiện tại <span class="text-error">*</span>
            </label>
            <div class="relative">
                <input type="password" id="current-password" name="current_password" required autocomplete="current-password" placeholder="Nhập mật khẩu đang sử dụng"
                    class="{{ $fieldClass }} {{ $errors->has('current_password') ? 'border-error' : 'border-outline-variant' }}">
                <button type="button" data-toggle-password="current-password" aria-label="Hiện hoặc ẩn mật khẩu" class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">visibility</span>
                </button>
            </div>
            @error('current_password') <p class="text-xs text-error mt-1.5" role="alert">{{ $message }}</p> @enderror
        </div>

        <!-- New Password -->
        <div>
            <label for="new-password" class="block text-xs text-on-surface font-semibold mb-1.5">
                Mật khẩu mới <span class="text-error">*</span>
            </label>
            <div class="relative">
                <input type="password" id="new-password" name="password" required autocomplete="new-password" placeholder="Nhập mật khẩu mới"
                    class="{{ $fieldClass }} {{ $errors->has('password') ? 'border-error' : 'border-outline-variant' }}">
                <button type="button" data-toggle-password="new-password" aria-label="Hiện hoặc ẩn mật khẩu" class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">visibility</span>
                </button>
            </div>
            @error('password') <p class="text-xs text-error mt-1.5" role="alert">{{ $message }}</p> @enderror

            <!-- Strength Meter: tính theo các tiêu chuẩn bên dưới, cập nhật khi gõ -->
            <div class="mt-2.5 bg-surface-container-low p-2.5 rounded-lg border border-outline-variant/60">
                <div class="flex items-center justify-between mb-1.5 gap-2">
                    <span class="text-xs text-on-surface-variant">Độ mạnh mật khẩu:</span>
                    <span id="strength-label" class="text-xs font-semibold text-on-surface-variant">Chưa nhập</span>
                </div>
                <div class="grid grid-cols-4 gap-1.5 h-1.5 w-full" aria-hidden="true">
                    <div data-strength-bar class="bg-outline-variant/50 rounded-full h-full"></div>
                    <div data-strength-bar class="bg-outline-variant/50 rounded-full h-full"></div>
                    <div data-strength-bar class="bg-outline-variant/50 rounded-full h-full"></div>
                    <div data-strength-bar class="bg-outline-variant/50 rounded-full h-full"></div>
                </div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="confirm-password" class="block text-xs text-on-surface font-semibold mb-1.5">
                Xác nhận mật khẩu mới <span class="text-error">*</span>
            </label>
            <div class="relative">
                <input type="password" id="confirm-password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại chính xác mật khẩu mới"
                    class="{{ $fieldClass }} border-outline-variant">
                <button type="button" data-toggle-password="confirm-password" aria-label="Hiện hoặc ẩn mật khẩu" class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">visibility</span>
                </button>
            </div>
            <p id="confirm-hint" class="text-xs text-error mt-1.5 hidden" role="alert">Mật khẩu xác nhận chưa trùng khớp.</p>
        </div>

        <!-- Checklist -->
        <div class="pt-2">
            <div class="flex items-center gap-1.5 mb-2.5">
                <span class="material-symbols-outlined text-primary text-[18px]" aria-hidden="true">verified</span>
                <span class="text-xs text-on-surface font-semibold">Tiêu chuẩn mật khẩu HUIT:</span>
            </div>
            <ul class="space-y-2 bg-surface-container-low/70 p-3.5 rounded-lg border border-outline-variant/60 text-xs text-on-surface">
                @foreach ($rules as $rule)
                    <li data-rule="{{ $rule['key'] }}" class="flex items-center gap-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-outline text-[16px]" aria-hidden="true">radio_button_unchecked</span>
                        <span>{{ $rule['text'] }}</span>
                    </li>
                @endforeach
                <li class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-outline text-[16px]" aria-hidden="true">history</span>
                    <span>Không trùng {{ $policy['history'] }} mật khẩu gần nhất (hệ thống kiểm tra khi lưu)</span>
                </li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="pt-3 flex flex-col gap-3">
            <button type="submit" class="w-full h-11 bg-primary hover:bg-primary-container text-white font-semibold text-sm rounded-lg shadow-sm flex items-center justify-center gap-2 transition-all active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">save</span>
                <span>Cập nhật mật khẩu</span>
            </button>
            <a href="{{ route('root') }}" class="w-full text-center py-1.5 text-xs text-on-surface-variant hover:text-on-surface transition-colors">
                Hủy bỏ và quay lại
            </a>
        </div>
    </form>
</div>

<!-- Notice -->
<div class="w-full max-w-[540px] mt-4 bg-surface-container-low border border-outline-variant rounded-xl p-4 flex gap-3 items-start shadow-xs">
    <span class="material-symbols-outlined text-primary text-[22px] flex-shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">info</span>
    <div class="text-xs text-on-surface leading-relaxed">
        <span class="font-semibold text-primary">Lưu ý bảo mật:</span> Sau khi đổi mật khẩu thành công, các phiên đăng nhập khác của bạn sẽ tự động đăng xuất để đảm bảo an toàn tài khoản.
    </div>
</div>

<script>
    (function () {
        const minLength = {{ (int) $policy['min_length'] }};
        const newPassword = document.getElementById('new-password');
        const confirmPassword = document.getElementById('confirm-password');
        const confirmHint = document.getElementById('confirm-hint');
        const label = document.getElementById('strength-label');
        const bars = document.querySelectorAll('[data-strength-bar]');
        const levels = [
            { text: 'Chưa nhập', label: 'text-on-surface-variant', bar: '' },
            { text: 'Yếu', label: 'text-error', bar: 'bg-error' },
            { text: 'Trung bình', label: 'text-amber-600', bar: 'bg-amber-500' },
            { text: 'Khá', label: 'text-primary', bar: 'bg-primary' },
            { text: 'Mạnh', label: 'text-emerald-600', bar: 'bg-emerald-500' },
        ];
        const labelColors = levels.map(function (level) { return level.label; });
        const barColors = ['bg-error', 'bg-amber-500', 'bg-primary', 'bg-emerald-500'];

        const checks = {
            length: function (value) { return value.length >= minLength; },
            mixed: function (value) { return /[a-z]/.test(value) && /[A-Z]/.test(value); },
            number: function (value) { return /[0-9]/.test(value); },
            symbol: function (value) { return /[^A-Za-z0-9]/.test(value); },
        };

        function paintRules(value) {
            document.querySelectorAll('[data-rule]').forEach(function (item) {
                const ok = value !== '' && checks[item.dataset.rule](value);
                const icon = item.querySelector('.material-symbols-outlined');
                icon.textContent = ok ? 'check_circle' : 'radio_button_unchecked';
                icon.classList.toggle('text-primary', ok);
                icon.classList.toggle('text-outline', !ok);
                item.classList.toggle('text-on-surface', ok);
                item.classList.toggle('text-on-surface-variant', !ok);
            });
        }

        function paintStrength(value) {
            const active = Object.keys(checks).filter(function (key) { return checks[key](value); }).length;
            const score = value === '' ? 0 : Math.max(1, Math.min(4, active + (value.length >= 12 ? 1 : 0) - (checks.length(value) ? 0 : 1)));
            const level = levels[score];
            label.textContent = level.text;
            labelColors.forEach(function (cls) { label.classList.remove(cls); });
            label.classList.add(level.label);
            bars.forEach(function (bar, index) {
                barColors.forEach(function (cls) { bar.classList.remove(cls); });
                bar.classList.toggle('bg-outline-variant/50', index >= score);
                if (index < score) bar.classList.add(level.bar);
            });
        }

        function paintConfirm() {
            const mismatch = confirmPassword.value !== '' && confirmPassword.value !== newPassword.value;
            confirmHint.classList.toggle('hidden', !mismatch);
        }

        newPassword.addEventListener('input', function () {
            paintRules(newPassword.value);
            paintStrength(newPassword.value);
            paintConfirm();
        });
        confirmPassword.addEventListener('input', paintConfirm);

        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.togglePassword);
                const icon = button.querySelector('.material-symbols-outlined');
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.textContent = show ? 'visibility_off' : 'visibility';
            });
        });
    })();
</script>
@endsection
