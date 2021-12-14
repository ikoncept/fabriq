<?php

namespace Ikoncept\Fabriq;

use Illuminate\Support\Facades\Broadcast;
// use Illuminate\Support\ServiceProvider;
use Illuminate\Broadcasting\BroadcastServiceProvider as ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes(['middleware' => ['api', 'auth:sanctum']]);

        require __DIR__.'/../routes/channels.php';
    }
}
