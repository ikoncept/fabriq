<?php

namespace Ikoncept\Fabriq;

use Dyrynda\Artisan\Console\Commands\MakeUser;
use Ikoncept\Fabriq\Console\AddRoleToUserCommand;
use Ikoncept\Fabriq\Console\ControllerMakeCommand;
use Ikoncept\Fabriq\Console\CreateMenuCommand;
use Ikoncept\Fabriq\Console\CreatePageRootCommand;
use Ikoncept\Fabriq\Console\MakeRevisionField;
use Ikoncept\Fabriq\Console\InstallFabriqCommand;
use Ikoncept\Fabriq\Console\PublishNotification;
use Ikoncept\Fabriq\Console\ResourceMakeCommand;
use Ikoncept\Fabriq\Console\SendNotificationReminders;
use Ikoncept\Fabriq\Console\TransformerMakeCommand;
use Ikoncept\Fabriq\Console\UpdateFabriqCommand;
use Ikoncept\Fabriq\Console\VueApiModelMakeCommand;
use Ikoncept\Fabriq\Console\VueEditTemplateMakeCommand;
use Ikoncept\Fabriq\Console\VueIndexTemplateMakeCommand;
use Ikoncept\Fabriq\Console\VueResourceMakeCommand;
use Ikoncept\Fabriq\Repositories\Decorators\CachingMenuRepository;
use Ikoncept\Fabriq\Repositories\Decorators\CachingPageRepository;
use Ikoncept\Fabriq\Repositories\EloquentMenuRepository;
use Ikoncept\Fabriq\Repositories\EloquentPageRepository;
use Ikoncept\Fabriq\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Infab\Core\CoreServiceProvider;
use Infab\TranslatableRevisions\TranslatableRevisionsServiceProvider;
use League\Fractal\Manager;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;
use Illuminate\Support\Str;

class FabriqCoreServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fabriq.php' => config_path('fabriq.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../config/fortify.php' => config_path('fortify.php'),
            ], 'config');

            $this->loadMigrationsFrom([realpath(__DIR__.'/../database/migrations')]);

            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang'),
            ], 'fabriq-translations');

            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs'),
            ], 'fabriq-stubs');

            // Used for updates, exludes user routes files
            $this->publishes($this->updatePaths(), 'fabriq-frontend-assets');

            // Used for fresh installs
            $this->publishes($this->installPaths(), 'fabriq-frontend-install-assets');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views'),
            ], 'fabriq-views');
        }
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fabriq.php', 'fabriq');
        $this->mergeConfigFrom(__DIR__.'/../config/fortify.php', 'fortify');
        $this->mergeConfigFrom(
            __DIR__.'/../config/ikoncept-websockets.php',
            'broadcasting.connections'
        );

        $this->app->register(BroadcastServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(TranslatableRevisionsServiceProvider::class);
        $this->app->register(CoreServiceProvider::class);
        $this->app->register(PermissionServiceProvider::class);
        $this->app->register(MediaLibraryServiceProvider::class);
        $this->app->register(QueryBuilderServiceProvider::class);
        $this->app->register(MacroServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(FortifyServiceProvider::class);

        $this->app->get('config')->set(
            'media-library',
            array_merge($this->app->get('config')->get('media-library'), $this->app->get('config')->get('fabriq.media-library'))
        );

        $this->commands([
            AddRoleToUserCommand::class,
            ControllerMakeCommand::class,
            CreateMenuCommand::class,
            CreatePageRootCommand::class,
            MakeRevisionField::class,
            InstallFabriqCommand::class,
            PublishNotification::class,
            ResourceMakeCommand::class,
            SendNotificationReminders::class,
            TransformerMakeCommand::class,
            VueApiModelMakeCommand::class,
            VueEditTemplateMakeCommand::class,
            VueIndexTemplateMakeCommand::class,
            VueResourceMakeCommand::class,
            MakeUser::class,
            UpdateFabriqCommand::class
        ]);

        $this->app->singleton(PageRepositoryInterface::class, function () {
            $baseRepo = new EloquentPageRepository(Fabriq::getModelClass('slug'));
            $cachingRepo = new CachingPageRepository($baseRepo, $this->app->get('cache.store'));
            return $cachingRepo;
        });

        $this->app->singleton('Ikoncept\Fabriq\Repositories\Interfaces\MenuRepositoryInterface', function () {
            $baseRepo = new EloquentMenuRepository(new Manager(), Fabriq::getModelClass('menuItem'), Fabriq::getModelClass('menu'));
            $cachingRepo = new CachingMenuRepository($baseRepo, $this->app->get('cache.store'));
            return $cachingRepo;
        });
    }

    protected function updatePaths() : array
    {
        list($updatePaths, $installPaths) = $this->resourceDirectories();

        $merged = array_merge($updatePaths->toArray(), [
            __DIR__.'/../resources/js/routes/fabriq-routes.js' => resource_path('js/routes/fabriq-routes.js'),
            __DIR__.'/../resources/js/routes/router.js' => resource_path('js/routes/router.js')
        ]);

        return  array_merge($merged, $this->standardPaths());
    }

    protected function installPaths() : array
    {
        list($updatePaths, $installPaths) = $this->resourceDirectories();

        $merged = array_merge($updatePaths->toArray(), $installPaths->toArray());
        return array_merge($merged, $this->standardPaths());
    }

    protected function resourceDirectories() : array
    {
        $resourceDirectories = (array) glob(__DIR__.'/../resources/js/*');

        list($updateFolders, $installFolders) = collect($resourceDirectories)->mapWithKeys(function ($item) {
            $path = pathinfo((string)$item, PATHINFO_BASENAME);

            return [__DIR__.'/../resources/js/' . $path => resource_path('js/' . $path)];
        })
            ->partition(function ($item, $key) {
                return ! Str::contains((string) $key, 'routes');
            });
        return [$updateFolders, $installFolders];
    }

    protected function standardPaths() : array
    {
        return [
            __DIR__.'/../resources/css' => resource_path('css'),
            __DIR__.'/../resources/images' => public_path('images'),
            __DIR__.'/../resources/js' => resource_path('js'),
            __DIR__.'/../resources/fonts' => public_path('fonts'),
            __DIR__.'/../tailwind.config.js' => 'tailwind.config.js',
            __DIR__.'/../vite.config.js' => 'vite.config.js',
            __DIR__.'/../postcss.config.js' => 'postcss.config.js',
            __DIR__.'/../package.json' => 'package.json',
            __DIR__.'/../jsconfig.json' => 'jsconfig.json',
            __DIR__.'/../.eslintrc' => '.eslintrc',
            __DIR__.'/../.babelrc' => '.babelrc',
            __DIR__.'/../.styleci.yml' => '.styleci.yml',
            __DIR__.'/../pnpm-lock.yaml' => 'pnpm-lock.yaml',
            __DIR__.'/../.npmrc' => '.npmrc'
       ];
    }
}
