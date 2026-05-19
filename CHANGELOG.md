# Changelog

All notable changes to `filament-export-cleanup` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

## [1.0.0](https://github.com/Christos-Papoulas/filament-export-cleanup/releases/tag/1.0.0) - 2026-05-19

### Added

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
