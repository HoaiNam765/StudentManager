<?php

namespace App\Modules\System\Services;

use App\Modules\System\Contracts\ImporterContract;
use App\Support\Exceptions\BusinessRuleException;

/**
 * Registry trung tâm lưu danh sách Importer đã đăng ký.
 * Mỗi module đăng ký một Importer bằng cách gọi register() trong ServiceProvider.
 *
 * Ví dụ:
 *   $this->app->make(ImportRegistry::class)->register(new StudentImporter());
 */
final class ImportRegistry
{
    /** @var array<string, ImporterContract> */
    private array $importers = [];

    /** @var array<string, callable(): ImporterContract> */
    private array $lazy = [];

    /**
     * Đăng ký một Importer.
     */
    public function register(ImporterContract $importer): void
    {
        $this->importers[$importer->key()] = $importer;
    }

    /**
     * Đăng ký Importer theo kiểu lazy (chỉ khởi tạo khi cần).
     *
     * @param  callable(): ImporterContract  $factory
     */
    public function registerLazy(string $key, callable $factory): void
    {
        $this->lazy[$key] = $factory;
    }

    /**
     * Lấy Importer theo key. Ném BusinessRuleException nếu không tìm thấy.
     */
    public function get(string $key): ImporterContract
    {
        if (isset($this->importers[$key])) {
            return $this->importers[$key];
        }

        if (isset($this->lazy[$key])) {
            $importer = ($this->lazy[$key])();
            $this->importers[$key] = $importer;

            return $importer;
        }

        throw new BusinessRuleException(
            "Không tìm thấy Importer '{$key}'.",
            'Liên hệ quản trị viên để kiểm tra cấu hình import.'
        );
    }

    /**
     * Kiểm tra xem key có tồn tại không.
     */
    public function has(string $key): bool
    {
        return isset($this->importers[$key]) || isset($this->lazy[$key]);
    }

    /**
     * Danh sách tất cả Importer đã đăng ký (bao gồm lazy chưa khởi tạo sẽ được khởi tạo).
     *
     * @return array<string, ImporterContract>
     */
    public function all(): array
    {
        foreach (array_keys($this->lazy) as $key) {
            $this->get($key); // khởi tạo lazy
        }

        return $this->importers;
    }

    /**
     * Danh sách key đã đăng ký mà không khởi tạo lazy.
     *
     * @return list<string>
     */
    public function keys(): array
    {
        return array_unique(array_merge(
            array_keys($this->importers),
            array_keys($this->lazy),
        ));
    }
}
