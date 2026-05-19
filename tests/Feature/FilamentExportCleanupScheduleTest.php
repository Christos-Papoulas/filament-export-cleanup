<?php

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

it('registers the cleanup command on the schedule when scheduling is enabled', function () {
    config()->set('filament-export-cleanup.schedule.enabled', true);
    config()->set('filament-export-cleanup.schedule.expression', "dailyAt('02:00')");

    $event = scheduledCleanupEvent();

    expect($event)->toBeInstanceOf(Event::class)
        ->and($event->expression)->toBe('0 2 * * *');
});

it('does not register the cleanup command on the schedule when scheduling is disabled', function () {
    config()->set('filament-export-cleanup.schedule.enabled', false);

    expect(scheduledCleanupEvent())->toBeNull();
});

function scheduledCleanupEvent(): ?Event
{
    $schedule = app(Schedule::class);

    return collect($schedule->events())
        ->first(fn (Event $event): bool => str_contains($event->command ?? '', 'cleanup:filament-export'));
}
