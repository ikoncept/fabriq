<?php

namespace Ikoncept\Fabriq\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Infab\TranslatableRevisions\Events\DefinitionsPublished;

class BustPageCacheListener
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
     * @return void
     */
    public function handle(DefinitionsPublished $event)
    {
        $tagName = Str::lower(class_basename($event->model));

        Log::info('Flushing menu cache');
        Cache::tags('fabriq_menu')->flush();

        if (! $event->model->slugs) {
            return;
        }

        foreach ($event->model->slugs as $slug) {
            Log::info('Flushing page cache', ['name' => $event->model->name, 'key' => 'fabriq_'.$tagName.'_'.$slug->slug]);
            Cache::tags('fabriq_'.$tagName.'_'.$slug->slug)->flush();
        }
    }
}
