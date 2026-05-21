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

        $frequency = config('filament-export-cleanup.schedule.frequency');

        if ($frequency === null || $frequency === '') {
            return;
        }

        $event = $schedule->command(FilamentExportCleanupCommand::class);

        $this->applyScheduleFrequency($event, $frequency);
    }

    protected function applyScheduleFrequency(Event $event, mixed $frequency): void
    {
        if ($frequency instanceof \Closure) {
            $frequency($event);

            return;
        }

        if (is_string($frequency) && $frequency !== '') {
            $event->cron($frequency);
        }
    }
}
