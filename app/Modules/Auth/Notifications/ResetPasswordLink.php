<?php

namespace App\Modules\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email chứa liên kết đặt lại mật khẩu (FR-AUTH-003, sự kiện EV-AUTH-01). Ở P1 driver mail là `log` nên nội dung
 * chỉ ghi vào storage/logs; module NOT sẽ gửi email thật (điểm cắm: kênh `mail` hoặc thêm kênh mới ở `via()`).
 */
class ResetPasswordLink extends Notification
{
    use Queueable;

    public function __construct(public readonly string $token) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function url(object $notifiable): string
    {
        return route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $minutes = (int) config('auth.passwords.users.expire');

        return (new MailMessage)
            ->subject('[Quản lý sinh viên] Đặt lại mật khẩu')
            ->greeting('Xin chào '.$notifiable->name.',')
            ->line('Bạn (hoặc ai đó) vừa yêu cầu đặt lại mật khẩu cho tài khoản '.($notifiable->username ?? $notifiable->email).'.')
            ->action('Đặt lại mật khẩu', $this->url($notifiable))
            ->line("Liên kết chỉ dùng được một lần và hết hạn sau {$minutes} phút.")
            ->line('Nếu bạn không yêu cầu, hãy bỏ qua email này; mật khẩu hiện tại vẫn giữ nguyên.');
    }
}
