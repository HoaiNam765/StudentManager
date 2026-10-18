<?php

namespace App\Modules\Auth\Services;

/**
 * Mô tả ngắn thiết bị từ chuỗi User-Agent để hiển thị ("Chrome trên Windows"), đủ cho lịch sử đăng nhập và danh sách
 * phiên; không cần chính xác tuyệt đối nên không dùng thư viện ngoài.
 */
final readonly class DeviceInfo
{
    public function __construct(
        public string $browser,
        public string $platform,
        public string $type,
    ) {}

    public static function fromUserAgent(?string $userAgent): self
    {
        $ua = (string) $userAgent;

        $browser = match (true) {
            $ua === '' => 'Không rõ trình duyệt',
            str_contains($ua, 'Edg/') => 'Microsoft Edge',
            str_contains($ua, 'OPR/') || str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'CocCoc') || str_contains($ua, 'coc_coc') => 'Cốc Cốc',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Chrome/') || str_contains($ua, 'CriOS/') => 'Chrome',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Trình duyệt khác',
        };

        $platform = match (true) {
            $ua === '' => 'thiết bị không rõ',
            str_contains($ua, 'iPhone') => 'iPhone',
            str_contains($ua, 'iPad') => 'iPad',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac OS X') || str_contains($ua, 'Macintosh') => 'macOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'thiết bị khác',
        };

        $type = match (true) {
            str_contains($ua, 'iPad') || (str_contains($ua, 'Android') && ! str_contains($ua, 'Mobile')) => 'tablet',
            str_contains($ua, 'Mobile') || str_contains($ua, 'iPhone') => 'mobile',
            default => 'desktop',
        };

        return new self($browser, $platform, $type);
    }

    public function label(): string
    {
        return "{$this->browser} trên {$this->platform}";
    }
}
