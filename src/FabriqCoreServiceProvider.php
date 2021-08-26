<?php

namespace Ikoncept\Fabriq;

use Illuminate\Support\ServiceProvider;
use Infab\Core\CoreServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\SanctumServiceProvider;

class FabriqCoreServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/fabriq.php' => config_path('fabriq.php'),
        ], 'config');

        $this->registerResources();
    }

    protected function registerResources() : void
    {
        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes() : void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api-admin-protected.php');
        });

    }


    /**
     * Get the Nova route group configuration array.
     *
     * @return array
     */
    protected function routeConfiguration()
    {
        return [
            'prefix' => 'api/admin',
            'middleware' => ['auth:sanctum', 'role:admin', 'verified'],
        ];
    }



    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fabriq.php', 'fabriq');
        $this->app->register('Spatie\Permission\PermissionServiceProvider');
        $this->app->register('Infab\TranslatableRevisions\TranslatableRevisionsServiceProvider');
        $this->app->register(SanctumServiceProvider::class);
        $this->app->register(CoreServiceProvider::class);

        $this->app['router']->aliasMiddleware('role', \Spatie\Permission\Middlewares\RoleMiddleware::class);

    }
}
