@extends('layouts.portal')
@section('title', 'Lịch sử điều chỉnh điểm')
@section('page-title', 'Lịch sử điểm')
@section('page')
<div class="space-y-5 pb-12">
    <div><a href="{{ route('admin.pages.grades', ['tab'=>'adjustments']) }}" class="text-primary text-sm hover:underline">← Quay lại yêu cầu điều chỉnh</a>
    <h1 class="text-2xl font-bold mt-2">Lịch sử điểm của sinh viên</h1>
    <p class="text-on-surface-variant text-sm">{{ $enrollment->student?->full_name }} ({{ $enrollment->student?->student_code }}) · {{ $enrollment->subject?->name }} · {{ $enrollment->section?->code }}</p></div>
    <section class="rounded-2xl bg-white border border-outline-variant p-5 space-y-3">
        <h2 class="font-semibold">Các phiên bản điểm (#126)</h2>
        @forelse ($versions as $version)
            <details class="border border-outline-variant rounded-lg p-4"><summary class="cursor-pointer font-semibold">Phiên bản {{ $version->version }} · {{ $version->created_at?->format('d/m/Y H:i') }} · {{ $version->reason }}</summary>
                <div class="grid sm:grid-cols-2 gap-3 mt-4 text-sm">
                    @foreach (['before'=>'Điểm trước', 'after'=>'Điểm sau'] as $key => $label)
                        @php $result = data_get($version->snapshot, $key.'.result', []); @endphp
                        <div class="bg-surface-container-low rounded-lg p-3"><strong>{{ $label }}</strong>
                        <p class="mt-2">Hệ 10: {{ $result['score_10'] ?? '—' }} · Chữ: {{ $result['letter'] ?? '—' }} · Hệ 4: {{ $result['score_4'] ?? '—' }}</p></div>
                    @endforeach
                </div>
            </details>
        @empty<div class="text-on-surface-variant text-sm">Chưa có phiên bản điểm nào được ghi nhận.</div>@endforelse
    </section>
    <section class="rounded-2xl bg-white border border-outline-variant p-5 space-y-3"><h2 class="font-semibold">Yêu cầu điều chỉnh</h2>
        @forelse ($requests as $change)
            <div class="p-3 bg-surface-container-low rounded-lg text-sm"><div class="flex flex-wrap justify-between gap-2"><strong>#{{ $change->id }} — {{ $change->status }}</strong><span>{{ $change->created_at?->format('d/m/Y H:i') }}</span></div><p class="mt-2">{{ $change->reason }}</p>
                @if ($change->evidence_path)<a class="text-primary hover:underline" href="{{ route('admin.pages.grade-evidence', ['gradeChangeRequest'=>$change->id]) }}">Tải minh chứng (có kiểm tra quyền)</a>@endif
            </div>
        @empty<p class="text-on-surface-variant text-sm">Chưa có yêu cầu điều chỉnh.</p>@endforelse
    </section>
</div>
@endsection
