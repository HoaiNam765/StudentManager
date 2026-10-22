@extends('layouts.admin-original')
@section('title', 'Tra cứu GPA sinh viên')
@section('page-title', 'Tra cứu GPA')
@section('page')
<div class="space-y-5 pb-12">
    <div><a class="text-primary text-sm hover:underline" href="{{ route('admin.pages.grades') }}">← Quay lại Quản lý điểm</a><h1 class="text-2xl font-bold mt-2">Tra cứu GPA (#125)</h1>
    <p class="text-sm text-on-surface-variant">Dữ liệu GPA đã tính của sinh viên có kết quả công bố thuộc phạm vi quản lý của bạn. Không tự tính lại hoặc thay đổi điểm.</p></div>
    <form method="GET" action="{{ route('admin.pages.grade-gpa') }}" class="rounded-xl p-4 bg-white border border-outline-variant flex gap-2 flex-wrap">
        <label for="student_code" class="text-sm font-medium self-center">Mã số sinh viên</label>
        <input id="student_code" name="student_code" value="{{ $studentCode }}" maxlength="50" class="border border-outline-variant px-3 py-2 rounded-lg text-sm" placeholder="Nhập MSSV" required>
        <button type="submit" class="rounded-lg bg-primary text-white px-4 py-2 text-sm">Tra cứu</button>
    </form>
    @if ($student)
        <section class="rounded-xl bg-white border border-outline-variant p-5"><h2 class="font-semibold">{{ $student->full_name }} — {{ $student->student_code }}</h2>
            <div class="grid sm:grid-cols-3 gap-3 mt-4"><div class="p-4 bg-surface-container-low rounded-xl"><p class="text-sm text-on-surface-variant">GPA tích lũy</p><strong class="text-2xl">{{ $cumulative?->gpa ?? '—' }}</strong></div>
            <div class="p-4 bg-surface-container-low rounded-xl"><p class="text-sm text-on-surface-variant">Xếp loại</p><strong>{{ $cumulative?->classification ?? '—' }}</strong></div>
            <div class="p-4 bg-surface-container-low rounded-xl"><p class="text-sm text-on-surface-variant">Tín chỉ đạt</p><strong class="text-2xl">{{ $cumulative?->credits_earned ?? '—' }}</strong></div></div>
        </section>
        <section class="rounded-xl bg-white border border-outline-variant overflow-hidden"><h2 class="font-semibold p-4 border-b border-outline-variant">GPA theo học kỳ</h2>
            <div class="overflow-x-auto"><table class="w-full text-sm text-left"><thead class="bg-surface-container-low"><tr><th class="p-3">Mã học kỳ</th><th class="p-3">GPA</th><th class="p-3">Xếp loại</th><th class="p-3">TC đã học</th><th class="p-3">TC đạt</th></tr></thead>
            <tbody>@forelse ($termRows as $row)<tr class="border-t border-outline-variant"><td class="p-3">{{ $row->term_id }}</td><td class="p-3">{{ $row->gpa }}</td><td class="p-3">{{ $row->classification }}</td><td class="p-3">{{ $row->credits_attempted }}</td><td class="p-3">{{ $row->credits_earned }}</td></tr>@empty<tr><td colspan="5" class="p-5 text-center text-on-surface-variant">Chưa có GPA học kỳ được tính.</td></tr>@endforelse</tbody></table></div>
        </section>
    @elseif ($studentCode !== '')
        <div class="p-5 rounded-xl border border-outline-variant bg-white text-on-surface-variant">Không tìm thấy sinh viên có kết quả đã công bố trong phạm vi bạn được phép tra cứu.</div>
    @else
        <div class="p-5 rounded-xl border border-outline-variant bg-white text-on-surface-variant">Nhập MSSV để xem GPA đã tính. Không hiển thị danh sách sinh viên ngoài phạm vi được phân quyền.</div>
    @endif
</div>
@endsection
