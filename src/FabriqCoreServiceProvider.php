<?php

namespace Ikoncept\Fabriq;

use Ikoncept\Fabriq\Console\Commands\InstallFabriqCommand;
use Ikoncept\Fabriq\Console\Commands\PublishControllerCommand;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Infab\Core\CoreServiceProvider;
use Illuminate\Support\Facades\Route;
use Infab\TranslatableRevisions\TranslatableRevisionsServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;
use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use Spatie\Permission\PermissionServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;

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
        ], 'fabriq-config');

        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));

        $this->registerResources();
    }

    protected function registerResources() : void
    {
        // $this->registerRoutes();
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
        $this->app->register(EventServiceProvider::class);
        $this->app->register(TranslatableRevisionsServiceProvider::class);
        $this->app->register(CoreServiceProvider::class);
        $this->app->register(PermissionServiceProvider::class);
        $this->app->register(QueryBuilderServiceProvider::class);
        $this->app->register(MacroServiceProvider::class);

        $this->commands([
            PublishControllerCommand::class,
            InstallFabriqCommand::class,
        ]);

    }
}
