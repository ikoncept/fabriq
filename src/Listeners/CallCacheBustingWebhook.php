<?php

namespace Ikoncept\Fabriq\Listeners;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Support\Facades\RateLimiter;
use Infab\TranslatableRevisions\Events\DefinitionsUpdated;
use Spatie\WebhookServer\WebhookCall;

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

        $tagsToFlush = collect($event->model->getRevisionOptions()->cacheTagsToFlush)->map(function ($tag) use ($model) {
            $parts = explode('|', $tag);
            if (isset($parts[1])) {
                $key = $parts[1];

                if ($key === 'slug' && $model->slugs) {

                    $slugs = collect([]);
                    foreach ($model->slugs as $slug) {
                        $slugs->push("{$parts[0]}_{$parts[1]}_{$slug->slug}");
                    }

                    return $slugs->toArray();
                }

                return "{$parts[0]}_{$parts[1]}_{$model->$key}";
            }

            return $tag;
        })->flatten();

        if (! $tagsToFlush->count()) {
            return;
        }

        // 1 per 5 seconds for the same key
        RateLimiter::attempt(
            key: hash('adler32', json_encode($tagsToFlush)),
            maxAttempts: 1,
            callback: function () use ($tagsToFlush) {
                WebhookCall::create()
                    ->url(config('fabriq.webhooks.endpoint'))
                    ->payload([
                        'type' => 'cache_expiration',
                        'invalid_cache_tags' => $tagsToFlush->toArray(),
                    ])
                    ->useSecret(config('fabriq.webhooks.secret'))
                    ->dispatch();
            },
            decaySeconds: 1
        );
    }
}
