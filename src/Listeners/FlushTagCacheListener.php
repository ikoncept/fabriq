<?php

namespace Ikoncept\Fabriq\Listeners;

use Illuminate\Support\Facades\Cache;

class FlushTagCacheListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $tagsToFlush = $event->model->getRevisionOptions()->cacheTagsToFlush;

        if (! $tagsToFlush) {
            return;
        }

        Cache::tags($tagsToFlush)->flush();
    }
}
