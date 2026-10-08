@extends('layouts.account')

@section('title', 'Phiên đăng nhập - Hệ thống Quản lý Học vụ - HUIT')
@section('page-title', 'Phiên đăng nhập')

@php
    $deviceIcons = ['desktop' => 'computer', 'mobile' => 'smartphone', 'tablet' => 'tablet_mac'];
@endphp

@section('account')
<div class="w-full max-w-[720px] space-y-5">
    <div>
        <h1 class="text-2xl text-on-surface font-bold tracking-tight">Phiên đăng nhập</h1>
        <p class="text-sm text-on-surface-variant mt-1.5">Các thiết bị đã đăng nhập vào tài khoản của bạn.</p>
    </div>

    <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-xs text-amber-900 flex items-start gap-2" role="note">
        <span class="material-symbols-outlined text-[18px] shrink-0" aria-hidden="true">info</span>
        <span>Danh sách dưới đây là dữ liệu mẫu để xem giao diện; chưa lấy từ phiên thật. Khi đổi mật khẩu, hệ thống đã tự đăng xuất các phiên khác.</span>
    </div>

    <ul class="space-y-3">
        @foreach ($sessions as $session)
            <li class="bg-surface-container-lowest rounded-xl border {{ $session['is_current'] ? 'border-primary/40' : 'border-outline-variant' }} shadow-sm p-4 sm:p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-primary border border-outline-variant shrink-0">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">{{ $deviceIcons[$session['device_type']] ?? 'devices' }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-sm text-on-surface font-semibold break-words">{{ $session['device_name'] }}</h2>
                        @if ($session['is_current'])
                            <span class="inline-flex rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 text-[11px] font-semibold">Phiên hiện tại</span>
                        @endif
                    </div>
                    <p class="text-xs text-on-surface-variant mt-1">{{ $session['browser'] }}</p>
                    <p class="text-xs text-on-surface-variant mt-0.5">{{ $session['location'] }} • {{ $session['ip_address'] }} • {{ $session['network'] }}</p>
                    <p class="text-xs text-on-surface mt-1.5 font-medium">{{ $session['login_time'] }}</p>
                </div>
            </li>
        @endforeach
    </ul>

    <button type="button" disabled title="Chưa hoạt động, sẽ có khi tính năng phiên đăng nhập được xây dựng" class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-outline-variant text-sm font-semibold text-on-surface-variant bg-surface-container-low cursor-not-allowed">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">logout</span>
        Đăng xuất khỏi các thiết bị khác
    </button>
</div>
@endsection
