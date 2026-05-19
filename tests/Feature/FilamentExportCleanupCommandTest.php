<?php

use Illuminate\Support\Facades\Storage;

it('deletes old exports and reports how many were deleted', function () {
    $userId = createTestUserId();
    $disk = config('filament-export-cleanup.file_disk');

    $oldExport = createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(73),
    ]);

    createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(1),
    ]);

    $oldExportFilePath = exportFilePath($oldExport);

    $this->artisan('cleanup:filament-export')
        ->expectsOutput('Deleted 1 export files')
        ->assertSuccessful();

    expect(Storage::disk($disk)->exists($oldExportFilePath))->toBeFalse();
    $this->assertDatabaseMissing('exports', ['id' => $oldExport->id]);
});

it('reports when no export files need deletion', function () {
    $userId = createTestUserId();

    createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(1),
    ]);

    $this->artisan('cleanup:filament-export')
        ->expectsOutput('No export files to delete')
        ->assertSuccessful();
});

it('reports no export files to delete when cleanup is disabled', function () {
    config()->set('filament-export-cleanup.enabled', false);

    $userId = createTestUserId();
    $disk = config('filament-export-cleanup.file_disk');

    $oldExport = createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(73),
    ]);

    $oldExportFilePath = exportFilePath($oldExport);

    $this->artisan('cleanup:filament-export')
        ->expectsOutput('No export files to delete')
        ->assertSuccessful();

    expect(Storage::disk($disk)->exists($oldExportFilePath))->toBeTrue();
    $this->assertDatabaseHas('exports', ['id' => $oldExport->id]);
});
