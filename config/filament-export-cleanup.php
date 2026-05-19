<?php

// config for Pachristos/FilamentExportCleanup

return [
    /**
     * Master switch to enable/disable the cleanup process.
     */
    'enabled' => env('FILAMENT_EXPORT_CLEANUP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    |
    | Delete files and `exports` table rows older than this hours are removed.
    |
    */
    'retention_hours' => env('FILAMENT_EXPORT_CLEANUP_RETENTION_HOURS', 72),

    /**
     * Whether to delete the database records of the exports.
     */
    'delete_database_records' => env('FILAMENT_EXPORT_CLEANUP_DELETE_DATABASE_RECORDS', true),

    /**
     * The disk to use for the export files.
     * This should be the same as the disk used for the export files.
     * It accepts 'local' or 'public'.
     * Currently s3 is not supported.
     */
    'file_disk' => env('FILAMENT_EXPORT_CLEANUP_FILE_DISK', 'local'),

    'schedule' => [
        'enabled' => env('FILAMENT_EXPORT_CLEANUP_SCHEDULE_ENABLED', true),
        'expression' => env('FILAMENT_EXPORT_CLEANUP_SCHEDULE_EXPRESSION', "dailyAt('02:00')"),
    ],
];
