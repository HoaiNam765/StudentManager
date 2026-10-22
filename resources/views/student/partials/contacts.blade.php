@foreach ([...$details['contacts'], null] as $contact)
<form method="post" action="{{ $contact ? route('student.pages.profile.contacts.update', $contact['id']) : route('student.pages.profile.contacts.store') }}" class="space-y-4 border-b border-slate-100 pb-4">
    @csrf @if ($contact) @method('PUT') @endif
    <p class="font-semibold text-sm">{{ $contact ? 'Người liên hệ' : 'Thêm người liên hệ' }}</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach (['full_name' => 'Họ và tên', 'relationship' => 'Mối quan hệ', 'phone' => 'Số điện thoại', 'email' => 'Email'] as $name => $label)
        <label class="space-y-1 text-sm">{{ $label }}<input name="{{ $name }}" type="{{ $name === 'email' ? 'email' : 'text' }}" value="{{ $contact[$name] ?? '' }}" @required(in_array($name, ['full_name', 'relationship'])) class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-on-surface text-sm"></label>
        @endforeach
    </div>
    <button class="px-3 py-2 bg-primary text-white rounded-lg text-sm">{{ $contact ? 'Lưu thay đổi' : 'Thêm người liên hệ' }}</button>
</form>
@if ($contact)
<form method="post" action="{{ route('student.pages.profile.contacts.destroy', $contact['id']) }}" onsubmit="return confirm('Xóa người liên hệ này?')">@csrf @method('DELETE')<button class="text-sm text-red-700">Xóa người liên hệ</button></form>
@endif
@endforeach
