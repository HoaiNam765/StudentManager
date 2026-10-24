{{--
    #9 — Quản trị năm học, học kỳ, lịch học vụ và ngày nghỉ.
    Dữ liệu do resources/js/pages/admin-academic-year.js gọi API ACY trong routes/admin.php.
--}}
@extends('layouts.portal')

@section('title', 'Năm học & Học kỳ - Quản lý Đào tạo')
@section('page-title', 'Năm học & Học kỳ')

@section('page')
<div data-page="admin-academic-year" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div class="space-y-1 min-w-0">
            <h1 class="text-headline-lg font-bold text-on-surface">Năm học, học kỳ & lịch học vụ</h1>
            <p class="text-body-md text-on-surface-variant">Khởi tạo học kỳ mới, đặt học kỳ hiện hành, cấu hình các mốc học vụ và ngày nghỉ bằng dữ liệu thật của hệ thống.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2 text-sm">
                <span class="text-on-surface-variant">Học kỳ hiện hành:</span>
                <strong data-field="current-term" class="ml-1 text-on-surface">Đang tải…</strong>
            </div>
            <button type="button" data-action="reload" class="inline-flex items-center gap-1.5 rounded-lg border border-outline-variant px-3 py-2 text-sm font-semibold text-on-surface hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">refresh</span>
                Làm mới
            </button>
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <nav class="flex flex-wrap gap-2" aria-label="Nhóm chức năng năm học">
        <button type="button" data-tab="years" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Năm học & Học kỳ</button>
        <button type="button" data-tab="milestones" class="rounded-lg border border-outline-variant bg-white px-4 py-2 text-sm font-semibold text-on-surface hover:bg-surface-container-low">Lịch học vụ</button>
        <button type="button" data-tab="holidays" class="rounded-lg border border-outline-variant bg-white px-4 py-2 text-sm font-semibold text-on-surface hover:bg-surface-container-low">Ngày nghỉ</button>
        <button type="button" data-tab="requests" class="rounded-lg border border-outline-variant bg-white px-4 py-2 text-sm font-semibold text-on-surface hover:bg-surface-container-low">Đề nghị sửa lịch</button>
    </nav>

    <section data-panel="years" class="space-y-5">
        <div class="grid gap-5 xl:grid-cols-[1.35fr_0.65fr]">
            <article class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-headline-sm font-bold text-on-surface">Danh sách năm học</h2>
                        <p class="text-xs text-on-surface-variant">Mỗi năm học hiển thị các học kỳ và trạng thái vận hành.</p>
                    </div>
                    <span data-field="year-count" class="text-label-sm text-secondary"></span>
                </div>
                <div data-region="years" class="p-4"></div>
            </article>

            <div class="space-y-5">
                <article data-write="create" class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
                    <h2 class="text-headline-sm font-bold text-on-surface">Tạo / sửa năm học</h2>
                    <form data-form="year" class="mt-4 space-y-3">
                        <input type="hidden" name="id">
                        <label class="block text-sm font-medium text-on-surface">Tên năm học
                            <input name="name" required maxlength="50" placeholder="2026 - 2027" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="block text-sm font-medium text-on-surface">Bắt đầu
                                <input name="start_date" type="date" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm">
                            </label>
                            <label class="block text-sm font-medium text-on-surface">Kết thúc
                                <input name="end_date" type="date" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm">
                            </label>
                        </div>
                        <div data-region="year-form-alert" hidden></div>
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" data-action="year-save" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-container">Lưu năm học</button>
                            <button type="button" data-action="year-cancel" hidden class="rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold hover:bg-surface-container-low">Hủy sửa</button>
                        </div>
                    </form>
                </article>

                <article data-write="create" class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
                    <h2 class="text-headline-sm font-bold text-on-surface">Tạo học kỳ</h2>
                    <form data-form="term" class="mt-4 space-y-3">
                        <label class="block text-sm font-medium text-on-surface">Năm học
                            <select name="academic_year_id" required class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select>
                        </label>
                        <label class="block text-sm font-medium text-on-surface">Tên học kỳ
                            <input name="name" required maxlength="100" placeholder="Học kỳ 1" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm">
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="block text-sm font-medium text-on-surface">Loại
                                <select name="type" required class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm">
                                    <option value="main">Học kỳ chính</option>
                                    <option value="summer">Học kỳ hè</option>
                                </select>
                            </label>
                            <label class="block text-sm font-medium text-on-surface">Số tuần
                                <input name="weeks" type="number" min="1" value="15" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm">
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="block text-sm font-medium text-on-surface">Bắt đầu
                                <input name="start_date" type="date" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm">
                            </label>
                            <label class="block text-sm font-medium text-on-surface">Kết thúc
                                <input name="end_date" type="date" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm">
                            </label>
                        </div>
                        <div data-region="term-form-alert" hidden></div>
                        <button type="submit" data-action="term-save" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-container">Tạo học kỳ</button>
                    </form>
                    <p class="mt-3 text-xs text-on-surface-variant">Repo hiện chưa có API sửa học kỳ; sau khi tạo chỉ có thể chuyển trạng thái / đặt hiện hành theo workflow backend.</p>
                </article>
            </div>
        </div>
    </section>

    <section data-panel="milestones" hidden class="space-y-5">
        <article class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <label class="block text-sm font-medium text-on-surface min-w-[260px]">Học kỳ cần cấu hình
                    <select data-input="milestone-term" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select>
                </label>
                <div data-write="update" class="flex flex-wrap items-end gap-2">
                    <label class="block text-sm font-medium text-on-surface min-w-[240px]">Sao chép từ học kỳ
                        <select data-input="copy-from" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select>
                    </label>
                    <button type="button" data-action="copy-milestones" class="rounded-lg border border-outline-variant px-3 py-2 text-sm font-semibold hover:bg-surface-container-low">Sao chép lịch</button>
                </div>
            </div>
        </article>

        <article class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-headline-sm font-bold text-on-surface">Các mốc lịch học vụ</h2>
                    <p data-field="milestone-note" class="mt-1 text-xs text-on-surface-variant"></p>
                </div>
                <span data-field="pending-count" class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-900"></span>
            </div>
            <form data-form="milestones" class="mt-4 space-y-4">
                <div data-region="milestones"></div>
                <label data-write="update" class="block text-sm font-medium text-on-surface">Lý do thay đổi (cần thiết nếu học kỳ đã bắt đầu)
                    <textarea name="reason" rows="2" maxlength="1000" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm" placeholder="Ví dụ: Điều chỉnh kế hoạch đào tạo..."></textarea>
                </label>
                <div data-region="milestone-form-alert" hidden></div>
                <button data-write="update" type="submit" data-action="milestones-save" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-container">Lưu lịch học vụ</button>
            </form>
        </article>
    </section>

    <section data-panel="holidays" hidden class="space-y-5">
        <div class="grid gap-5 xl:grid-cols-[1.2fr_0.8fr]">
            <article class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                    <div>
                        <h2 class="text-headline-sm font-bold text-on-surface">Ngày nghỉ / ngày lễ</h2>
                        <p class="text-xs text-on-surface-variant">Ngày nghỉ được TTB dùng khi sinh buổi học thực tế.</p>
                    </div>
                    <label class="text-sm font-medium text-on-surface">Lọc theo học kỳ
                        <select data-input="holiday-term" class="mt-1 min-w-[220px] rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select>
                    </label>
                </div>
                <div data-region="holidays" class="p-4"></div>
            </article>

            <article data-write="create" class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
                <h2 class="text-headline-sm font-bold text-on-surface">Thêm / sửa ngày nghỉ</h2>
                <form data-form="holiday" class="mt-4 space-y-3">
                    <input type="hidden" name="id">
                    <label class="block text-sm font-medium text-on-surface">Tên ngày nghỉ
                        <input name="name" required maxlength="255" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm" placeholder="Tết Dương lịch">
                    </label>
                    <label class="block text-sm font-medium text-on-surface">Loại
                        <select name="type" required class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm">
                            <option value="public_holiday">Nghỉ lễ</option>
                            <option value="summer_break">Nghỉ hè</option>
                            <option value="school_break">Nghỉ theo lịch của trường</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-on-surface">Áp dụng cho học kỳ
                        <select name="term_id" class="mt-1 w-full rounded-lg border border-outline-variant bg-white px-3 py-2 text-sm"></select>
                        <span class="mt-1 block text-xs text-on-surface-variant">Để trống nếu áp dụng toàn trường.</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block text-sm font-medium text-on-surface">Từ ngày
                            <input name="starts_on" type="date" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm">
                        </label>
                        <label class="block text-sm font-medium text-on-surface">Đến ngày
                            <input name="ends_on" type="date" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm">
                        </label>
                    </div>
                    <label class="block text-sm font-medium text-on-surface">Ghi chú
                        <textarea name="note" rows="2" maxlength="500" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2 text-sm"></textarea>
                    </label>
                    <div data-region="holiday-form-alert" hidden></div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" data-action="holiday-save" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-container">Lưu ngày nghỉ</button>
                        <button type="button" data-action="holiday-cancel" hidden class="rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold hover:bg-surface-container-low">Hủy sửa</button>
                    </div>
                </form>
            </article>
        </div>
    </section>

    <section data-panel="requests" hidden class="space-y-5">
        <article class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-headline-sm font-bold text-on-surface">Đề nghị sửa lịch học vụ</h2>
                    <p class="text-xs text-on-surface-variant">Học kỳ đã bắt đầu sẽ tạo đề nghị thay vì áp dụng ngay; người có quyền ACY.approve xử lý tại đây.</p>
                </div>
                <button type="button" data-action="reload-requests" class="rounded-lg border border-outline-variant px-3 py-2 text-sm font-semibold hover:bg-surface-container-low">Làm mới</button>
            </div>
            <div data-region="requests" class="p-4"></div>
        </article>
    </section>
</div>
@endsection
