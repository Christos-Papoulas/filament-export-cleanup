<?php

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

it('registers the cleanup command on the schedule when scheduling is enabled', function () {
    config()->set('filament-export-cleanup.schedule.enabled', true);
    config()->set(
        'filament-export-cleanup.schedule.frequency',
        fn (Event $event) => $event->dailyAt('02:00')
    );

    $event = scheduledCleanupEvent();

    expect($event)->toBeInstanceOf(Event::class)
        ->and($event->expression)->toBe('0 2 * * *');
});

it('can change the frequency of the clean up to something else', function () {
    config()->set('filament-export-cleanup.schedule.enabled', true);
    config()->set(
        'filament-export-cleanup.schedule.frequency',
        fn (Event $event) => $event->hourly()->weekdays()
    );

    $event = scheduledCleanupEvent();

    expect($event)->toBeInstanceOf(Event::class)
        ->and($event->expression)->toBe('0 * * * 1-5');
});

it('accepts a raw cron expression string for the frequency', function () {
    config()->set('filament-export-cleanup.schedule.enabled', true);
    config()->set('filament-export-cleanup.schedule.frequency', '15 4 * * 0');

    $event = scheduledCleanupEvent();

    expect($event)->toBeInstanceOf(Event::class)
        ->and($event->expression)->toBe('15 4 * * 0');
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
