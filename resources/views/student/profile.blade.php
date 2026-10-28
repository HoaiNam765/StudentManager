@extends('layouts.portal')
@section('title', 'Hồ sơ cá nhân và Yêu cầu chỉnh sửa - Cổng Sinh viên')
@section('page-title', 'Hồ sơ cá nhân và Yêu cầu chỉnh sửa')
@section('page')

<!-- 1. PROFILE HEADER CARD (Banner thông tin đầu trang) -->
<section class="bg-surface-container-lowest rounded-xl border border-slate-200 p-6 shadow-sm">
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
<!-- Left: Avatar & Administrative Information -->
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
<div class="relative group">
@if ($profile->photo_path)<img alt="Ảnh sinh viên" class="w-24 h-24 rounded-xl object-cover border-2 border-primary/20 shadow-sm" src="{{ asset('storage/'.$profile->photo_path) }}"/>@else<div class="w-24 h-24 rounded-xl border-2 border-primary/20 shadow-sm bg-blue-100 text-primary flex items-center justify-center font-bold text-2xl">{{ $student['initials'] }}</div>@endif
<button class="absolute inset-0 bg-inverse-surface/50 rounded-xl flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[2px]" onclick="document.getElementById('photoModal').showModal()" title="Đổi ảnh thẻ sinh viên" type="button">
<span class="material-symbols-outlined text-[20px]" data-icon="photo_camera">photo_camera</span>
<span class="text-[10px] font-medium mt-0.5">Đổi ảnh</span>
</button>
</div>
<div class="space-y-1.5">
<div class="flex flex-wrap items-center gap-2.5">
<h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">{{ mb_strtoupper($student['name']) }}</h1>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                {{ $details['status_label'] }} • Khóa {{ $student['cohort'] }}
              </span>
</div>
<div class="flex flex-wrap items-center gap-y-1 gap-x-3 font-body-sm text-body-sm text-on-surface-variant">
<span><strong class="text-on-surface">MSSV:</strong> {{ $student['student_id'] }}</span>
<span class="text-outline-variant">•</span>
<span><strong class="text-on-surface">Lớp hành chính:</strong> {{ $student['class'] }}</span>
<span class="text-outline-variant">•</span>
<span><strong class="text-on-surface">Ngành:</strong> {{ $student['major'] }}</span>
<span class="text-outline-variant">•</span>
<span><strong class="text-on-surface">Khoa:</strong> {{ $student['major'] }}</span>
</div>
<!-- Cố vấn học tập -->
<div class="flex items-center gap-2 pt-1 font-body-sm text-body-sm text-secondary">
<span class="material-symbols-outlined text-[16px] text-primary" data-icon="school">school</span>
<span>Cố vấn học tập: <strong class="text-on-surface">{{ $advisor?->full_name ?? 'Chưa phân công' }}</strong></span>
<span class="text-outline-variant">|</span>
<a class="hover:text-primary flex items-center gap-1 text-[11px]" href="mailto:{{ $advisor?->email ?? '' }}" title="Gửi thư cho CVHT">
<span class="material-symbols-outlined text-[14px]" data-icon="mail">mail</span>
                {{ $advisor?->email ?? '' }}
              </a>
<span class="text-outline-variant">|</span>
<span class="flex items-center gap-1 text-[11px]">
<span class="material-symbols-outlined text-[14px]" data-icon="call">call</span>
                {{ $advisor?->phone ?? '—' }}
              </span>
</div>
</div>
</div>
<!-- Right: 3 Quick Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 self-stretch lg:self-auto">
<!-- Stat 1: Tín chỉ tích lũy -->
<div class="bg-surface-container-low border border-slate-200/80 rounded-lg p-3.5 flex flex-col justify-between min-w-[160px]">
<div class="flex items-center justify-between text-on-surface-variant">
<span class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-secondary">Tích lũy tín chỉ</span>
<span class="material-symbols-outlined text-[18px] text-primary" data-icon="fact_check">fact_check</span>
</div>
<div class="my-1.5">
<div class="flex items-baseline gap-1">
<span class="font-headline-lg text-headline-lg font-bold text-primary">{{ $student['credits_earned'] ?? '—' }}</span>
<span class="text-on-surface-variant text-sm font-medium">/ {{ $student['credits_total'] ?? '—' }} TC</span>
</div>
<div class="w-full bg-slate-200 h-1.5 rounded-full mt-2 overflow-hidden">
<div class="bg-primary h-full rounded-full" style="width: {{ $student['credits_pct'] }}%"></div>
</div>
</div>
<span class="text-[11px] text-on-surface-variant font-medium">Tiến độ đào tạo: {{ $student['credits_pct'] }}%</span>
</div>
<!-- Stat 2: GPA tích lũy -->
<div class="bg-surface-container-low border border-slate-200/80 rounded-lg p-3.5 flex flex-col justify-between min-w-[160px]">
<div class="flex items-center justify-between text-on-surface-variant">
<span class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-secondary">GPA tích lũy</span>
<span class="material-symbols-outlined text-[18px] text-tertiary" data-icon="grade">grade</span>
</div>
<div class="my-1.5">
<div class="flex items-baseline gap-1">
<span class="font-headline-lg text-headline-lg font-bold text-on-surface">{{ $student['gpa_4'] === null ? '—' : number_format($student['gpa_4'], 2) }}</span>
<span class="text-on-surface-variant text-sm font-medium">/ 4.0</span>
</div>
<span class="inline-block mt-1 font-body-sm text-body-sm text-secondary"><a href="{{ route('student.pages.grades') }}">Xem bảng điểm</a></span>
</div>
<span class="text-[11px] text-emerald-700 font-semibold flex items-center gap-0.5">
<span class="material-symbols-outlined text-[14px]" data-icon="trending_up">trending_up</span>
              Theo kết quả đã công bố
            </span>
</div>
<!-- Stat 3: Xếp loại học lực -->
<div class="bg-surface-container-low border border-slate-200/80 rounded-lg p-3.5 flex flex-col justify-between min-w-[160px]">
<div class="flex items-center justify-between text-on-surface-variant">
<span class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-secondary">Xếp loại học lực</span>
<span class="material-symbols-outlined text-[18px] text-amber-700" data-icon="military_tech">military_tech</span>
</div>
<div class="my-1.5">
<span class="font-headline-lg text-headline-lg font-bold text-emerald-700">{{ $student['classification'] }}</span>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Theo kết quả đã công bố</p>
</div>
<span class="text-[11px] text-secondary font-medium">Chưa có kết quả xét học vụ</span>
</div>
</div>
</div>
</section>
<!-- 2. GRID 12 CỘT: CỘT TRÁI (7 CỘT) & CỘT PHẢI (5 CỘT) -->
<div class="grid grid-cols-12 gap-6">
<!-- ================= CỘT TRÁI: HỒ SƠ CHI TIẾT (COL-SPAN-7) ================= -->
<div class="col-span-12 lg:col-span-7 space-y-6">
<!-- NHÓM 1: Thông tin cá nhân cơ bản (Dữ liệu gốc pháp lý) -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-surface-bright">
<div class="flex items-center gap-2.5">
<span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]" data-icon="badge">badge</span>
</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">1. Thông tin cá nhân cơ bản</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Thông tin từ hồ sơ sinh viên của nhà trường</p>
</div>
</div>
<span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-md border border-slate-200 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="lock">lock</span>
              Bảo mật cấp cao
            </span>
</div>
<!-- Thông báo bảo mật -->
<div class="p-6 space-y-5">
<div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-3.5 flex items-start gap-3">
<span class="material-symbols-outlined text-[20px] text-amber-700 mt-0.5 flex-shrink-0" data-icon="shield_person">shield_person</span>
<p class="font-body-sm text-body-sm text-amber-800 leading-relaxed">
                Các trường định danh pháp lý được khóa bảo mật. Để thay đổi cần gửi yêu cầu kèm minh chứng gốc (CCCD gắn chip, Giấy khai sinh trích lục hoặc xác nhận cư trú CT07).
              </p>
</div>
<!-- Fields Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Họ và tên (Khóa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Họ và tên</label>
<button class="text-primary text-[11px] font-semibold hover:underline flex items-center gap-0.5" onclick="document.getElementById('editModal').showModal()" type="button">
<span>Yêu cầu chỉnh sửa</span>
<span class="material-symbols-outlined text-[12px]" data-icon="edit">edit</span>
</button>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="text" value="{{ $student['name'] }}"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock" title="Chỉ đọc">lock</span>
</div>
</div>
<!-- Số CCCD (Khóa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Số CCCD / Định danh</label>
<button class="text-primary text-[11px] font-semibold hover:underline flex items-center gap-0.5" onclick="document.getElementById('editModal').showModal()" type="button">
<span>Yêu cầu chỉnh sửa</span>
<span class="material-symbols-outlined text-[12px]" data-icon="edit">edit</span>
</button>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="text" value="{{ $details['national_id_masked'] ?? '—' }}"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock" title="Chỉ đọc">lock</span>
</div>
</div>
<!-- Ngày sinh (Khóa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Ngày sinh</label>
<span class="text-[11px] text-slate-500 font-medium">Khóa</span>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="text" value="{{ $profile->date_of_birth?->format('d/m/Y') ?? '—' }}"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock">lock</span>
</div>
</div>
<!-- Giới tính (Khóa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Giới tính</label>
<span class="text-[11px] text-slate-500 font-medium">Khóa</span>
</div>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="text" value="{{ $details['personal']['gender']['name'] ?? '—' }}"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock">lock</span>
</div>
</div>
<!-- Dân tộc, Tôn giáo, Nơi sinh (ReadOnly) -->
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Dân tộc</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="{{ $details['personal']['ethnicity']['name'] ?? '—' }}"/>
</div>
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Tôn giáo</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="{{ $details['personal']['religion']['name'] ?? '—' }}"/>
</div>
<div class="sm:col-span-2 space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Nơi sinh (theo Giấy khai sinh)</label>
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed text-sm" disabled="" type="text" value="{{ $details['personal']['place_of_birth'] ?? '—' }}"/>
</div>
</div>
</div>
</div>
<!-- NHÓM 2: Thông tin liên hệ & Cư trú (Cho phép sinh viên cập nhật trực tiếp) -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-surface-bright">
<div class="flex items-center gap-2.5">
<span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]" data-icon="contact_phone">contact_phone</span>
</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">2. Thông tin liên hệ &amp; Cư trú</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Sinh viên chủ động cập nhật để nhận thông báo khẩn cấp từ nhà trường</p>
</div>
</div>
<span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md border border-emerald-200">
              Có thể cập nhật
            </span>
</div>
<form class="p-6 space-y-4" method="post" action="{{ route('student.pages.profile.update') }}">
@csrf @method('PUT')

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Email SV (Cố định) -->
<div class="space-y-1">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Email sinh viên (Cấp bởi trường)</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-slate-100 border border-slate-300 rounded-lg px-3 py-2 text-on-surface font-medium cursor-not-allowed pr-8 text-sm" disabled="" type="email" value="{{ $details['school_email'] }}"/>
<span class="material-symbols-outlined text-[16px] text-slate-500 absolute right-2.5 top-2.5" data-icon="lock">lock</span>
</div>
</div>
<!-- Email cá nhân (Có thể sửa) -->
<div class="space-y-1">
<div class="flex items-center justify-between">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Email cá nhân dự phòng</label>
<span class="inline-flex items-center gap-0.5 text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded">
<span class="material-symbols-outlined text-[12px]" data-icon="verified">verified</span>
                    Email liên hệ
                  </span>
</div>
<input aria-label="Ô nhập liệu" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm" type="email" name="personal_email" value="{{ old('personal_email', $details['contact']['personal_email']) }}"/>
</div>
<!-- SĐT di động (Có thể sửa) -->
<div class="space-y-1 sm:col-span-2">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Số điện thoại di động chính thức</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-3 py-2 text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm" type="tel" name="phone" value="{{ old('phone', $details['contact']['phone']) }}"/>
<span class="material-symbols-outlined text-[18px] text-slate-500 absolute left-3 top-2.5" data-icon="smartphone">smartphone</span>
</div>
</div>
<!-- Địa chỉ thường trú -->
<div class="space-y-1 sm:col-span-2">
<label class="font-label-md text-label-md text-on-surface-variant font-medium">Địa chỉ thường trú / Hộ khẩu hiện tại</label>
<div class="relative">
<input aria-label="Ô nhập liệu" class="w-full bg-white border border-slate-300 rounded-lg pl-10 pr-3 py-2 text-on-surface focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm" type="text" name="permanent_address" value="{{ old('permanent_address') }}" placeholder="{{ $details['contact']['permanent_address_masked'] ?? 'Nhập địa chỉ mới nếu cần thay đổi' }}"/>
<span class="material-symbols-outlined text-[18px] text-slate-500 absolute left-3 top-2.5" data-icon="location_on">location_on</span>
</div>
</div>
</div>
<!-- Nút Cập nhật thông tin liên hệ -->
<div class="pt-2 flex justify-end">
<button class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors shadow-sm" type="submit">
<span class="material-symbols-outlined text-[18px]" data-icon="save">save</span>
<span>Cập nhật thông tin liên hệ</span>
</button>
</div>
</form>
</div>
<!-- NHÓM 3: Thông tin gia đình & Liên hệ khẩn cấp -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-surface-bright">
<div class="flex items-center gap-2.5">
<span class="w-8 h-8 rounded-lg bg-tertiary/10 text-tertiary flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]" data-icon="family_restroom">family_restroom</span>
</span>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">3. Thông tin gia đình &amp; Liên hệ khẩn cấp</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Sử dụng trong các trường hợp bảo hộ quyền lợi và sự cố bất khả kháng</p>
</div>
</div>
</div>
<div class="p-6 space-y-4">
@include('student.partials.contacts')
</div>
</div>
</div>
<!-- ================= CỘT PHẢI: LỊCH SỬ YÊU CẦU & MODAL DEMO (COL-SPAN-5) ================= -->
<div class="col-span-12 lg:col-span-5 space-y-6">
<!-- CARD: Lịch sử yêu cầu chỉnh sửa thông tin học vụ -->
<div class="bg-surface-container-lowest rounded-xl border border-slate-200 shadow-sm overflow-hidden">
<div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-surface-bright">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[20px] text-primary" data-icon="history">history</span>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Lịch sử yêu cầu chỉnh sửa</h2>
</div>
<button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors shadow-sm" onclick="document.getElementById('editModal').showModal()" type="button">
<span class="material-symbols-outlined text-[16px]" data-icon="add_circle">add_circle</span>
<span>Tạo yêu cầu mới</span>
</button>
</div>
<div class="p-5 space-y-4">
@include('student.partials.profile-requests')
</div>
</div>
<!-- Card Hướng dẫn quy trình điều chỉnh -->
<div class="bg-surface-container-low rounded-xl border border-slate-200/80 p-5 space-y-3">
<div class="flex items-center gap-2 text-primary font-headline-sm text-headline-sm font-semibold">
<span class="material-symbols-outlined" data-icon="help_outline">help_outline</span>
<span>Quy trình xử lý yêu cầu</span>
</div>
<ol class="space-y-2 text-xs text-on-surface-variant list-decimal list-inside leading-relaxed">
<li>Sinh viên chọn trường thông tin pháp lý cần điều chỉnh và điền giá trị mới chính xác.</li>
<li>Đính kèm file scan minh chứng gốc (định dạng PDF hoặc JPEG, dung lượng &lt; 5MB).</li>
<li>Cán bộ có quyền đối chiếu minh chứng và duyệt hoặc từ chối yêu cầu.</li>
<li>Kết quả được cập nhật trong hồ sơ sau khi yêu cầu được duyệt.</li>
</ol>
</div>
</div>
</div>

@include('student.partials.profile-dialogs')
@endsection
