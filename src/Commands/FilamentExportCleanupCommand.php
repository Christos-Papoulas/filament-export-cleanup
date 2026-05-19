<?php

namespace Pachristos\FilamentExportCleanup\Commands;

use Illuminate\Console\Command;

class FilamentExportCleanupCommand extends Command
{
    public $signature = 'filament-export-cleanup';

    public $description = 'Delete old Filament export files and database records';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
