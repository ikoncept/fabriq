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

        $this->info('Installing controllers');
        $files = scandir(__DIR__ . '/../../../stubs');
        $names = collect($files)->map(function($item) {
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
        return 0;
    }
}
