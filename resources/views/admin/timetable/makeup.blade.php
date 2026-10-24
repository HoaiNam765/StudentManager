@extends('layouts.portal')

@section('title', 'Dạy bù & phòng học - Quản lý Đào tạo')
@section('page-title', 'Dạy bù & phòng học')

@section('page')
<div data-page="admin-makeup" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
        <h1 class="text-headline-lg font-bold">Nghỉ, dạy bù & lịch phòng</h1>
        <p class="mt-1 text-sm text-on-surface-variant">Trang này nối phần backend đã có: sinh buổi, hủy buổi và xếp dạy bù. Luồng “giảng viên gửi đề xuất → ACAD duyệt” chưa có API trong repo hiện tại nên không được giả lập ở đây.</p>
        <div class="mt-4 grid sm:grid-cols-2 gap-3 max-w-3xl">
            <label class="text-xs font-semibold text-on-surface-variant">Học kỳ<select data-filter="term" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select></label>
            <label class="text-xs font-semibold text-on-surface-variant">Lớp học phần<select data-filter="section" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select></label>
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <div class="grid xl:grid-cols-[minmax(0,1.15fr)_minmax(340px,.85fr)] gap-6 items-start">
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
            <div class="px-5 py-4 border-b border-outline-variant flex flex-wrap items-center justify-between gap-3">
                <div><h2 class="text-headline-sm font-bold">Các buổi học</h2><p data-field="session-summary" class="text-xs text-on-surface-variant mt-1"></p></div>
                <div class="flex gap-2">@if (($pageData['can']['update'] ?? false))<button type="button" data-action="generate" class="rounded-lg border border-primary text-primary px-3 py-2 text-xs font-semibold">Sinh lại buổi học</button>@endif<button type="button" data-action="reload" class="rounded-lg border border-outline-variant px-3 py-2 text-xs font-semibold">Làm mới</button></div>
            </div>
            <div data-region="sessions" class="p-4"></div>
        </section>

        <div class="space-y-6">
            @if (($pageData['can']['create'] ?? false))
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
                <h2 class="text-headline-sm font-bold">Xếp buổi dạy bù</h2>
                <p class="text-xs text-on-surface-variant mt-1">Chọn nút “Dạy bù” ở một buổi Nghỉ/Hủy trước.</p>
                <form data-form="makeup" class="mt-4 grid sm:grid-cols-2 gap-3">
                    <input type="hidden" name="session_id">
                    <label class="sm:col-span-2 text-sm">Buổi gốc<input data-field="source-session" readonly class="mt-1 w-full rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2"></label>
                    <label class="text-sm">Ngày<input name="date" type="date" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                    <label class="text-sm">Phòng<select name="room_id" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                    <label class="text-sm">Tiết từ<select name="period_from" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                    <label class="text-sm">Tiết đến<select name="period_to" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                    <label class="sm:col-span-2 text-sm">Ghi chú<input name="note" maxlength="500" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                    <div class="sm:col-span-2 flex justify-end"><button type="submit" disabled class="rounded-lg bg-primary disabled:opacity-50 text-white px-4 py-2 text-sm font-semibold">Xếp dạy bù</button></div>
                </form>
            </section>
            @endif

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
                <h2 class="text-headline-sm font-bold">Tra cứu lịch sử dụng phòng</h2>
                <div class="mt-3 grid sm:grid-cols-2 gap-2"><label class="text-sm">Phòng<select data-room-filter class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label><label class="text-sm">Ngày<input data-room-date type="date" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label></div>
                <button type="button" data-action="room-schedule" class="mt-3 rounded-lg border border-outline-variant px-3 py-2 text-xs font-semibold">Xem lịch phòng</button>
                <div data-region="room-schedule" class="mt-3"></div>
            </section>
        </div>
    </div>
</div>
@endsection
