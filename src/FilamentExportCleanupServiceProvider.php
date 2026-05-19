<?php

namespace Pachristos\FilamentExportCleanup;

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
}
