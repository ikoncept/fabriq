<?php

namespace Ikoncept\Fabriq;

use Ikoncept\Fabriq\Console\Commands\InstallFabriqCommand;
use Ikoncept\Fabriq\Console\Commands\PublishControllerCommand;
use Ikoncept\Fabriq\Repositories\Decorators\CachingMenuRepository;
use Ikoncept\Fabriq\Repositories\Decorators\CachingPageRepository;
use Ikoncept\Fabriq\Repositories\EloquentMenuRepository;
use Ikoncept\Fabriq\Repositories\EloquentPageRepository;
use Ikoncept\Fabriq\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infab\Core\CoreServiceProvider;
use Infab\TranslatableRevisions\TranslatableRevisionsServiceProvider;
use League\Fractal\Manager;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
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

        $this->loadMigrationsFrom([realpath(__DIR__.'/../database/migrations')]);

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang'),
        ], 'fabriq-translations');

        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js'),
            __DIR__.'/../resources/images' => resource_path('images'),
            __DIR__.'/../resources/css' => resource_path('css'),
            __DIR__.'/../tailwind.config.js' => 'tailwind.config.js',
            __DIR__.'/../webpack.mix.js' => 'webpack.mix.js',
            __DIR__.'/../package.json' => 'package.json',
            __DIR__.'/../jsconfig.json' => 'jsconfig.json',
            __DIR__.'/../.eslintrc' => '.eslintrc',
            __DIR__.'/../.babelrc' => '.babelrc',
            __DIR__.'/../.styleci.yml' => '.styleci.yml',
            __DIR__.'/../yarn.lock' => 'yarn.lock',
        ], 'fabriq-frontend-assets');
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
        $this->app->register(MediaLibraryServiceProvider::class);
        $this->app->register(QueryBuilderServiceProvider::class);
        $this->app->register(MacroServiceProvider::class);

        $this->commands([
            PublishControllerCommand::class,
            InstallFabriqCommand::class,
        ]);


        $this->app->singleton(PageRepositoryInterface::class, function () {
            $pageClass = config('fabriq.modelMap.page');
            $slugClass = config('fabriq.modelMap.slug');
            $baseRepo = new EloquentPageRepository(new $pageClass, new $slugClass);
            $cachingRepo = new CachingPageRepository($baseRepo, $this->app['cache.store']);
            return $cachingRepo;
        });

        $this->app->singleton('Ikoncept\Fabriq\Repositories\Interfaces\MenuRepositoryInterface', function () {
            $menuItemClass = config('fabriq.modelMap.menuItem');
            $menuClass = config('fabriq.modelMap.menu');
            $baseRepo = new EloquentMenuRepository(new Manager(), new $menuItemClass, new $menuClass);
            $cachingRepo = new CachingMenuRepository($baseRepo, $this->app['cache.store']);
            return $cachingRepo;
        });

    }
}
