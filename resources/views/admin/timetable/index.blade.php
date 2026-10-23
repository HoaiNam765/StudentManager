@extends('layouts.portal')

@section('title', 'Xếp thời khóa biểu - Quản lý Đào tạo')
@section('page-title', 'Xếp thời khóa biểu')

@section('page')
<div data-page="admin-timetable" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
        <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-4">
            <div>
                <h1 class="text-headline-lg font-bold">Xếp thời khóa biểu</h1>
                <p class="mt-1 text-sm text-on-surface-variant">Gán thứ, tiết, phòng và tuần học; kiểm tra xung đột trước khi công bố.</p>
            </div>
            <div class="grid sm:grid-cols-2 gap-2 min-w-[min(100%,560px)]">
                <label class="text-xs font-semibold text-on-surface-variant">Học kỳ<select data-filter="term" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select></label>
                <label class="text-xs font-semibold text-on-surface-variant">Lớp học phần<select data-filter="section" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select></label>
            </div>
        </div>
        <div data-region="term-status" class="mt-4"></div>
    </section>

    <div data-region="alert" hidden></div>

    <div class="grid xl:grid-cols-[minmax(0,.8fr)_minmax(0,1.2fr)] gap-6 items-start">
        <div class="space-y-6">
            @if (($pageData['can']['create'] ?? false) || ($pageData['can']['update'] ?? false))
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div><h2 class="text-headline-sm font-bold" data-form-title>Lịch học của lớp</h2><p class="text-xs text-on-surface-variant mt-1">Tuần 1 là tuần chứa ngày bắt đầu học kỳ.</p></div>
                    <button type="button" data-action="cancel-edit" hidden class="text-xs text-primary hover:underline">Hủy sửa</button>
                </div>
                <form data-form="rule" class="grid sm:grid-cols-2 gap-3">
                    <label class="text-sm">Thứ<select name="day_of_week" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"><option value="1">Thứ Hai</option><option value="2">Thứ Ba</option><option value="3">Thứ Tư</option><option value="4">Thứ Năm</option><option value="5">Thứ Sáu</option><option value="6">Thứ Bảy</option><option value="7">Chủ nhật</option></select></label>
                    <label class="text-sm">Phòng<select name="room_id" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                    <label class="text-sm">Tiết bắt đầu<select name="period_from" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                    <label class="text-sm">Tiết kết thúc<select name="period_to" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                    <label class="text-sm">Tuần từ<input name="week_from" type="number" min="1" value="1" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                    <label class="text-sm">Tuần đến<input name="week_to" type="number" min="1" value="15" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                    <label class="text-sm">Mẫu tuần<select name="week_parity" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"><option value="all">Mọi tuần</option><option value="odd">Tuần lẻ</option><option value="even">Tuần chẵn</option></select></label>
                    <label class="text-sm">Ghi chú<input name="note" maxlength="500" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                    <label class="sm:col-span-2 text-sm">Lý do thay đổi sau công bố<input name="reason" maxlength="500" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2" placeholder="Chỉ cần khi lịch đã công bố"></label>
                    <label class="sm:col-span-2 flex items-center gap-2 text-xs"><input type="checkbox" name="urgent" value="1"> Thay đổi khẩn cấp (chỉ dùng khi không đủ thời gian báo trước)</label>
                    <div class="sm:col-span-2 flex justify-end"><button type="submit" class="rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold">Lưu lịch</button></div>
                </form>
            </section>
            @endif

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
                <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-2"><h2 class="text-headline-sm font-bold">Xung đột</h2><button type="button" data-action="check-conflicts" class="rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold">Kiểm tra lại</button></div>
                <div data-region="conflicts" class="p-4"></div>
            </section>
        </div>

        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
            <div class="px-5 py-4 border-b border-outline-variant flex flex-wrap items-center justify-between gap-3">
                <div><h2 class="text-headline-sm font-bold">Lịch đã xếp</h2><p data-field="summary" class="text-xs text-on-surface-variant mt-1"></p></div>
                <div class="flex flex-wrap gap-2">
                    @if (($pageData['can']['update'] ?? false))<button type="button" data-action="generate" class="rounded-lg border border-primary text-primary px-3 py-2 text-xs font-semibold">Sinh buổi học</button>@endif
                    <button type="button" data-action="reload" class="rounded-lg border border-outline-variant px-3 py-2 text-xs font-semibold">Làm mới</button>
                </div>
            </div>
            <div data-region="rules" class="p-4"></div>
        </section>
    </div>
</div>
@endsection
