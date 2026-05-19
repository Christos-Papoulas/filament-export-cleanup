<?php

use Illuminate\Support\Facades\Storage;
use Pachristos\FilamentExportCleanup\FilamentExportCleanup;

it('removes export files on disk and deletes database records when they are older than the configured retention', function () {
    // Arrange
    $userId = createTestUserId();
    $disk = config('filament-export-cleanup.file_disk');

    $oldExport = createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(73),
    ]);

    $recentExport = createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(1),
    ]);

    $oldExportFilePath = exportFilePath($oldExport);
    $recentExportFilePath = exportFilePath($recentExport);

    // Assert before act
    expect(Storage::disk($disk)->exists($oldExportFilePath))->toBeTrue()
        ->and(Storage::disk($disk)->exists($recentExportFilePath))->toBeTrue();

    // Act
    app(FilamentExportCleanup::class)->run();

    // Expect
    expect(Storage::disk($disk)->exists($oldExportFilePath))->toBeFalse();
    expect(Storage::disk($disk)->exists($recentExportFilePath))->toBeTrue();

    $this->assertDatabaseHas('exports', ['id' => $recentExport->id]);
    $this->assertDatabaseMissing('exports', ['id' => $oldExport->id]);
});

it('removes export files on disk and does not delete database records when delete_database_records is false', function () {
    // Arrange
    config()->set('filament-export-cleanup.delete_database_records', false);
    $userId = createTestUserId();
    $disk = config('filament-export-cleanup.file_disk');

    $oldExport = createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(73),
    ]);

    $recentExport = createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(1),
    ]);

    $oldExportFilePath = exportFilePath($oldExport);
    $recentExportFilePath = exportFilePath($recentExport);

    // Assert before act
    expect(Storage::disk($disk)->exists($oldExportFilePath))->toBeTrue()
        ->and(Storage::disk($disk)->exists($recentExportFilePath))->toBeTrue();

    // Act
    app(FilamentExportCleanup::class)->run();

    // Expect
    expect(Storage::disk($disk)->exists($oldExportFilePath))->toBeFalse();
    expect(Storage::disk($disk)->exists($recentExportFilePath))->toBeTrue();

    $this->assertDatabaseHas('exports', ['id' => $recentExport->id]);
    $this->assertDatabaseHas('exports', ['id' => $oldExport->id]);
});

it('does nothing when enabled is false', function () {
    // Arrange
    config()->set('filament-export-cleanup.enabled', false);
    $userId = createTestUserId();
    $disk = config('filament-export-cleanup.file_disk');

    $oldExport = createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(73),
    ]);

    $recentExport = createCompletedExportWithFile($userId, [
        'completed_at' => now()->subHours(1),
    ]);

    $oldExportFilePath = exportFilePath($oldExport);
    $recentExportFilePath = exportFilePath($recentExport);

    // Assert before act
    expect(Storage::disk($disk)->exists($oldExportFilePath))->toBeTrue()
        ->and(Storage::disk($disk)->exists($recentExportFilePath))->toBeTrue();

    // Act
    app(FilamentExportCleanup::class)->run();

    // Expect
    expect(Storage::disk($disk)->exists($oldExportFilePath))->toBeTrue();
    expect(Storage::disk($disk)->exists($recentExportFilePath))->toBeTrue();

    $this->assertDatabaseHas('exports', ['id' => $recentExport->id]);
    $this->assertDatabaseHas('exports', ['id' => $oldExport->id]);
});
