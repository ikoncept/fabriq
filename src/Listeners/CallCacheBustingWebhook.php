<?php

namespace Ikoncept\Fabriq\Listeners;

use Ikoncept\Fabriq\Actions\BustCacheWithWebhook;
use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Services\CacheBuster;
use Infab\TranslatableRevisions\Events\DefinitionsUpdated;

class CallCacheBustingWebhook
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

        $model = $event->model;

        if (! config('fabriq.webhooks.enabled')) {
            return;
        }

        // Case for pages, skip busting on update
        if (get_class($event) === DefinitionsUpdated::class && Fabriq::getFqnModel('page') === get_class($model)) {
            return;
        }

        $tagsToFlush = (new CacheBuster)->getCacheTags($model);

        if (! $tagsToFlush->count()) {
            return;
        }

        (new BustCacheWithWebhook)->handle($tagsToFlush);
    }
}
