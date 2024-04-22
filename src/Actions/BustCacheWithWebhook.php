<?php

namespace Ikoncept\Fabriq\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\WebhookServer\WebhookCall;

class BustCacheWithWebhook
{
    public function handle(Collection $tagsToFlush)
    {
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
