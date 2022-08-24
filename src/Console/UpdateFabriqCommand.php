<?php

namespace Ikoncept\Fabriq\Console;

use Illuminate\Console\Command;

class UpdateFabriqCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Fabriq CMS';

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
        $this->info('Updating front end assets');
        $this->info('Checking if working directory is clean');
        $result = exec('git status --short');

        if ((bool) $result) {
            $answer = $this->ask('Working directory not clean, continue anyways?', 'yes', ['yes', 'no']);
            if ($answer == 'no') {
                $this->info('Okay, exiting');

                return 0;
            }
        }

        $this->call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-frontend-assets',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--provider' => 'Ikoncept\Fabriq\FabriqCoreServiceProvider',
            '--tag' => 'fabriq-views',
            '--force' => true,
        ]);

        $this->info('Front end assets has been installed');

        $this->info('Migrating...');
        $this->call('migrate');

        $this->info('Fabriq has been updated');

        return 0;
    }
}
