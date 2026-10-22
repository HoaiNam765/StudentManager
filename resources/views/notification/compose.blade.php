{{--
    Soạn thông báo và lịch sử đã gửi (FR-NOT-006, 009, 015; FE #30 quản trị, #31 giảng viên). Dữ liệu:
    resources/js/pages/announcement-compose.js gọi API announcements/* và recipient-groups/* của cổng hiện tại.
    Danh sách đối tượng chỉ gồm những gì người gửi được chọn; máy chủ kiểm tra lại phạm vi khi xem trước và khi gửi.
--}}
@extends('layouts.portal')

@section('title', 'Gửi thông báo')
@section('page-title', 'Gửi thông báo')

@section('page')
@php($field = 'w-full px-3 py-2 border border-outline-variant rounded-lg text-body-md text-on-surface bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20')
<div data-page="announcement-compose" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 space-y-1">
        <h1 class="text-headline-lg font-bold text-on-surface">Gửi thông báo</h1>
        <p class="text-body-md text-on-surface-variant">Chọn đối tượng trong phạm vi của bạn, xem trước số người nhận rồi gửi ngay hoặc hẹn giờ. Không đưa điểm, số nợ, CCCD vào nội dung.</p>
    </section>

    <div data-region="alert" hidden></div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <form data-form="compose" class="xl:col-span-3 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 space-y-5" novalidate>
            <fieldset class="space-y-3">
                <legend class="text-headline-sm font-bold text-on-surface">1. Đối tượng nhận</legend>
                <div class="flex flex-wrap items-end gap-2">
                    <label class="text-label-md text-on-surface-variant flex flex-col gap-1">
                        Loại
                        <select data-input="type" class="{{ $field }} min-w-[150px]"></select>
                    </label>
                    <label class="text-label-md text-on-surface-variant flex flex-col gap-1 flex-1 min-w-[220px]">
                        Đối tượng
                        <select data-input="target" class="{{ $field }}"></select>
                    </label>
                    <button type="button" data-action="add-target" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold hover:bg-surface-container-low inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span> Thêm
                    </button>
                </div>
                <label class="block text-label-md text-on-surface-variant">
                    Danh sách MSSV (mỗi dòng hoặc cách nhau dấu phẩy)
                    <textarea data-input="codes" rows="2" class="{{ $field }} mt-1" placeholder="2602010001, 2602010002"></textarea>
                </label>
                <div data-region="chips" class="flex flex-wrap gap-2"></div>
                <div class="flex flex-wrap items-center gap-2">
                    <select data-input="group" class="{{ $field }} max-w-xs"><option value="">— Dùng nhóm đã lưu —</option></select>
                    <button type="button" data-action="save-group" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold hover:bg-surface-container-low">Lưu thành nhóm</button>
                    <button type="button" data-action="delete-group" class="px-3 py-2 rounded-lg border border-red-300 text-red-800 text-label-md font-semibold hover:bg-red-50">Xóa nhóm</button>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" data-action="preview" class="px-3 py-2 rounded-lg border border-primary text-primary text-label-md font-semibold hover:bg-[#EEF2FD] inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">groups</span> Xem trước số người nhận
                    </button>
                    <span data-field="preview" class="text-sm text-on-surface-variant"></span>
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-headline-sm font-bold text-on-surface">2. Nội dung</legend>
                <label class="block text-label-md text-on-surface-variant">Tiêu đề
                    <input name="title" maxlength="200" required class="{{ $field }} mt-1">
                </label>
                <label class="block text-label-md text-on-surface-variant">Nội dung
                    <textarea name="body" rows="6" maxlength="5000" required class="{{ $field }} mt-1"></textarea>
                </label>
                <label class="block text-label-md text-on-surface-variant">Tệp đính kèm (PDF, Word, Excel, ảnh; tối đa 5 MB)
                    <input name="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" class="mt-1 block text-sm">
                </label>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-headline-sm font-bold text-on-surface">3. Gửi</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="block text-label-md text-on-surface-variant">Hẹn giờ gửi (để trống = gửi ngay)
                        <input name="scheduled_at" type="datetime-local" class="{{ $field }} mt-1">
                    </label>
                    <label class="block text-label-md text-on-surface-variant">Hạn hiển thị (để trống = không hết hạn)
                        <input name="expires_at" type="datetime-local" class="{{ $field }} mt-1">
                    </label>
                </div>
                <div class="flex flex-wrap gap-5 text-sm text-on-surface">
                    <label class="inline-flex items-center gap-2"><input name="pinned" type="checkbox" class="w-4 h-4"> Ghim lên đầu</label>
                    <label class="inline-flex items-center gap-2"><input name="urgent" type="checkbox" class="w-4 h-4"> Khẩn</label>
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-lg bg-primary hover:bg-primary-container text-white text-label-md font-semibold inline-flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">send</span> Gửi thông báo
                </button>
            </fieldset>
        </form>

        <section class="xl:col-span-2 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
            <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-2">
                <h2 class="text-headline-sm font-bold text-on-surface">Đã gửi</h2>
                <button type="button" data-action="reload-history" class="text-label-md text-primary font-semibold">Làm mới</button>
            </div>
            <div data-region="history" class="divide-y divide-outline-variant"></div>
        </section>
    </div>
</div>
@endsection
