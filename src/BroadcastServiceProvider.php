<?php

namespace Ikoncept\Fabriq;

use Illuminate\Broadcasting\BroadcastServiceProvider as ServiceProvider;
// use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

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
