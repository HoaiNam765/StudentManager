<?php

namespace Tests\Support;

use PDO;
use RuntimeException;

/**
 * Bảo vệ dữ liệu khi chạy test: test dùng migrate:fresh nên sẽ xóa sạch CSDL đang kết nối.
 * Chỉ cho phép chạy trên SQLite hoặc CSDL có tên kết thúc bằng `_test`.
 */
final class TestDatabaseGuard
{
    public const SUFFIX = '_test';

    public static function assertSafe(string $driver, string $database): void
    {
        if ($driver === 'sqlite') {
            return;
        }

        if (! str_ends_with($database, self::SUFFIX)) {
            throw new RuntimeException(
                "Từ chối chạy test trên CSDL '{$database}' vì test sẽ xóa sạch dữ liệu. "
                .'Tên CSDL kiểm thử phải kết thúc bằng `'.self::SUFFIX.'` (xem DB_DATABASE trong phpunit.xml).'
            );
        }
    }

    /**
     * Tạo CSDL kiểm thử MySQL nếu chưa có, để mỗi người không phải tạo tay.
     *
     * @param  array<string, mixed>  $config  cấu hình kết nối trong config/database.php
     */
    public static function ensureMysqlDatabaseExists(array $config): void
    {
        $database = (string) $config['database'];
        self::assertSafe('mysql', $database);

        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%s', $config['host'], $config['port']),
            (string) $config['username'],
            (string) $config['password'],
        );

        $charset = $config['charset'] ?? 'utf8mb4';
        $collation = $config['collation'] ?? 'utf8mb4_unicode_ci';

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$charset} COLLATE {$collation}");
    }
}
