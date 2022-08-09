<?php

namespace Ikoncept\Fabriq;

use Ikoncept\Fabriq\Listeners\BustPageCacheListener;
use Ikoncept\Fabriq\Listeners\FlushTagCacheListener;
use Ikoncept\Fabriq\Listeners\UpdateSlugListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Infab\TranslatableRevisions\Events\DefinitionsPublished;
use Infab\TranslatableRevisions\Events\DefinitionsUpdated;
use Infab\TranslatableRevisions\Events\TranslatedRevisionDeleted;
use Infab\TranslatableRevisions\Events\TranslatedRevisionUpdated;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DefinitionsUpdated::class => [
            UpdateSlugListener::class,
        ],
        DefinitionsPublished::class => [
            BustPageCacheListener::class,
        ],
        TranslatedRevisionDeleted::class => [
            FlushTagCacheListener::class,
        ],
        TranslatedRevisionUpdated::class => [
            FlushTagCacheListener::class,
        ],
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
