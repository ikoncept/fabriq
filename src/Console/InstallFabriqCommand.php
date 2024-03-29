<?php

namespace Ikoncept\Fabriq\Console;

use Illuminate\Console\Command;

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
        $this->info('Installing front end assets');
        $this->call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-frontend-install-assets',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-views',
            '--force' => true,
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
            $this->call('fabriq:create-page-root', [
                '--silent' => true,
            ]);
            $answer = $this->ask('Do you want to create a new user?', 'yes');

            if ($answer === 'yes') {
                $this->call('make:user');
                $this->call('fabriq:add-role-to-user');
            }
        }

        $this->info('Fabriq has been installed');

        return 0;
    }
}
