# Filament Export Cleanup

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pachristos/filament-export-cleanup.svg?style=flat-square)](https://packagist.org/packages/pachristos/filament-export-cleanup)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/Christos-Papoulas/filament-export-cleanup/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Christos-Papoulas/filament-export-cleanup/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pachristos/filament-export-cleanup.svg?style=flat-square)](https://packagist.org/packages/pachristos/filament-export-cleanup)

Automatically remove old Filament export files from disk and optionally delete their `exports` table rows.

[Filament](https://filamentphp.com) table exports store files on disk and keep metadata in the database. Over time these accumulate. This package finds completed exports older than a configurable retention period, deletes their file directories, and optionally removes the database records.

## Requirements

- PHP 8.2+
- Laravel 11 or 12
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
| `schedule.expression` | `FILAMENT_EXPORT_CLEANUP_SCHEDULE_EXPRESSION` | `dailyAt('02:00')` | When to run (see [Scheduling](#scheduling)) |

Set `file_disk` to the same disk your Filament exports use (`local` or `public`). S3 and other remote disks are not supported yet.

## Usage

### Scheduled cleanup (recommended)

When `schedule.enabled` is `true`, the package registers `cleanup:filament-export` on Laravel's scheduler. Ensure your server runs the scheduler (for example via cron):

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

The default schedule runs daily at 02:00.

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

The `schedule.expression` config accepts:

- A `dailyAt('HH:MM')` string (default: `dailyAt('02:00')`)
- A standard cron expression (for example `0 3 * * 0` for Sundays at 03:00)

Examples in `.env`:

```dotenv
FILAMENT_EXPORT_CLEANUP_SCHEDULE_EXPRESSION="dailyAt('03:30')"
# or
FILAMENT_EXPORT_CLEANUP_SCHEDULE_EXPRESSION="0 3 * * *"
```

Disable automatic scheduling and run cleanup only manually or from your own scheduler:

```dotenv
FILAMENT_EXPORT_CLEANUP_SCHEDULE_ENABLED=false
```

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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
