<?php

namespace Ikoncept\Fabriq\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class CalendarService
{
    /**
     * Get daily intervals
     *
     * @param object $events
     * @param CarbonImmutable $endDate
     * @return Collection
     */
    public static function getComputedDailyIntervals($events, CarbonImmutable $endDate) : Collection
    {
        $now = CarbonImmutable::now();
        $computedEvents = collect([]);
        foreach($events as $event) {
            if($event->daily_interval) {
                $toBeModified = Carbon::make($event->start);
                $toBeModifiedEnd = Carbon::make($event->end);
                while ($endDate->timestamp >= $toBeModified->timestamp) {
                    $newEvent = $event->replicate();
                    $newEvent->id = $event->id;
                    $newEvent->start = Carbon::make($toBeModified)->addDays($event->daily_interval);
                    $newEvent->end = Carbon::make($toBeModifiedEnd)->addDays($event->daily_interval);
                    $newEvent->updated_at = $now;
                    $toBeModified->addDays($event->daily_interval);
                    $toBeModifiedEnd->addDays($event->daily_interval);
                    $computedEvents->push($newEvent);
                }
            }
        }
        return $computedEvents;
    }
}
