# Filament Export Cleanup

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pachristos/filament-export-cleanup.svg?style=flat-square)](https://packagist.org/packages/pachristos/filament-export-cleanup)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/Christos-Papoulas/filament-export-cleanup/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Christos-Papoulas/filament-export-cleanup/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pachristos/filament-export-cleanup.svg?style=flat-square)](https://packagist.org/packages/pachristos/filament-export-cleanup)

Automatically remove old Filament export files from disk and optionally delete their `exports` table rows.

[Filament](https://filamentphp.com) table exports store files on disk and keep metadata in the database. Over time these accumulate. This package finds completed exports older than a configurable retention period, deletes their file directories, and optionally removes the database records.

## Requirements

- PHP 8.2+
- Laravel 11, 12 or 13
- [Filament Actions](https://filamentphp.com) (exports) v4 or v5

## Installation

Install the package via Composer:

```bash
composer require pachristos/filament-export-cleanup
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="filament-export-cleanup-config"
```

Or use the install command, which publishes the config for you:

```bash
php artisan filament-export-cleanup:install
```

The package auto-registers its service provider. No further setup is required beyond configuration.

## Configuration

Configuration lives in `config/filament-export-cleanup.php`. All options can be overridden with environment variables.

| Option | Env variable | Default | Description |
|--------|--------------|---------|-------------|
| `enabled` | `FILAMENT_EXPORT_CLEANUP_ENABLED` | `true` | Master switch for cleanup |
| `retention_hours` | `FILAMENT_EXPORT_CLEANUP_RETENTION_HOURS` | `72` | Delete exports completed more than this many hours ago |
| `delete_database_records` | `FILAMENT_EXPORT_CLEANUP_DELETE_DATABASE_RECORDS` | `true` | Also delete rows from the `exports` table |
| `file_disk` | `FILAMENT_EXPORT_CLEANUP_FILE_DISK` | `local` | Filesystem disk to clean (must match your Filament export disk) |
| `schedule.enabled` | `FILAMENT_EXPORT_CLEANUP_SCHEDULE_ENABLED` | `true` | Register the cleanup command on the Laravel scheduler |
| `schedule.frequency` | — | Weekdays at `02:00` | When to run (see [Scheduling](#scheduling)) |

Set `file_disk` to the same disk your Filament exports use (`local` or `public`). S3 and other remote disks are not supported yet.

## Usage

### Scheduled cleanup (recommended)

When `schedule.enabled` is `true`, the package registers `cleanup:filament-export` on Laravel's scheduler. Ensure your server runs the scheduler (for example via cron):

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

The default schedule runs on weekdays at 02:00.

### Manual cleanup

Run the Artisan command at any time:

```bash
php artisan cleanup:filament-export
```

The command reports how many export file directories were removed, or that nothing needed deletion.

### Programmatic cleanup

Resolve the service from the container:

```php
use Pachristos\FilamentExportCleanup\FilamentExportCleanup;

$deletedIds = app(FilamentExportCleanup::class)->run();
```

Or use the facade:

```php
use FilamentExportCleanup;

$deletedIds = FilamentExportCleanup::run();
```

`run()` returns an array of deleted export IDs. When `enabled` is `false`, it returns an empty array without doing any work.

## Scheduling

The `schedule.frequency` config controls when the cleanup command is registered on Laravel's scheduler. You can configure it with any of Laravel's [schedule frequency options](https://laravel.com/docs/13.x/scheduling#schedule-frequency-options) by passing a `Closure` that receives the scheduled `Event` and chains any combination of frequency methods (`dailyAt()`, `hourly()`, `weekdays()`, `cron()`, timezone constraints, etc.). A raw cron expression string is also accepted.

The default runs the cleanup on weekdays at 02:00:

```php
use Illuminate\Console\Scheduling\Event;

'schedule' => [
    'enabled' => env('FILAMENT_EXPORT_CLEANUP_SCHEDULE_ENABLED', true),
    'frequency' => fn (Event $event) => $event
        ->weekdays()
        ->dailyAt('02:00'),
],
```

Use any frequency method (or combination) Laravel supports. A few examples:

```php
'frequency' => fn (Event $event) => $event->hourly(),

'frequency' => fn (Event $event) => $event
    ->twiceDaily(1, 13)
    ->timezone('Europe/Athens'),

'frequency' => fn (Event $event) => $event
    ->weeklyOn(0, '03:00'),
```

Or supply a raw cron expression directly:

```php
'frequency' => '0 3 * * 0',
```

Disable automatic scheduling and run cleanup only manually or from your own scheduler:

```dotenv
FILAMENT_EXPORT_CLEANUP_SCHEDULE_ENABLED=false
```

See the [Laravel schedule frequency options](https://laravel.com/docs/13.x/scheduling#schedule-frequency-options) documentation for the full list of available methods.

## How it works

1. Query the Filament `exports` table for records where `completed_at` is older than `retention_hours` and `file_disk` matches your configured disk.
2. Delete each export's file directory from disk via Filament's `Export` model.
3. If `delete_database_records` is `true`, delete the matching rows from the `exports` table.

Exports still within the retention window are left untouched.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Sponsoring

Development of this package is supported by [Christos Papoulas](https://github.com/sponsors/Christos-Papoulas).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
