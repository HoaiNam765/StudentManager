<?php

namespace App\Modules\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Gửi tên đăng nhập và mật khẩu tạm cho tài khoản mới hoặc khi quản trị viên cấp lại (FR-AUTH-009, BR-AUTH-10).
 * Mật khẩu tạm chỉ xuất hiện trong email này, không lưu bản gốc và không trả về qua API (BR-AUTH-02).
 * P1 dùng driver mail `log`; module NOT sẽ gửi email thật.
 */
class TemporaryPasswordIssued extends Notification
{
    use Queueable;

    public function __construct(public readonly string $temporaryPassword) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days = (int) config('studentmanager.auth.temporary_password_days');

        return (new MailMessage)
            ->subject('[Quản lý sinh viên] Thông tin đăng nhập')
            ->greeting('Xin chào '.$notifiable->name.',')
            ->line('Tên đăng nhập: '.$notifiable->username)
            ->line('Mật khẩu tạm: '.$this->temporaryPassword)
            ->action('Đăng nhập', route('login'))
            ->line("Bạn phải đổi mật khẩu ở lần đăng nhập đầu tiên. Mật khẩu tạm hết hạn sau {$days} ngày nếu chưa dùng.");
    }
}
