# Changelog

All notable changes to `filament-export-cleanup` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.1.1](https://github.com/Christos-Papoulas/filament-export-cleanup/compare/1.1.0...1.1.1) - 2026-07-31

### Added

- Documented security disclosure policy (`.github/SECURITY.md`) for private vulnerability reporting.

### Documentation

- Documented Laravel 13 support in the README requirements.
- Added Plumb score badge to the README.

### Changed

- Pinned GitHub Actions to commit SHAs and bumped `actions/checkout` to v7.

## [v1.1.0](https://github.com/Christos-Papoulas/filament-export-cleanup/compare/v1.0.1...v1.1.0) - 2026-05-21

### Changed

- Replaced `schedule.expression` with `schedule.frequency`, which accepts a `Closure` receiving the scheduled `Event` and supports any of Laravel's [schedule frequency options](https://laravel.com/docs/13.x/scheduling#schedule-frequency-options) (chained methods like `dailyAt()`, `hourly()`, `weekdays()`, `twiceDaily()`, `timezone()`, etc.) or a raw cron expression string.
- Default schedule changed from daily at 02:00 to weekdays at 02:00.
- Removed the `FILAMENT_EXPORT_CLEANUP_SCHEDULE_EXPRESSION` environment variable; configure `schedule.frequency` directly in `config/filament-export-cleanup.php`.

### Documentation

- Updated `README.md` to document the new `schedule.frequency` option with examples and a link to Laravel's schedule frequency options.

## [v1.0.1](https://github.com/Christos-Papoulas/filament-export-cleanup/releases/tag/1.0.0/compare/v1.0.0...v1.0.1) - 2026-05-19

- update docs

## [v1.0.0](https://github.com/Christos-Papoulas/filament-export-cleanup/releases/tag/1.0.0/compare/1.0.0...v1.0.0) - 2026-05-19

- Initial release of `pachristos/filament-export-cleanup`
- `cleanup:filament-export` Artisan command to remove old Filament export files
- Automatic Laravel scheduler registration (daily at 02:00 by default, configurable)
- Configurable retention period (`retention_hours`, default 72 hours)
- Optional deletion of `exports` database records (`delete_database_records`)
- Master enable/disable switch (`enabled`)
- Filesystem disk filter (`file_disk`) for `local` and `public` disks
- `filament-export-cleanup:install` command to publish configuration
- `FilamentExportCleanup` service and facade for programmatic cleanup
- Support for Filament Actions v4 and v5
- Support for Laravel 11 and 12
