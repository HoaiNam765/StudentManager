<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\TestDatabaseGuard;

abstract class TestCase extends BaseTestCase
{
    /**
     * Chạy trước RefreshDatabase / DatabaseMigrations (các trait này gọi migrate:fresh).
     */
    protected function setUpTraits()
    {
        $this->prepareTestDatabase();

        return parent::setUpTraits();
    }

    private function prepareTestDatabase(): void
    {
        static $prepared = false;

        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        TestDatabaseGuard::assertSafe($config['driver'], (string) $config['database']);

        if (! $prepared && $config['driver'] === 'mysql') {
            TestDatabaseGuard::ensureMysqlDatabaseExists($config);
            $prepared = true;
        }
    }
}
