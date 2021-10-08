<?php

namespace Ikoncept\Fabriq\Tests;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\Sanctum;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class AdminUserTestCase extends Orchestra
{
    public function setUp() : void
    {
        parent::setUp();
        Fabriq::routes(
            function ($router) {
                $router->all();
            }
        );
        $this->setUpDatabase($this->app);

        Artisan::call('fabriq:install');
        Artisan::call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-translations'
        ]);

        $user = User::factory()->create([
            'name' => 'Albin N',
            'email' => 'albin@infab.io',
            'password' => bcrypt('secret')
        ]);

        $this->actingAs($user);
    }

    public function tearDown() : void
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
        $app['config']->set('fabriq.models.user',  \Ikoncept\Fabriq\Models\User::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Ikoncept\Fabriq\FabriqCoreServiceProvider::class,
            \Ikoncept\Fabriq\FortifyServiceProvider::class
        ];
    }

}
