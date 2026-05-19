<?php

declare(strict_types=1);

namespace Pachristos\FilamentExportCleanup\Facades;

use Illuminate\Support\Facades\Facade;

class FilamentExportCleanup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Pachristos\FilamentExportCleanup\FilamentExportCleanup::class;
    }
}
