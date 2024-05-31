<?php

namespace Ikoncept\Fabriq\Actions;

use Illuminate\Support\Facades\RateLimiter;
use Spatie\WebhookServer\WebhookCall;

class BustCacheWithWebhook
{
    public function handle(array $keysToForget, array $tagsToFlush = [])
    {
        // 1 per 5 seconds for the same key
        RateLimiter::attempt(
            key: hash('adler32', json_encode([$keysToForget, $tagsToFlush])),
            maxAttempts: 1,
            callback: function () use ($keysToForget, $tagsToFlush) {
                foreach (explode(',', config('fabriq.webhooks.endpoint')) as $url) {
                    WebhookCall::create()
                        ->url($url)
                        ->payload([
                            'type' => 'cache_expiration',
                            'invalid_cache_keys' => $keysToForget,
                            'invalid_cache_tags' => $tagsToFlush,
                        ])
                        ->useSecret(config('fabriq.webhooks.secret'))
                        ->dispatch();
                }
            },
            decaySeconds: 1
        );
    }
}
