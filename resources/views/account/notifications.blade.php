@extends('layouts.portal')

@section('title', 'Tùy chọn thông báo - Hệ thống Quản lý Học vụ - HUIT')
@section('page-title', 'Tùy chọn thông báo')

{{--
    Tùy chọn nhận thông báo (FR-NOT-007; BR-NOT-02): bật / tắt từng loại thông báo không bắt buộc trong ứng dụng.
    Dữ liệu: resources/js/pages/notification-preferences.js gọi GET / PUT /notifications/preferences.
--}}
@section('page')
<div data-page="notification-preferences" class="w-full max-w-[720px] space-y-5">
    <div>
        <h1 class="text-2xl text-on-surface font-bold tracking-tight">Tùy chọn thông báo</h1>
        <p class="text-sm text-on-surface-variant mt-1.5">Chọn loại thông báo bạn muốn nhận trong hệ thống. Thông báo bắt buộc (bảo mật, học vụ, học phí, thay đổi lịch) luôn được gửi.</p>
    </div>

    <div class="rounded-lg bg-[#EEF2FD] border border-primary/30 px-4 py-3 text-xs text-on-surface flex items-start gap-2" role="note">
        <span class="material-symbols-outlined text-[18px] shrink-0 text-primary" aria-hidden="true">info</span>
        <span>Hiện hệ thống gửi thông báo trong ứng dụng (chuông thông báo). Email, SMS sẽ có ở giai đoạn sau.</span>
    </div>

    <div data-region="alert" hidden></div>
    <ul data-region="list" class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm divide-y divide-surface-container"></ul>
</div>

<script type="application/json" id="page-data">@json(['api' => ['preferences' => route('notifications.preferences'), 'save' => route('notifications.preferences.save')]])</script>
@endsection
