@extends('layouts.admin-original')
@section('title', 'Chi tiết sổ điểm lớp học phần')
@section('page-title', 'Chi tiết sổ điểm')
@section('page')
<div class="space-y-5 pb-12">
    <div class="flex flex-wrap justify-between gap-3 items-start">
        <div><a href="{{ route('admin.pages.grades') }}" class="text-primary text-sm hover:underline">← Quay lại Quản lý điểm</a>
            <h1 class="text-2xl font-bold mt-2">Sổ điểm lớp {{ $courseSection->code }}</h1>
            <p class="text-sm text-on-surface-variant">{{ $courseSection->subject?->name }} · {{ $courseSection->term?->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2 text-sm">
            <a class="px-3 py-2 rounded-lg bg-primary text-white" href="{{ route('admin.course-sections.gradebook.export', ['courseSection' => $courseSection->id, 'format' => 'xlsx']) }}">Xuất Excel</a>
            <a class="px-3 py-2 rounded-lg border border-outline-variant" href="{{ route('admin.course-sections.gradebook.export', ['courseSection' => $courseSection->id, 'format' => 'pdf']) }}">Xuất PDF</a>
        </div>
    </div>
    @php $current = $workflow?->status instanceof \BackedEnum ? $workflow->status->value : ($workflow?->status ?? 'draft'); @endphp
    <div class="rounded-xl bg-white border border-outline-variant p-4 text-sm flex flex-wrap gap-5">
        <span>Trạng thái: <strong>{{ $current }}</strong></span>
        <span>Số sinh viên: <strong>{{ count($book['students']) }}</strong></span>
        <span>Điểm TB lớp: <strong>{{ $report['statistics']['mean'] ?? '—' }}</strong></span>
        <span>Tỷ lệ đạt: <strong>{{ isset($report['statistics']['pass_rate']) ? $report['statistics']['pass_rate'].'%' : '—' }}</strong></span>
    </div>
    <section class="rounded-2xl border border-outline-variant bg-white overflow-hidden">
        <h2 class="font-semibold px-5 py-4 border-b border-outline-variant">Danh sách sinh viên và điểm thành phần (#124, #127)</h2>
        @if (empty($book['students']))
            <div class="p-10 text-center text-on-surface-variant">Lớp chưa có sinh viên đăng ký hợp lệ.</div>
        @else
            <div class="overflow-x-auto"><table class="w-full text-sm text-left">
                <thead class="bg-surface-container-low text-on-surface-variant"><tr><th class="p-3">MSSV</th><th class="p-3">Họ tên</th>
                    @foreach ($book['components'] as $component)<th class="p-3">{{ $component['name'] }} ({{ $component['weight'] }}%)</th>@endforeach
                    <th class="p-3">Điểm 10</th><th class="p-3">Điểm chữ</th><th class="p-3">Hệ 4</th><th class="p-3">Lịch sử</th></tr></thead>
                <tbody class="divide-y divide-outline-variant/50">
                    @foreach ($book['students'] as $row)
                        @php $result = $results->get($row['enrollment_id']); @endphp
                        <tr><td class="p-3 font-mono">{{ $row['student']['student_code'] ?? '—' }}</td><td class="p-3">{{ $row['student']['full_name'] ?? '—' }}</td>
                            @foreach ($row['scores'] as $item)<td class="p-3">{{ $item['score'] ?? '—' }}</td>@endforeach
                            <td class="p-3">{{ $result?->score_10 ?? '—' }}</td><td class="p-3">{{ $result?->letter ?? '—' }}</td>
                            <td class="p-3">{{ $result?->score_4 ?? '—' }}</td>
                            <td class="p-3"><a class="text-primary hover:underline" href="{{ route('admin.pages.grade-history', ['enrollment' => $row['enrollment_id']]) }}">Xem</a></td></tr>
                    @endforeach
                </tbody>
            </table></div>
        @endif
    </section>
    <div class="rounded-xl bg-white border border-outline-variant p-5">
        <h2 class="font-semibold mb-3">Phổ điểm (từ kết quả đã tính)</h2>
        <div class="flex flex-wrap gap-3 text-sm">@forelse ($report['statistics']['distribution'] as $label => $number)<span class="px-3 py-2 bg-surface-container-low rounded-lg">{{ $label }}: {{ $number }}</span>@empty<span class="text-on-surface-variant">Chưa có kết quả để thống kê.</span>@endforelse</div>
    </div>
</div>
@endsection
