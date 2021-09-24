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
     * @param  DefinitionsPublished  $event
     * @return void
     */
    public function handle(DefinitionsPublished $event)
    {
        $tagName = Str::lower(class_basename($event->model));
        foreach($event->model->slugs as $slug) {
            Log::info('Flushing page cache', ['name' => $event->model->name, 'key' => 'cms_' . $tagName . '_' . $slug->slug]);
            Cache::tags('cms_' . $tagName . '_' . $slug->slug)->flush();

            Log::info('Flushing menu cache');
            Cache::tags('cms_menu')->flush();
        }
    }
}
