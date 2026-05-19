<?php

namespace Pachristos\FilamentExportCleanup\Commands;

use Illuminate\Console\Command;
use Pachristos\FilamentExportCleanup\FilamentExportCleanup;

class FilamentExportCleanupCommand extends Command
{
    protected $signature = 'cleanup:filament-export';

    protected $description = 'Delete old Filament export files and database records';

    public function handle(): int
    {
        $deletedIds = app(FilamentExportCleanup::class)->run();

        if (count($deletedIds) > 0) {
            $this->info('Deleted '.count($deletedIds).' export files');
        } else {
            $this->info('No export files to delete');
        }

        return self::SUCCESS;
    }
}
