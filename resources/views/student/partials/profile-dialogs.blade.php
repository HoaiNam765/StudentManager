<dialog id="editModal" class="m-auto w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="dialog" class="flex justify-between"><h2 class="font-semibold">Yêu cầu chỉnh sửa thông tin</h2><button aria-label="Đóng">×</button></form>
    <form method="post" enctype="multipart/form-data" action="{{ route('student.pages.profile.request') }}" class="space-y-4 mt-4">
        @csrf
        <p class="text-xs text-secondary">Chỉ điền trường cần thay đổi. Hồ sơ được cập nhật sau khi yêu cầu được duyệt.</p>
        @foreach (['full_name' => 'Họ và tên mới', 'date_of_birth' => 'Ngày sinh mới', 'national_id' => 'Số CCCD mới'] as $name => $label)
        <label class="block text-sm">{{ $label }}<input name="{{ $name }}" type="{{ $name === 'date_of_birth' ? 'date' : 'text' }}" value="{{ old($name) }}" class="w-full border border-slate-300 rounded-lg px-3 py-2"></label>
        @endforeach
        <label class="block text-sm">Ngành mới<select name="major_id" class="w-full border border-slate-300 rounded-lg px-3 py-2"><option value="">Giữ nguyên ngành</option>@foreach ($majors as $major)<option value="{{ $major->id }}" @selected(old('major_id') == $major->id)>{{ $major->code }} – {{ $major->name }}</option>@endforeach</select></label>
        <label class="block text-sm">Lý do<textarea required name="reason" maxlength="1000" class="w-full border border-slate-300 rounded-lg px-3 py-2">{{ old('reason') }}</textarea></label>
        <label class="block text-sm">Minh chứng (PDF/JPG/PNG, tối đa 5 MB)<input required type="file" name="evidence" accept=".pdf,.jpg,.jpeg,.png" class="block w-full mt-1"></label>
        <button class="px-4 py-2 bg-primary text-white rounded-lg">Gửi yêu cầu</button>
    </form>
</dialog>
<dialog id="photoModal" class="m-auto rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="dialog" class="flex justify-between gap-6"><h2 class="font-semibold">Đổi ảnh sinh viên</h2><button aria-label="Đóng">×</button></form>
    <form method="post" enctype="multipart/form-data" action="{{ route('student.pages.profile.photo') }}" class="space-y-4 mt-4">@csrf
        <label class="block text-sm">Ảnh JPG/PNG, tối đa 2 MB<input required type="file" name="photo" accept=".jpg,.jpeg,.png" class="block mt-2"></label>
        <button class="px-4 py-2 bg-primary text-white rounded-lg">Lưu ảnh</button>
    </form>
</dialog>
