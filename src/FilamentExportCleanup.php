<?php

declare(strict_types=1);

namespace Pachristos\FilamentExportCleanup;

use Exception;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Collection;

class FilamentExportCleanup
{
    public function run(): array
    {
        if (! config('filament-export-cleanup.enabled')) {
            return [];
        }

        $recordsToCleanup = $this->getOldExportRecords();

        $deletedIds = $this->cleanupFiles($recordsToCleanup);
        if (config('filament-export-cleanup.delete_database_records')) {
            $this->cleanupDatabaseRecords($deletedIds);
        }

        return $deletedIds;
    }

    protected function cleanupFiles(Collection $records): array
    {
        $deletedIds = [];
        $records->each(function (Export $record) use (&$deletedIds) {
            $record->deleteFileDirectory();
            $deletedIds[] = $record->id;
        });

        return $deletedIds;
    }

    protected function cleanupDatabaseRecords(array $deletedIds): void
    {
        Export::whereIn('id', $deletedIds)->delete();
    }

    protected function getOldExportRecords(): Collection
    {
        $hours = config('filament-export-cleanup.retention_hours');
        if (empty($hours)) {
            throw new Exception('retention_hours config option is not set');
        }

        $disk = config('filament-export-cleanup.file_disk');
        if (empty($disk)) {
            throw new Exception('file_disk config option is not set');
        }

        return Export::where('completed_at', '<', now()->subHours($hours))
            ->where('file_disk', $disk)
            ->get();
    }
}
