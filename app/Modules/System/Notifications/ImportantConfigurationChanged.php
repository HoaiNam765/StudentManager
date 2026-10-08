<?php

namespace App\Modules\System\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Báo cho các ADMIN khác khi có thay đổi cấu hình quan trọng (BR-SYS-08).
 * Ở P1 gửi qua kênh mail (driver `log` trong .env nên chỉ ghi vào log); module NOT sẽ thêm kênh trong hệ thống.
 */
class ImportantConfigurationChanged extends Notification
{
    use Queueable;

    /** @param  list<string>  $lines  Từng thay đổi, ví dụ "Hết phiên sau số phút không hoạt động: 120 → 60" */
    public function __construct(
        public readonly string $summary,
        public readonly array $lines,
        public readonly string $actorName,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('[Quản lý sinh viên] '.$this->summary)
            ->greeting('Thông báo thay đổi cấu hình')
            ->line("{$this->actorName} vừa thực hiện: {$this->summary}.");

        foreach ($this->lines as $line) {
            $mail->line('• '.$line);
        }

        return $mail->line('Chi tiết trước – sau xem trong nhật ký kiểm toán.');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return ['summary' => $this->summary, 'lines' => $this->lines, 'actor' => $this->actorName];
    }
}
