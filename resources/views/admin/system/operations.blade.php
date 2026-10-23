@extends('layouts.portal')

@section('title', 'Vận hành hệ thống - Quản lý Đào tạo')
@section('page-title', 'Vận hành hệ thống')

@section('page')
<div data-page="admin-system-operations" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5">
        <h1 class="text-headline-lg font-bold">Khóa sổ, import & vận hành dữ liệu</h1>
        <p class="mt-1 text-sm text-on-surface-variant">Chỉ bật những thao tác đã có backend thật. Sao lưu/phục hồi và TermLock đầy đủ của #173 hiện chưa có API trong repo.</p>
    </section>

    <div data-region="alert" hidden></div>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
        <div class="px-5 py-4 border-b border-outline-variant"><h2 class="text-headline-sm font-bold">Kết thúc học kỳ</h2><p class="text-xs text-on-surface-variant mt-1">Khóa thời khóa biểu và chuyển trạng thái học kỳ theo các bước backend hiện cho phép.</p></div>
        <div class="p-5 space-y-4">
            <label class="block max-w-xl text-sm">Học kỳ<select data-filter="term" class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
            <div data-region="term-close"></div>
        </div>
    </section>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm">
        <div class="px-5 py-4 border-b border-outline-variant"><h2 class="text-headline-sm font-bold">Import dữ liệu</h2><p class="text-xs text-on-surface-variant mt-1">Luồng thật: upload → kiểm tra → preview → lưu → có thể rollback khi backend cho phép.</p></div>
        <div class="p-5 space-y-5">
            @if (($pageData['can']['importCreate'] ?? false))
            <form data-form="import" class="grid md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                <label class="text-sm">Loại dữ liệu<select name="importer" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2"></select></label>
                <label class="text-sm">Tệp<input name="file" type="file" required accept=".xlsx,.xls,.csv" class="mt-1 block w-full rounded-lg border border-outline-variant px-3 py-2 text-sm"></label>
                <button type="submit" class="rounded-lg bg-primary text-white px-4 py-2 text-sm font-semibold">Tải lên</button>
            </form>
            @endif
            <div data-region="imports"></div>
        </div>
    </section>

    <section class="rounded-xl border border-dashed border-outline-variant bg-surface-container-low p-5">
        <h2 class="text-headline-sm font-bold">Sao lưu & phục hồi</h2>
        <p class="mt-1 text-sm text-on-surface-variant">Chưa triển khai: repo hiện không có API BackupJob/restore tương ứng. Không tạo nút giả hoặc thao tác trực tiếp DB từ frontend.</p>
    </section>
</div>
@endsection
