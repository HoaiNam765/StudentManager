@extends('layouts.app')

@section('title', 'Đổi mật khẩu - Hệ thống Quản lý Học vụ - HUIT')

@section('content')
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 min-w-[260px] h-screen bg-surface-container-lowest flex flex-col justify-between border-r border-outline-variant fixed left-0 top-0 z-40 select-none">
        <div class="p-4 flex flex-col gap-5">
            <div class="flex items-center gap-3 px-1 py-1">
                <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center text-white shadow-sm flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl font-bold">school</span>
                </div>
                <div class="flex flex-col overflow-hidden">
                    <span class="text-sm font-bold text-primary tracking-tight leading-none">HUIT Học Vụ</span>
                    <span class="text-xs text-on-surface-variant truncate mt-1">Cổng thông tin Đào tạo</span>
                </div>
            </div>

            <a href="#" class="w-full bg-[#EEF2FD] hover:bg-surface-container text-primary text-xs font-semibold py-2.5 px-3 rounded-lg flex items-center justify-center gap-2 transition-colors border border-outline-variant active:scale-[0.99]">
                <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                <span>Đăng ký môn học</span>
            </a>

            <div class="flex flex-col gap-1">
                <div class="px-3 pb-1 text-on-surface-variant text-[11px] uppercase tracking-wider font-semibold">Học vụ chính</div>
                <nav class="flex flex-col gap-0.5">
                    <a href="{{ route('student.dashboard') ?? '#' }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">dashboard</span>
                        <span class="text-sm">Tổng quan</span>
                    </a>
                    <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">school</span>
                        <span class="text-sm">Chương trình đào tạo</span>
                    </a>
                    <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                        <span class="text-sm">Kế hoạch học tập</span>
                    </a>
                    <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">grade</span>
                        <span class="text-sm">Quản lý điểm</span>
                    </a>
                    <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">payments</span>
                        <span class="text-sm">Tra cứu học phí</span>
                    </a>
                </nav>

                <div class="px-3 pt-3 pb-1 text-on-surface-variant text-[11px] uppercase tracking-wider font-semibold">Cài đặt tài khoản</div>
                <nav class="flex flex-col gap-0.5">
                    <a href="{{ route('password.change') ?? '#' }}" class="bg-secondary-container text-primary font-semibold rounded-lg px-3 py-2 flex items-center gap-3 border-l-4 border-primary transition-colors">
                        <span class="material-symbols-outlined text-[20px] text-primary" style="font-variation-settings: 'FILL' 1;">lock_reset</span>
                        <span class="text-sm font-semibold">Đổi mật khẩu</span>
                    </a>
                    <a href="{{ route('sessions.index') ?? '#' }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">devices</span>
                        <span class="text-sm">Phiên đăng nhập</span>
                    </a>
                    <a href="{{ route('notifications.settings') ?? '#' }}" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">notifications_active</span>
                        <span class="text-sm">Tùy chọn thông báo</span>
                    </a>
                    <a href="#" class="text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-normal rounded-lg px-3 py-2 flex items-center gap-3 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">help_outline</span>
                        <span class="text-sm">Trợ giúp</span>
                    </a>
                </nav>
            </div>
        </div>

        <div class="p-3 border-t border-outline-variant bg-surface-container-low m-2 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-8 h-8 rounded-full bg-primary text-white text-xs flex items-center justify-center font-bold flex-shrink-0">
                    NA
                </div>
                <div class="flex flex-col min-w-0">
                    <p class="text-xs font-semibold text-on-surface truncate">Nguyễn Văn An</p>
                    <p class="text-[11px] text-on-surface-variant truncate">MSSV: 2001210123</p>
                </div>
            </div>
            <button title="Đăng xuất" type="button" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-surface-container rounded-lg transition-colors flex-shrink-0">
                <span class="material-symbols-outlined text-[20px]">logout</span>
            </button>
        </div>
    </aside>

    <!-- Workspace -->
    <div class="flex-1 flex flex-col ml-64 min-h-screen bg-background">
        <!-- Topbar -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant flex items-center justify-between px-6 sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-4">
                <nav class="flex items-center text-on-surface-variant text-xs gap-2">
                    <a href="#" class="hover:text-primary transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">home</span>
                        <span>Trang chủ</span>
                    </a>
                    <span class="text-outline-variant">/</span>
                    <span class="hover:text-primary cursor-pointer">Cài đặt tài khoản</span>
                    <span class="text-outline-variant">/</span>
                    <span class="text-primary font-semibold">Đổi mật khẩu</span>
                </nav>
                <div class="h-4 w-[1px] bg-outline-variant"></div>
                <div class="hidden lg:flex items-center gap-1.5 bg-[#EEF2FD] text-primary text-xs px-2.5 py-1 rounded-full border border-outline-variant">
                    <span class="material-symbols-outlined text-[15px]">event</span>
                    <span class="font-medium">Học kỳ 1 • 2026-2027</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative w-56 hidden md:block">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">search</span>
                    <input type="text" placeholder="Tìm kiếm chức năng..." class="w-full pl-9 pr-3 py-1.5 bg-surface-container-low text-on-surface border border-outline-variant rounded-lg text-xs placeholder:text-outline focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                </div>
                <button type="button" class="relative p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full ring-2 ring-surface-container-lowest"></span>
                </button>
                <button type="button" class="p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[22px]">help</span>
                </button>
                <div class="h-6 w-[1px] bg-outline-variant"></div>
                <div class="flex items-center gap-2 pl-1 cursor-pointer group">
                    <div class="w-8 h-8 rounded-full bg-primary-container text-white text-xs flex items-center justify-center font-bold shadow-xs">
                        NA
                    </div>
                    <span class="text-xs text-on-surface font-semibold group-hover:text-primary transition-colors hidden sm:inline">Khoa CNTT</span>
                    <span class="material-symbols-outlined text-[18px] text-on-surface-variant">arrow_drop_down</span>
                </div>
            </div>
        </header>

        <!-- Main Form -->
        <main class="flex-1 overflow-y-auto p-8 flex flex-col justify-between">
            <div class="max-w-[1440px] w-full mx-auto flex flex-col items-center">
                <!-- Page Title -->
                <div class="w-full max-w-[540px] text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-surface-container text-primary mb-3 shadow-sm border border-outline-variant">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">security</span>
                    </div>
                    <h1 class="text-2xl text-on-surface font-bold tracking-tight">Đổi mật khẩu</h1>
                    <p class="text-sm text-on-surface-variant mt-1.5">
                        Cập nhật mật khẩu định kỳ để bảo vệ tài khoản và dữ liệu học vụ cá nhân.
                    </p>
                </div>

                <!-- Card -->
                <div class="w-full max-w-[540px] bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm p-8">
                    <div class="flex items-center gap-3 pb-5 mb-6 border-b border-surface-container">
                        <div class="w-9 h-9 rounded-lg bg-surface-container-low flex items-center justify-center text-primary border border-outline-variant">
                            <span class="material-symbols-outlined text-[20px]">lock_clock</span>
                        </div>
                        <div>
                            <h2 class="text-base text-on-surface font-semibold">Thiết lập mật khẩu mới</h2>
                            <p class="text-xs text-on-surface-variant">Vui lòng điền đầy đủ các thông tin xác thực bảo mật bên dưới</p>
                        </div>
                    </div>

                    <form action="{{ route('password.update') ?? '#' }}" method="POST" class="space-y-5">
                        @csrf
                        <!-- Current Password -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="current-password" class="text-xs text-on-surface font-semibold">
                                    Mật khẩu hiện tại <span class="text-error">*</span>
                                </label>
                                <a href="#" class="text-xs text-primary hover:underline font-medium">Quên mật khẩu?</a>
                            </div>
                            <div class="relative">
                                <input type="password" id="current-password" name="current_password" required placeholder="Nhập mật khẩu đang sử dụng" class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                <button type="button" onclick="togglePassword('current-password', this)" class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="new-password" class="block text-xs text-on-surface font-semibold mb-1.5">
                                Mật khẩu mới <span class="text-error">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="new-password" name="new_password" required value="HuitSecure@2026" placeholder="Nhập mật khẩu mới" class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                <button type="button" onclick="togglePassword('new-password', this)" class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <!-- Strength Meter -->
                            <div class="mt-2.5 bg-surface-container-low p-2.5 rounded-lg border border-outline-variant/60">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs text-on-surface-variant">Thước đo độ mạnh mật khẩu:</span>
                                    <span class="text-xs font-semibold text-primary flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">verified_user</span>
                                        Độ mạnh: Rất mạnh (85%)
                                    </span>
                                </div>
                                <div class="grid grid-cols-4 gap-1.5 h-1.5 w-full">
                                    <div class="bg-primary rounded-full h-full"></div>
                                    <div class="bg-primary rounded-full h-full"></div>
                                    <div class="bg-primary rounded-full h-full"></div>
                                    <div class="bg-outline-variant/50 rounded-full h-full"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="confirm-password" class="block text-xs text-on-surface font-semibold mb-1.5">
                                Xác nhận mật khẩu mới <span class="text-error">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="confirm-password" name="password_confirmation" required value="HuitSecure@2026" placeholder="Nhập lại chính xác mật khẩu mới" class="w-full h-10 px-3 pr-10 bg-surface-container-lowest border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                <button type="button" onclick="togglePassword('confirm-password', this)" class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                        </div>

                        <!-- Checklist -->
                        <div class="pt-2">
                            <div class="flex items-center gap-1.5 mb-2.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">verified</span>
                                <span class="text-xs text-on-surface font-semibold">Tiêu chuẩn mật khẩu HUIT:</span>
                            </div>
                            <ul class="space-y-2 bg-surface-container-low/70 p-3.5 rounded-lg border border-outline-variant/60 text-xs text-on-surface">
                                <li class="flex items-center gap-2 text-on-surface">
                                    <span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span>Tối thiểu 8 ký tự (tối đa 32 ký tự)</span>
                                </li>
                                <li class="flex items-center gap-2 text-on-surface">
                                    <span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span>Ít nhất 1 chữ hoa (A-Z) và 1 chữ thường (a-z)</span>
                                </li>
                                <li class="flex items-center gap-2 text-on-surface">
                                    <span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span>Ít nhất 1 chữ số (0-9)</span>
                                </li>
                                <li class="flex items-center gap-2 text-on-surface">
                                    <span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span>Ít nhất 1 ký tự đặc biệt (!@#$%^&*...)</span>
                                </li>
                                <li class="flex items-center gap-2 text-on-surface">
                                    <span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    <span>Không trùng khớp với mật khẩu gần nhất</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Buttons -->
                        <div class="pt-3 flex flex-col gap-3">
                            <button type="submit" class="w-full h-11 bg-primary hover:bg-primary-container text-white font-semibold text-sm rounded-lg shadow-sm flex items-center justify-center gap-2 transition-all active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                <span>Cập nhật mật khẩu ngay</span>
                            </button>
                            <a href="#" class="w-full text-center py-1.5 text-xs text-on-surface-variant hover:text-on-surface transition-colors">
                                Hủy bỏ và quay lại
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Notice -->
                <div class="w-full max-w-[540px] mt-4 bg-surface-container-low border border-outline-variant rounded-xl p-4 flex gap-3 items-start shadow-xs">
                    <span class="material-symbols-outlined text-primary text-[22px] flex-shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">info</span>
                    <div class="text-xs text-on-surface leading-relaxed">
                        <span class="font-semibold text-primary">Lưu ý bảo mật:</span> Sau khi đổi mật khẩu thành công, tất cả các phiên đăng nhập khác trên thiết bị di động hoặc máy tính công cộng sẽ tự động đăng xuất để đảm bảo an toàn tài khoản.
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="w-full max-w-[1440px] mx-auto mt-12 pt-6 border-t border-outline-variant text-center text-xs text-on-surface-variant">
                <p>© 2026 Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT) - Cổng Quản lý Học vụ & Khảo thí | Hỗ trợ kỹ thuật: <a href="mailto:daotao@huit.edu.vn" class="text-primary hover:underline">daotao@huit.edu.vn</a></p>
            </footer>
        </main>
    </div>
</div>

<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
@endsection