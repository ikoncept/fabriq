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
                $router->forInternalArticles();
            }
        );
        $this->setUpDatabase($this->app);
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        Artisan::call('fabriq:install');

        // include_once(__DIR__  . '/../database/migrations/2014_10_12_000000_create_users_table.php');
        // (new \CreateUsersTable())->up();
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

        // $this->artisan('vendor:publish', [
        //     '--provider' => 'Infab\TranslatableRevisions\TranslatableRevisionsServiceProvider'
        // ]);
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));
        // $this->artisan('migrate', [
        //     '--database' => 'sqlite',
        //     '--realpath' => realpath(__DIR__.'/../database/migrations'),
        // ]);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Ikoncept\Fabriq\FabriqCoreServiceProvider::class,
        ];
    }

}
