<?php

use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Pachristos\FilamentExportCleanup\Tests\TestCase;

uses(TestCase::class)->in('Feature');

/**
 * @param  array<string, mixed>  $attributes
 */
function createCompletedExportWithFile(int $userId, array $attributes = []): Export
{
    $export = Export::query()->create(array_merge([
        'completed_at' => now(),
        'file_disk' => config('filament-export-cleanup.file_disk'),
        'file_name' => 'export.csv',
        'exporter' => 'Tests\\FakeExporter',
        'total_rows' => 1,
        'successful_rows' => 1,
        'user_id' => $userId,
    ], $attributes));

    Storage::disk(config('filament-export-cleanup.file_disk'))
        ->put(exportFilePath($export), 'export contents');

    return $export;
}

function createTestUserId(): int
{
    return (int) DB::table('users')->insertGetId([
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function exportFilePath(Export $export): string
{
    return $export->getFileDirectory().'/export.csv';
}
