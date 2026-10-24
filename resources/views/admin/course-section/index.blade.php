@extends('layouts.portal')

@section('title', 'Quản lý lớp học phần - Quản lý Đào tạo')
@section('page-title', 'Lớp học phần')

@section('page')
<div data-page="admin-course-sections" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col xl:flex-row xl:items-end justify-between gap-4">
        <div>
            <h1 class="text-headline-lg font-bold text-on-surface">Quản lý lớp học phần</h1>
            <p class="mt-1 text-sm text-on-surface-variant">Mở lớp theo học kỳ, phân công giảng viên, chuyển trạng thái và xử lý lớp thiếu sĩ số.</p>
        </div>
        <div class="grid sm:grid-cols-3 gap-2 w-full xl:w-auto">
            <label class="text-xs font-semibold text-on-surface-variant">Học kỳ
                <select data-filter="term" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Trạng thái
                <select data-filter="status" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm">
                    <option value="">Tất cả</option>
                    <option value="planned">Dự kiến</option>
                    <option value="registration_open">Mở đăng ký</option>
                    <option value="registration_closed">Đóng đăng ký</option>
                    <option value="in_progress">Đang học</option>
                    <option value="finished">Kết thúc</option>
                    <option value="locked">Đã khóa</option>
                    <option value="cancelled">Đã hủy</option>
                </select>
            </label>
            <label class="text-xs font-semibold text-on-surface-variant">Tìm kiếm
                <input data-filter="q" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm" placeholder="Mã lớp / học phần">
            </label>
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <div class="grid xl:grid-cols-[minmax(0,1.25fr)_minmax(360px,.75fr)] gap-6 items-start">
        <div class="space-y-6">
            @if (($pageData['can']['create'] ?? false))
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-headline-sm font-bold">Mở lớp học phần mới</h2>
                        <p class="text-xs text-on-surface-variant mt-1">Mã lớp do hệ thống tự sinh.</p>
                    </div>
                </div>
                <form data-form="create" class="grid md:grid-cols-2 xl:grid-cols-3 gap-3">
                    <label class="text-sm">Học kỳ<select name="term_id" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                    <label class="text-sm">Học phần<select name="subject_id" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                    <label class="text-sm">Loại lớp<select name="section_type" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"><option value="theory">Lý thuyết</option><option value="practice">Thực hành</option><option value="mixed">Kết hợp</option></select></label>
                    <label class="text-sm">Hình thức<select name="delivery_mode" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"><option value="in_person">Trực tiếp</option><option value="online">Trực tuyến</option><option value="hybrid">Kết hợp</option></select></label>
                    <label class="text-sm">Sĩ số tối thiểu<input name="min_size" type="number" min="1" value="15" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                    <label class="text-sm">Sĩ số tối đa<input name="max_size" type="number" min="1" value="60" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></label>
                    <label class="text-sm md:col-span-2 xl:col-span-3">Ghi chú<textarea name="note" rows="2" maxlength="500" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></textarea></label>
                    <div class="md:col-span-2 xl:col-span-3 flex justify-end"><button type="submit" class="rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold">Mở lớp</button></div>
                </form>
            </section>
            @endif

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
                <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-3">
                    <h2 class="text-headline-sm font-bold">Danh sách lớp học phần</h2>
                    <button type="button" data-action="reload" class="rounded-lg border border-outline-variant px-3 py-2 text-sm font-semibold hover:bg-surface-container-low">Làm mới</button>
                </div>
                <div data-region="list" class="p-4"></div>
            </section>

            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
                <div class="px-5 py-4 border-b border-outline-variant">
                    <h2 class="text-headline-sm font-bold">Lớp thiếu sĩ số</h2>
                    <p class="text-xs text-on-surface-variant mt-1">Dữ liệu theo học kỳ đang chọn; có thể hủy hoặc gộp vào lớp cùng học phần còn đủ chỗ.</p>
                </div>
                <div data-region="under-min" class="p-4"></div>
            </section>
        </div>

        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm xl:sticky xl:top-20">
            <div class="px-5 py-4 border-b border-outline-variant"><h2 class="text-headline-sm font-bold">Chi tiết lớp</h2></div>
            <div data-region="detail" class="p-5"></div>
        </section>
    </div>
</div>
@endsection
