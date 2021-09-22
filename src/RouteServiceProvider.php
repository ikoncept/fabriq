<?php

namespace Ikoncept\Fabriq;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Middlewares\RoleMiddleware;

class RouteServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('role', RoleMiddleware::class);
    }
}

