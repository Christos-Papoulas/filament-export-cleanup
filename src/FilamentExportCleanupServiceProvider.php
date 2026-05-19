<?php

declare(strict_types=1);

namespace Pachristos\FilamentExportCleanup;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Pachristos\FilamentExportCleanup\Commands\FilamentExportCleanupCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentExportCleanupServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-export-cleanup')
            ->hasConfigFile()
            ->hasCommand(FilamentExportCleanupCommand::class)
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->publishConfigFile();
            });
    }

    public function packageBooted(): void
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $this->registerSchedule($schedule);
        });

        if ($this->app->resolved(Schedule::class)) {
            $this->registerSchedule($this->app->make(Schedule::class));
        }
    }

    protected function registerSchedule(Schedule $schedule): void
    {
        if (! config('filament-export-cleanup.schedule.enabled')) {
            return;
        }

        $expression = config('filament-export-cleanup.schedule.expression');

        if ($expression === null || $expression === '') {
            return;
        }

        $event = $schedule->command(FilamentExportCleanupCommand::class);

        $this->applyScheduleExpression($event, $expression);
    }

    protected function applyScheduleExpression(Event $event, string $expression): void
    {
        if (preg_match("/^dailyAt\('(\d{1,2}:\d{2})'\)$/", $expression, $matches)) {
            $event->dailyAt($matches[1]);

            return;
        }

        $event->cron($expression);
    }
}
