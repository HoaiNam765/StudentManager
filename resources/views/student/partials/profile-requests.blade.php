@forelse ($details['change_requests'] as $change)
<div class="border border-amber-200 bg-amber-50/50 rounded-lg p-4 space-y-2.5 transition-all hover:shadow-sm">
    <div class="flex items-start justify-between gap-2"><span class="font-bold text-amber-800">#{{ $change['id'] }}</span><span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">{{ $change['status_label'] }}</span></div>
    <p class="text-xs text-secondary">Gửi ngày: {{ $change['created_at'] }}</p>
    <p class="text-sm">{{ $change['reason'] }}</p>
    @foreach ($change['changes'] as $field => $values)
    <p class="text-xs">{{ ['full_name' => 'Họ tên', 'date_of_birth' => 'Ngày sinh', 'national_id' => 'CCCD', 'major_id' => 'Mã ngành'][$field] ?? $field }}: {{ $values['old'] ?? '—' }} → {{ $values['new'] ?? '—' }}</p>
    @endforeach
    @if ($change['decision_note'])<p class="text-xs">Phản hồi: {{ $change['decision_note'] }}</p>@endif
    @if ($change['status'] === 'pending')
    <form method="post" action="{{ route('student.pages.profile.cancel', $change['id']) }}">@csrf<button class="text-primary text-xs font-semibold hover:underline">Hủy yêu cầu</button></form>
    @endif
</div>
@empty
<p class="text-sm text-secondary">Chưa có yêu cầu chỉnh sửa.</p>
@endforelse
