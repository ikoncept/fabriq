<?php

namespace Ikoncept\Fabriq\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class InstallFabriqCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs Fabriq CMS into the Laravel project';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $installControllers = false;

        if (config('app.env') != 'testing') {
            $installControllers =  $this->choice('Do you want to publish controllers?', ['Yes', 'No'], 1);
        }
        if($installControllers) {
            $this->info('Installing controllers');
            $files = scandir(__DIR__ . '/../../../stubs');
            $names = collect($files)->filter(function($item){
                return Str::contains($item, 'Controller');
            })->map(function($item) {
                return Str::singular(explode('.stub', $item)[0]);
            })->filter(function($item) {
                return $item !== '..' && $item !== '.';
            });

            foreach($names as $name) {
                $this->call('fabriq:publish-controller', [
                    'name' => 'Fabriq/' . $name,
                    '--model' => 'N\A',
                    '--stub' => $name . '.stub'
                ]);
            }

            $this->info('All controllers has been installed');
        }

        $this->info('Installing front end assets');
        $this->call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-frontend-assets',
            '--force' => true
        ]);
        $this->call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-views',
            '--force' => true
        ]);

        $this->info('Front end assets has been installed');

        $this->info('Installing translations');
        $this->call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-translations',
        ]);


        $this->info('Translations has been installed');


        $this->info('Migrating...');
        $this->call('migrate');

        $this->info('Creating page root');

        if (config('app.env') != 'testing') {
            $this->call('fabriq:create-page-root');
        }

        $this->info('Fabriq has been installed');

        return 0;
    }
}
