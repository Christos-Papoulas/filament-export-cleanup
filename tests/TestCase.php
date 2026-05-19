<?php

namespace Pachristos\FilamentExportCleanup\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use Pachristos\FilamentExportCleanup\FilamentExportCleanupServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Pachristos\\FilamentExportCleanup\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadMigrationsFrom(dirname(__DIR__).'/vendor/filament/actions/database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            FilamentExportCleanupServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        config()->set('filesystems.disks.local', [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ]);
    }
}
