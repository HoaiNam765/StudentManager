<?php

namespace App\Modules\AcademicYear\Notifications;

use App\Modules\AcademicYear\Models\MilestoneChangeRequest;
use App\Modules\AcademicYear\Models\MilestoneType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Báo kết quả duyệt đề nghị sửa lịch học vụ (BR-ACY-05). P1 gửi qua mail (driver `log`); module NOT sẽ báo thêm
 * cho người liên quan (giảng viên, sinh viên của học kỳ) theo Phụ lục A.
 */
class MilestonesChanged extends Notification
{
    use Queueable;

    public function __construct(
        public readonly MilestoneChangeRequest $request,
        public readonly bool $approved,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $term = $this->request->term;
        $mail = (new MailMessage)
            ->subject("[Quản lý sinh viên] Đề nghị sửa lịch học vụ {$term->name} ".($this->approved ? 'đã được duyệt' : 'bị từ chối'))
            ->line(($this->approved ? 'Đề nghị đã được duyệt và áp dụng' : 'Đề nghị bị từ chối').' bởi '.($this->request->decider?->name ?? '').'.');

        foreach ($this->request->changes as $type => $change) {
            $mail->line('• '.(MilestoneType::tryFrom($type)?->label() ?? $type).': '.($change['old'] ?? '(chưa có)').' → '.($change['new'] ?? '(bỏ mốc)'));
        }

        if ($this->request->decision_note !== null) {
            $mail->line('Ghi chú của người duyệt: '.$this->request->decision_note);
        }

        return $mail;
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return ['request_id' => $this->request->id, 'approved' => $this->approved];
    }
}
