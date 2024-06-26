<?php

namespace Ikoncept\Fabriq\Tests;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class AdminUserTestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Fabriq::routes(function ($router) {
            $router->all();
        });

        Fabriq::routes(function ($router) {
            $router->allWeb();
        }, [
            'middleware' => ['web'],
        ]);

        $this->setUpDatabase($this->app);

        Artisan::call('fabriq:install');
        Artisan::call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-translations',
        ]);

        $user = User::factory()->create([
            'name' => 'Albin N',
            'email' => 'albin@infab.io',
            'password' => bcrypt('secret'),
        ]);

        $this->user = $user;
        $this->actingAs($user);
    }

    public function tearDown(): void
    {
        $dir = storage_path('/app/public/__test');
        File::deleteDirectory($dir);

        parent::tearDown();
    }

    public function setUpDatabase($app)
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('fabriq.webhooks.enabled', false);
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('filesystems.disks.__test', [
            'driver' => 'local',
            'root' => storage_path('app/public/__test'),
            'url' => env('APP_URL').'/storage/__test',
            'visibility' => 'public',
        ]);

        $app['config']->set('fabriq.models.user', \Ikoncept\Fabriq\Models\User::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Ikoncept\Fabriq\FabriqCoreServiceProvider::class,
            \Ikoncept\Fabriq\FortifyServiceProvider::class,
        ];
    }
}
