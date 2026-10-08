@extends('layouts.account')

@section('title', 'Tùy chọn thông báo - Hệ thống Quản lý Học vụ - HUIT')
@section('page-title', 'Tùy chọn thông báo')

@section('account')
<div class="w-full max-w-[720px] space-y-5">
    <div>
        <h1 class="text-2xl text-on-surface font-bold tracking-tight">Tùy chọn thông báo</h1>
        <p class="text-sm text-on-surface-variant mt-1.5">Chọn kênh nhận thông báo học vụ.</p>
    </div>

    <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-xs text-amber-900 flex items-start gap-2" role="note">
        <span class="material-symbols-outlined text-[18px] shrink-0" aria-hidden="true">info</span>
        <span>Giao diện mẫu: các công tắc chỉ để xem, chưa lưu vào hệ thống. Chức năng thông báo sẽ nối vào module Thông báo sau.</span>
    </div>

    <ul class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm divide-y divide-surface-container">
        @foreach ($channels as $key => $channel)
            <li class="p-4 sm:p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-primary border border-outline-variant shrink-0">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">{{ $channel['icon'] }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 id="channel-{{ $key }}" class="text-sm text-on-surface font-semibold">{{ $channel['label'] }}</h2>
                    <p class="text-xs text-on-surface-variant mt-0.5">{{ $channel['description'] }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-not-allowed shrink-0">
                    <input type="checkbox" class="sr-only peer" aria-labelledby="channel-{{ $key }}" disabled @checked($channel['enabled'])>
                    <span class="w-11 h-6 rounded-full bg-outline-variant peer-checked:bg-primary opacity-70 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:after:translate-x-5"></span>
                </label>
            </li>
        @endforeach
    </ul>
</div>
@endsection
