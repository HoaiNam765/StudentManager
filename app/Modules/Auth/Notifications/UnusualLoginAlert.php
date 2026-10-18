<?php

namespace App\Modules\Auth\Notifications;

use App\Modules\Auth\Models\LoginHistory;
use App\Support\Format;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Cảnh báo đăng nhập bất thường cho chủ tài khoản (EV-AUTH-02). Ở P1 driver mail là `log` nên nội dung chỉ ghi vào
 * storage/logs; module NOT sẽ gửi email thật và thêm thông báo trong hệ thống cho ADMIN.
 */
class UnusualLoginAlert extends Notification
{
    use Queueable;

    public function __construct(public readonly LoginHistory $entry) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Quản lý sinh viên] Đăng nhập từ thiết bị lạ')
            ->greeting('Xin chào '.$notifiable->name.',')
            ->line('Tài khoản '.($notifiable->username ?? $notifiable->email).' vừa đăng nhập từ một thiết bị và địa chỉ IP chưa từng dùng gần đây:')
            ->line('Thời điểm: '.Format::dateTime($this->entry->created_at))
            ->line('Thiết bị: '.$this->entry->device)
            ->line('Địa chỉ IP: '.($this->entry->ip_address ?? 'không rõ'))
            ->action('Xem các phiên đăng nhập', route('account.sessions'))
            ->line('Nếu đó là bạn, có thể bỏ qua email này. Nếu không, hãy đổi mật khẩu ngay và đăng xuất khỏi các thiết bị khác.');
    }
}
