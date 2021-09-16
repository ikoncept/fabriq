<?php

namespace Ikoncept\Fabriq;

use Ikoncept\Fabriq\Listeners\UpdateSlugListener;
use Infab\TranslatableRevisions\Events\DefinitionsUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        DefinitionsUpdated::class => [
            UpdateSlugListener::class
        ]
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
