<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('Commands have a Command suffix')
    ->expect('Pachristos\FilamentExportCleanup\Commands')
    ->classes()
    ->toHaveSuffix('Command');

arch('Facades classes extend Facade')
    ->expect('Pachristos\FilamentExportCleanup\Facades')
    ->classes()
    ->toExtend('Illuminate\Support\Facades\Facade');
