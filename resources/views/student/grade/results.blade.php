{{--
    Kết quả học tập của sinh viên (mock student/ket-qua-hoc-tap): GPA tích lũy, xếp loại, điểm từng học phần theo học kỳ.
    Dữ liệu: resources/js/pages/student-grades.js gọi GET /student/gpa, /student/transcript, /student/grades
    (chỉ lớp đã công bố điểm). Bảng điểm PDF: /student/transcript?format=pdf&lang=vi|en.
--}}
@extends('layouts.portal')

@section('title', 'Kết quả học tập - Cổng Sinh viên')
@section('page-title', 'Kết quả học tập')

@section('page')
<div data-page="student-grades" class="space-y-6">
    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="space-y-1 min-w-0">
            <h1 class="text-headline-lg font-bold text-on-surface">Kết quả học tập</h1>
            <p class="text-body-md text-on-surface-variant" data-field="subtitle">Đang tải…</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-action="reload" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold text-on-surface hover:bg-surface-container-low inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">refresh</span>
                Làm mới
            </button>
            <button type="button" data-action="pdf" data-lang="en" class="px-3 py-2 rounded-lg border border-outline-variant text-label-md font-semibold text-on-surface hover:bg-surface-container-low inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">translate</span>
                Bảng điểm tiếng Anh
            </button>
            <button type="button" data-action="pdf" data-lang="vi" class="px-3 py-2 rounded-lg bg-primary hover:bg-primary-container text-white text-label-md font-semibold inline-flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">picture_as_pdf</span>
                Tải bảng điểm PDF
            </button>
        </div>
    </section>

    <div data-region="alert" hidden></div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3" data-region="summary"></div>

    <div data-region="terms" class="space-y-6"></div>

    <p class="text-[11px] text-outline">Chỉ hiện học phần đã được Phòng Đào tạo công bố điểm. GPA tính theo thang 4 với tín chỉ của học phần tính GPA; học phần học lại lấy {{ config('grade_gpa.retake_policy') === 'latest' ? 'lần gần nhất' : 'kết quả cao nhất' }} cho GPA tích lũy. Thắc mắc về điểm, liên hệ giảng viên phụ trách lớp.</p>
</div>
@endsection
