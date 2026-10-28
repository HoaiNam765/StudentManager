@extends('layouts.portal')

@section('title', 'Phiên đăng nhập - Hệ thống Quản lý Học vụ - HUIT')
@section('page-title', 'Phiên đăng nhập')

@use('App\Support\Format')

@php
    $deviceIcons = ['desktop' => 'computer', 'mobile' => 'smartphone', 'tablet' => 'tablet_mac'];
    $hasOtherSessions = $sessions->contains(fn ($session) => ! $session['is_current']);
@endphp

@section('page')
<div class="w-full max-w-[720px] space-y-5">
    <div>
        <h1 class="text-2xl text-on-surface font-bold tracking-tight">Phiên đăng nhập</h1>
        <p class="text-sm text-on-surface-variant mt-1.5">Các thiết bị đang đăng nhập vào tài khoản của bạn. Phiên tự hết hạn sau {{ (int) config('session.lifetime') }} phút không hoạt động.</p>
    </div>

    @error('business_rule')
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800" role="alert">{{ $message }}</div>
    @enderror

    <ul class="space-y-3">
        @forelse ($sessions as $session)
            <li class="bg-surface-container-lowest rounded-xl border {{ $session['is_current'] ? 'border-primary/40' : 'border-outline-variant' }} shadow-sm p-4 sm:p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-primary border border-outline-variant shrink-0">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">{{ $deviceIcons[$session['device_type']] ?? 'devices' }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-sm text-on-surface font-semibold break-words">{{ $session['device'] }}</h2>
                        @if ($session['is_current'])
                            <span class="inline-flex rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 text-[11px] font-semibold">Phiên hiện tại</span>
                        @endif
                    </div>
                    <p class="text-xs text-on-surface-variant mt-1 break-all">{{ $session['user_agent'] ?? 'Không rõ trình duyệt' }}</p>
                    <p class="text-xs text-on-surface-variant mt-0.5">IP {{ $session['ip_address'] ?? 'không rõ' }}</p>
                    <p class="text-xs text-on-surface mt-1.5 font-medium">
                        Đăng nhập: {{ $session['logged_in_at'] ? Format::dateTime($session['logged_in_at']) : 'không rõ' }}
                        • Hoạt động gần nhất: {{ Format::dateTime($session['last_activity_at']) }}
                    </p>
                </div>
                @unless ($session['is_current'])
                    <form method="POST" action="{{ route('account.sessions.destroy', $session['handle']) }}" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 h-9 px-3 rounded-lg border border-outline-variant text-xs font-semibold text-on-surface hover:bg-surface-container-low">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">logout</span>
                            Thu hồi
                        </button>
                    </form>
                @endunless
            </li>
        @empty
            <li class="text-sm text-on-surface-variant">Không có phiên nào đang hoạt động.</li>
        @endforelse
    </ul>

    <form method="POST" action="{{ route('account.sessions.destroy-others') }}">
        @csrf
        @method('DELETE')
        <button type="submit" @disabled(! $hasOtherSessions) class="inline-flex items-center gap-2 h-10 px-4 rounded-lg border border-outline-variant text-sm font-semibold {{ $hasOtherSessions ? 'text-on-surface hover:bg-surface-container-low' : 'text-on-surface-variant bg-surface-container-low cursor-not-allowed' }}">
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">logout</span>
            Đăng xuất khỏi các thiết bị khác
        </button>
    </form>

    <div class="pt-2">
        <h2 class="text-base text-on-surface font-semibold">Lịch sử đăng nhập gần đây</h2>
        <p class="text-xs text-on-surface-variant mt-1">{{ $history->count() }} lần gần nhất, gồm cả lần thất bại. Thấy lần nào không phải bạn thì đổi mật khẩu ngay.</p>
        <ul class="mt-3 divide-y divide-outline-variant rounded-xl border border-outline-variant bg-surface-container-lowest">
            @forelse ($history as $entry)
                <li class="px-4 py-3 flex items-start justify-between gap-3 text-xs">
                    <div class="min-w-0">
                        <p class="text-on-surface font-medium">
                            {{ $entry->device }} • IP {{ $entry->ip_address ?? 'không rõ' }}
                            @if ($entry->unusual)
                                <span class="ml-1 inline-flex rounded-full bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 text-[11px] font-semibold">Thiết bị lạ</span>
                            @endif
                        </p>
                        <p class="text-on-surface-variant mt-0.5">{{ Format::dateTime($entry->created_at) }}</p>
                    </div>
                    @if ($entry->successful)
                        <span class="shrink-0 text-emerald-700 font-semibold">Thành công</span>
                    @else
                        <span class="shrink-0 text-red-700 font-semibold text-right">Thất bại<br><span class="font-normal">{{ $entry->failure_reason }}</span></span>
                    @endif
                </li>
            @empty
                <li class="px-4 py-3 text-xs text-on-surface-variant">Chưa có lịch sử đăng nhập.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
